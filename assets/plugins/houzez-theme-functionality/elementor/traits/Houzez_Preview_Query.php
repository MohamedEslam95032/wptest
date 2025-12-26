<?php
namespace HouzezThemeFunctionality\Elementor\Traits;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Houzez_Preview_Query {

    public function single_preview_query() {
        global $post;
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $is_preview_mode = isset($_GET['preview_id']) && $_GET['preview'] == true;

        $template_type = get_post_meta($post->ID, 'fts_template_type', true);

        //'include' => array(156),

        if ( ( $is_edit_mode || $is_preview_mode ) && ( is_singular('fts_builder') || is_singular('favethemes-blocks') ) ) {
            $property_post_id = get_posts([
                'post_type' => 'property',
                'posts_per_page' => 1,
                'fields' => 'ids'
            ]);

            if (!empty($property_post_id)) {
                // Set up a new query to override the global $post
                $GLOBALS['post'] = get_post($property_post_id[0]);
                setup_postdata($GLOBALS['post']);
            }
        }
    }

    public function single_property_preview_query() {
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $is_preview_mode = isset($_GET['preview_id']) && $_GET['preview'] == true;

        if ( ( $is_edit_mode || $is_preview_mode ) && ( is_singular('fts_builder') || is_singular('favethemes-blocks') ) ) {
            $property_post_id = get_posts([
                'post_type' => 'property',
                'posts_per_page' => 1,
                'fields' => 'ids'
            ]);

            if (!empty($property_post_id)) {
                // Set up a new query to override the global $post
                $GLOBALS['post'] = get_post($property_post_id[0]);
                setup_postdata($GLOBALS['post']);
            }
        }
    }

    public function single_agent_preview_query() {
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $is_preview_mode = isset($_GET['preview_id']) && $_GET['preview'] == true;

        if ( ( $is_edit_mode || $is_preview_mode ) && ( is_singular('fts_builder') || is_singular('favethemes-blocks') ) ) {
            $property_post_id = get_posts([
                'post_type' => 'houzez_agent',
                'posts_per_page' => 1,
                'fields' => 'ids'
            ]);

            if (!empty($property_post_id)) {
                // Set up a new query to override the global $post
                $GLOBALS['post'] = get_post($property_post_id[0]);
                setup_postdata($GLOBALS['post']);
            }
        }
    }

    public function single_agency_preview_query() {
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $is_preview_mode = isset($_GET['preview_id']) && $_GET['preview'] == true;

        if ( ( $is_edit_mode || $is_preview_mode ) && ( is_singular('fts_builder') || is_singular('favethemes-blocks') ) ) {
            $property_post_id = get_posts([
                'post_type' => 'houzez_agency',
                'posts_per_page' => 1,
                'fields' => 'ids'
            ]);

            if (!empty($property_post_id)) {
                // Set up a new query to override the global $post
                $GLOBALS['post'] = get_post($property_post_id[0]);
                setup_postdata($GLOBALS['post']);
            }
        }
    }

    public function single_post_preview_query() {
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $is_preview_mode = isset($_GET['preview_id']) && $_GET['preview'] == true;

        if ( ( $is_edit_mode || $is_preview_mode ) && ( is_singular('fts_builder') || is_singular('favethemes-blocks') ) ) {
            $property_post_id = get_posts([
                'post_type' => 'post',
                'posts_per_page' => 1,
                'fields' => 'ids'
            ]);

            if (!empty($property_post_id)) {
                // Set up a new query to override the global $post
                $GLOBALS['post'] = get_post($property_post_id[0]);
                setup_postdata($GLOBALS['post']);
            }
        }
    }

    public function reset_preview_query() {
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $is_preview_mode = isset($_GET['preview_id']) && $_GET['preview'] == true;

        if ( ( $is_edit_mode || $is_preview_mode ) && ( is_singular('fts_builder') || is_singular('favethemes-blocks') ) ) {
            wp_reset_postdata();
        }
    }
}