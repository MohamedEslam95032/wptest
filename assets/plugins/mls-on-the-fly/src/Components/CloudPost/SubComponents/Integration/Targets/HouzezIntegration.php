<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets;


use Realtyna\Core\Utilities\SettingsField;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;
use Realtyna\MlsOnTheFly\Settings\Settings;

/**
 * Integration class for Houzez.
 *
 * This class provides integration between the MLS On The Fly<sup>&reg;</sup> and Houzez, a WordPress theme
 * that allows users to create and manage property listings.
 *
 * The integration features include:
 *
 * - Setting the default post author for newly created posts.
 * - Setting the default contact ID for newly created property listings.
 * - Uploading the Cloud Post IDs to ensure synchronization with Houzez.
 *
 * @autor Chandler.p
 *         chandler.p@realtyna.com
 */
class HouzezIntegration implements IntegrationInterface
{
    /**
     * An array of meta keys that are allowed to be saved.
     *
     * @var array
     */
    public array $metaWhiteList = [];
    public string $name = 'houzez';
    public array $customPostTypes = [
        'property',
    ];
    public array $customTaxonomies = [
        'location',
        'tax_feature',
        'property_type',
        'property_city',
        'property_state',
        'property_country',
        'property_label',
        'property_status',
        'property_area',
        'property_feature'
    ];

    /**
     * Constructor.
     *
     * This method initializes the integration class by setting the allowed meta keys.
     * @throws \ReflectionException
     */
    public function __construct()
    {
//        //Load this integration custom taxonomies array
//        // TODO I had to use init hook to manage when does mapping files will be read
        add_action('init', function () {
            /** @var Mapping $mapping */
            $mapping = App::get(Mapping::class);
            $mappingFile = $mapping->mappingConfig->getQueryMapping();
            $this->customTaxonomies = array_keys($mappingFile['taxonomies']);
        }, 99999);

        add_filter('realtyna/mls-on-the-fly/admin/settings/tabs', array(__CLASS__, 'filter_settings_tabs'));
        add_action('realtyna_mls_on_the_fly_upload_cloud_post_ids', [$this, 'uploadCloudPostID'], 10, 2);
        add_filter('realtyna_mls_on_the_fly_current_post_id_to_inject', [$this, 'currentPostIDToInject'], 10, 2);
        add_filter('realtyna_mls_on_the_fly_cloud_posts', function ($posts, $listings, $RFQuery) {
            $contactType = Settings::get_setting('houzez_default_property_contact_type', 'agent_info');
            if ($contactType != 'leave_blank') {
                foreach ($posts as $post) {
                    if (!($post instanceof \WP_Post)) {
                        continue;
                    }
                    $post->meta_data['fave_agent_display_option'] = [$contactType];
                    if ($contactType == 'agent_info') {
                        $defaultAgent = Settings::get_setting('houzez_default_property_agent');
                        if ($defaultAgent) {
                            $post->meta_data['fave_agents'] = [$defaultAgent];
                        }
                    } elseif ($contactType == 'agency_info') {
                        $defaultAgency = Settings::get_setting('houzez_default_property_agency');
                        if ($defaultAgency) {
                            $post->meta_data['fave_property_agency'] = [$defaultAgency];
                        }
                    }
                }
            }


            foreach ($posts as $post) {
                if ($post instanceof \WP_Post) {
                    $additionalFeaturesTitles = explode('|', $post->meta_data['fave_additional_feature_title']);
                    $additionalFeaturesValues = explode('|', $post->meta_data['fave_additional_feature_value']);

                    $additionalFeatures = []; // Initialize the array

                    foreach ($additionalFeaturesTitles as $index => $title) {
                        // Get the corresponding value from $additionalFeaturesValues
                        $value = $additionalFeaturesValues[$index];

                        // Only add to the array if the value is not empty
                        if (!empty($value)) {
                            $additionalFeatures[] = [
                                "fave_additional_feature_title" => $title,
                                "fave_additional_feature_value" => $value,
                            ];
                        }
                    }

                    $post->meta_data['additional_features'][] = $additionalFeatures;
                }
            }


            return $posts;
        }, 10, 3);

        add_filter('realtyna_mls_on_the_fly_rf_skip', function ($RFSkip, $paged, $postsPerPage) {
            if ($paged == 1 || $paged == 0) {
                return false;
            }
            return $RFSkip;
        }, 10, 3);

        // Houzez will query the database directly, so we rewrote the code to use get_posts()
        // Remove existing actions
        remove_action('wp_ajax_nopriv_houzez_get_auto_complete_search', 'houzez_get_auto_complete_search');
        remove_action('wp_ajax_houzez_get_auto_complete_search', 'houzez_get_auto_complete_search');
        // Add the rewritten code
        add_action('wp_ajax_nopriv_houzez_get_auto_complete_search', [$this, 'houzez_get_auto_complete_search']);
        add_action('wp_ajax_houzez_get_auto_complete_search', [$this, 'houzez_get_auto_complete_search']);


        add_action('add_meta_boxes_houzez_agent', [$this, 'custom_agent_meta_box']);
        add_action('save_post_houzez_agent', [$this, 'custom_save_agent_meta']);

        // Add meta box for houzez_agency
        add_action('add_meta_boxes_houzez_agency', [$this, 'custom_agency_meta_box']);
        add_action('save_post_houzez_agency', [$this, 'custom_save_agency_meta']);


        add_filter('mls_on_the_fly_meta_mapping_file', [$this, 'modify_featured_mapping']);


        add_action('wp_head', [$this, 'enqueue_custom_slider_styles']);

        add_action('mls_on_the_fly_settings_pictures_tab_after', [$this, 'addOptionToPicturesTab']);
        return self::class;
    }
    function addOptionToPicturesTab(): void
    {
        $settings = Settings::get_settings();
        $checked = isset($settings['change_houzez_default_thumbnail_size']) && $settings['change_houzez_default_thumbnail_size'];
        SettingsField::checkbox(array(
            'parent_name' => 'mls-on-the-fly-settings',
            'child_name' => 'change_houzez_default_thumbnail_size',
            'id' => 'mls-on-the-fly-settings-change-houzez-default-thumbnail-size',
            'label' => __('If you want to change the thumbnail size of the Houzez carousel images activate this.', 'realtyna-mls-on-the-fly'),
            'value' => 'yes',
            'checked' => $checked,
            'description' => ''
        ));

        if($checked){
            SettingsField::input(array(
                'parent_name' => 'mls-on-the-fly-settings',
                'child_name' => 'slider_pictures_height',
                'id' => 'mls-on-the-fly-settings-slider-pictures-height',
                'label' => __( 'Thumbnail size', 'realtyna-mls-on-the-fly' ),
                'type'  => 'number',
                'value' => $settings['slider_pictures_height'] ?? 250,
            ));

        }

    }

    function enqueue_custom_slider_styles(): void
    {
        $slider_pictures_height = Settings::get_setting('slider_pictures_height', 250);
        $change_picture_size = Settings::get_setting('change_houzez_default_thumbnail_size', 'no') == 'yes';
        if($change_picture_size):
        ?>
        <style>
            .listing-image-wrap .slick-list {
                height: <?php echo $slider_pictures_height; ?>px !important; /* Set slider height */
                overflow: hidden; /* Ensures no overflow from images */
            }

            .listing-image-wrap .slick-list .slick-track {
                height: 100%;
                display: flex; /* Ensures slides align properly */
                align-items: center; /* Center images vertically */
            }

            .listing-image-wrap .slick-list .slick-track .slick-slide img {
                width: 100%; /* Makes sure the image spans the width */
                height: <?php echo $slider_pictures_height; ?>px; /* Ensures uniform height */
                object-fit: cover; /* Crops image to fit the container */
            }

        </style>
        <?php
        endif;
    }

    /**
     * Modifies the meta mapping for the 'fave_featured' key based on settings.
     *
     * @param array $metaMapping The original meta mapping array.
     * @return array The modified meta mapping array.
     */
    public function modify_featured_mapping(array $metaMapping): array
    {
        // Check if 'fave_featured' exists in the meta mapping
        $metaMapping['fave_featured'] = [
            'mapping' => '',
            'default' => '',
        ];

        // Retrieve settings for the featured listing key and values
        $featuredListingsKey = Settings::get_setting('featured_key', 'none');
        $featuredListingsValues = Settings::get_setting('featured_values', []);

        // Initialize mapping value
        $mappingValue = '';

        // If a valid featured key is set and there are values, construct the mapping condition
        if ($featuredListingsKey !== 'none' && !empty($featuredListingsValues)) {
            // Wrap each value in single quotes for SQL compatibility
            $quotedValues = array_map(fn($value) => "'$value'", $featuredListingsValues);

            // Construct SQL-like condition based on the number of values
            if (count($quotedValues) === 1) {
                $mappingValue = "IF('{".$featuredListingsKey."}' == {$quotedValues[0]}, '1', '0')";
            } else {
                $mappingValue = "IF(in_array('{" . $featuredListingsKey . "}', [" . implode(
                        ',',
                        $quotedValues
                    ) . "]), '1', '0')";
            }
        }

        // Ensure 'fave_featured' key exists in the meta mapping and update its 'mapping' value
        $metaMapping['fave_featured']['mapping'] = $mappingValue;
        return $metaMapping;
    }

    /**
     * Add custom tab to settings page
     *
     * @param array $tabs
     *
     * @return array
     * @author Cyrus <cyrus.a@realtyna.com>
     *
     */
    public static function filter_settings_tabs($tabs)
    {
        $tabs['houzez_integration'] = array(
            'title' => __('Houzez', 'realtyna-mls-on-the-fly'),
            'priority' => 40,
            'filename' => REALTYNA_MLS_ON_THE_FLY_TEMPLATES_PATH . "admin/settings-page/houzez-integration-tab.php"
        );
        return $tabs;
    }

    function custom_agent_meta_box()
    {
        add_meta_box(
            'custom_agent_mls_id',
            'List Agent MLS ID',
            [$this, 'custom_agent_meta_box_callback'],
            'houzez_agent',
            'normal',
            'default'
        );
    }

    function custom_agent_meta_box_callback($post)
    {
        $value = get_post_meta($post->ID, 'list_agent_mls_id', true);
        ?>
        <label for="list_agent_mls_id">List Agent MLS ID:</label>
        <input type="text" id="list_agent_mls_id" name="list_agent_mls_id" value="<?php
        echo esc_attr($value); ?>">
        <?php
    }

    function custom_save_agent_meta($post_id)
    {
        if (!isset($_POST['list_agent_mls_id'])) {
            return;
        }

        $meta_value = sanitize_text_field($_POST['list_agent_mls_id']);
        update_post_meta($post_id, 'list_agent_mls_id', $meta_value);
    }

    function custom_agency_meta_box()
    {
        add_meta_box(
            'custom_agency_mls_office_id',
            'MLS Office ID',
            [$this, 'custom_agency_meta_box_callback'],
            'houzez_agency',
            'normal',
            'default'
        );
    }

    function custom_agency_meta_box_callback($post)
    {
        $value = get_post_meta($post->ID, 'mls_office_id', true);
        ?>
        <label for="mls_office_id">MLS Office ID:</label>
        <input type="text" id="mls_office_id" name="mls_office_id" value="<?php
        echo esc_attr($value); ?>">
        <?php
    }

    function custom_save_agency_meta($post_id)
    {
        if (!isset($_POST['mls_office_id'])) {
            return;
        }

        $meta_value = sanitize_text_field($_POST['mls_office_id']);
        update_post_meta($post_id, 'mls_office_id', $meta_value);
    }

    public function houzez_get_auto_complete_search()
    {
        $current_language = apply_filters('wpml_current_language', null);
        global $wpdb;
        $key = sanitize_text_field($_POST['key']);
        $key = $wpdb->esc_like($key);
        $keyword_field = houzez_option('keyword_field');
        $houzez_local = houzez_get_localization();
        $response = '';

        if ($keyword_field != 'prop_city_state_county') {
            if ($keyword_field == "prop_title") {
                $args = array(
                    'post_type' => 'property',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    's' => $key,
                );

                $data = get_posts($args);

                if (!empty($data)) {
                    $search_url = add_query_arg('keyword', $key, houzez_get_search_template_link());

                    echo '<div class="auto-complete-keyword">';
                    echo '<ul class="list-group">';

                    $new_data = array();

                    foreach ($data as $post) {
                        $propID = $post->ID;

                        $post_language = apply_filters(
                            'wpml_element_language_code',
                            null,
                            array('element_id' => $propID, 'element_type' => 'post')
                        );

                        if ($post_language !== $current_language) {
                            continue;
                        }

                        $new_data[] = $post;

                        $prop_beds = get_post_meta($propID, 'fave_property_bedrooms', true);
                        $prop_baths = get_post_meta($propID, 'fave_property_bathrooms', true);
                        $prop_size = houzez_get_listing_area_size($propID);
                        $prop_type = houzez_taxonomy_simple('property_type');
                        $prop_img = get_the_post_thumbnail_url($propID, array(40, 40));

                        if (empty($prop_img)) {
                            $prop_img = houzez_get_image_placeholder_url('thumbnail');
                        }

                        ?>

                        <li class="list-group-item" data-text="<?php
                        echo $post->post_title; ?>">
                            <div class="d-flex align-items-center">
                                <div class="auto-complete-image-wrap">
                                    <a href="<?php
                                    the_permalink($propID); ?>">
                                        <img class="img-fluid rounded" src="<?php
                                        echo $prop_img; ?>" width="40" height="40" alt="image">
                                    </a>
                                </div><!-- auto-complete-image-wrap -->
                                <div class="auto-complete-content-wrap ml-3">
                                    <div class="auto-complete-title">
                                        <a href="<?php
                                        the_permalink($propID); ?>"><?php
                                            echo $post->post_title; ?></a>
                                    </div>
                                </div><!-- auto-complete-content-wrap -->
                            </div><!-- d-flex -->
                        </li><!-- list-group-item -->
                        <?php
                    }

                    echo '</ul>';

                    echo '<div class="auto-complete-footer">';
                    echo '<span class="auto-complete-count"><i class="houzez-icon icon-pin mr-1"></i> ' . sizeof(
                            $new_data
                        ) . ' ' . $houzez_local['listins_found'] . '</span>';
                    echo '<a target="_blank" href="' . $search_url . '" class="search-result-view">' . $houzez_local['view_all_results'] . '</a>';
                    echo '</div>';


                    echo '</div>';
                } else {
                    ?>
                    <ul class="list-group">
                        <li class="list-group-item"> <?php
                            echo $houzez_local['auto_result_not_found']; ?> </li>
                    </ul>
                    <?php
                }
            } elseif ($keyword_field == "prop_address") {
                $args = array(
                    'post_type' => 'property',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => 'fave_property_map_address',
                            'value' => $key,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'fave_property_zip',
                            'value' => $key,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'fave_property_address',
                            'value' => $key,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'fave_property_id',
                            'value' => $key,
                            'compare' => 'LIKE',
                        ),
                    ),
                );

                $posts = get_posts($args);

                if (!empty($posts)) {
                    echo '<ul class="list-group">';

                    $new_data = array();

                    foreach ($posts as $post) {
                        $post_language = apply_filters(
                            'wpml_element_language_code',
                            null,
                            array('element_id' => $post->ID, 'element_type' => 'post')
                        );

                        if ($post_language !== $current_language) {
                            continue;
                        }

                        $new_data[] = $post;
                        ?>

                        <li class="list-group-item" data-text="<?php
                        echo $post->post_title; ?>">
                            <div class="d-flex align-items-center">
                                <div class="auto-complete-content-wrap flex-fill">
                                    <i class="houzez-icon icon-pin mr-1"></i> <?php
                                    echo $post->post_title; ?>
                                </div><!-- auto-complete-content-wrap -->
                            </div><!-- d-flex -->
                        </li>
                        <?php
                    }

                    echo '</ul>';
                } else {
                    ?>
                    <ul class="list-group">
                        <li class="list-group-item"> <?php
                            echo $houzez_local['auto_result_not_found']; ?> </li>
                    </ul>
                    <?php
                }
            }
        } else {
            // Other code for 'prop_city_state_county' case
        }

        wp_die();
    }

    public function currentPostIDToInject($postID, $data)
    {
        if (isset($data['listing_id'])) {
            return $data['listing_id'];
        }
        return $postID;
    }

    /**
     * Uploads Cloud Post IDs to ensure synchronization with EPL.
     *
     * This method is called when the Cloud Post IDs need to be uploaded to ensure synchronization
     * with EPL. It takes the new ID as a reference and updates all EPL mappings' post IDs to
     * ensure a consistent synchronization between WordPress and EPL.
     *
     * @param int $newID The new Cloud Post ID.
     * @param int $oldID The old Cloud Post ID.
     */
    public function uploadCloudPostID(int $newID, int $oldID): void
    {
    }


    /**
     * Checks if a meta key is allowed to be saved.
     *
     * This method checks if a meta key is allowed to be saved based on the white list of allowed meta keys. If the meta key is in the white list, it is allowed to be saved. If the meta key is not in the white list and it contains the string 'property_', it is not allowed to be saved.
     *
     * @param bool|null $check Whether to allow the meta key to be saved or not.
     * @param string $metaKey The meta key to be checked.
     *
     * @return bool|null Whether to allow the meta key to be saved or not.
     */
    public function checkMetaToSave($check, $metaKey): bool|null
    {
        if (in_array($metaKey, $this->metaWhiteList)) {
            return true;
        }
        if (str_contains($metaKey, 'fave_property_')) {
            return false;
        }

        if (str_contains($metaKey, 'houzez_')) {
            return false;
        }

        return $check;
    }

    public function getMappingDir(): string
    {
        return '';
    }

}
