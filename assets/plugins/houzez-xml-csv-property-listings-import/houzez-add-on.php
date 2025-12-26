<?php
/*
Plugin Name: WP All Import - Houzez Add-On
Plugin URI: http://www.wpallimport.com/
Description: Supporting imports into the Houzez theme.
Version: 2.3.3
Author: Favethemes
*/


include "rapid-addon.php";

$houzez_addon = new RapidAddon( 'Houzez Add-On', 'houzez_addon' );

$houzez_addon->disable_default_images();

$houzez_addon->import_images( 'houzez_addon_gallery_images', 'Gallery Images' );

function houzez_addon_gallery_images( $post_id, $attachment_id, $image_filepath, $import_options ) {
    add_post_meta( $post_id, 'fave_property_images', $attachment_id ) || update_post_meta( $post_id, 'fave_property_images', $attachment_id );
}

$houzez_addon->import_images( 'houzez_addon_floorplan_images', 'Floor Plan Images' );

function houzez_addon_floorplan_images( $post_id, $attachment_id, $image_filepath, $import_options ) {
    static $last_post_id, $fp_count;

    $fp_count = ($post_id === $last_post_id) ? $fp_count + 1 : 0;
    $fp_meta = get_post_meta( $post_id, 'floor_plans', true );

    // Ensure $fp_meta is an array
    if ( !is_array( $fp_meta ) ) {
        $fp_meta = array();
    }

    $image = wp_get_attachment_image_src( $attachment_id, 'full' );
    if ( !is_array( $image ) ) return;

    // Ensure the $fp_meta[$fp_count] is an array
    if ( !isset($fp_meta[$fp_count]) || !is_array($fp_meta[$fp_count]) ) {
        $fp_meta[$fp_count] = array();
    }

    $fp_meta[$fp_count]['fave_plan_image'] = $image[0]; // This is line 32 with the Bug
    update_post_meta( $post_id, 'floor_plans', $fp_meta );
    $last_post_id = $post_id;
}



$houzez_addon->import_files( 'houzez_addon_property_attachments', 'Property Attachments' );

function houzez_addon_property_attachments( $post_id, $attachment_id, $image_filepath, $import_options ) {

    add_post_meta( $post_id, 'fave_attachments', $attachment_id ) || update_post_meta( $post_id, 'fave_attachments', $attachment_id );

}


$houzez_addon->add_field( 'fave_currency', 'Select Currency', 'text', null, 'example: USD' );
$houzez_addon->add_field( 'fave_property_price', 'Sale or Rent Price', 'text', null, 'Only digits, example: 557000' );

$houzez_addon->add_field( 'fave_property_sec_price', 'Second Price ( Display optional price for rental or square feet )', 'text', null, 'Only digits, example: 700' );

$houzez_addon->add_field( 'fave_property_price_prefix', 'Before Price label', 'text', null, 'Example: Start From' );
$houzez_addon->add_field( 'fave_property_price_postfix', 'After Price label', 'text', null, 'Example: Per Month' );

$houzez_addon->add_field( 'fave_property_size', 'Area Size', 'text', null, 'Only digits, example: 2500' );

$houzez_addon->add_field( 'fave_property_size_prefix', 'Area Size Postfix', 'text', null, 'Example: Sq Ft' );

$houzez_addon->add_field( 'fave_property_land', 'Land Area', 'text', null, 'Only digits, example: 2500' );

$houzez_addon->add_field( 'fave_property_land_postfix', 'Land Area Postfix', 'text', null, 'Example: Sq Ft' );

$houzez_addon->add_field( 'fave_property_bedrooms', 'Bedrooms', 'text', null, 'Example: 4' );

$houzez_addon->add_field( 'fave_property_bathrooms', 'Bathrooms', 'text', null, 'Example: 2' );
$houzez_addon->add_field( 'fave_property_rooms', 'Rooms', 'text', null, 'Example: 2' );

$houzez_addon->add_field( 'fave_property_garage', 'Garages', 'text', null, 'Example: 1' );

$houzez_addon->add_field( 'fave_private_note', 'Private Note', 'textarea', null, 'Example: 1' );

$houzez_addon->add_field( 'fave_property_garage_size', 'Garages Size', 'text', null, 'Example: 100 sq ft' );
$houzez_addon->add_field( 'fave_virtual_tour', '360° Virtual Tour', 'textarea', null, 'Enter virtual tour embeded code or iframe' );

$houzez_addon->add_field( 'fave_video_url', 'Video URL', 'text', null, 'Provide video URL. YouTube, Vimeo, SWF File and MOV File are supported.' );
$houzez_addon->add_field( 'fave_video_image', 'Video Image', 'image', null, 'Will be displayed as a place holder. Required for the video to be displayed. Minimum width of 818px and minimum height 417px. Larger sizes will be cropped.' );

$houzez_addon->add_field( 'fave_energy_class', 'Energy Class', 'text', null, 'Example: A+, A, B' );
$houzez_addon->add_field( 'fave_energy_global_index', 'Global Energy Performance Index', 'text', null, 'For example: 92.42 kWh / m²a' );
$houzez_addon->add_field( 'fave_renewable_energy_global_index', 'Renewable energy performance index', 'text', null, 'For example: 00.00 kWh / m²a' );
$houzez_addon->add_field( 'fave_energy_performance', 'Energy performance of the building', 'text', null, '' );
$houzez_addon->add_field( 'fave_epc_current_rating', 'EPC Current Rating', 'text', null, '' );
$houzez_addon->add_field( 'fave_epc_potential_rating', 'EPC Potential Rating', 'text', null, '' );

$houzez_addon->add_field( 'fave_agents', 'Agent', 'text' );
$houzez_addon->add_field( 'fave_property_agency', 'Agency', 'text' );

$houzez_addon->add_field( 'fave_property_map', 'Show Map', 'radio', array(
    '1' => 'Yes',
    '0' => 'No'
) );

$houzez_addon->add_field(
    'location_settings',
    'Property Map Location',
    'radio',
    array(
        'search_by_address'     => array(
            'Search by Address',
            $houzez_addon->add_options(
                $houzez_addon->add_field(
                    'fave_property_map_address',
                    'Property Address',
                    'text'
                ),
                'Google Geocode API Settings',
                array(
                    $houzez_addon->add_field(
                        'address_geocode',
                        'Request Method',
                        'radio',
                        array(
                            'address_no_key'            => array(
                                'No API Key',
                                'Limited number of requests.'
                            ),
                            'address_google_developers' => array(
                                'Google Developers API Key - <a href="https://developers.google.com/maps/documentation/geocoding/#api_key">Get free API key</a>',
                                $houzez_addon->add_field(
                                    'address_google_developers_api_key',
                                    'API Key',
                                    'text'
                                ),
                                'Up to 2500 requests per day and 5 requests per second.'
                            ),
                            'address_google_for_work'   => array(
                                'Google for Work Client ID & Digital Signature - <a href="https://developers.google.com/maps/documentation/business">Sign up for Google for Work</a>',
                                $houzez_addon->add_field(
                                    'address_google_for_work_client_id',
                                    'Google for Work Client ID',
                                    'text'
                                ),
                                $houzez_addon->add_field(
                                    'address_google_for_work_digital_signature',
                                    'Google for Work Digital Signature',
                                    'text'
                                ),
                                'Up to 100,000 requests per day and 10 requests per second'
                            )
                        ) // end Request Method options array
                    ) // end Request Method nested radio field
                ) // end Google Geocode API Settings fields
            ) // end Google Gecode API Settings options panel
        ), // end Search by Address radio field
        'search_by_coordinates' => array(
            'Search by Coordinates',
            $houzez_addon->add_field(
                'property_latitude',
                'Latitude',
                'text',
                null,
                'Example: 34.0194543'
            ),
            $houzez_addon->add_options(
                $houzez_addon->add_field(
                    'property_longitude',
                    'Longitude',
                    'text',
                    null,
                    'Example: -118.4911912'
                ),
                'Google Geocode API Settings',
                array(
                    $houzez_addon->add_field(
                        'coord_geocode',
                        'Request Method',
                        'radio',
                        array(
                            'coord_no_key'            => array(
                                'No API Key',
                                'Limited number of requests.'
                            ),
                            'coord_google_developers' => array(
                                'Google Developers API Key - <a href="https://developers.google.com/maps/documentation/geocoding/#api_key">Get free API key</a>',
                                $houzez_addon->add_field(
                                    'coord_google_developers_api_key',
                                    'API Key',
                                    'text'
                                ),
                                'Up to 2500 requests per day and 5 requests per second.'
                            ),
                            'coord_google_for_work'   => array(
                                'Google for Work Client ID & Digital Signature - <a href="https://developers.google.com/maps/documentation/business">Sign up for Google for Work</a>',
                                $houzez_addon->add_field(
                                    'coord_google_for_work_client_id',
                                    'Google for Work Client ID',
                                    'text'
                                ),
                                $houzez_addon->add_field(
                                    'coord_google_for_work_digital_signature',
                                    'Google for Work Digital Signature',
                                    'text'
                                ),
                                'Up to 100,000 requests per day and 10 requests per second'
                            )
                        ) // end Geocode API options array
                    ) // end Geocode nested radio field
                ) // end Geocode settings
            ) // end coordinates Option panel
        ) // end Search by Coordinates radio field
    ) // end Property Location radio field
);

// Floor Plans
$houzez_floor_plans_help = "If there are multiple floorplans then separate each value with a '|'";
$houzez_addon->add_options( false, 'Floor Plan Details', array(
    $houzez_addon->add_field( 'fave_plan_title', 'Floor Plan Titles', 'text', null, $houzez_floor_plans_help ),
    $houzez_addon->add_field( 'fave_plan_size', 'Floor Plan Sizes', 'text', null, $houzez_floor_plans_help ),
    $houzez_addon->add_field( 'fave_plan_rooms', 'Floor Plan Bedrooms', 'text', null, "Numeric - " . $houzez_floor_plans_help ),
    $houzez_addon->add_field( 'fave_plan_bathrooms', 'Floor Plan Bathrooms', 'text', null, "Numeric - " . $houzez_floor_plans_help ),
    $houzez_addon->add_field( 'fave_plan_price', 'Floor Plan Prices', 'text', null, $houzez_floor_plans_help ),
    $houzez_addon->add_field( 'fave_plan_description', 'Floor Plan Descriptions', 'text', null, $houzez_floor_plans_help )
) );


// Additional Features
$houzez_ad_help = "If there are multiple additional features then separate each value with a '|'";
$houzez_addon->add_options( false, 'Additional Details', array(
    $houzez_addon->add_field( 'fave_additional_feature_title', 'Titles', 'text', null, $houzez_ad_help ),
    $houzez_addon->add_field( 'fave_additional_feature_value', 'Values', 'text', null, $houzez_ad_help )
) );


// Multi Units / Sub Listings
$houzez_mu_help = "If there are multiple units then separate each value with a '|'";
$houzez_addon->add_options( false, 'Multi Units / Sub Properties', array(

    $houzez_addon->add_field( 'fave_multi_units_ids', 'Listing IDs', 'textarea', null, 'Enter listing IDs with comma separater(eg: 4,5,6)' ),

    //$houzez_addon->add_field( 'fave_multi_units_ids_label', 'Or', 'label', null ),
    
    $houzez_addon->add_field( 'fave_mu_title', 'Titles', 'text', null, $houzez_mu_help ),
    $houzez_addon->add_field( 'fave_mu_type', 'Property Type', 'text', null, $houzez_mu_help ),
    $houzez_addon->add_field( 'fave_mu_price', 'Prices', 'text', null, $houzez_mu_help ),
    $houzez_addon->add_field( 'fave_mu_beds', 'Bedrooms', 'text', null, $houzez_mu_help ),
    $houzez_addon->add_field( 'fave_mu_baths', 'Bathrooms', 'text', null, $houzez_mu_help ),
    $houzez_addon->add_field( 'fave_mu_size', 'Property Sizes', 'text', null, $houzez_mu_help ),
    $houzez_addon->add_field( 'fave_mu_size_postfix', 'Sizes Postfix', 'text', null, $houzez_mu_help ),
    $houzez_addon->add_field( 'fave_mu_availability_date', 'Availability Date', 'text', null, $houzez_mu_help ),
) );


$houzez_addon->add_options( null, 'Advanced Settings', array(

    $houzez_addon->add_field( 'fave_property_id', 'Property ID', 'text', null, 'To help search directly for a property. Example: HZ01' ),
    $houzez_addon->add_field( 'fave_property_year', 'Year Built', 'text', null, '' ),
    $houzez_addon->add_field( 'fave_property_address', 'Address(*only street name and building no)', 'text', null, '' ),
    $houzez_addon->add_field( 'fave_property_zip', 'Zip/Postcode', 'text', null, '' ),
    $houzez_addon->add_field( 'fave_property_country', 'Country', 'text', null, 'Provide country short name. Example US for United States, CA for Canada etc' ),

    $houzez_addon->add_field( 'fave_featured', 'Featured Property?', 'radio', array(
        '0' => 'No',
        '1' => 'Yes',
    ) ),

    $houzez_addon->add_field( 'fave_agent_display_option', 'What to display in agent information box?', 'radio', array(
        'author_info'  => 'Author Information',
        'agent_info'   => 'Agent Information',
        'agency_info'  => 'Agency Information',
        'none'         => 'Hide Information Box'
    ) ),

    //$houzez_addon->add_field( 'fave_agents', 'Agent ID', 'text', null, 'Enter agent id. Example: 333' ),

    $houzez_addon->add_field( 'fave_prop_homeslider', 'Add this property to Homepage Slider?', 'radio', array(
        'no'  => 'No',
        'yes' => 'Yes',
    ) ),

    $houzez_addon->add_field( 'fave_prop_slider_image', 'Slider Image', 'image', null, 'Recommended image size is 2000px by 700px. May use bigger or smaller image but keep the same height to width ratio and use the exact same size for all images in slider.' ),

) );


$houzez_addon->set_import_function( 'houzez_addon_import' );

$houzez_addon->admin_notice();
/* Check dependent plugins */
$houzez_addon->admin_notice( 'Houzez Add-on requires WP All Import <a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=houzez" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="https://themeforest.net/item/houzez-real-estate-wordpress-theme-/15752549">Houzez</a> theme.',
    array('themes' => array("Houzez", 'Houzez Child'))
);

$houzez_addon->run( array(
    "themes"     => array("Houzez", 'Houzez Child'),
    "post_types" => array("property")
) );

function houzez_addon_import( $post_id, $data, $import_options, $article ) {

    global $houzez_addon;

    // all fields except for slider and image fields
    $fields = array(
        'fave_currency',
        'fave_property_price',
        'fave_property_sec_price',
        'fave_property_price_prefix',
        'fave_property_price_postfix',
        'fave_property_size',
        'fave_property_size_prefix',
        'fave_property_land',
        'fave_property_land_postfix',
        'fave_property_bedrooms',
        'fave_property_bathrooms',
        'fave_property_rooms',
        'fave_property_garage',
        'fave_property_garage_size',
        'fave_private_note',
        'fave_virtual_tour',
        'fave_property_map',
        'fave_property_id',
        'fave_property_year',
        'fave_property_address',
        'fave_property_zip',
        'fave_property_country',
        'fave_featured',
        'fave_agent_display_option',
        'fave_prop_homeslider',
        'fave_video_url',
        'fave_energy_class',
        'fave_energy_global_index',
        'fave_energy_performance',
        'fave_renewable_energy_global_index',
        'fave_epc_current_rating',
        'fave_epc_potential_rating',
    );


    // image fields
    $image_fields = array(
        'fave_prop_slider_image',
        'fave_video_image',
    );

    // floorplan fields
    $floorplan_fields = array(
        'fave_plan_title',
        'fave_plan_size',
        'fave_plan_rooms',
        'fave_plan_bathrooms',
        'fave_plan_price',
        'fave_plan_description'
    );

    // Additional Features fields
    $additional_features_fields = array(
        'fave_additional_feature_title',
        'fave_additional_feature_value'
    );

    // Multi Units fields
    $multi_units_fields = array(
        'fave_mu_title',
        'fave_mu_type',
        'fave_mu_price',
        'fave_mu_beds',
        'fave_mu_baths',
        'fave_mu_size',
        'fave_mu_size_postfix',
        'fave_mu_availability_date'
    );

    $fields = array_merge( $fields, $image_fields, $floorplan_fields, $multi_units_fields, $additional_features_fields );

    $floorplan_meta = array();
    $multi_units_meta = array();
    $additional_features_meta = array();

    // update everything in fields arrays
    foreach ( $fields as $field ) {

        if ( empty( $article['ID'] ) or $houzez_addon->can_update_meta( $field, $import_options ) ) {

            // Image fields
            if ( in_array( $field, $image_fields ) ) {
                if ( empty( $article['ID'] ) or $houzez_addon->can_update_image( $import_options ) ) {

                    $id = $data[$field]['attachment_id'];

                    if ( strlen( $id ) == 0 ) {
                        delete_post_meta( $post_id, $field );
                    } else {
                        update_post_meta( $post_id, $field, $id );
                    }

                }
            } else if (in_array($field, $floorplan_fields) && isset($data[$field])) {
                foreach (explode("|", $data[$field]) as $fp_key => $fp_value) {
                    $t_fp_value = trim($fp_value);
                    if (!empty($t_fp_value)) {
                        // Ensure $floorplan_meta is an array
                        if (!isset($floorplan_meta) || !is_array($floorplan_meta)) {
                            $floorplan_meta = array();
                        }

                        // Ensure $floorplan_meta[$fp_key] is an array
                        if (!isset($floorplan_meta[$fp_key]) || !is_array($floorplan_meta[$fp_key])) {
                            $floorplan_meta[$fp_key] = array();
                        }

                        $floorplan_meta[$fp_key][$field] = $t_fp_value;
                    }
                }
            } else if ( in_array( $field, $additional_features_fields ) ) {
                foreach ( explode( "|", $data[$field] ) as $fp_key => $fp_value ) {
                    $t_fp_value = trim( $fp_value );
                    if (!empty($t_fp_value)) {

                        if (!isset($additional_features_meta[$fp_key]) || !is_array($additional_features_meta[$fp_key])) {
                            $additional_features_meta[$fp_key] = array();
                        }
                        $additional_features_meta[$fp_key][$field] = trim($fp_value);
                    }
                }
            } else if ( in_array( $field, $multi_units_fields ) && isset($data[$field]) ) {
                // Ensure $multi_units_meta is an array
                if (!isset($multi_units_meta) || !is_array($multi_units_meta)) {
                    $multi_units_meta = array();
                }

                foreach ( explode( "|", $data[$field] ) as $fp_key => $fp_value ) {
                    $t_fp_value = trim( $fp_value );
                    if (!empty($t_fp_value)) {
                        // Debugging output
                        error_log("Processing fp_key: $fp_key, fp_value: $t_fp_value");

                        // Ensure $multi_units_meta[$fp_key] is an array
                        if (!isset($multi_units_meta[$fp_key]) || !is_array($multi_units_meta[$fp_key])) {
                            $multi_units_meta[$fp_key] = array();
                        }

                        // Final assignment
                        $multi_units_meta[$fp_key][$field] = $t_fp_value;
                    }
                }
            }
            else {

                // Check if the key exists and is not null before using its value.
                // If it doesn't exist or is null, default to an empty string.
                $field_value = isset($data[$field]) ? $data[$field] : '';

                // Now you can safely check the length.
                if (strlen($field_value) == 0) {
                    delete_post_meta( $post_id, $field );
                } else {
                    update_post_meta( $post_id, $field, $data[$field] );
                }
            }
        }
    }

    update_option( 'floor_plans', $floorplan_meta );
    update_post_meta( $post_id, 'floor_plans', $floorplan_meta );
    update_post_meta( $post_id, 'fave_multi_units', $multi_units_meta );
    update_post_meta( $post_id, 'additional_features', $additional_features_meta );



    // clear image fields to override import settings
    $fields = array(
        'fave_attachments',
        'fave_property_images'
    );

    if ( empty( $article['ID'] ) or $houzez_addon->can_update_image( $import_options ) ) {

        foreach ( $fields as $field ) {

            delete_post_meta( $post_id, $field );

        }

    }


    // update agent, create a new one if not found
    $field = 'fave_agents';
    $post_type = 'houzez_agent';

    if ( empty( $article['ID'] ) or $houzez_addon->can_update_meta( $field, $import_options ) ) {

        $postID = houzez_import_addon_get_page_id_by_title( $data[$field], $post_type );

        if ( !empty($postID) ) {

            update_post_meta( $post_id, $field, $postID );

        } else if( ! empty($data[$field]) ) {

            // insert title and attach to property
            $postarr = array(
                'post_content' => '',
                'post_name'    => $data[$field],
                'post_title'   => $data[$field],
                'post_type'    => $post_type,
                'post_status'  => 'publish',
                'post_excerpt' => ''
            );

            wp_insert_post( $postarr );

            if ( $postID = houzez_import_addon_get_page_id_by_title( $data[$field], $post_type ) ) {

                update_post_meta( $post_id, $field, $postID );

            }

        }
    }

    // update agency, create a new one if not found
    $field = 'fave_property_agency';
    $post_type = 'houzez_agency';

    if ( empty( $article['ID'] ) or $houzez_addon->can_update_meta( $field, $import_options ) ) {

        $postID = houzez_import_addon_get_page_id_by_title( $data[$field], $post_type );

        if ( !empty($postID) ) {

            update_post_meta( $post_id, $field, $postID );

        } else if( ! empty($data[$field]) ) {

            // insert title and attach to property
            $postarr = array(
                'post_content' => '',
                'post_name'    => $data[$field],
                'post_title'   => $data[$field],
                'post_type'    => $post_type,
                'post_status'  => 'publish',
                'post_excerpt' => ''
            );

            wp_insert_post( $postarr );

            if ( $postID = houzez_import_addon_get_page_id_by_title( $data[$field], $post_type ) ) {

                update_post_meta( $post_id, $field, $postID );

            }

        }
    }

    // update property location
    $field = 'fave_property_map_address';

    $address = $data[$field];

    $lat = $data['property_latitude'];

    $long = $data['property_longitude'];

    //  build search query
    if ( $data['location_settings'] == 'search_by_address' ) {

        $search = (!empty($address) ? 'address=' . rawurlencode( $address ) : null);

    } else {

        $search = (!empty($lat) && !empty($long) ? 'latlng=' . rawurlencode( $lat . ',' . $long ) : null);

    }

    // build api key
    if ( $data['location_settings'] == 'search_by_address' ) {

        if ( $data['address_geocode'] == 'address_google_developers' && !empty($data['address_google_developers_api_key']) ) {

            $api_key = '&key=' . $data['address_google_developers_api_key'];

        } elseif ( $data['address_geocode'] == 'address_google_for_work' && !empty($data['address_google_for_work_client_id']) && !empty($data['address_google_for_work_signature']) ) {

            $api_key = '&client=' . $data['address_google_for_work_client_id'] . '&signature=' . $data['address_google_for_work_signature'];

        }

    } else {

        if ( $data['coord_geocode'] == 'coord_google_developers' && !empty($data['coord_google_developers_api_key']) ) {

            $api_key = '&key=' . $data['coord_google_developers_api_key'];

        } elseif ( $data['coord_geocode'] == 'coord_google_for_work' && !empty($data['coord_google_for_work_client_id']) && !empty($data['coord_google_for_work_signature']) ) {

            $api_key = '&client=' . $data['coord_google_for_work_client_id'] . '&signature=' . $data['coord_google_for_work_signature'];

        }

    }

    // if all fields are updateable and $search has a value
    if ( $houzez_addon->can_update_meta( $field, $import_options ) && $houzez_addon->can_update_meta( 'fave_property_location', $import_options ) && !empty ($search) ) {

        // build $request_url for api call
        $request_url = 'https://maps.googleapis.com/maps/api/geocode/json?' . $search . $api_key;
        $curl = curl_init();

        curl_setopt( $curl, CURLOPT_URL, $request_url );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

        $houzez_addon->log( '- Getting location data from Geocoding API: ' . $request_url );

        $json = curl_exec( $curl );
        curl_close( $curl );

        // parse api response
        if ( !empty($json) ) {

            $details = json_decode( $json, true );

            if ( $data['location_settings'] == 'search_by_address' ) {

                $lat = $details['results'][0]['geometry']['location']['lat'];

                $long = $details['results'][0]['geometry']['location']['lng'];

            } else {

                $address = $details['results'][0]['formatted_address'];

            }

        }

    }

    // update location fields
    $fields = array(
        'fave_property_map_address'  => $address,
        'fave_property_location' => $lat . ',' . $long,
        'houzez_geolocation_lat' => $lat,
        'houzez_geolocation_long' => $long,
    );

    $houzez_addon->log( '- Updating location data' );

    foreach ( $fields as $key => $value ) {

        if ( empty( $article['ID'] ) or $houzez_addon->can_update_meta( $key, $import_options ) ) {

            update_post_meta( $post_id, $key, $value );

        }
    }
    
}


if( ! function_exists('houzez_import_addon_get_page_id_by_title') ) {
  function houzez_import_addon_get_page_id_by_title($page_title, $post_type = 'page') {
      $args = array(
          'post_type' => $post_type,
          'post_status' => 'publish',
          'title' => $page_title,
          'fields' => 'ids', // Only get post IDs
          'posts_per_page' => 1,
      );

      $query = new WP_Query($args);

      if ($query->have_posts()) {
          return $query->posts[0]; // Return the first post ID
      }

      return null;
  }
}