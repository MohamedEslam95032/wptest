<?php
namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Compatibilities\SitemapProviders;


use DI\DependencyException;
use DI\NotFoundException;
use OutOfBoundsException;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use WP_Query;
use wpl_db;
use wpl_global;
use wpl_property;
use wpl_settings;
use WPSEO_Meta;
use WPSEO_Post_Type;
use WPSEO_Sitemap_Image_Parser;
use WPSEO_Sitemap_Provider;
use WPSEO_Sitemaps;
use WPSEO_Sitemaps_Router;
use Yoast\WP\SEO\Models\SEO_Links;

/**
 * Sitemap provider for the property post type.
 */
class WPSEO_Cloud_Post_Type_Sitemap_Provider implements WPSEO_Sitemap_Provider
{
    protected static $image_parser; // Holds an instance of the image parser
    protected static $parsed_home_url; // Holds the parsed home URL
    private $include_images; // Whether to include images in the sitemap
    private string $last_modification_date; // Stores the last modification date of the post type
    private IntegrationInterface $activeIntegration; // Holds the active integration instance

    /**
     * Constructor initializes the class and sets up filters.
     */
    public function __construct()
    {
        $this->include_images = false;
        try {
            // Retrieve the active integration instance from the container
            $this->activeIntegration = App::get(IntegrationInterface::class);
        } catch (DependencyException|NotFoundException $e) {
            // Handle exceptions silently
        }

        // Filter to adjust the number of sitemap entries per page based on the active integration
        add_filter('wpseo_sitemap_entries_per_page', function (){
            return 50; // Set to 50
        });
    }

    /**
     * Retrieves the image parser instance.
     *
     * @return WPSEO_Sitemap_Image_Parser
     */
    protected function get_image_parser()
    {
        if (!isset(self::$image_parser)) {
            self::$image_parser = new WPSEO_Sitemap_Image_Parser();
        }
        return self::$image_parser;
    }

    /**
     * Retrieves the parsed home URL.
     *
     * @return array|false
     */
    protected function get_parsed_home_url()
    {
        if (!isset(self::$parsed_home_url)) {
            self::$parsed_home_url = wp_parse_url(home_url());
        }
        return self::$parsed_home_url;
    }

    /**
     * Determines if the provider handles the given post type.
     *
     * @param string $type The post type.
     * @return bool
     */
    public function handles_type($type)
    {
        $type = str_replace('cloud-', '', $type);
        return in_array($type, $this->activeIntegration->customPostTypes);
    }

    /**
     * Generates index links for the sitemap.
     *
     * @param int $max_entries Maximum number of entries per page.
     * @return array
     */
    public function get_index_links($max_entries)
    {
        $post_types = $this->activeIntegration->customPostTypes;
        $index = [];
        $last_modified_times = [];

        foreach ($post_types as $post_type) {
            $total_count = $this->get_post_type_count($post_type);
            $last_modified_times[$post_type] = $this->last_modification_date;
            if ($total_count === 0) {
                continue;
            }

            $max_pages = ($total_count > $max_entries) ? (int)ceil($total_count / $max_entries) : 1;

            $date = $last_modified_times[$post_type];
            if($date == '0000-00-00 00:00:00'){
                $date = date('Y-m-d H:i:s');
                $date = date('Y-m-d H:i:s', strtotime($date . ' - 2 hours'));
            }

            for ($page_counter = 0; $page_counter < $max_pages; $page_counter++) {
                $current_page = ($page_counter === 0) ? '' : ($page_counter + 1);
                $random_offset = rand(0, 86399); // Random seconds in a day (0 to 23 hours, 59 minutes, 59 seconds)
                $index[] = [
                    'loc' => WPSEO_Sitemaps_Router::get_base_url(
                        'cloud-' . $post_type . '-sitemap' . $current_page . '.xml'
                    ),
                    'lastmod' => date('Y-m-d H:i:s', strtotime($date . ' - ' . intval($page_counter * .1) . ' days - ' . $random_offset . ' seconds')),
                ];
            }

        }
        return $index;
    }

    /**
     * Generates the sitemap links for a specific post type and page.
     *
     * @param string $type The post type.
     * @param int $max_entries Maximum number of entries per page.
     * @param int $current_page The current page number.
     * @return array
     * @throws OutOfBoundsException If the requested page is invalid.
     */
    public function get_sitemap_links($type, $max_entries, $current_page)
    {
        $type = str_replace('cloud-', '', $type);
        if (!in_array($type, $this->activeIntegration->customPostTypes)) {
            throw new OutOfBoundsException('Invalid sitemap page requested');
        }

        $links = [];
        $steps = min(100, $max_entries);
        $offset = ($current_page > 1) ? (($current_page - 1) * $max_entries) : 0;
        $total = ($offset + $max_entries);
        $post_type_entries = $this->get_post_type_count($type);

        if ($total > $post_type_entries) {
            $total = $post_type_entries;
        }

        if ($current_page === 1) {
            $links = array_merge($links, $this->get_first_links($type));
        }

        if ($post_type_entries < $offset) {
            throw new OutOfBoundsException('Invalid sitemap page requested');
        }

        if ($post_type_entries === 0) {
            return $links;
        }

        while ($total > $offset) {
            $posts = $this->get_posts($type, $steps, $offset);
            $offset += $steps;

            if (empty($posts)) {
                continue;
            }

            foreach ($posts as $post) {
                if (WPSEO_Meta::get_value('meta-robots-noindex', $post->ID) === '1') {
                    continue;
                }

                $url = $this->get_url($post);
                if (!isset($url['loc'])) {
                    continue;
                }

                $url = apply_filters('wpseo_sitemap_entry', $url, 'post', $post);

                if (!empty($url)) {
                    $links[] = $url;
                }
            }
        }
        return $links;
    }

    /**
     * Validates if the post type is accessible and indexable.
     *
     * @param string $post_type The post type.
     * @return bool
     */
    public function is_valid_post_type($post_type)
    {
        return $post_type === 'property' && WPSEO_Post_Type::is_post_type_accessible($post_type) &&
            WPSEO_Post_Type::is_post_type_indexable($post_type) && !apply_filters(
                'wpseo_sitemap_exclude_post_type',
                false,
                $post_type
            );
    }

    /**
     * Retrieves the count of posts for a given post type.
     *
     * @param string $post_type The post type.
     * @return int
     */
    protected function get_post_type_count($post_type)
    {
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new WP_Query($args);

        if (count($query->posts) > 0 && isset($query->posts[0])) {
            $lastPost = $query->posts[0];
            $this->last_modification_date = $lastPost->post_date;
        }

        return $query->found_posts;
    }

    /**
     * Retrieves the first set of links for the post type archive.
     *
     * @param string $post_type The post type.
     * @return array
     */
    protected function get_first_links($post_type)
    {
        $links = [];
        $archive_url = get_post_type_archive_link($post_type);

        if ($archive_url) {
            $links[] = [
                'loc' => $archive_url,
                'mod' => WPSEO_Sitemaps::get_last_modified_gmt($post_type),
                'chf' => 'daily',
                'pri' => 1,
            ];
        }

        return apply_filters('wpseo_sitemap_post_type_first_links', $links, $post_type);
    }

    /**
     * Retrieves a set of posts based on type, count, and offset.
     *
     * @param string $type The post type.
     * @param int $count The number of posts to retrieve.
     * @param int $offset The offset to start retrieving posts.
     * @return array
     */
    protected function get_posts($type, $count, $offset)
    {
        $args = [
            'post_type' => $type,
            'posts_per_page' => $count,
            'offset' => $offset,
            'orderby' => 'modified',
            'order' => 'ASC',
        ];

        $query = new WP_Query($args);
        $posts = $query->posts;
        $post_ids = wp_list_pluck($posts, 'ID');

        update_meta_cache('post', $post_ids);

        return $posts;
    }

    /**
     * Generates the URL for a specific post, handling different integrations.
     *
     * @param object $post The post object.
     * @return array|false
     */
    protected function get_url($post)
    {
        $url = [];

        if ($this->activeIntegration->name == 'wpl') {
            if (isset($post->post_name) && $post->post_name != '') {
                // Retrieve necessary metadata for the property
                $post->meta_data['zip_id'] = $post->meta_data['wpl_zip_name'] ?? null;
                $mls_id = $post->meta_data['wpl_mls_id'];
                $ref_id = $post->meta_data['wpl_ref_id'];
                $source = $post->meta_data['wpl_source'];
                $kind = $post->meta_data['wpl_kind'];
                $rfData = (array)$post->meta_data['realty_feed_raw_data'];

                if (empty($kind)) {
                    $kind = 0;
                    $post->meta_data['kind'] = 0;
                }
                $rf_property = new \wpl_rf_property();
                $rows = $rf_property->rf_after_mapping([$post]);
                $wplUrl = \wpl_property::get_property_link($rf_property->parse_post_meta($rows[0], true));
                $url['loc'] = $wplUrl;
            }
        } else {
            $url['loc'] = apply_filters('wpseo_xml_sitemap_post_url', get_permalink($post), $post);
        }

        // Validate if the URL is external
        $link_type = YoastSEO()->helpers->url->get_link_type(wp_parse_url($url['loc']), $this->get_parsed_home_url());

        if ($link_type === SEO_Links::TYPE_EXTERNAL) {
            return false;
        }

        // Set the modification date and other attributes
        $modified = max($post->post_modified_gmt, $post->post_date_gmt);
        if ($modified !== '0000-00-00 00:00:00') {
            $url['mod'] = $modified;
        }

        $url['chf'] = 'daily';

        // Check for canonical URL
        $canonical = WPSEO_Meta::get_value('canonical', $post->ID);
        if ($canonical && $canonical !== $url['loc']) {
            return false;
        }

        $url['pri'] = 1;

        // Optionally include images in the sitemap
        if ($this->include_images) {
            $this->get_image_parser()->parse_images($post);
            $images = $this->get_image_parser()->get_images();

            if (!empty($images)) {
                $url['images'] = $images;
            }
        }

        return $url;
    }

    /**
     * Retrieves an array of modification dates for all posts in a post type.
     *
     * @param string $post_type The post type.
     * @param int $max_entries The maximum number of entries per page.
     * @return array
     */
    protected function get_all_dates($post_type, $max_entries)
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT post_modified_gmt
            FROM $wpdb->posts
            WHERE post_type = %s
                AND post_status IN ( 'publish', 'inherit' )
                AND ( post_password = '' OR post_password IS NULL )
            ORDER BY post_modified_gmt ASC",
            $post_type
        );

        $dates = $wpdb->get_col($query);

        if (empty($dates)) {
            return [];
        }

        $date_index = [];
        $total_dates = count($dates);

        for ($i = 1; $i <= ceil($total_dates / $max_entries); $i++) {
            $position = $i * $max_entries;
            $position = ($position >= $total_dates) ? ($total_dates - 1) : $position;
            $date_index[] = $dates[$position];
        }

        return $date_index;
    }
}
