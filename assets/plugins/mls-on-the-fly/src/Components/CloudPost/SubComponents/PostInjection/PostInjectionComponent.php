<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection;

use Exception;
use Realtyna\Core\Abstracts\ComponentAbstract;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\CacheManager\Cache;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\MetaBoxes\RawDataMetaBox;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\SubComponents\AttachmentHandler;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\SubComponents\CloudPostIdHandler;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\SubComponents\PostMetaHandler;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Exceptions\EntityNotDefinedException;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RF;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RFQuery;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RFResponse;
use Realtyna\MlsOnTheFly\Settings\Settings;
use stdClass;
use WP_Post;
use WP_Query;
use WP_Term_Query;

class PostInjectionComponent extends ComponentAbstract
{
    private IntegrationInterface $integration;
    private Mapping $mapping;
    private RF $RF;
    private int $cloudPostId;
    private int $thumbnailID = -20000000;

    /**
     * @throws \ReflectionException
     */
    public function register(): void
    {
        $this->integration = App::get(IntegrationInterface::class);
        $this->mapping = App::get(Mapping::class);
        $this->RF = App::get(RF::class);
        $this->cloudPostId = App::get(CloudPostIdHandler::class)->getCloudPostId();

        App::get(PostMetaHandler::class);
        App::get(AttachmentHandler::class);
        App::get(RawDataMetaBox::class);

        $this->registerHooks();
        $this->registerAdminHooks();
    }

    private function registerHooks(): void
    {
        add_action('rest_api_init', [$this, 'actionInjectCachedPosts']);
        add_filter('posts_pre_query', [$this, 'injectPostsFromRF'], 10, 2);
        add_action('init', [$this, 'actionInjectCachedPosts']);

        add_filter('terms_pre_query', [$this, 'injectTermsFromRF'], 10, 2);
        add_action('edited_term_taxonomy', [$this, 'updateTermCountEditedTerm']);
    }

    private function registerAdminHooks(): void
    {
        add_action('admin_init', [$this, 'actionInjectCachedPosts']);
        add_action('clean_post_cache', [$this, 'actionInjectCachedPosts']);
        add_action('updated_post_meta', [$this, 'actionInjectCachedPosts']);
        add_action('pre_post_update', [$this, 'actionInjectCachedPosts'], 10, 2);
    }



    /**
     * Injects data from the Realty Feed (RF) into WordPress posts based on the query.
     *
     * This method is called as a filter for 'posts_pre_query' in WordPress. It checks if the
     * query is related to the target custom post types, and if so, it injects data from RF into
     * the posts returned by the query. The method also handles error logging and post attachment.
     *
     * @return array|null The modified array of WordPress posts after injection.
     * @throws DependencyException
     * @throws NotFoundException
     * @author Chandler P <chandler@realtyna.net>
     * @package Realtyna
     * @subpackage MLS_ON_THE_FLY
     * @version 1.3.0
     */
    public function injectPostsFromRF(array|null $posts, WP_Query $wp_query): array|null
    {
        // Extract post types from the query
        $postTypes = $this->extractPostTypesFromQuery($wp_query);

        // Check if injection is needed for the post types in the query
        if (isset($wp_query->query['inject']) && !$wp_query->query['inject']) {
            $needToInject = false;
        } else {
            $needToInject = (bool)array_intersect($postTypes, array_merge($this->integration->customPostTypes, ['advance_search_property']));
        }


        if ($needToInject) {


            // We don't have such a thing as parent and child properties so we return nothing
            if(isset($wp_query->query['post_parent']) && is_numeric($wp_query->query['post_parent'])){
                return $posts;
            }

            $this->updatePostTypesInQuery($wp_query, $postTypes);

            // Create and execute the RF query
            $RFQuery = $this->mapping->mapWPQueryRFQuery($wp_query);

            $RFQuery = apply_filters('realtyna_mls_on_the_fly_query', $RFQuery, $wp_query, $this->integration->name);
            $RFQuery = apply_filters('realtyna_rf_shell_query', $RFQuery, $wp_query, $this->integration->name);

            try {
                $RFResponse = $this->RF->get($RFQuery);
            } catch (EntityNotDefinedException|Exception $e) {
                Log::error($e->getMessage(), [$e]);
                return $posts;
            }

            $cacheKey = $RFQuery->getCacheKey();

            // Attempt to get cached posts from Cache class
            $cachedPosts = Cache::get("RF-Query-Cache-Posts-$cacheKey");
            if ($cachedPosts) {
                $posts = $cachedPosts;
            } else {
                $properties = $RFResponse->items;
                if (isset($wp_query->query['data_type']) && $wp_query->query['data_type'] == 'raw') {
                    return $properties->toArray();
                }
                $this->getPreviousPostIds($properties);
                $posts = $this->mapRFProperties($properties, $RFResponse, $wp_query);
            }

            if ($posts == null) {
                Log::error('Did not create any posts');
                return $posts;
            }

            Log::info('Mapped ' . count($posts) . ' posts');
            $this->attachMedias($posts, $wp_query->is_single);
            $this->addPostsToCache($posts);

            if(!$cachedPosts){
                // Add the posts to Cache class
                Cache::set("RF-Query-Cache-Posts-$cacheKey", $posts, 300);
            }


            $this->updateWPQueryProperties($wp_query, $RFResponse, $posts);

            $fields = $wp_query->query['fields'] ?? false;
            $posts = ($fields === 'ids') ? array_column($posts, 'ID') : $posts;

        } else {
            $posts = $this->handlePostParentQuery($wp_query, $posts);
        }

        return $posts;
    }


    /**
     * Extracts post types from the WP_Query object's request.
     *
     * @param WP_Query $wp_query The WordPress query object.
     *
     * @return array Extracted post types.
     */
    private function extractPostTypesFromQuery(WP_Query $wp_query): array
    {
        $postTypes = [];

        if ($wp_query->request != null) {
            preg_match_all("/_posts.post_type = '([^']+)'/", $wp_query->request, $postTypes);
            $postTypes = $postTypes[1];
        }

        if (empty($postTypes) && isset($wp_query->query['post_type'])) {
            $postTypes = is_array(
                $wp_query->query['post_type']
            ) ? $wp_query->query['post_type'] : [$wp_query->query['post_type']];
        }

        return $postTypes;
    }

    /**
     * Updates the post_type in the WP_Query object.
     *
     * @param WP_Query $wp_query The WordPress query object.
     * @param array $postTypes The post types to update.
     *
     * @return void
     */
    private function updatePostTypesInQuery(WP_Query $wp_query, array $postTypes): void
    {
        $wp_query->query['post_type'] = $postTypes;
        Log::info('Injecting data from RF to post type: ', [$this->integration->customPostTypes, $wp_query->query]);
    }

    /**
     * Get and update previous post IDs for listings.
     *
     * @param \Illuminate\Support\Collection $listings The collection of listings.
     * @return void
     */
    /**
     * Get and update previous post IDs for listings using WPDB.
     *
     * @param array $listings The array of listings.
     * @return void
     */
    public function getPreviousPostIds(array &$listings): void
    {
        global $wpdb;

        // Table name with prefix
        $table_name = $wpdb->prefix . 'realtyna_rf_mappings';
        $listingKeys = [];
        foreach ($listings as $listing) {
            // Assuming ListingKey is accessible via a method or property, e.g., $property->getListingKey()
            $listingKeys[] = $listing->ListingKey ?? 0;
        }
        // Prepare placeholders for the query
        $placeholders = implode(',', array_fill(0, count($listingKeys), '%s'));

        $existingMappings = [];
        if(!empty($listingKeys)){
            // Retrieve existing mappings for all listing keys in a single query
            $query = $wpdb->prepare(
                "SELECT listing_key, post_id 
        FROM $table_name 
        WHERE listing_key IN ($placeholders)",
                ...$listingKeys
            );

            $existingMappings = $wpdb->get_results($query, OBJECT_K);
        }



        // Create an array to store mapping data for new listings
        $mappingData = [];
        $currentTimestamp = current_time('mysql');
        foreach ($listings as &$listing) {
            $listingKey = $listing->ListingKey;
            if (isset($existingMappings[$listingKey])) {
                // Existing mapping found, get the post_id
                $cloudPostId = $existingMappings[$listingKey]->post_id;

                // Update the 'updated_at' timestamp
                $wpdb->update(
                    $table_name,
                    ['updated_at' => $currentTimestamp],
                    ['listing_key' => $listingKey],
                    ['%s'],
                    ['%s']
                );
            } else {
                // No entry exists, create a new RFMapping entry
                $cloudPostId = $this->cloudPostId;
                $mappingData[] = $wpdb->prepare(
                    "(%s, %d, %s, %s)",
                    $listingKey,
                    $cloudPostId,
                    $currentTimestamp,
                    $currentTimestamp
                );

                // Increment the cloudPostId counter
                $this->cloudPostId++;
            }

            // Assign the calculated post ID to the listing and update post_author
            $listing->post_id = $cloudPostId;
            $listing->ID = $cloudPostId;
            $listing->post_author = 1;
        }

        // Insert new mappings in bulk
        if (!empty($mappingData)) {
            $query = "INSERT INTO $table_name (listing_key, post_id, updated_at, created_at) VALUES " . implode(
                    ',',
                    $mappingData
                );
            $wpdb->query($query);
        }
    }

    /**
     * Map RF properties to WP posts.
     *
     * @param array $properties The collection of RF properties.
     * @param WP_Query $wp_query The WordPress query object.
     * @return array|null The mapped posts array or null if mapping failed.
     *
     * @since 1.4.7.1
     * @package Realtyna
     * @subpackage MLS_ON_THE_FLY
     */
    private function mapRFProperties($properties, RFResponse $RFResponse, WP_Query $wp_query): ?array
    {
        $posts = $this->mapping->mapRFPropertiesToWPPost($properties, $wp_query);
        $posts = apply_filters('realtyna_mls_on_the_fly_cloud_posts', $posts, $properties, $RFResponse);
        $posts = apply_filters('realtyna_rf_shell_cloud_posts', $posts, $properties, $RFResponse);
        return $posts;
    }


    /**
     * Attaches media items to WordPress posts.
     *
     * This method attaches media items to WordPress posts based on the provided data.
     *
     * @param array $posts An array of WordPress posts to which media items will be attached.
     * @param bool $injectAllMedias If true, all media items are attached; otherwise, only one media item per post is attached.
     * @return array An array of WordPress media attachments that have been attached to the posts.
     */
    public function attachMedias(array &$posts, bool $injectAllMedias = false): array
    {
        // Array to store media attachments
        $attachments = [];

        foreach ($posts as $post) {
            // Skip if the current item is not an instance of WP_Post
            if (!($post instanceof \WP_Post)) {
                continue;
            }

            // Initialize the meta data for the post
            $post->meta_data['fave_attachments'] = [];
            $post->meta_data['fave_property_images'] = [];
            $post->meta_data['wpcf-property-featured-image'] = [];
            $post->meta_data['wpcf-property-images'] = [];
            $post->meta_data['REAL_HOMES_property_images'] = [];

            // Check if the post has any associated media
            if (isset($post->medias)) {
                $photosCount = 0; // Counter to limit the number of photos processed

                foreach ($post->medias as $media) {
                    // Skip the first photo if there are multiple photos (special handling for the first photo)
                    if ($photosCount === 0 && count($post->medias) > 1) {
                        $photosCount++;
                        continue;
                    }
                    // Determine the appropriate media URL to use based on settings and injectAllMedias flag
                    if (!$injectAllMedias) {
                        $mediaURL = $media['Thumbnail'] ?? $media['MediaURL'];
                    } else {
                        $mediaURL = (Settings::get_setting('pictures_size', 'thumbnail') === 'thumbnail')
                            ? $media['Thumbnail']
                            : $media['MediaURL'];
                    }

                    // Create a new media attachment object with the media details
                    $attachment = new stdClass();
                    $attachment->ID = $this->thumbnailID;
                    $attachment->post_title = $media['LongDescription'] ?? $media['ShortDescription'] ?? '';
                    $attachment->post_name = 1;
                    $attachment->post_status = 'inherit';
                    $attachment->post_type = 'attachment';
                    $attachment->post_mime_type = "image/jpeg";
                    $attachment->filter = "raw";
                    $attachment->media_url = $mediaURL;
                    $attachment->post_parent = $post->ID;

                    // Add the new attachment to the list of attachments
                    $attachments[] = new WP_Post($attachment);

                    // Set the '_thumbnail_id' meta data if it is not already set
                    if ($post->meta_data != null && !isset($post->meta_data['_thumbnail_id'])) {
                        $post->meta_data['_thumbnail_id'] = $attachment->ID;
                        $this->thumbnailID -= 100; // Decrement the thumbnail ID for uniqueness
                        continue;
                    }

                    // Update various meta fields with the media information
                    $post->meta_data['fave_property_images'][] = $this->thumbnailID;
                    $post->meta_data['wpcf-property-featured-image'][] = $mediaURL;
                    $post->meta_data['wpcf-property-images'][] = $mediaURL;
                            $post->meta_data['REAL_HOMES_property_images'][] = $this->thumbnailID;

                    $this->thumbnailID -= 100; // Decrement the thumbnail ID for uniqueness
                    $photosCount++; // Increment the photo counter

                    // Stop processing if we have reached the limit and injectAllMedias is false
                    if (!$injectAllMedias && $photosCount > 10) {
                        break;
                    }
                }
            }

            // Cache the processed media attachments for the post
            $this->addPostsToCache($attachments);
        }

        // Return the list of attachments
        return $attachments;
    }

    public function addPostsToCache($posts): void
    {
        foreach ($posts as $post) {
            if ($post instanceof WP_Post) {
                $this->addPostToCache($post);
            }
        }
    }

    /**
     * Adds a post to the WordPress object cache.
     *
     * This method is responsible for adding a post to the WordPress object cache, including
     * its meta_data if available. It ensures that the post and its metadata are correctly
     * cached for improved performance.
     *
     * @param WP_Post $post The WordPress post object to be cached.
     */
    public function addPostToCache(WP_Post $post): void
    {
        // Save post metadata in a separate variable and unset it from the post object
        if ($post->meta_data) {
            $meta = $post->meta_data;

            // To ensure compatibility with post-metas, wrap each value in an array
            foreach ($meta as $key => $value) {
                if (is_array($value)) {
                    $meta[$key] = $value;
                } else {
                    $meta[$key] = [$value];
                }
            }

            // Merge the post meta from the object with the post meta from the database
            $DBMeta = get_post_meta($post->ID);
            if ($meta && $DBMeta) {
                $meta = array_merge($meta, $DBMeta);
            }

            // Delete and set the post's meta cache
            wp_cache_set($post->ID, $meta, 'post_meta', 30);
        }
        // Delete and set the post cache
        wp_cache_set($post->ID, $post, 'posts', 30);
        wp_cache_set($post->ID, $post, '', 30);
    }

    /**
     * Update WP_Query properties with RF response data.
     *
     * @param WP_Query $wp_query The WordPress query object.
     * @param mixed $RFResponse The RF response.
     * @param array $posts The array of posts.
     *
     * @since 1.4.7.1
     * @package Realtyna
     * @subpackage MLS_ON_THE_FLY
     */
    private function updateWPQueryProperties(WP_Query $wp_query, $RFResponse, array $posts): void
    {
        $wp_query->posts = $posts;
        $wp_query->post_count = $RFResponse->count ?? count($posts);
        $wp_query->found_posts = $RFResponse->count ?? count($posts);
        $wp_query->max_num_pages = $RFResponse->page_count ?? null;
        if ($wp_query->query_vars['fields'] == 'id=>parent') {
            $wp_query->query_vars['fields'] = '';
        }
        if (isset($wp_query->query['orderby']) && $wp_query->query['orderby'] == 'menu_order title') {
            $wp_query->query['orderby'] = '';
        }
        if (isset($wp_query->query['posts_per_page']) && $wp_query->query['posts_per_page'] == -1) {
            $wp_query->query['posts_per_page'] = 200;
        }
    }

    /**
     * Handles the case where post_parent is set in the query.
     *
     * @param WP_Query $wp_query The WordPress query object.
     * @param array|null $posts The original array of WordPress posts.
     *
     * @return array|null The modified array of WordPress posts after injection.
     */
    private function handlePostParentQuery(WP_Query $wp_query, array|null $posts): ?array
    {
        if (isset($wp_query->query['post_parent']) && $wp_query->post_type == 'attachment') {
            $post_parent = get_post($wp_query->query['post_parent']);
            if (isset($post_parent->medias)) {
                $posts = [$post_parent];
                $posts = $this->attachMedias($posts, true);
            }
        }

        return $posts;
    }


    /**
     * Action to inject cached posts based on a specific post ID or post data.
     *
     * This method is used as an action in WordPress. It checks for a specific post ID or post data
     * in the request (both POST and GET data) and, if found, retrieves corresponding data from the
     * Realty Feed (RF) and injects it into the WordPress cache.
     * @throws EntityNotDefinedException
     */
    public function actionInjectCachedPosts(): void
    {
        global $wpdb;

        // Merge POST and GET data to look for a specific post ID or post data
        $data = array_merge($_POST, $_GET);
        if (isset($data['ids']) && $data['ids']) {
            $postIDs = explode(',', $data['ids']);
        } else {
            $postIDs = $data['post_id'] ?? $data['post'] ?? $data['post_ID'] ?? 0;
        }
        if (!is_array($postIDs)) {
            $postIDs = [$postIDs];
        }

        foreach ($postIDs as $postID) {
            $postID = apply_filters('realtyna_mls_on_the_fly_current_post_id_to_inject', $postID, $data);
            if ($postID) {
                // Check if an RFMapping exists for the specified post ID
                $table_name = $wpdb->prefix . 'realtyna_rf_mappings';
                $RFMapping = $wpdb->get_row(
                    $wpdb->prepare("SELECT * FROM $table_name WHERE post_id = %d", $postID)
                );

                if ($RFMapping) {
                    // Configure the RFQuery to retrieve data for the 'Property' entity with the matching ListingKey
                    $RFQuery = new RFQuery();
                    $RFQuery->set_entity('Property');
                    $RFQuery->add_filter('orWhere', 'ListingKey', 'eq', $RFMapping->listing_key);

                    // Retrieve data from the Realty Feed (RF)
                    $RFResponse = $this->RF->get($RFQuery);
                    $listings = $RFResponse->items;

                    // Update or create RFMapping entries and assign post IDs to listings
                    $this->getPreviousPostIds($listings);

                    $posts = [];
                    foreach ($listings as $listing) {
                        $posts[] = $this->mapping->mapRFPropertyToWPPost($listing);
                    }

                    $posts = apply_filters('realtyna_mls_on_the_fly_cloud_posts', $posts, $listings, $RFQuery);

                    // Add the injected posts to the cache
                    $this->attachMedias($posts, true);
                    $this->addPostsToCache($posts);
                }
            }
        }
    }

    /**
     * Injects terms from the RFTerm model based on the provided parameters.
     *
     * @param array|null $terms The original terms.
     * @param WP_Term_Query $term_query The WordPress term query.
     *
     * @return array|int|null The modified terms after injection.
     * @author Chandler P <chandler@realtyna.net>
     * @version 1.3.0
     */
    public function InjectTermsFromRF(
        array|null $terms,
        WP_Term_Query $term_query
    ): array|null|int {
        $taxonomies = $term_query->query_vars['taxonomy'] ?? [];
        // Check if injection is needed for the taxonomy in the query
        $needToInject = (bool)array_intersect($taxonomies, $this->integration->customTaxonomies);

        if ($needToInject) {
            $term_query->query_vars['hide_empty'] = false;
            if (isset($term_query->query_vars['object_ids']) && !empty($term_query->query_vars['object_ids'])) {
                $postIDs = $term_query->query_vars['object_ids'];
                $terms = [];
                foreach ($postIDs as $postID) {
                    $RFProperty = get_post_meta($postID, 'realty_feed_raw_data', true);

                    if ($RFProperty) {
                        $terms = array_merge(
                            $terms,
                            $this->mapping->mapRFPropertyToWPTerm($RFProperty, $taxonomies, $term_query)
                        );
                    }
                }
            } else {
                return $terms;
            }
        }

        return $terms;
    }


    /**
     * Update term count edited term
     *
     * @param \WP_Term|int $term
     *
     * @return void
     * @author Cyrus <cyrus.a@realtyna.com>
     */
    public function updateTermCountEditedTerm(\WP_Term|int $term): void
    {
        $term_id = is_numeric($term) ? $term : $term['term_id'];
        $meta_key = 'realtyna_mls_on_the_fly_term_count';
        $count = get_term_meta($term_id, $meta_key, true);
        if ($count) {
            $this->updateTermCount($term_id, $count);
        }
    }

    /**
     * Update term count
     *
     * @param int $term_id
     * @param int $count
     *
     * @return void
     * @author Cyrus <cyrus.a@realtyna.com>
     *
     */
    public function updateTermCount(int $term_id, int $count): void
    {
        if (!$term_id) {
            return;
        }

        update_term_meta($term_id, 'realtyna_mls_on_the_fly_term_count', $count);

        global $wpdb;
        $wpdb->update($wpdb->term_taxonomy, array('count' => $count), array('term_taxonomy_id' => $term_id));
    }

}