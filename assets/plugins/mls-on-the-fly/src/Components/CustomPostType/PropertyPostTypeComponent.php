<?php

namespace Realtyna\MlsOnTheFly\Components\CustomPostType;

use Realtyna\Core\Abstracts\ComponentAbstract;
use Realtyna\MlsOnTheFly\Components\CustomPostType\Models\Basic;
use Realtyna\MlsOnTheFly\Components\CustomPostType\Models\Price;

/**
 * Register property CPT
 *
 * @author Melvin <melvin.g@realtyna.com>
 *
 * @version 1.0.0
 */
class PropertyPostTypeComponent extends ComponentAbstract
{
    /**
     * This function calls the necessary hooks
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function register(): void
    {
        /** Register Property CPT */
        add_action('init', [$this, 'registerPropertyPostType']);

        /** Set property's url slug */
        add_filter('mls_otf_property_slug', [$this, 'setPropertySlug']);
        add_filter('mls_otf_property_rest_base', [$this, 'setPropertySlug']);

        /** Register Property related custom taxonomies */
        add_action('init', [$this, 'registerPropertyTaxonomies'], 0);

        /** Set property feature's url slug */
        add_filter('mls_otf_property_feature_slug', [$this, 'setPropertyFeatureSlug']);
        add_filter('mls_otf_property_feature_rest_base', [$this, 'setPropertyFeatureSlug']);

        /** Set property type's url slug */
        add_filter('mls_otf_property_type_slug', [$this, 'setPropertyTypeSlug']);
        add_filter('mls_otf_property_type_rest_base', [$this, 'setPropertyTypeSlug']);

        /** Set property location's url slug */
        add_filter('mls_otf_property_city_slug', [$this, 'setPropertyCitySlug']);
        add_filter('mls_otf_property_city_rest_base', [$this, 'setPropertyCitySlug']);

        /** Set property status's url slug */
        add_filter('mls_otf_property_status_slug', [$this, 'setPropertyStatusSlug']);
        add_filter('mls_otf_property_status_rest_base', [$this, 'setPropertyStatusSlug']);

        /** Custom columns for properties */
        add_filter('manage_edit-property_columns', [$this, 'propertyEditColumns']);

        /** Property custom columns */
        add_action('manage_pages_custom_column', [$this, 'propertyCustomColumns'], 10, 2);

        /** Make property price column sortable */
        add_filter('manage_edit-property_sortable_columns', [$this, 'sortablePriceColumn']);

        /** Sort properties based on price */
        add_action('pre_get_posts', [$this, 'priceOrderby']);

        /** Modify property list row actions */
        add_filter('page_row_actions', [$this, 'modifyPropertyListRowActions'], 10, 2);

        /** Clone property as draft */
        add_action('admin_action_x_wpl_clone_property_as_draft', [$this, 'clonePropertyPostAsDraft']);

        /** Expiration of the property */
        add_action('admin_action_x_wpl_expire_listing', [$this, 'expireListing']);

        /** Publish of the property */
        add_action('admin_action_x_wpl_publish_listing', [$this, 'publishListing']);

        /** Add custom post status */
        add_action('init', [$this, 'customPostStatus'], 1);

    }

    /**
     * Register Property CPT
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function registerPropertyPostType(): void
    {
        if (post_type_exists('mof_property')) {
            return;
        }

        $propertyPostTypeLabels = array(
            'name' => esc_html__('Properties', 'mls-on-the-fly'),
            'singular_name' => esc_html__('Property', 'mls-on-the-fly'),
            'add_new' => esc_html__('Add New', 'mls-on-the-fly'),
            'add_new_item' => esc_html__('Add New Property', 'mls-on-the-fly'),
            'edit_item' => esc_html__('Edit Property', 'mls-on-the-fly'),
            'new_item' => esc_html__('New Property', 'mls-on-the-fly'),
            'view_item' => esc_html__('View Property', 'mls-on-the-fly'),
            'search_items' => esc_html__('Search Property', 'mls-on-the-fly'),
            'not_found' => esc_html__('No Property found', 'mls-on-the-fly'),
            'not_found_in_trash' => esc_html__('No Property found in Trash', 'mls-on-the-fly'),
            'parent_item_colon' => esc_html__('Parent Property', 'mls-on-the-fly'),
        );

        $propertyPostTypeArgs = array(
            'labels' => apply_filters('mls_otf_property_post_type_labels', $propertyPostTypeLabels),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'has_archive' => true,
            'capability_type' => 'post',
            'hierarchical' => true,
            'menu_icon' => 'dashicons-building',
            'menu_position' => 5,
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'revisions',
                'author',
                'page-attributes',
                'excerpt',
                'comments'
            ),
            'rewrite' => array(
                'slug' => apply_filters('mls_otf_property_slug', 'property'),
            ),
            'show_in_rest' => true,
            'rest_base' => apply_filters('mls_otf_property_rest_base', 'properties'),
        );

        $propertyPostTypeArgs = apply_filters('mls_otf_property_post_type_args', $propertyPostTypeArgs);

        register_post_type('mof_property', $propertyPostTypeArgs);
    }

    /**
     * This function set property's url slug by hooking itself with related filter.
     *
     * @param string $existingSlug - Existing property slug.
     *
     * @return string
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function setPropertySlug($existingSlug)
    {
        $newSlug = get_option('_x_wpl_property_slug');

        if (!empty($newSlug)) {
            return $newSlug;
        }

        return $existingSlug;
    }

    /**
     * Register Property related custom taxonomies
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function registerPropertyTaxonomies()
    {
        $this->registerPropertyFeatureTaxonomy();
        $this->registerPropertyTypeTaxonomy();
        $this->registerPropertyCityTaxonomy();
        $this->registerPropertyStatusTaxonomy();
    }

    /**
     * Register Property Feature Taxonomy
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function registerPropertyFeatureTaxonomy()
    {
        if (taxonomy_exists('property-feature')) {
            return;
        }

        $featureLabels = array(
            'name' => esc_html__('Property Features', 'mls-on-the-fly'),
            'singular_name' => esc_html__('Property Feature', 'mls-on-the-fly'),
            'search_items' => esc_html__('Search Property Features', 'mls-on-the-fly'),
            'popular_items' => esc_html__('Popular Property Features', 'mls-on-the-fly'),
            'all_items' => esc_html__('All Property Features', 'mls-on-the-fly'),
            'parent_item' => esc_html__('Parent Property Feature', 'mls-on-the-fly'),
            'parent_item_colon' => esc_html__('Parent Property Feature:', 'mls-on-the-fly'),
            'edit_item' => esc_html__('Edit Property Feature', 'mls-on-the-fly'),
            'update_item' => esc_html__('Update Property Feature', 'mls-on-the-fly'),
            'add_new_item' => esc_html__('Add New Property Feature', 'mls-on-the-fly'),
            'new_item_name' => esc_html__('New Property Feature Name', 'mls-on-the-fly'),
            'separate_items_with_commas' => esc_html__('Separate Property Features with commas', 'mls-on-the-fly'),
            'add_or_remove_items' => esc_html__('Add or remove Property Features', 'mls-on-the-fly'),
            'choose_from_most_used' => esc_html__('Choose from the most used Property Features', 'mls-on-the-fly'),
            'menu_name' => esc_html__('Property Features', 'mls-on-the-fly'),
        );

        register_taxonomy(
            'mof-property-feature',
            array('mof_property'),
            array(
                'hierarchical' => true,
                'labels' => apply_filters('mls_otf_property_feature_labels', $featureLabels),
                'show_ui' => true,
                'show_in_menu' => 'mls-on-the-fly',
                'query_var' => true,
                'rewrite' => array(
                    'slug' => apply_filters('mls_otf_property_feature_slug', 'property-feature'),
                ),
                'show_in_rest' => true,
                'rest_base' => apply_filters('mls_otf_property_feature_rest_base', 'property-features')
            )
        );
    }

    /**
     * Register Property Type Taxonomy
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function registerPropertyTypeTaxonomy()
    {
        if (taxonomy_exists('property-type')) {
            return;
        }

        $typeLabels = array(
            'name' => esc_html__('Property Types', 'mls-on-the-fly'),
            'singular_name' => esc_html__('Property Type', 'mls-on-the-fly'),
            'search_items' => esc_html__('Search Property Types', 'mls-on-the-fly'),
            'popular_items' => esc_html__('Popular Property Types', 'mls-on-the-fly'),
            'all_items' => esc_html__('All Property Types', 'mls-on-the-fly'),
            'parent_item' => esc_html__('Parent Property Type', 'mls-on-the-fly'),
            'parent_item_colon' => esc_html__('Parent Property Type:', 'mls-on-the-fly'),
            'edit_item' => esc_html__('Edit Property Type', 'mls-on-the-fly'),
            'update_item' => esc_html__('Update Property Type', 'mls-on-the-fly'),
            'add_new_item' => esc_html__('Add New Property Type', 'mls-on-the-fly'),
            'new_item_name' => esc_html__('New Property Type Name', 'mls-on-the-fly'),
            'separate_items_with_commas' => esc_html__('Separate Property Types with commas', 'mls-on-the-fly'),
            'add_or_remove_items' => esc_html__('Add or remove Property Types', 'mls-on-the-fly'),
            'choose_from_most_used' => esc_html__('Choose from the most used Property Types', 'mls-on-the-fly'),
            'menu_name' => esc_html__('Property Types', 'mls-on-the-fly'),
        );

        register_taxonomy(
            'mof-property-type',
            array('mof_property'),
            array(
                'hierarchical' => true,
                'labels' => apply_filters('mls_otf_property_type_labels', $typeLabels),
                'show_ui' => true,
                'show_in_menu' => 'mls-on-the-fly',
                'query_var' => true,
                'rewrite' => array(
                    'slug' => apply_filters('mls_otf_property_type_slug', 'property-type'),
                ),
                'show_in_rest' => true,
                'rest_base' => apply_filters('mls_otf_property_type_rest_base', 'property-types')
            )
        );
    }

    /**
     * Register Property City Taxonomy
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function registerPropertyCityTaxonomy()
    {
        if (taxonomy_exists('property-city')) {
            return;
        }

        $city_labels = array(
            'name' => esc_html__('Property Cities', 'mls-on-the-fly'),
            'singular_name' => esc_html__('Property City', 'mls-on-the-fly'),
            'search_items' => esc_html__('Search Property Cities', 'mls-on-the-fly'),
            'popular_items' => esc_html__('Popular Property Cities', 'mls-on-the-fly'),
            'all_items' => esc_html__('All Property Cities', 'mls-on-the-fly'),
            'parent_item' => esc_html__('Parent Property City', 'mls-on-the-fly'),
            'parent_item_colon' => esc_html__('Parent Property City:', 'mls-on-the-fly'),
            'edit_item' => esc_html__('Edit Property City', 'mls-on-the-fly'),
            'update_item' => esc_html__('Update Property City', 'mls-on-the-fly'),
            'add_new_item' => esc_html__('Add New Property City', 'mls-on-the-fly'),
            'new_item_name' => esc_html__('New Property City Name', 'mls-on-the-fly'),
            'separate_items_with_commas' => esc_html__('Separate Property Cities with commas', 'mls-on-the-fly'),
            'add_or_remove_items' => esc_html__('Add or remove Property Cities', 'mls-on-the-fly'),
            'choose_from_most_used' => esc_html__('Choose from the most used Property Cities', 'mls-on-the-fly'),
            'menu_name' => esc_html__('Property Cities', 'mls-on-the-fly'),
        );

        register_taxonomy(
            'mof-property-city',
            array('mof_property'),
            array(
                'hierarchical' => true,
                'labels' => apply_filters('mls_otf_property_city_labels', $city_labels),
                'show_ui' => true,
                'show_in_menu' => 'mls-on-the-fly',
                'query_var' => true,
                'rewrite' => array(
                    'slug' => apply_filters('mls_otf_property_city_slug', 'property-city'),
                ),
                'show_in_rest' => true,
                'rest_base' => apply_filters('mls_otf_property_city_rest_base', 'property-cities')
            )
        );
    }

    /**
     * Register Property Status Taxonomy
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function registerPropertyStatusTaxonomy()
    {
        if (taxonomy_exists('property-status')) {
            return;
        }

        $statusLabels = array(
            'name' => esc_html__('Property Statuses', 'mls-on-the-fly'),
            'singular_name' => esc_html__('Property Status', 'mls-on-the-fly'),
            'search_items' => esc_html__('Search Property Status', 'mls-on-the-fly'),
            'popular_items' => esc_html__('Popular Property Status', 'mls-on-the-fly'),
            'all_items' => esc_html__('All Property Status', 'mls-on-the-fly'),
            'parent_item' => esc_html__('Parent Property Status', 'mls-on-the-fly'),
            'parent_item_colon' => esc_html__('Parent Property Status:', 'mls-on-the-fly'),
            'edit_item' => esc_html__('Edit Property Status', 'mls-on-the-fly'),
            'update_item' => esc_html__('Update Property Status', 'mls-on-the-fly'),
            'add_new_item' => esc_html__('Add New Property Status', 'mls-on-the-fly'),
            'new_item_name' => esc_html__('New Property Status Name', 'mls-on-the-fly'),
            'separate_items_with_commas' => esc_html__('Separate Property Status with commas', 'mls-on-the-fly'),
            'add_or_remove_items' => esc_html__('Add or remove Property Status', 'mls-on-the-fly'),
            'choose_from_most_used' => esc_html__('Choose from the most used Property Status', 'mls-on-the-fly'),
            'menu_name' => esc_html__('Property Status', 'mls-on-the-fly'),
        );

        register_taxonomy(
            'mof-property-status',
            array('mof_property'),
            array(
                'hierarchical' => true,
                'labels' => apply_filters('mls_otf_property_status_labels', $statusLabels),
                'show_ui' => true,
                'show_in_menu' => 'mls-on-the-fly',
                'query_var' => true,
                'rewrite' => array(
                    'slug' => apply_filters('mls_otf_property_status_slug', 'property-status'),
                ),
                'show_in_rest' => true,
                'rest_base' => apply_filters('mls_otf_property_status_rest_base', 'property-statuses')
            )
        );
    }

    /**
     * This function set property feature's url slug by hooking itself with related filter
     *
     * @param string $existingSlug - Existing property location slug.
     *
     * @return string
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function setPropertyFeatureSlug($existingSlug)
    {
        $newSlug = get_option('_x_wpl_property_feature_slug');

        if (!empty($newSlug)) {
            return $newSlug;
        }

        return $existingSlug;
    }

    /**
     * This function set property type's url slug by hooking itself with related filter.
     */
    public function setPropertyTypeSlug($existingSlug)
    {
        $newSlug = get_option('_x_wpl_property_type_slug');

        if (!empty($newSlug)) {
            return $newSlug;
        }

        return $existingSlug;
    }

    /**
     * This function set property location's url slug by hooking itself with related filter.
     *
     * @param string $existingSlug - Existing property location slug.
     *
     * @return string
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function setPropertyCitySlug($existingSlug)
    {
        $newSlug = get_option('_x_wpl_property_city_slug');

        if (!empty($newSlug)) {
            return $newSlug;
        }

        return $existingSlug;
    }

    /**
     * This function set property status's url slug by hooking itself with related filter
     *
     * @param string $existingSlug - Existing property location slug.
     *
     * @return string
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function setPropertyStatusSlug($existingSlug)
    {
        $newSlug = get_option('_x_wpl_property_status_slug');

        if (!empty($newSlug)) {
            return $newSlug;
        }

        return $existingSlug;
    }

    /**
     * Custom columns for properties
     *
     * @param array $columns
     *
     * @return array
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function propertyEditColumns($columns)
    {
        // Property initial columns.
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => esc_html__('Property Title', 'mls-on-the-fly'),
            'property-thumbnail' => esc_html__('Thumbnail', 'mls-on-the-fly'),
            'city' => esc_html__('City', 'mls-on-the-fly'),
            'type' => esc_html__('Type', 'mls-on-the-fly'),
            'status' => esc_html__('Status', 'mls-on-the-fly'),
            'price' => esc_html__('Price', 'mls-on-the-fly'),
            'id' => esc_html__('Property ID', 'mls-on-the-fly'),
        );

        // Add property published time column.
        $columns['date'] = esc_html__('Publish Time', 'mls-on-the-fly');

        // WPML Support
        if (defined('ICL_SITEPRESS_VERSION')) {
            global $sitepress;
            $wpmlColumns = new WPML_Custom_Columns($sitepress);
            $columns = $wpmlColumns->add_posts_management_column($columns);
        }

        // Reverse the array for RTL
        if (is_rtl()) {
            $columns = array_reverse($columns);
        }

        return $columns;
    }

    /**
     * Property custom columns
     *
     * @param array $column
     *
     * @param int $postId
     *
     * @return array
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function propertyCustomColumns($column, $postId)
    {
        switch ($column) {
            case 'property-thumbnail':
                ?>
                <div class="x-wpl-properties-table-thumbnail-wrap">
                    <?php
                    if (!empty(get_the_post_thumbnail($postId))) :
                        ?>
                        <a href="<?php
                        the_permalink(); ?>" target="_blank"><?php
                            the_post_thumbnail(array(130, 130)); ?></a>
                    <?php
                    else :
                        ?>
                        <span class="x-wpl-properties-table-no-thumbnail"><?php
                            esc_html_e('No Thumbnail', 'mls-on-the-fly'); ?></span>
                    <?php
                    endif;

                    $isExpired = get_post_status($postId);
                    $isFeatured = get_post_meta($postId, 'mls_otf_property_featured');

                    if ($isExpired == 'expired') :
                        ?>
                        <span class="x-wpl-expired-property"
                              style="display: block; max-width: 130px; margin:0 0 5px 0; padding: 5px 0; background-color: #ff0000; color: #ffffff; text-align: center; font-weight: bold;"><?php
                            esc_html_e('Expired', 'mls-on-the-fly'); ?></span>
                    <?php
                    endif;
                    if (!empty($isFeatured)) :
                        ?>
                        <span class="x-wpl-featured-property"
                              style="display: block; max-width: 130px; padding: 5px 0; background-color: #00a9ff; color: #ffffff; text-align: center; font-weight: bold;"><?php
                            esc_html_e('Featured', 'mls-on-the-fly'); ?></span>
                    <?php
                    endif;
                    ?>
                </div>
                <?php
                break;
            case 'city':
                echo Basic::adminTaxonomyTerms('property-city', 'property', $postId);
                break;
            case 'type':
                echo Basic::adminTaxonomyTerms('property-type', 'property', $postId);
                break;
            case 'status':
                echo Basic::adminTaxonomyTerms('property-status', 'property', $postId);
                break;
            case 'price':
                Price::propertyPrice();
                break;
            case 'id':
                $propertyId = get_post_meta($postId, 'mls_otf_property_id');
                if (!empty($propertyId)) {
                    echo esc_html($propertyId);
                } else {
                    esc_html_e('NA', 'mls-on-the-fly');
                }
                break;
        }
    }

    /**
     * Make property price column sortable
     *
     * @param array $columns
     *
     * @return array
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function sortablePriceColumn($columns)
    {
        $columns['price'] = 'price';

        return $columns;
    }

    /**
     * Sort properties based on price
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function priceOrderby($query)
    {
        global $post_type, $pagenow;

        //if we are currently on the edit screen of the property post-type listings
        if ($pagenow == 'edit.php' && $post_type == 'property') {
            $orderby = $query->get('orderby');
            if ('price' == $orderby) {
                $query->set('meta_key', '_x_wpl_property_price');
                $query->set('orderby', 'meta_value_num');
            }
        }
    }

    /**
     * Modify property list row actions
     *
     * @param string $actions
     *
     * @param object $post
     *
     * @return array
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function modifyPropertyListRowActions($actions, $post)
    {
        // Check for your post type.
        if ($post->post_type == "property") {
            // Post status
            $postStatus = get_post_status($post->ID);

            // Build your links URL.
            $url = admin_url('admin.php');
            $cloneLink = add_query_arg(
                array(
                    'action' => 'mls_otf_clone_property_as_draft',
                    'listing_id' => $post->ID,
                ),
                $url
            );
            $expireLink = add_query_arg(
                array(
                    'action' => 'mls_otf_expire_listing',
                    'listing_id' => $post->ID,
                ),
                $url
            );
            $publishLink = add_query_arg(
                array(
                    'action' => 'mls_otf_publish_listing',
                    'listing_id' => $post->ID,
                ),
                $url
            );

            $listRowActions = array(
                'clone' => sprintf(
                    '<a href="%1$s">%2$s</a>',
                    esc_url($cloneLink),
                    esc_html(__('Clone', 'mls-on-the-fly'))
                ),
            );

            if ($postStatus === 'publish') {
                $listRowActions = array_merge($listRowActions, array(
                    'expire' => sprintf(
                        '<a href="%1$s">%2$s</a>',
                        esc_url($expireLink),
                        esc_html(__('Expire', 'mls-on-the-fly'))
                    )
                ));
            } else {
                $listRowActions = array_merge($listRowActions, array(
                    'publish' => sprintf(
                        '<a href="%1$s">%2$s</a>',
                        esc_url($publishLink),
                        esc_html(__('Publish', 'mls-on-the-fly'))
                    )
                ));
            }

            // You can check if the current user has some custom rights.
            if (current_user_can('edit_post', $post->ID)) {
                // Add the new Clone quick link.
                $actions = array_merge($actions, $listRowActions);
            }
        }

        return $actions;
    }

    /**
     * Clone property as draft
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function clonePropertyPostAsDraft()
    {
        global $wpdb;

        if (!(isset($_GET['listing_id']) || isset($_POST['listing_id']) || (isset($_REQUEST['action']) && 'mls_otf_clone_property_as_draft' == $_REQUEST['action']))) {
            wp_die('No post to clone has been supplied!');
        }

        /*
         * get the original post id
         */
        $postId = (isset($_GET['listing_id']) ? $_GET['listing_id'] : $_POST['listing_id']);
        /*
         * and all the original post data then
         */
        $post = get_post($postId);

        /*
         * if you don't want current user to be the new post author,
         * then change next couple of lines to this: $newPostAuthor = $post->post_author;
         */
        $currentUser = wp_get_current_user();
        $newPostAuthor = $currentUser->ID;

        /*
         * if post data exists, create the post clone
         */
        if (isset($post) && $post != null) {
            /*
             * new post data array
             */
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status' => $post->ping_status,
                'post_author' => $newPostAuthor,
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_name' => $post->post_name,
                'post_parent' => $post->post_parent,
                'post_password' => $post->post_password,
                'post_status' => 'draft',
                'post_title' => $post->post_title,
                'post_type' => $post->post_type,
                'to_ping' => $post->to_ping,
                'menu_order' => $post->menu_order
            );

            /*
             * insert the post by wp_insert_post() function
             */
            $newPostId = wp_insert_post($args);
            /*
             * get all current post terms ad set them to the new post draft
             */
            $taxonomies = get_object_taxonomies(
                $post->post_type
            ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
            foreach ($taxonomies as $taxonomy) {
                $post_terms = wp_get_object_terms($postId, $taxonomy, array('fields' => 'slugs'));
                wp_set_object_terms($newPostId, $post_terms, $taxonomy, false);
            }

            /*
             * Clone all post meta
             */
            $postMetaInfos = $wpdb->get_results(
                "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$postId"
            );
            if (count($postMetaInfos) != 0) {
                $sqlQuery = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                foreach ($postMetaInfos as $metaInfo) {
                    $metaKey = $metaInfo->meta_key;
                    if ($metaKey == '_wp_old_slug') {
                        continue;
                    }
                    $metaValue = addslashes($metaInfo->meta_value);
                    $sqlQuerySel[] = "SELECT $newPostId, '$metaKey', '$metaValue'";
                }
                $sqlQuery .= implode(" UNION ALL ", $sqlQuerySel);
                $wpdb->query($sqlQuery);
            }

            /**
             * Check if auto property id auto generation is enabled.
             */
            $autoPropertyId = get_option('_x_wpl_auto_property_id_check');

            if (!empty($autoPropertyId) && '1' === $autoPropertyId) {
                $propertyIdPattern = get_option('_x_wpl_auto_property_id_pattern');
                $propertyIdValue = preg_replace('/{ID}/', $newPostId, $propertyIdPattern);

                update_post_meta($newPostId, '_x_wpl_property_id', $propertyIdValue);
            }

            /*
             * finally, redirect to the edit post screen for the new draft
             */
            //wp_redirect( admin_url( 'post.php?action=edit&post=' . $newPostId ) );
            wp_redirect(admin_url('edit.php?post_type=property'));
            exit;
        } else {
            wp_die('Post creation failed, could not find original post: ' . $postId);
        }
    }

    /**
     * Expiration of the property
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function expireListing()
    {
        if (!(isset($_GET['listing_id']) || isset($_POST['listing_id']) || (isset($_REQUEST['action']) && 'mls_otf_expire_listing' == $_REQUEST['action']))) {
            wp_die('No property exist');
        }

        /*
         * get the original listing id
         */
        $listingId = (isset($_GET['listing_id']) ? $_GET['listing_id'] : $_POST['listing_id']);
        $postId = absint($listingId);

        $listingData = array(
            'ID' => $postId,
            'post_status' => 'expired'
        );
        wp_update_post($listingData);

        // TODO: Send email

        wp_redirect(admin_url('edit.php?post_type=property'));
        exit;
    }

    /**
     * Publish of the property
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function publishListing()
    {
        if (!(isset($_GET['listing_id']) || isset($_POST['listing_id']) || (isset($_REQUEST['action']) && 'mls_otf_publish_listing' == $_REQUEST['action']))) {
            wp_die('No property exist');
        }

        /*
         * get the original listing id
         */
        $listingId = (isset($_GET['listing_id']) ? $_GET['listing_id'] : $_POST['listing_id']);
        $postId = absint($listingId);

        $listingData = array(
            'ID' => $postId,
            'post_status' => 'publish'
        );
        wp_update_post($listingData);

        // TODO: Send email

        wp_redirect(admin_url('edit.php?post_type=property'));
        exit;
    }

    /**
     * Add custom post status
     *
     * @return void
     * @author Melvin <melvin.g@realtyna.com>
     *
     */
    public function customPostStatus()
    {
        $args = array(
            'label' => _x('Expired', 'Status General Name', 'mls-on-the-fly'),
            'label_count' => _n_noop('Expired (%s)', 'Expired (%s)', 'mls-on-the-fly'),
            'public' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search' => false,
        );

        register_post_status('expired', $args);
    }
}
