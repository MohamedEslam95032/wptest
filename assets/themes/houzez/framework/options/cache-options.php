<?php
/**
 * Cache Options for Houzez Theme
 *
 * @package Houzez
 * @since 1.0.0
 */

global $houzez_opt_name;

// Cache Settings
Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__('Cache Settings', 'houzez'),
    'id'     => 'houzez_cache_options',
    'desc'   => esc_html__('Manage caching settings to improve performance.', 'houzez'),
    'icon'   => 'el el-dashboard',
    'fields' => array(
        array(
            'id'       => 'enable_cache',
            'type'     => 'switch',
            'title'    => esc_html__('Enable Caching', 'houzez'),
            'desc'     => esc_html__('Enable or disable all caching functionality.', 'houzez'),
            'default'  => 1,
            'on'       => esc_html__('Enabled', 'houzez'),
            'off'      => esc_html__('Disabled', 'houzez'),
        ),
        array(
            'id'       => 'cache_section_start',
            'type'     => 'section',
            'title'    => esc_html__('Cache Type Settings', 'houzez'),
            'subtitle' => esc_html__('Configure individual cache types', 'houzez'),
            'indent'   => true,
            'required' => array('enable_cache', '=', '1'),
        ),
        array(
            'id'       => 'enable_count_cache',
            'type'     => 'switch',
            'title'    => esc_html__('Enable Count Cache', 'houzez'),
            'desc'     => esc_html__('Cache property counts for taxonomies.', 'houzez'),
            'default'  => 1,
            'on'       => esc_html__('Enabled', 'houzez'),
            'off'      => esc_html__('Disabled', 'houzez'),
            'required' => array('enable_cache', '=', '1'),
        ),
        array(
            'id'       => 'count_cache_expiration',
            'type'     => 'slider',
            'title'    => esc_html__('Count Cache Expiration', 'houzez'),
            'subtitle' => esc_html__('Hours before count cache expires', 'houzez'),
            'desc'     => esc_html__('Set how long count cache data should be stored before refreshing.', 'houzez'),
            'default'  => 12,
            'min'      => 1,
            'max'      => 72,
            'step'     => 1,
            'display_value' => 'text',
            'required' => array(
                array('enable_cache', '=', '1'),
                array('enable_count_cache', '=', '1'),
            ),
        ),
        array(
            'id'       => 'enable_query_cache',
            'type'     => 'switch',
            'title'    => esc_html__('Enable Query Cache', 'houzez'),
            'desc'     => esc_html__('Cache property query results.', 'houzez'),
            'default'  => 1,
            'on'       => esc_html__('Enabled', 'houzez'),
            'off'      => esc_html__('Disabled', 'houzez'),
            'required' => array('enable_cache', '=', '1'),
        ),
        array(
            'id'       => 'query_cache_expiration',
            'type'     => 'slider',
            'title'    => esc_html__('Query Cache Expiration', 'houzez'),
            'subtitle' => esc_html__('Hours before query cache expires', 'houzez'),
            'desc'     => esc_html__('Set how long query cache data should be stored before refreshing.', 'houzez'),
            'default'  => 6,
            'min'      => 1,
            'max'      => 48,
            'step'     => 1,
            'display_value' => 'text',
            'required' => array(
                array('enable_cache', '=', '1'),
                array('enable_query_cache', '=', '1'),
            ),
        ),
        array(
            'id'       => 'enable_html_cache',
            'type'     => 'switch',
            'title'    => esc_html__('Enable HTML Cache', 'houzez'),
            'desc'     => esc_html__('Cache rendered HTML fragments.', 'houzez'),
            'default'  => 1,
            'on'       => esc_html__('Enabled', 'houzez'),
            'off'      => esc_html__('Disabled', 'houzez'),
            'required' => array('enable_cache', '=', '1'),
        ),
        array(
            'id'       => 'html_cache_expiration',
            'type'     => 'slider',
            'title'    => esc_html__('HTML Cache Expiration', 'houzez'),
            'subtitle' => esc_html__('Hours before HTML cache expires', 'houzez'),
            'desc'     => esc_html__('Set how long HTML cache data should be stored before refreshing.', 'houzez'),
            'default'  => 3,
            'min'      => 1,
            'max'      => 24,
            'step'     => 1,
            'display_value' => 'text',
            'required' => array(
                array('enable_cache', '=', '1'),
                array('enable_html_cache', '=', '1'),
            ),
        ),
        array(
            'id'     => 'cache_section_end',
            'type'   => 'section',
            'indent' => false,
            'required' => array('enable_cache', '=', '1'),
        ),
        array(
            'id'       => 'cache_actions',
            'type'     => 'section',
            'title'    => esc_html__('Cache Actions', 'houzez'),
            'subtitle' => esc_html__('Manage existing cache data', 'houzez'),
            'indent'   => true,
        ),
        array(
            'id'       => 'clear_cache_button',
            'type'     => 'button_set',
            'title'    => esc_html__('Clear Cache', 'houzez'),
            'subtitle' => esc_html__('Clear all cached data', 'houzez'),
            'desc'     => esc_html__('Use this button to clear all cached data. This will force fresh data to be generated on the next page load.', 'houzez'),
            'options'  => array(
                'clear_all' => esc_html__('Clear All Cache', 'houzez'),
            ),
        ),
        array(
            'id'     => 'cache_actions_end',
            'type'   => 'section',
            'indent' => false,
        ),
    )
));

// Add AJAX handler for clearing cache
if (!function_exists('houzez_clear_all_cache_ajax')) {
    function houzez_clear_all_cache_ajax() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'redux_clear_cache')) {
            wp_send_json_error(array('message' => esc_html__('Security check failed', 'houzez')));
        }
        
        // Clear all caches
        houzez_clear_all_listing_caches();
        
        wp_send_json_success(array('message' => esc_html__('All caches cleared successfully', 'houzez')));
    }
}
add_action('wp_ajax_houzez_clear_all_cache', 'houzez_clear_all_cache_ajax');

// Add custom Redux field for the clear cache button
if (!function_exists('houzez_register_custom_redux_fields')) {
    function houzez_register_custom_redux_fields($field, $value) {
        if ($field['type'] === 'button_set' && $field['id'] === 'clear_cache_button') {
            $nonce = wp_create_nonce('redux_clear_cache');
            
            echo '<div class="redux-field-container">';
            echo '<button type="button" class="button button-primary" id="houzez-clear-cache-button" data-nonce="' . esc_attr($nonce) . '">' . esc_html__('Clear All Cache', 'houzez') . '</button>';
            echo '<span class="spinner" style="float:none;margin-left:10px;"></span>';
            echo '<div class="clear-cache-result"></div>';
            echo '</div>';
            
            // Add JavaScript for the button
            echo '<script type="text/javascript">
                jQuery(document).ready(function($) {
                    $("#houzez-clear-cache-button").on("click", function() {
                        var button = $(this);
                        var spinner = button.next(".spinner");
                        var result = $(".clear-cache-result");
                        
                        spinner.css("visibility", "visible");
                        button.prop("disabled", true);
                        result.html("");
                        
                        $.ajax({
                            url: ajaxurl,
                            type: "POST",
                            data: {
                                action: "houzez_clear_all_cache",
                                nonce: button.data("nonce")
                            },
                            success: function(response) {
                                spinner.css("visibility", "hidden");
                                button.prop("disabled", false);
                                
                                if (response.success) {
                                    result.html("<div class=\"notice notice-success\"><p>" + response.data.message + "</p></div>");
                                } else {
                                    result.html("<div class=\"notice notice-error\"><p>" + response.data.message + "</p></div>");
                                }
                            },
                            error: function() {
                                spinner.css("visibility", "hidden");
                                button.prop("disabled", false);
                                result.html("<div class=\"notice notice-error\"><p>' . esc_html__('An error occurred while clearing cache', 'houzez') . '</p></div>");
                            }
                        });
                    });
                });
            </script>';
            
            return '';
        }
        
        return null;
    }
}
add_filter('redux/houzez_options/field/render/button_set', 'houzez_register_custom_redux_fields', 10, 2); 