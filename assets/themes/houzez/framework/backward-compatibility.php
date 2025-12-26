<?php
/**
 * Backward compatibility functions
 *
 * These functions provide backward compatibility for code that
 * calls standalone functions that have been refactored into classes
 */

if(!function_exists('houzez_get_custom_search_field')) {
    /**
     * Backward compatibility wrapper for Houzez_Property_Search::get_custom_search_field
     * 
     * @param string $key The field key/slug to render
     * @return void
     */
    function houzez_get_custom_search_field($key) {
        // Call the class method
        Houzez_Property_Search::get_custom_search_field($key);
    }
}

if( ! function_exists('houzez_enqueue_maps_api') ) {
    function houzez_enqueue_maps_api() {
        houzez_enqueue_maps_api_geolocation_field();
    }
}