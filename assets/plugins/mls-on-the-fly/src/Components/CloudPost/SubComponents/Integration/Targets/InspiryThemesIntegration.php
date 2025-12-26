<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets;

use Realtyna\Core\Utilities\SettingsField;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Settings\Settings;

class InspiryThemesIntegration implements IntegrationInterface
{

    /**
     * An array of meta keys that are allowed to be saved.
     *
     * @var array
     */
    public array $metaWhiteList = array();
    public string $name = 'inspirythemes';
    public array $customPostTypes = array(
        'property',
    );
    public array $customTaxonomies = array(
        'property-feature',
        'property-type',
        'property-city',
        'property-status',
    );

    /**
     * Constructor.
     *
     * This method initializes the integration class by setting the allowed meta keys.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        add_filter('realtyna_mls_on_the_fly_cloud_posts', function ($posts, $listings, $RFQuery) {
            foreach ($posts as $post) {
                if ($post instanceof \WP_Post) {
                    $additionalFeaturesTitles = explode('|', $post->meta_data['REAL_HOMES_property_additional_feature_title']);
                    $additionalFeaturesValues = explode('|', $post->meta_data['REAL_HOMES_property_additional_feature_value']);

                    $additionalFeatures = []; // Initialize the array

                    foreach ($additionalFeaturesTitles as $index => $title) {
                        // Get the corresponding value from $additionalFeaturesValues
                        $value = $additionalFeaturesValues[$index];

                        // Only add to the array if the value is not empty
                        if (!empty($value)) {
                            $additionalFeatures[] = [
                                $title,
                                $value,
                            ];
                        }
                    }
                    $post->meta_data['REAL_HOMES_additional_details_list'] = serialize($additionalFeatures);
                }
            }

            return $posts;
        }, 10, 3);

        add_action('wp_head', [$this, 'enqueue_custom_slider_styles']);
        add_action('mls_on_the_fly_settings_pictures_tab_after', [$this, 'addOptionToPicturesTab']);

        return self::class;
    }

    function addOptionToPicturesTab(): void
    {
        $settings = Settings::get_settings();
        $checked = isset($settings['change_inspiry_themes_default_thumbnail_size']) && $settings['change_inspiry_themes_default_thumbnail_size'];
        SettingsField::checkbox(array(
            'parent_name' => 'mls-on-the-fly-settings',
            'child_name' => 'change_inspiry_themes_default_thumbnail_size',
            'id' => 'mls-on-the-fly-settings-change-inspiry-themes-default-thumbnail-size',
            'label' => __('If you want to change the thumbnail size of the Inspiry Themes carousel images activate this.', 'realtyna-mls-on-the-fly'),
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
                'value' => $settings['slider_pictures_height'] ?? 310,
            ));

        }

    }



    function enqueue_custom_slider_styles(): void
    {
        $slider_pictures_height = Settings::get_setting('slider_pictures_height', 250);
        $change_picture_size = Settings::get_setting('change_inspiry_themes_default_thumbnail_size', 'no') == 'yes';
        if($change_picture_size):
            ?>
            <style>
                .rh_prop_card__thumbnail {
                    height:  <?php echo $slider_pictures_height; ?>px !important; /* Set fixed height */
                    overflow: hidden;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #f3f3f3;
                }

                .rh_prop_card__thumbnail img {
                    width: 100% !important;
                    height:  <?php echo $slider_pictures_height + 50; ?>px !important;
                    object-fit: cover; /* Crop image without distortion */
                    display: block;
                }

            </style>
        <?php
        endif;
    }

    public function checkMetaToSave($check, $metaKey): bool|null
    {
        if (in_array($metaKey, $this->metaWhiteList)) {
            return true;
        }

        if (str_contains($metaKey, 'REAL_HOMES_')) {
            return false;
        }

        return $check;
    }

    public function getMappingDir(): string
    {
        return '';
    }
}
