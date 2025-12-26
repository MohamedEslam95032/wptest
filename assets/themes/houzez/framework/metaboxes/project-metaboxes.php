<?php
include_once( get_theme_file_path('/framework/metaboxes/project/information.php') );
include_once( get_theme_file_path('/framework/metaboxes/project/media.php') );
include_once( get_theme_file_path('/framework/metaboxes/project/map.php') );
include_once( get_theme_file_path('/framework/metaboxes/project/attachments.php') );
include_once( get_theme_file_path('/framework/metaboxes/project/timeline.php') );
include_once( get_theme_file_path('/framework/metaboxes/project/units.php') );
include_once( get_theme_file_path('/framework/metaboxes/project/payment-plan.php') );

if( !function_exists('houzez_register_project_metaboxes') ) {
     
    function houzez_register_project_metaboxes( $meta_boxes ) {

        $meta_boxes_tabs = array();

        $meta_boxes_fields = array();

        $meta_boxes[] = array(
            'id'         => 'houzez-project-meta-box',
            'title'      => esc_html__('Project', 'houzez'),
            'post_types' => array( 'project' ),
            'tabs'       => apply_filters( 'houzez_project_metabox_tabs', $meta_boxes_tabs ),
            'tab_style'  => 'left',
            'fields'     => apply_filters( 'houzez_project_metabox_fields', $meta_boxes_fields ),
        );

        return apply_filters( 'houzez_theme_meta', $meta_boxes );

    }

    add_filter( 'rwmb_meta_boxes', 'houzez_register_project_metaboxes' );
}