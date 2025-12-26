<?php

namespace Realtyna\MlsOnTheFly\Settings;

/**
 * Class Settings
 *
 * @package Realtyna\MlsOnTheFly\Settings
 */
class Settings
{
    /**
     * @var array List of allowed option keys
     */
    private static array $allowed_options = [
        'self_custom_post_type',
        'cache_time',
        'terms_mode',
        'api_key',
        'client_id',
        'client_secret',
        'show_raw_data',
        'default_integration',
        'houzez_default_post_author',
        'houzez_default_property_contact_type',
        'houzez_default_property_agent',
        'houzez_default_property_agency',
        'rf_origin',
        'rf_referer',
        'pictures_size',
        'slider_pictures_height',
        'url_patterns',
        'featured_key',
        'change_houzez_default_thumbnail_size',
        'featured_values',
        'properties_notes_filter',
        'change_inspiry_themes_default_thumbnail_size'
    ];

    /**
     * Return Settings
     *
     * @return array
     */
    public static function get_settings(): array
    {
        $settings = get_option(REALTYNA_MLS_ON_THE_FLY_SLUG . '_settings', []);
        return apply_filters('mls_on_the_fly/settings/all', $settings);
    }

    /**
     * Update Settings
     *
     * @param array $settings
     *
     * @return void
     */
    public static function update_settings(array $settings): void
    {
        $allowed_options = self::get_allowed_options();
        $filtered_settings = array_intersect_key($settings, array_flip($allowed_options));

        /**
         * Filter settings before saving
         *
         * @param array $filtered_settings
         */
        $filtered_settings = apply_filters('mls_on_the_fly/settings/update', $filtered_settings);

        update_option(REALTYNA_MLS_ON_THE_FLY_SLUG . '_settings', $filtered_settings);

        /**
         * Fires after settings are updated
         *
         * @param array $filtered_settings
         */
        do_action('mls_on_the_fly/settings/updated', $filtered_settings);
    }

    /**
     * Return the setting
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function get_setting(string $key, mixed $default = null): mixed
    {
        $allowed_options = self::get_allowed_options();
        if (!in_array($key, $allowed_options)) {
            return $default;
        }

        $settings = self::get_settings();
        $value = $settings[$key] ?? $default;

        /**
         * Filter the setting value when getting it
         *
         * @param mixed  $value
         * @param string $key
         */
        return apply_filters('mls_on_the_fly/settings/get/' . $key, $value, $key);
    }

    /**
     * Update the setting
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public static function update_setting(string $key, mixed $value): void
    {
        $allowed_options = self::get_allowed_options();
        if (!in_array($key, $allowed_options)) {
            return;
        }

        $settings = self::get_settings();
        $settings[$key] = $value;

        self::update_settings($settings);
    }

    /**
     * Delete the settings
     *
     * @return void
     */
    public static function delete_settings(): void
    {
        delete_option(REALTYNA_MLS_ON_THE_FLY_SLUG . '_settings');

        /**
         * Fires after settings are deleted
         */
        do_action('mls_on_the_fly/settings/deleted');
    }

    /**
     * Get the list of allowed options
     *
     * @return array
     */
    public static function get_allowed_options(): array
    {
        return apply_filters('mls_on_the_fly/settings/allowed_options', self::$allowed_options);
    }
}
