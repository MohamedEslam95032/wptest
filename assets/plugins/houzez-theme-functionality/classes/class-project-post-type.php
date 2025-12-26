<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Post_Type_Project {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        
        // Add form.
        add_action( 'init', array( __CLASS__, 'definition' ) );
        add_action( 'init', array( __CLASS__, 'taxonomies' ) );
    }

    public static function definition() {
        register_post_type( 'project', array(
            'labels' => array( 'name' => 'Projects' ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'rest_base' => 'projects',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'taxonomies' => array( 'project_type', 'project_status' ),
        ));
    }

    public static function taxonomies() {
        register_taxonomy( 'project_type', 'project', 
            array( 
                'hierarchical' => true, 
                'label' => 'Project Types',
                'labels' => array( 'name' => 'Project Types', 'add_new_item' => 'Add New Type' ),
                'show_in_rest' => true,
                'rest_base' => 'project_types',
                'rest_controller_class' => 'WP_REST_Terms_Controller',
            ) 
        );

        register_taxonomy( 'project_status', 'project', 
            array( 
                'hierarchical' => true, 
                'label' => 'Project Statuses', 
                'labels' => array( 'name' => 'Project Statuses', 'add_new_item' => 'Add New Status' ),
                'show_in_rest' => true,
                'rest_base' => 'project_statuses',
                'rest_controller_class' => 'WP_REST_Terms_Controller',
            ) 
        );

        register_taxonomy( 'project_label', 'project', 
            array( 
                'hierarchical' => true, 
                'label' => 'Project Labels',
                'labels' => array( 'name' => 'Project Labels', 'add_new_item' => 'Add New Label' ),
                'show_in_rest' => true,
                'rest_base' => 'project_labels',
                'rest_controller_class' => 'WP_REST_Terms_Controller',
            ) 
        );  

        register_taxonomy( 'project_amenities', 'project', 
            array( 
                'hierarchical' => true, 
                'label' => 'Project Amenities',
                'labels' => array( 'name' => 'Project Amenities', 'add_new_item' => 'Add New Amenity' ),
                'show_in_rest' => true,
                'rest_base' => 'project_amenities',
                'rest_controller_class' => 'WP_REST_Terms_Controller',
            ) 
        );  
    }  
   
}