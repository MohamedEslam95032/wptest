<?php
/**
 * Houzez Cache Functions
 *
 * Functions for handling caching throughout the Houzez theme
 * Uses WordPress Transients API for reliable caching with or without persistent cache plugins
 *
 * @package Houzez
 * @since 1.0.0
 * @link https://developer.wordpress.org/apis/transients/
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cache keys and groups constants
 */
define('HOUZEZ_CACHE_GROUP_TAXONOMY', 'houzez_taxonomy_data');
define('HOUZEZ_CACHE_GROUP_LISTINGS', 'houzez_listings_data');
define('HOUZEZ_CACHE_GROUP_HTML', 'houzez_html_fragments');

/**
 * Helper function to implement caching for taxonomy queries
 * 
 * Stores taxonomy listing count data using WordPress Transients API
 * for improved performance on taxonomy archive pages.
 * 
 * @since 1.0.0
 * @param WP_Term $term The taxonomy term object
 * @param int $total_listings Total number of listings found
 * @param int $cache_time Cache time in seconds (default: 1 hour)
 * @return void
 */
if (!function_exists('houzez_cache_taxonomy_data')) {
    function houzez_cache_taxonomy_data($term, $total_listings, $cache_time = 0) {
        // Check if caching is enabled
        if (!houzez_is_cache_enabled('count')) {
            return;
        }
        
        if (empty($term) || is_wp_error($term)) {
            return;
        }
        
        // Use the provided cache time or get from settings
        if ($cache_time <= 0) {
            $cache_time = houzez_get_cache_expiration('count');
        }
        
        $transient_key = 'houzez_count_' . $term->taxonomy . '_' . $term->term_id;
        
        // Store the data using transients
        set_transient($transient_key, $total_listings, $cache_time);
    }
}

/**
 * Retrieves cached taxonomy count data if available
 * 
 * @since 1.0.0
 * @param WP_Term $term The taxonomy term object
 * @return mixed|false The cached data or false if not found
 */
if (!function_exists('houzez_get_cached_taxonomy_data')) {
    function houzez_get_cached_taxonomy_data($term) {
        if (empty($term) || is_wp_error($term)) {
            return false;
        }
        
        $transient_key = 'houzez_count_' . $term->taxonomy . '_' . $term->term_id;
        
        return get_transient($transient_key);
    }
}

/**
 * Caches query results for taxonomy pages
 * 
 * @since 1.0.0
 * @param WP_Term $term The taxonomy term object
 * @param WP_Query $query The query object with results
 * @param array $query_args The arguments used for the query
 * @param int $cache_time Cache time in seconds (default: 1 hour)
 * @return void
 */
if (!function_exists('houzez_cache_taxonomy_query')) {
    function houzez_cache_taxonomy_query($term, $query, $query_args, $cache_time = 0) {
        // Check if caching is enabled
        if (!houzez_is_cache_enabled('query')) {
            return;
        }
        
        if (empty($term) || is_wp_error($term) || !is_object($query)) {
            return;
        }
        
        // Use the provided cache time or get from settings
        if ($cache_time <= 0) {
            $cache_time = houzez_get_cache_expiration('query');
        }
        
        // Create a unique key based on the term and query args
        $args_hash = md5(serialize($query_args));
        $transient_key = 'houzez_query_' . $term->taxonomy . '_' . $term->term_id . '_' . $args_hash;
        
        // Only cache the necessary data, not the entire query object
        $cached_data = array(
            'posts' => $query->posts,
            'found_posts' => $query->found_posts,
            'max_num_pages' => $query->max_num_pages,
            'current_page' => $query->query_vars['paged'] ?? 1,
            'post_count' => $query->post_count,
            'args_hash' => $args_hash
        );
        
        set_transient($transient_key, $cached_data, $cache_time);
    }
}

/**
 * Retrieves cached query results for taxonomy pages
 * 
 * @since 1.0.0
 * @param WP_Term $term The taxonomy term object
 * @param array $query_args The arguments used for the query
 * @return array|false The cached query data or false if not found
 */
if (!function_exists('houzez_get_cached_taxonomy_query')) {
    function houzez_get_cached_taxonomy_query($term, $query_args) {
        if (empty($term) || is_wp_error($term)) {
            return false;
        }
        
        // Create the same unique key used for caching
        $args_hash = md5(serialize($query_args));
        $transient_key = 'houzez_query_' . $term->taxonomy . '_' . $term->term_id . '_' . $args_hash;
        
        return get_transient($transient_key);
    }
}

/**
 * Caches rendered HTML fragments for taxonomy pages
 * 
 * @since 1.0.0
 * @param WP_Term $term The taxonomy term object
 * @param string $fragment_name Identifier for the HTML fragment
 * @param string $html The rendered HTML to cache
 * @param array $context Additional context data that affects the rendering
 * @param int $cache_time Cache time in seconds (default: 1 hour)
 * @return void
 */
if (!function_exists('houzez_cache_html_fragment')) {
    function houzez_cache_html_fragment($term, $fragment_name, $html, $context = array(), $cache_time = 0) {
        // Check if caching is enabled
        if (!houzez_is_cache_enabled('html')) {
            return;
        }
        
        if (empty($term) || is_wp_error($term) || empty($fragment_name)) {
            return;
        }
        
        // Use the provided cache time or get from settings
        if ($cache_time <= 0) {
            $cache_time = houzez_get_cache_expiration('html');
        }
        
        // Create a unique key based on the term, fragment name, and context
        $context_hash = !empty($context) ? '_' . md5(serialize($context)) : '';
        $transient_key = 'houzez_html_' . $term->taxonomy . '_' . $term->term_id . '_' . $fragment_name . $context_hash;
        
        set_transient($transient_key, $html, $cache_time);
    }
}

/**
 * Retrieves cached HTML fragments for taxonomy pages
 * 
 * @since 1.0.0
 * @param WP_Term $term The taxonomy term object
 * @param string $fragment_name Identifier for the HTML fragment
 * @param array $context Additional context data that affects the rendering
 * @return string|false The cached HTML or false if not found
 */
if (!function_exists('houzez_get_cached_html_fragment')) {
    function houzez_get_cached_html_fragment($term, $fragment_name, $context = array()) {
        if (empty($term) || is_wp_error($term) || empty($fragment_name)) {
            return false;
        }
        
        // Create the same unique key used for caching
        $context_hash = !empty($context) ? '_' . md5(serialize($context)) : '';
        $transient_key = 'houzez_html_' . $term->taxonomy . '_' . $term->term_id . '_' . $fragment_name . $context_hash;
        
        return get_transient($transient_key);
    }
}

/**
 * Clears all cached data for a specific taxonomy term
 * 
 * @since 1.0.0
 * @param WP_Term $term The taxonomy term object
 * @return bool True on successful removal
 */
if (!function_exists('houzez_clear_taxonomy_cache')) {
    function houzez_clear_taxonomy_cache($term) {
        if (empty($term) || is_wp_error($term)) {
            return false;
        }
        
        global $wpdb;
        
        // Clear count cache
        $count_key = 'houzez_count_' . $term->taxonomy . '_' . $term->term_id;
        delete_transient($count_key);
        
        // We need to use SQL to find and delete all transients related to this term
        // since we can't easily know all the exact keys
        $term_pattern = $wpdb->esc_like('_transient_houzez_') . '%' . $wpdb->esc_like($term->taxonomy . '_' . $term->term_id) . '%';
        
        // Find all transient keys matching our pattern
        $transient_keys = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
                $term_pattern
            )
        );
        
        // Delete each transient
        foreach ($transient_keys as $key) {
            $key = str_replace('_transient_', '', $key);
            delete_transient($key);
        }
        
        // If a persistent object cache is being used, we should also directly clear
        // the object cache for these keys to ensure complete clearing
        if (wp_using_ext_object_cache()) {
            // Clear the count key from object cache directly
            wp_cache_delete($count_key, 'transient');
            
            // For each transient key we found, also delete from object cache
            foreach ($transient_keys as $key) {
                $key = str_replace('_transient_', '', $key);
                wp_cache_delete($key, 'transient');
            }
        }
        
        return true;
    }
}

/**
 * Clears all property listing related caches
 * 
 * Use this when you need to invalidate all caches, such as when
 * global settings change that would affect all listings.
 * 
 * @since 1.0.0
 * @return void
 */
if (!function_exists('houzez_clear_all_listing_caches')) {
    function houzez_clear_all_listing_caches() {
        global $wpdb;
        
        // Find all transients related to Houzez
        $transient_patterns = array(
            $wpdb->esc_like('_transient_houzez_count_'),
            $wpdb->esc_like('_transient_houzez_query_'),
            $wpdb->esc_like('_transient_houzez_html_')
        );
        
        $all_keys = array();
        
        foreach ($transient_patterns as $pattern) {
            $transient_keys = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
                    $pattern . '%'
                )
            );
            
            // Delete each transient
            foreach ($transient_keys as $key) {
                $clean_key = str_replace('_transient_', '', $key);
                delete_transient($clean_key);
                $all_keys[] = $clean_key;
            }
        }
        
        // If a persistent object cache is being used, also clear directly from object cache
        if (wp_using_ext_object_cache()) {
            foreach ($all_keys as $key) {
                wp_cache_delete($key, 'transient');
            }
            
            // As a fallback, we can also flush the entire 'transient' group
            // This is more aggressive but ensures all our caches are cleared
            if (function_exists('wp_cache_flush_group')) {
                wp_cache_flush_group('transient');
            }
        }
    }
}

/**
 * Cache Invalidation Hooks
 * 
 * These hooks ensure that caches are cleared when relevant data changes
 */

/**
 * Clear caches when a property post is saved, updated, or deleted
 */
if (!function_exists('houzez_clear_property_cache_on_save')) {
    function houzez_clear_property_cache_on_save($post_id, $post, $update) {
        // Only process property post type
        if ($post->post_type !== 'property') {
            return;
        }
        
        // Get all taxonomies for this property
        $taxonomies = get_object_taxonomies('property');
        
        // For each taxonomy, get the terms and clear their caches
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($post_id, $taxonomy);
            
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    houzez_clear_taxonomy_cache($term);
                }
            }
        }
    }
}
add_action('save_post', 'houzez_clear_property_cache_on_save', 10, 3);
add_action('before_delete_post', function($post_id) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'property') {
        houzez_clear_property_cache_on_save($post_id, $post, true);
    }
});

/**
 * Clear caches when a property's status changes
 */
if (!function_exists('houzez_clear_cache_on_property_status_change')) {
    function houzez_clear_cache_on_property_status_change($new_status, $old_status, $post) {
        if ($post->post_type === 'property') {
            // Clear all caches related to this property
            houzez_clear_property_cache_on_save($post->ID, $post, true);
        }
    }
}
add_action('transition_post_status', 'houzez_clear_cache_on_property_status_change', 10, 3);

/**
 * Clear caches when a taxonomy term is added, updated, or deleted
 */
if (!function_exists('houzez_clear_cache_on_term_change')) {
    function houzez_clear_cache_on_term_change($term_id, $tt_id, $taxonomy) {
        // Only process property taxonomies
        $property_taxonomies = get_object_taxonomies('property');
        if (!in_array($taxonomy, $property_taxonomies)) {
            return;
        }
        
        // Get the term and clear its cache
        $term = get_term($term_id, $taxonomy);
        if (!empty($term) && !is_wp_error($term)) {
            houzez_clear_taxonomy_cache($term);
        }
    }
}
add_action('created_term', 'houzez_clear_cache_on_term_change', 10, 3);
add_action('edited_term', 'houzez_clear_cache_on_term_change', 10, 3);
add_action('delete_term', 'houzez_clear_cache_on_term_change', 10, 3);

/**
 * Clear all caches when theme options are updated
 */
if (!function_exists('houzez_clear_cache_on_option_update')) {
    function houzez_clear_cache_on_option_update($option_name) {
        // Check if it's a Houzez option
        if (strpos($option_name, 'houzez_') === 0 || $option_name === 'houzez_options') {
            houzez_clear_all_listing_caches();
        }
    }
}
add_action('updated_option', 'houzez_clear_cache_on_option_update', 10, 1);

/**
 * Check if Houzez caching is enabled
 * 
 * @since 1.0.0
 * @param string $cache_type Optional. Specific cache type to check (count, query, html). Default is all.
 * @return bool Whether caching is enabled
 */
if (!function_exists('houzez_is_cache_enabled')) {
    function houzez_is_cache_enabled($cache_type = 'all') {
        // Get the main cache enabled setting
        $cache_enabled = houzez_option('enable_cache', 1);
        
        if (!$cache_enabled) {
            return false;
        }
        
        // If checking for all cache types or a specific type is enabled
        if ($cache_type === 'all') {
            return true;
        }
        
        // Check for specific cache types
        switch ($cache_type) {
            case 'count':
                return (bool) houzez_option('enable_count_cache', 1);
            case 'query':
                return (bool) houzez_option('enable_query_cache', 1);
            case 'html':
                return (bool) houzez_option('enable_html_cache', 1);
            default:
                return true;
        }
    }
}

/**
 * Get cache expiration time in seconds
 * 
 * @since 1.0.0
 * @param string $cache_type The type of cache (count, query, html)
 * @return int Cache expiration time in seconds
 */
if (!function_exists('houzez_get_cache_expiration')) {
    function houzez_get_cache_expiration($cache_type = 'all') {
        // Default cache times
        $default_times = array(
            'count' => 12 * HOUR_IN_SECONDS,
            'query' => 6 * HOUR_IN_SECONDS,
            'html' => 3 * HOUR_IN_SECONDS,
            'all' => 6 * HOUR_IN_SECONDS
        );
        
        // Get the expiration time from options, or use default
        switch ($cache_type) {
            case 'count':
                $option_value = houzez_option('count_cache_expiration', 12);
                return intval($option_value) * HOUR_IN_SECONDS;
            case 'query':
                $option_value = houzez_option('query_cache_expiration', 6);
                return intval($option_value) * HOUR_IN_SECONDS;
            case 'html':
                $option_value = houzez_option('html_cache_expiration', 3);
                return intval($option_value) * HOUR_IN_SECONDS;
            default:
                return $default_times['all'];
        }
    }
} 