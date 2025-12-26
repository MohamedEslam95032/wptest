<?php
if( !function_exists('houzez_taxonomies_metaboxes') ) {

    function houzez_taxonomies_metaboxes( $meta_boxes ) {
        $houzez_prefix = 'fave_';

        $meta_boxes[] = array(
            'id'        => 'houzez_taxonomies_map_settings',
            'title'     => esc_html__('Map Settings', 'houzez' ),
            'taxonomies' => array('property_country', 'property_state', 'property_city', 'property_area'),
            

            'fields'    => array(
                array(
                    'name'      => esc_html__('Show header map', 'houzez' ),
                    'desc'      => esc_html__('Enable to show header map', 'houzez' ),
                    'id'        => $houzez_prefix . 'taxonomy_show_header_map',
                    'type'      => 'switch',
                    'std'       => 0,
                ),
                array(
                    'name'      => esc_html__('Map listings', 'houzez' ),
                    'desc'      => esc_html__('Enable to show map listings', 'houzez' ),
                    'id'        => $houzez_prefix . 'taxonomy_map_listings',
                    'type'      => 'select',
                    'options'   => array(
                        'all_listings' => esc_html__('All listings', 'houzez' ),
                        'limited_listings' => esc_html__('Listings Per Page', 'houzez' ),
                    ),
                    'std'       => 'all_listings',
                    'visible' => array( $houzez_prefix.'taxonomy_show_header_map', '=', 1 ),
                ),
                array(
                    'name'      => esc_html__('Latitude', 'houzez' ),
                    'desc'      => esc_html__('Enter the latitude of the city for map. ie: 25.761681', 'houzez' ),
                    'id'        => $houzez_prefix . 'taxonomy_latitude',
                    'type'      => 'text',
                    'visible' => array( $houzez_prefix.'taxonomy_show_header_map', '=', 1 ),
                ),
                array(
                    'name'      => esc_html__('Longitude', 'houzez' ),
                    'desc'      => esc_html__('Enter the longitude of the city for map. ie: -80.191788', 'houzez' ),
                    'id'        => $houzez_prefix . 'taxonomy_longitude',
                    'type'      => 'text',
                    'visible' => array( $houzez_prefix.'taxonomy_show_header_map', '=', 1 ),
                ),
                
            )
        );
        
        $meta_boxes[] = array(
            'id'        => 'houzez_taxonomies',
            'title'     => esc_html__('Other Settings', 'houzez' ),
            'taxonomies' => array('property_status', 'property_type', 'property_label', 'property_country', 'property_state', 'property_city', 'property_area'),
            

            'fields'    => array(
                array(
                    'name'      => esc_html__('Image', 'houzez' ),
                    'desc'      => esc_html__('Recommended image size 770 x 700 px', 'houzez' ),
                    'id'        => $houzez_prefix . 'taxonomy_img',
                    'type'      => 'image_advanced',
                    'max_file_uploads' => 1,
                ),
            )
        );

        $meta_boxes[] = array(
            'id'        => 'houzez_taxonomies_marker',
            'title'     => '',
            'taxonomies' => array( 'property_type' ),
            

            'fields'    => array(
                array(
                    'name'      => esc_html__('Map Marker Icon', 'houzez' ),
                    'id'        => $houzez_prefix . 'marker_icon',
                    'desc'      => esc_html__('Recommended image size 44 x 56 px', 'houzez' ),
                    'type'      => 'image_advanced',
                    'class'      => 'houzez_full_width',
                    'max_file_uploads' => 1,
                ),
                array(
                    'name'      => esc_html__('Map Marker Retina Icon', 'houzez' ),
                    'id'        => $houzez_prefix . 'marker_retina_icon',
                    'desc'      => esc_html__('Recommended image size 88 x 112 px', 'houzez' ),
                    'type'      => 'image_advanced',
                    'class'      => 'houzez_full_width',
                    'max_file_uploads' => 1,
                )
            )
        );

        $meta_boxes[] = array(
            'id'        => 'houzez_taxonomies_custom_link',
            'title'     => '',
            'taxonomies' => array('property_status', 'property_type', 'property_label', 'property_country', 'property_state', 'property_city', 'property_area'),
            

            'fields'    => array(
                array(
                    'name'      => esc_html__('Custom Link', 'houzez' ),
                    'id'        => $houzez_prefix . 'prop_taxonomy_custom_link',
                    'type'      => 'text',
                    'desc' => esc_html__('Enter a custom link for this taxonomy if you want to link it with an external site', 'houzez'),
                ),
                
            )
        );

        $meta_boxes[] = array(
            'id'        => 'houzez_features_tax_meta',
            'title'     => '',
            'taxonomies' => array('property_feature'),
            
            'fields'    => array(
                array(
                    'name'      => esc_html__('Icon Type', 'houzez' ),
                    'id'        => $houzez_prefix . 'feature_icon_type',
                    'type'      => 'select',
                    'options'   => array(
                        'fontawesome' => esc_html__('Fontawesome v6', 'houzez' ),
                        'custom' => esc_html__('Custom Image', 'houzez' ),
                    ),
                    'std'       => array( 'fontawesome' ),
                    'desc'      => '',
                ),
                array(
                    'name'      => esc_html__('Icon', 'houzez' ),
                    'id'        => $houzez_prefix . 'prop_features_icon',
                    'type'      => 'text',
                    'placeholder' => esc_html__('Enter the fontawesome icon class', 'houzez'),
                    'visible' => array( $houzez_prefix.'feature_icon_type', '=', 'fontawesome' ),
                ),
                array(
                    'name'      => esc_html__('Icon', 'houzez' ),
                    'id'        => $houzez_prefix . 'feature_img_icon',
                    'type'      => 'image_advanced',
                    'max_file_uploads' => 1,
                    'desc'      =>esc_html__('Upload icon', 'houzez' ),
                    'visible' => array( $houzez_prefix.'feature_icon_type', '=', 'custom' ),
                )
                
            )
        );

        return apply_filters('houzez_taxonomies_meta', $meta_boxes);

    }

    add_filter( 'rwmb_meta_boxes', 'houzez_taxonomies_metaboxes' );
}