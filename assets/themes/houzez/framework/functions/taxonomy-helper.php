<?php
/**
 * Taxonomy Helper Functions
 *
 * Functions to support taxonomy templates in the Houzez theme
 *
 * @package Houzez
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retrieves and validates current taxonomy data
 * 
 * This function safely gets the current taxonomy term information,
 * performing validation to prevent errors when terms don't exist.
 * 
 * @since 1.0.0
 * @return array {
 *     Array of taxonomy data
 *     
 *     @type WP_Term|null $current_term    The current taxonomy term object or null if invalid
 *     @type string       $taxonomy_title  The name of the current term or empty string
 *     @type string       $taxonomy_name   The taxonomy name or empty string
 * }
 */
if (!function_exists('houzez_get_current_taxonomy_data')) {
    function houzez_get_current_taxonomy_data() {
        // Initialize with empty default values
        $taxonomy_data = array(
            'current_term'   => null,
            'taxonomy_title' => '',
            'taxonomy_name'  => ''
        );
        
        // Validate that taxonomy query vars exist
        if (get_query_var('term') && get_query_var('taxonomy')) {
            $term_slug = get_query_var('term');
            $taxonomy  = get_query_var('taxonomy');
            $current_term = get_term_by('slug', $term_slug, $taxonomy);
            
            // Validate that term exists and is not an error
            if ($current_term && !is_wp_error($current_term)) {
                $taxonomy_data['current_term']   = $current_term;
                $taxonomy_data['taxonomy_title'] = $current_term->name;
                $taxonomy_data['taxonomy_name']  = $taxonomy;
            }
        }
        
        return $taxonomy_data;
    }
} 