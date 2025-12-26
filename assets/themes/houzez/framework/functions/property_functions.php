<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 07/10/15
 * Time: 11:31 AM
 */

 /**
 * Setup common listing template variables and query
 * 
 * @param string $default_view Default view type (grid or list)
 * @return array Array containing common listing variables
 */
 if( ! function_exists('houzez_setup_listing_template') ) {
    
    function houzez_setup_listing_template($default_view = 'grid') {
        global $post, $listings_tabs, $total_listing_found;
        
        $is_sticky = '';
        $sticky_sidebar = houzez_option('sticky_sidebar');
        if( $sticky_sidebar['property_listings'] != 0 ) { 
            $is_sticky = 'houzez_sticky'; 
        }

        $page_content_position = houzez_get_listing_data('listing_page_content_area');

        $listing_args = array(
            'post_type' => 'property',
            'post_status' => 'publish'
        );

        $listing_args = apply_filters( 'houzez20_property_filter', $listing_args );
        $listing_args = houzez_prop_sort ( $listing_args );

        $listings_query = new WP_Query( $listing_args );
        $total_listing_found = $listings_query->found_posts;
        $fave_prop_no = get_post_meta( $post->ID, 'fave_prop_no', true );
        $fave_prop_no = !empty($fave_prop_no) ? (int)$fave_prop_no : null;

        $current_view = isset($_GET['listing-view']) ? $_GET['listing-view'] : $default_view;
        $view_class = $current_view == 'list' ? 'list-view' : 'grid-view';

        $template_data = array(
            'is_sticky' => $is_sticky,
            'page_content_position' => $page_content_position,
            'listings_query' => $listings_query,
            'total_listing_found' => $total_listing_found,
            'fave_prop_no' => $fave_prop_no,
            'current_view' => $current_view,
            'view_class' => $view_class
        );

        // Allow filtering of final template data
        return apply_filters('houzez_listing_template_data', $template_data);
    }
}

/**
 * Get listing view settings based on the default view option
 *
 * @param string $default_view_option The default view option (e.g., 'grid-view-v1')
 * @return array {
 *     Array of view settings
 *     
 *     @type string $view_type            Either 'grid' or 'list'
 *     @type string $view_version         The view version (e.g., 'view')
 *     @type string $item_version         The item version (e.g., 'v1')
 *     @type string $current_view         The current active view (from URL or default)
 *     @type string $current_item_template The template to use for items
 * }
 */
if (!function_exists('houzez_get_listing_view_settings')) {
     
    function houzez_get_listing_view_settings($default_view_option) {
        // Initialize with default values
        $view_settings = array(
            'view_type'            => 'list',
            'view_version'         => 'view',
            'item_version'         => 'v1',
            'current_view'         => 'list',
            'current_item_template' => 'list-v1'
        );
        
        // Extract view components from the default view setting
        $view_parts = explode('-', $default_view_option);
        
        // Parse the view type (first part)
        if (!empty($view_parts[0])) {
            $view_settings['view_type'] = $view_parts[0]; // 'list' or 'grid'
        }
        
        // Parse the view version (second part)
        $view_settings['view_version'] = isset($view_parts[1]) ? $view_parts[1] : 'view';
        
        // Parse the item version (third part)
        $view_settings['item_version'] = isset($view_parts[2]) ? $view_parts[2] : 'v1';
        
        // Get current view from URL parameter or use default
        $view_from_url = isset($_GET['listing-view']) ? sanitize_text_field($_GET['listing-view']) : '';
        $view_settings['current_view'] = !empty($view_from_url) ? $view_from_url : $view_settings['view_type'];
        
        // Determine the template based on current view
        if ($view_settings['current_view'] == 'list') {
            $view_settings['current_item_template'] = 'list-' . $view_settings['item_version'];
        } else {
            $view_settings['current_item_template'] = $view_settings['item_version'];
        }
        
        // Allow theme/plugins to filter the template
        $view_settings['current_item_template'] = apply_filters(
            'houzez_item_template', 
            $view_settings['current_item_template'], 
            $view_settings['current_view'], 
            $view_settings['item_version']
        );
        
        return $view_settings;
    }
}

/**
 * Generates the CSS classes for the listing view container
 * 
 * Creates the appropriate class string based on the current view,
 * item version, layout, and grid columns configuration.
 * This function handles theme options layout settings for property listings.
 * 
 * @since 1.0.0
 * @param string $current_view  Current view type ('grid' or 'list')
 * @param string $item_version  Item version (e.g., 'v1', 'v2', 'v4')
 * @param string $layout        Layout type ('no-sidebar', 'left-sidebar', 'right-sidebar')
 * @param int    $grid_columns  Number of columns for grid view (2, 3, 4)
 * @return string CSS classes for the listing view container
 */
if (!function_exists('houzez_get_listing_view_class')) {
    function houzez_get_listing_view_class($current_view, $item_version, $layout, $grid_columns) {
        // Base class for all listing views
        $listing_view_class = 'listing-view';
        
        // Special handling for v4 which always uses grid view
        if ($item_version == 'v4') {
            $listing_view_class .= ' grid-view row gy-4 gx-4';
        } else {
            // Handle list view
            if ($current_view == 'list') {
                $listing_view_class .= ' list-view row gy-4 gx-4';
            } 
            // Handle grid view
            else {
                $listing_view_class .= ' grid-view row';
                
                // Apply different column classes based on layout and grid columns setting
                if ($layout == 'no-sidebar') {
                    // 4-column grid is only available for v1, v2, v6
                    if ($grid_columns == 4 && in_array($item_version, array('v1', 'v2', 'v3', 'v5', 'v6', 'v7'))) {
                        $listing_view_class .= ' row-cols-1 row-cols-xl-4 row-cols-lg-3 row-cols-md-2 gy-4 gx-4';
                    } 
                    // 2-column grid
                    elseif ($grid_columns == 2) {
                        $listing_view_class .= ' row-cols-1 row-cols-md-2 gy-4 gx-4';
                    } 
                    // Default 3-column grid
                    else {
                        $listing_view_class .= ' row-cols-lg-3 row-cols-md-2 row-cols-sm-1 gy-4 gx-4';
                    }
                } 
                // With sidebar, always use 2 columns
                else {
                    $listing_view_class .= ' row-cols-1 row-cols-md-2 gy-4 gx-4';
                }
            }
        }
        
        // Allow theme/plugins to filter the classes
        return apply_filters('houzez_listing_view_class', $listing_view_class, $current_view, $layout, $grid_columns);
    }
}

/**
 * Generate HTML for property overview icon
 *
 * @param string $icon_key The icon key (e.g., 'bed', 'bath')
 * @param string $version The version of the overview (v1, v2, v3)
 * @return string The HTML for the icon
 */
if (!function_exists('houzez_get_overview_icon')) {
    function houzez_get_overview_icon($icon_key, $version = '') {
        
        $icon_html = '';
        $img_size = ($version == 'v2') ? '24' : '16';
        $icon_spacing = ($version != 'v2') ? ' me-2' : '';
        
        if (houzez_option('icons_type') == 'font-awesome') {
            $icon_html = '<i class="'.houzez_option('fa_'.$icon_key).$icon_spacing.'" aria-hidden="true"></i>';
        } elseif (houzez_option('icons_type') == 'custom') {
            $cus_icon = houzez_option($icon_key);

            if (!empty($cus_icon['url'])) {
                $alt_title = isset($cus_icon['title']) ? $cus_icon['title'] : '';
                $icon_html = '<img class="img-fluid'.$icon_spacing.'" src="'.esc_url($cus_icon['url']).'" width="'.$img_size.'" height="'.$img_size.'" alt="'.esc_attr($alt_title).'" aria-hidden="true">';
            }
        } else {
            $default_icons = array(
                'bed' => 'icon-hotel-double-bed-1',
                'bath' => 'icon-bathroom-shower-1',
                'garage' => 'icon-car-1',
                'area-size' => 'icon-ruler-triangle',
                'land-area' => 'icon-real-estate-dimensions-map',
                'year-built' => 'icon-calendar-3',
                'property-id' => 'icon-tags',
                'room' => 'icon-architecture-door',
                //'type' => 'icon-house',
            );
            
            $icon_class = isset($default_icons[$icon_key]) ? $default_icons[$icon_key] : '';
            if (!empty($icon_class)) {  
                $icon_html = '<i class="houzez-icon '.$icon_class.$icon_spacing.'" aria-hidden="true"></i>';
            }
        }
        
        return $icon_html;
    }
}


/**
 * Generate HTML for property overview item
 *
 * @param string $key The property key (e.g., 'bed', 'bath')
 * @param string $value The property value
 * @param string $label The property label
 * @param string $version The version of the overview (v1, v2, v3)
 * @return string The HTML for the overview item
 */

if (!function_exists('houzez_get_overview_item')) {
    function houzez_get_overview_item($key, $value, $label, $version = '') {
        $output = '';
        $icon_html = houzez_get_overview_icon($key, $version);
        
        if ($version == 'v2') {
            $output .= '<div class="col" role="listitem">';
            $output .= '<ul class="list-unstyled d-flex align-items-center gap-3">';
            
            if( ! empty($icon_html) ) {
                $output .= '<li class="property-overview-item">'.$icon_html.'</li>';
            }
            $output .= '<li class="property-overview-description h-'.$key.'s">';
            $output .= '<strong>'.esc_attr($value).'</strong><br>';
            $output .= '<span class="hz-meta-label">'.esc_attr($label).'</span>';
            $output .= '</li>';
            
            $output .= '</ul>';
            $output .= '</div>';
        } elseif ($version == 'v3') {
            $output .= '<ul class="list-unstyled flex-fill m-0">';
            
            if ($key === 'type') {
                // Special case for type in v3 - title on top, value on bottom
                $output .= '<li class="property-overview-type hz-meta-label">'.esc_attr($label).'</li>';
                $output .= '<li><strong>'.esc_attr($value).'</strong></li>';
            } else {
                // Normal case for other properties
                $output .= '<li class="property-overview-item">'.$icon_html.'<strong>'.esc_attr($value).'</strong></li>';
                $output .= '<li class="h-'.$key.'s hz-meta-label">'.esc_attr($label).'</li>';
            }
            
            $output .= '</ul>';
        } else {
            // Default/Version 1 structure
            $output .= '<div class="col" role="listitem">';
                $output .= '<ul class="list-unstyled mb-0">';
                    $output .= '<li class="property-overview-item d-flex align-items-center">'.$icon_html.'<strong>'.esc_attr($value).'</strong></li>';
                    $output .= '<li class="h-'.$key.'s hz-meta-label">'.esc_attr($label).'</li>';
                $output .= '</ul>';
            $output .= '</div>';
        }
        
        return $output;
    }
}

/**
 * Display the expiration date for a listing
 *
 * This function checks if a manual expiration date is set for the current post.
 * If a manual date is set, it displays that date. Otherwise, it checks the submission type
 * and displays the appropriate expiration date based on the submission type.
 *
 * @since 1.0.0
 * @return void
 */

if( !function_exists('houzez_listing_expire')) {
    function houzez_listing_expire() {
        global $post;

        //If manual expire date set
        $manual_expire = get_post_meta( $post->ID, 'houzez_manual_expire', true );
        if( !empty( $manual_expire )) {
            $expiration_date = get_post_meta( $post->ID,'_houzez_expiration_date',true );
            echo '<i>'.( $expiration_date ? get_date_from_gmt(gmdate('Y-m-d H:i:s', $expiration_date), get_option('date_format').' '.get_option('time_format')) : __('Never', 'houzez')).'</i>';
        } else {
            $submission_type = houzez_option('enable_paid_submission');
            // Per listing
            if( $submission_type == 'per_listing' || $submission_type == 'free_paid_listing' || $submission_type == 'no' ) {
                $per_listing_expire_unlimited = houzez_option('per_listing_expire_unlimited');
                if( $per_listing_expire_unlimited != 0 ) {
                    $per_listing_expire = houzez_option('per_listing_expire');

                    $publish_date = $post->post_date;
                    echo '<i>'.date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $publish_date. ' + '.$per_listing_expire.' days' ) ).'</i>';
                }
            } elseif( $submission_type == 'membership' ) {
                $post_author = get_post_field( 'post_author', $post->ID );
                $agent_agency_id = houzez_get_agent_agency_id( $post_author );
                if( $agent_agency_id ) {
                    $post_author = $agent_agency_id;
                }

                $package_id = get_user_meta( $post_author, 'package_id', true );

                if( !empty($package_id) ) {
                    $billing_time_unit = get_post_meta( $package_id, 'fave_billing_time_unit', true );
                    $billing_unit = get_post_meta( $package_id, 'fave_billing_unit', true );

                    if( $billing_time_unit == 'Day')
                        $billing_time_unit = 'days';
                    elseif( $billing_time_unit == 'Week')
                        $billing_time_unit = 'weeks';
                    elseif( $billing_time_unit == 'Month')
                        $billing_time_unit = 'months';
                    elseif( $billing_time_unit == 'Year')
                        $billing_time_unit = 'years';

                    $pack_date =  get_user_meta( $post_author, 'package_activation',true );
                    $expired_date = strtotime($pack_date. ' + '.$billing_unit.' '.$billing_time_unit);
                    echo '<i>'.date_i18n( get_option('date_format').' '.get_option('time_format'),  $expired_date ).'</i>';
                }
            }
        }
    }
}

/**
 * Display the featured listing expiration date
 *
 * This function checks if the current listing is a featured listing and if it has an expiration date.
 * If the listing is featured and has an expiration date, it displays the expiration date.
 *
 * @since 1.0.0
 * @return void
 */

if( !function_exists('houzez_featured_listing_expire')) {
    function houzez_featured_listing_expire() {
        global $post;

        $submission_type = houzez_option('enable_paid_submission');
        $prop_featured_date = get_post_meta( $post->ID, 'houzez_featured_listing_date', true );
        // Per listing
        if( ( $submission_type == 'free_paid_listing' || $submission_type == 'no' ) && ( $prop_featured_date != '' ) ) {
            
            $featured_listing_expire = intval ( fave_option('featured_listing_expire', 30) );

            echo '<br><strong>'.esc_html__('Featured Expiration:', 'houzez').'</strong> <i>'.date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $prop_featured_date. ' + '.$featured_listing_expire.' days' ) ).'</i>';
            
        }
        
    }
}

/**
 * Get the dimensions of an image size
 *
 * This function returns the dimensions of an image size based on its name.
 * It defines a set of predefined image sizes and their corresponding dimensions.
 *
 * @since 1.0.0
 * @param string $size_name The name of the image size
 * @return array The dimensions of the image size
 */

if( ! function_exists('houzez_get_image_size_dimensions') ) {
    function houzez_get_image_size_dimensions($size_name) {
        // Define image sizes
        $image_sizes = array(
            'houzez-item-image-1' => array('width' => 592, 'height' => 444),
            'houzez-item-image-4' => array('width' => 758, 'height' => 564),
            'houzez-item-image-6' => array('width' => 584, 'height' => 438),
        );

        // Return the width and height if size exists, otherwise return default dimensions
        return isset($image_sizes[$size_name]) ? $image_sizes[$size_name] : array('width' => 592, 'height' => 444);
    }
}

/**
 * Get the property gallery for a listing
 *
 * This function retrieves the property gallery images for a listing based on the specified image size.
 * It checks if the property gallery is enabled and if the current post has a featured image or gallery images.
 * 
 * @since 2.0.0
 * @param string $size The size of the image to retrieve
 * @return string The property gallery HTML
 */

if( !function_exists('houzez_get_property_gallery') ) {
    function houzez_get_property_gallery($size = 'thumbnail') {
        global $post;

        if ( ! houzez_option('disable_property_gallery', 1) ) {
            return;
        }

        $gallery_images_limit = houzez_option('gallery_images_limit', 10);

        $i = 0;

        $default_size = houzez_get_image_size_dimensions($size);

        if($size == 'full') {
            $default_size['width'] = 1170;
            $default_size['height'] = 780;
        }

        $gallery_ids = get_post_meta( $post->ID, 'fave_property_images', false );

        if ( has_post_thumbnail() || $gallery_ids ) {
            $images = [];
            
            if ( has_post_thumbnail() && houzez_option('featured_img_in_gallery', 0) != 1 ) {
                $thumb_id = get_post_thumbnail_id($post);
                $thumb_meta = wp_get_attachment_metadata($thumb_id);
                $temp_array = []; // fresh array for the featured image
                $temp_array['image'] = get_the_post_thumbnail_url($post, $size);
                $temp_array['alt'] = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );

                if ($thumb_meta) {
                    $temp_array['width'] = isset($thumb_meta['sizes'][$size]['width']) ? $thumb_meta['sizes'][$size]['width'] : $default_size['width'];
                    $temp_array['height'] = isset($thumb_meta['sizes'][$size]['height']) ? $thumb_meta['sizes'][$size]['height'] : $default_size['height'];
                } else {
                    $temp_array['width'] = $default_size['width'];
                    $temp_array['height'] = $default_size['height'];
                }

                // Add srcset and sizes for featured image
                $temp_array['srcset'] = wp_get_attachment_image_srcset($thumb_id, $size, $thumb_meta);
                $temp_array['sizes'] = wp_get_attachment_image_sizes($thumb_id, $size, $thumb_meta);

                $images[] = $temp_array;
            }

            if ( empty($gallery_ids) ) {
                return;
            }

            foreach ( $gallery_ids as $id ) { 
                // Skip if attachment doesn't exist
                if ( ! get_post_status( $id ) ) {
                    continue;
                }
                
                $i++;
                $temp_array = []; // Reinitialize here for each gallery image
                $img = wp_get_attachment_image_url($id, $size);
                $img_meta = wp_get_attachment_metadata($id);
                $alt_text = get_post_meta( $id, '_wp_attachment_image_alt', true );

                if ($i == $gallery_images_limit && $gallery_images_limit != -1) {
                    break;
                }
                
                if ( $img ) {
                    $temp_array['image'] = $img;
                    $temp_array['alt'] = $alt_text;
                    
                    if ($img_meta) {
                        $temp_array['width'] = isset($img_meta['sizes'][$size]['width']) ? $img_meta['sizes'][$size]['width'] : $default_size['width'];
                        $temp_array['height'] = isset($img_meta['sizes'][$size]['height']) ? $img_meta['sizes'][$size]['height'] : $default_size['height'];
                    } else {
                        $temp_array['width'] = $default_size['width'];
                        $temp_array['height'] = $default_size['height'];
                    }

                    // Add srcset and sizes for gallery images
                    $temp_array['srcset'] = wp_get_attachment_image_srcset($id, $size, $img_meta);
                    $temp_array['sizes'] = wp_get_attachment_image_sizes($id, $size, $img_meta);
                    
                    $images[] = $temp_array;
                }
            }

            return 'data-images="' . esc_attr(json_encode($images)) . '"';
        }
    }
}

/**
 * Display the property gallery for a listing
 *
 * This function echoes the property gallery HTML for a listing based on the specified image size.
 * It uses the houzez_get_property_gallery function to get the gallery HTML and then echoes it.
 * 
 * @since 2.0.0
 * @param string $size The size of the image to retrieve
 * @return void
 */
if( !function_exists('houzez_property_gallery') ) {
    function houzez_property_gallery($size = 'thumbnail') {
       echo houzez_get_property_gallery($size);
    }
}

/**
 * Get the gallery class for a listing
 *
 * This function returns the gallery class for a listing based on the property gallery setting.
 * It checks if the property gallery is disabled and returns the appropriate class.
 *
 * @since 2.0.0 
 * @return string The gallery class
 */ 
if( !function_exists('houzez_get_gallery_class') ) {
    function houzez_get_gallery_class() {
        global $post;

        $class = '';
        if ( houzez_option('disable_property_gallery', 1) ) {
            $class = "hz-item-gallery-js";
        }

        
    }
}

/**
 * Display the gallery class for a listing
 *
 * This function echoes the gallery class for a listing based on the property gallery setting.
 * It uses the houzez_get_gallery_class function to get the class and then echoes it.
 *
 * @since 2.0.0
 * @return void
 */
if( !function_exists('houzez_property_gallery_class') ) {
    function houzez_property_gallery_class() {
       echo houzez_get_gallery_class();
    }
}

/*-----------------------------------------------------------------------------------*/
// Submit Property filter - deprecated use class Houzez_Submit_Listing instead
/*-----------------------------------------------------------------------------------*/
/**
 * Submit a new property listing
 *
 * This function is used to submit a new property listing.
 * It is deprecated and should be replaced with the Houzez_Submit_Listing class.
 * 
 * @since 1.0.0
 * @param array $new_property The new property data
 * @return void
 */
if( !function_exists('houzez_submit_listing') ) {
    function houzez_submit_listing($new_property) {}
}

/**
 * Update a property from a draft
 *
 * This function updates a property from a draft.
 * It sets the property status to publish if the listings are admin approved.
 * 
 * @since 1.0.0
 * @param int $property_id The ID of the property to update
 * @return void
 */
if( !function_exists('houzez_update_property_from_draft') ) {
    function houzez_update_property_from_draft( $property_id ) {
        $listings_admin_approved = houzez_option('listings_admin_approved');

        if( $listings_admin_approved != 'yes' ) {
            $prop_status = 'publish';
        } else {
            $prop_status = 'pending';
        }

        $updated_property = array(
            'ID' => $property_id,
            'post_type' => 'property',
            'post_status' => $prop_status
        );
        $prop_id = wp_update_post( $updated_property );
    }
}

add_action('wp_ajax_houzez_relist_free', 'houzez_relist_free');
if( !function_exists('houzez_relist_free') ) {
    function houzez_relist_free() {
        $listings_admin_approved = houzez_option('listings_admin_approved');

        if( $listings_admin_approved != 'yes' ) {
            $prop_status = 'publish';
        } else {
            $prop_status = 'pending';
        }
        
        $propID = $_POST['propID'];
        $updated_property = array(
            'ID' => $propID,
            'post_type' => 'property',
            'post_status' => $prop_status,
            'post_date'     => current_time( 'mysql' ),
        );
        $post_id = wp_update_post( $updated_property );
    }
}

/*-----------------------------------------------------------------------------------*/
// Save as draft - deprecated see class class-houzez-submit-property.php instead
/*-----------------------------------------------------------------------------------*/
if( !function_exists('save_property_as_draft') ) {
    function save_property_as_draft() {}
}

/*-----------------------------------------------------------------------------------*/
// validate Email
/*-----------------------------------------------------------------------------------*/
add_action('wp_ajax_houzez_check_email', 'houzez_check_email');
add_action('wp_ajax_nopriv_houzez_check_email', 'houzez_check_email');

if( !function_exists('houzez_check_email') ) {
    function houzez_check_email() {
        $allowed_html = array();
        $email = wp_kses( $_POST['useremail'], $allowed_html );

        if( email_exists( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This email address is already registered.', 'houzez') ) );
            wp_die();
        
        } elseif( !is_email( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid email address.', 'houzez') ) );
            wp_die();
        } else {
            echo json_encode( array( 'success' => true, 'msg' => esc_html__('Successfull', 'houzez') ) );
            wp_die();
        }

        wp_die();
    }
}

/*-----------------------------------------------------------------------------------*/
// Add custom post status Expired
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists('houzez_custom_post_status') ) {
    function houzez_custom_post_status() {

        $args = array(
            'label'                     => _x( 'Expired', 'Status General Name', 'houzez' ),
            'label_count'               => _n_noop( 'Expired (%s)',  'Expired (%s)', 'houzez' ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'expired', $args );

    }
    add_action( 'init', 'houzez_custom_post_status', 1 );
}

/*-----------------------------------------------------------------------------------*/
// Add custom post status DisApproved
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists('houzez_custom_post_disapproved') ) {
    function houzez_custom_post_disapproved() {

        $args = array(
            'label'                     => _x( 'Disapproved', 'Status General Name', 'houzez' ),
            'label_count'               => _n_noop( 'Disapproved (%s)',  'Disapproved (%s)', 'houzez' ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'disapproved', $args );

    }
    add_action( 'init', 'houzez_custom_post_disapproved', 1 );
}

/*-----------------------------------------------------------------------------------*/
// Add custom post status Hold
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists('houzez_custom_post_status_on_hold') ) {
    function houzez_custom_post_status_on_hold() {

        $args = array(
            'label'                     => _x( 'On Hold', 'Status General Name', 'houzez' ),
            'label_count'               => _n_noop( 'On Hold (%s)',  'On Hold (%s)', 'houzez' ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'on_hold', $args );

    }
    add_action( 'init', 'houzez_custom_post_status_on_hold', 1 );
}

/*-----------------------------------------------------------------------------------*/
// Add custom post status Sold
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists('houzez_custom_post_status_sold') ) {
    function houzez_custom_post_status_sold() {

        if( ! fave_option('enable_mark_as_sold', 0) ) {
            return;
        }
        $args = array(
            'label'                     => _x( 'Sold', 'Status General Name', 'houzez' ),
            'label_count'               => _n_noop( 'Sold (%s)',  'Sold (%s)', 'houzez' ),
            'public'                    => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => false,
        );
        register_post_status( 'houzez_sold', $args );

    }

    add_action( 'init', 'houzez_custom_post_status_sold', 1 );
    add_action( 'admin_init', 'houzez_custom_post_status_sold', 1 );
}

add_action( 'wp_ajax_houzez_save_search', 'houzez_save_search' );
if ( ! function_exists( 'houzez_save_search' ) ) {
    function houzez_save_search() {
        $data   = $_REQUEST; // You may also use $_POST if you prefer.
        $result = houzez_process_save_search( $data, true );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'msg' => $result->get_error_message() ) );
        } else {
            wp_send_json_success( $result );
        }
    }
}


/**
 * Process save search request.
 *
 * @param array $data Form or request data.
 * @param bool  $validate_nonce Whether to validate the nonce (set to false for REST API requests if using another authentication mechanism).
 * @return array|WP_Error Array on success or WP_Error on failure.
 */
if( !function_exists('houzez_process_save_search') ) {
    function houzez_process_save_search( $data, $validate_nonce = true ) {
        // Validate nonce if required.
        if ( $validate_nonce ) {
            if ( empty( $data['houzez_save_search_ajax'] ) || ! wp_verify_nonce( $data['houzez_save_search_ajax'], 'houzez-save-search-nounce' ) ) {
                return new WP_Error( 'invalid_nonce', esc_html__( 'Unverified Nonce!', 'houzez' ) );
            }
        }

        // Ensure user is logged in.
        $userID = get_current_user_id();
        if ( ! $userID ) {
            return new WP_Error( 'not_logged_in', esc_html__( 'User not logged in.', 'houzez' ) );
        }

        $user = get_userdata( $userID );
        $userEmail = ( $user && ! empty( $user->user_email ) ) ? sanitize_email( $user->user_email ) : '';

        // Sanitize incoming data.
        $search_args = isset( $data['search_args'] ) ? sanitize_text_field( $data['search_args'] ) : '';
        $request_url = isset( $data['search_URI'] ) ? esc_url_raw( $data['search_URI'] ) : '';

        global $wpdb;
        $table_name = $wpdb->prefix . 'houzez_search';

        // Insert search data into the database.
        $result = $wpdb->insert(
            $table_name,
            array(
                'auther_id' => $userID, // Note: Table column name 'auther_id' assumed to be intentional.
                'query'     => $search_args,
                'email'     => $userEmail,
                'url'       => $request_url,
                'time'      => current_time( 'mysql' ),
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );

        if ( false === $result ) {
            return new WP_Error( 'db_insert_failed', esc_html__( 'Failed to save search.', 'houzez' ) );
        }

        return array(
            'success' => true,
            'msg'     => esc_html__( 'Search is saved. You will receive an email notification when new properties matching your search will be published', 'houzez' ),
        );
    }
}

/*-----------------------------------------------------------------------------------*/
/*     Remove Search
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_delete_search', 'houzez_delete_search' );
if ( ! function_exists( 'houzez_delete_search' ) ) {
    function houzez_delete_search() {
        $data   = $_POST;
        $result = houzez_process_delete_search( $data );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'msg' => $result->get_error_message() ) );
        } else {
            wp_send_json_success( $result );
        }
    }
}


/**
 * Process delete search request.
 *
 * @param array $data The request data.
 * @return array|WP_Error Returns an array with success message or WP_Error on failure.
 */
if( !function_exists('houzez_process_delete_search') ) {
    function houzez_process_delete_search( $data ) {
        // Get current user ID; if not logged in, return error.
        $userID = get_current_user_id();
        if ( ! $userID ) {
            return new WP_Error( 'not_logged_in', esc_html__( 'User not logged in.', 'houzez' ) );
        }

        // Retrieve and sanitize search ID.
        $search_id = isset( $data['property_id'] ) ? intval( $data['property_id'] ) : 0;
        if ( $search_id === 0 ) {
            return new WP_Error( 'invalid_search_id', esc_html__( "You don't have the right to delete this", 'houzez' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'houzez_search';

        // Retrieve the search row.
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $search_id ) );
        if ( ! $row ) {
            return new WP_Error( 'not_found', esc_html__( 'Search not found', 'houzez' ) );
        }

        // Check whether the current user is the owner of the search.
        if ( intval( $row->auther_id ) !== $userID ) {
            return new WP_Error( 'permission_error', esc_html__( "You don't have the right to delete this", 'houzez' ) );
        }

        // Delete the search.
        $delete = $wpdb->delete( $table_name, array( 'id' => $search_id ), array( '%d' ) );
        if ( false === $delete ) {
            return new WP_Error( 'delete_failed', esc_html__( 'Failed to delete search, please try again.', 'houzez' ) );
        }

        return array(
            'success' => true,
            'msg'     => esc_html__( 'Deleted Successfully', 'houzez' )
        );
    }
}

/* -----------------------------------------------------------------------------------------------------------
 *  Resend Property for Approval per listing
 -------------------------------------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_houzez_resend_for_approval_perlisting', 'houzez_resend_for_approval_perlisting' );
add_action( 'wp_ajax_houzez_resend_for_approval_perlisting', 'houzez_resend_for_approval_perlisting' );

if( !function_exists('houzez_resend_for_approval_perlisting') ):

    function houzez_resend_for_approval_perlisting() {

        global $current_user;
        $prop_id = intval($_POST['propid']);

        wp_get_current_user();
        $userID = $current_user->ID;
        $post   = get_post($prop_id);

        if( $post->post_author != $userID){
            wp_die('get out of my cloud');
        }

        $time = current_time('mysql');
        $prop = array(
            'ID'            => $prop_id,
            'post_type'     => 'property',
            'post_status'   => 'pending',
            'post_date'     => current_time( 'mysql' ),
            'post_date_gmt' => get_gmt_from_date( $time )
        );
        wp_update_post( $prop );
        update_post_meta( $prop_id, 'fave_featured', 0 );
        update_post_meta( $prop_id, 'houzez_featured_listing_date', '' );
        update_post_meta( $prop_id, 'fave_payment_status', 'not_paid' );

        echo json_encode( array( 'success' => true, 'msg' => esc_html__('Sent for approval','houzez') ) );

        $submit_title =   get_the_title( $prop_id) ;

        $args = array(
            'submission_title' =>  $submit_title,
            'submission_url'   =>  get_permalink( $prop_id )
        );
        houzez_email_type( get_option('admin_email'), 'admin_expired_listings', $args );

        wp_die();



    }

endif; // end

/*-----------------------------------------------------------------------------------*/
// Houzez sold status 
/*-----------------------------------------------------------------------------------*/

add_filter('houzez_sold_status_filter', 'houzez_sold_status_filter_callback');
if( !function_exists('houzez_sold_status_filter_callback') ) {
    function houzez_sold_status_filter_callback( $query_args ) {
        if( houzez_option('show_sold_listings', 1) ) {
            $query_args['post_status'] = array('publish', 'houzez_sold');
        }

        return $query_args;
    }
}

// AJAX action registration.
add_action( 'wp_ajax_houzez_add_to_favorite', 'houzez_favorites' );
if ( ! function_exists( 'houzez_favorites' ) ) {
    function houzez_favorites() {
        // Optionally verify a nonce for security.
        // check_ajax_referer( 'houzez_favorites_nonce', 'security' );

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( array( 'message' => esc_html__( 'User not logged in.', 'houzez' ) ) );
        }

        $property_id = isset( $_POST['listing_id'] ) ? intval( $_POST['listing_id'] ) : 0;
        if ( ! $property_id ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid property ID.', 'houzez' ) ) );
        }

        $result = houzez_process_favorites( $user_id, $property_id );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }
        wp_send_json_success( $result );
    }
}


/**
 * Toggle property in user's favorites.
 *
 * @param int $user_id Current user ID.
 * @param int $property_id Property ID.
 * @return array|WP_Error Returns array with the result or a WP_Error on failure.
 */
if( ! function_exists('houzez_process_favorites') ) {
    function houzez_process_favorites( $user_id, $property_id ) {
        $property_id = intval( $property_id );
        if ( ! $property_id ) {
            return new WP_Error( 'invalid_property', esc_html__( 'Invalid property ID.', 'houzez' ) );
        }

        // Retrieve current favorites from user meta.
        $favorites = get_user_meta( $user_id, 'houzez_favorites', true );
        if ( empty( $favorites ) || ! is_array( $favorites ) ) {
            $favorites = array();
        }

        // Toggle property in favorites.
        if ( ! in_array( $property_id, $favorites, true ) ) {
            $favorites[] = $property_id;
            $message   = esc_html__( 'Added', 'houzez' );
            $added     = true;
        } else {
            $key = array_search( $property_id, $favorites, true );
            if ( false !== $key ) {
                unset( $favorites[ $key ] );
                // Re-index the array.
                $favorites = array_values( $favorites );
            }
            $message = esc_html__( 'Removed', 'houzez' );
            $added   = false;
        }

        // Save updated favorites.
        update_user_meta( $user_id, 'houzez_favorites', $favorites );

        // Trigger any additional actions.
        do_action( 'houzez_track_favorites', $user_id, $property_id );

        return array( 'added' => $added, 'response' => $message );
    }
}


/**
 * Properties sorting functionality
 *
 * Handles the sorting of property listings based on various criteria.
 *
 * @param array  $query_args   The existing WP_Query arguments to modify.
 * @param string $default_sort Default sort option if none is specified.
 * @return array Modified query arguments with sorting parameters.
 * @since 1.0.0
 */
if( !function_exists('houzez_prop_sort') ) {
    function houzez_prop_sort($query_args, $default_sort = '') {
        // Define allowed sort options
        $allowed_sort_options = array(
            'a_title', 'd_title',
            'a_price', 'd_price',
            'a_date', 'd_date',
            'featured', 'featured_random',
            'featured_first', 'featured_first_random',
            'featured_top',
            'random'
        );
        
        /**
         * Filter the allowed sorting options
         *
         * @param array $allowed_sort_options Array of allowed sort options
         * @return array
         */
        $allowed_sort_options = apply_filters('houzez_allowed_sort_options', $allowed_sort_options);
        
        // Get the sort option from various sources
        $sort_by = '';
        
        // If default provided, use it as initial value
        if (!empty($default_sort)) {
            $sort_by = $default_sort;
        }
        
        /**
         * Filter the default sort option
         *
         * @param string $sort_by The current sort option
         * @param array $query_args The current query arguments
         * @return string
         */
        $sort_by = apply_filters('houzez_default_sort_option', $sort_by, $query_args);
        
        // Check for GET parameter, sanitize the input
        if (isset($_GET['sortby'])) {
            $sort_by = sanitize_text_field($_GET['sortby']);
        } 
        // If no GET parameter but we're on specific pages, get the default from theme options
        else if (houzez_is_listings_template()) {
            $sort_by = get_post_meta(get_the_ID(), 'fave_properties_sort', true);
        } else if (is_page_template(array('template/template-search.php'))) {
            $sort_by = houzez_option('search_default_order');
        } else if (is_tax()) {
            $sort_by = houzez_option('taxonomy_default_order');
        } else if (is_singular('houzez_agent')) {
            $sort_by = houzez_option('agent_listings_order');
        } else if (is_singular('houzez_agency')) {
            $sort_by = houzez_option('agency_listings_order');
        }
        
        /**
         * Filter the sort option before processing
         *
         * @param string $sort_by The current sort option
         * @param array $query_args The current query arguments
         * @return string
         */
        $sort_by = apply_filters('houzez_pre_sort_option', $sort_by, $query_args);
        
        // Validate sort option
        if (!in_array($sort_by, $allowed_sort_options, true)) {
            // Use a safe default if invalid option provided
            $sort_by = 'd_date'; // Default to newest first
        }
        
        /**
         * Action before applying sort parameters
         *
         * @param string $sort_by The current sort option
         * @param array $query_args The current query arguments
         */
        do_action('houzez_before_apply_sort', $sort_by, $query_args);
        
        // Apply sorting parameters based on sort option
        switch ($sort_by) {
            case 'a_title':
                $query_args['orderby'] = 'title';
                $query_args['order'] = 'ASC';
                break;
                
            case 'd_title':
                $query_args['orderby'] = 'title';
                $query_args['order'] = 'DESC';
                break;
                
            case 'a_price':
                $query_args['orderby'] = 'meta_value_num';
                $query_args['meta_key'] = 'fave_property_price';
                $query_args['order'] = 'ASC';
                break;
                
            case 'd_price':
                $query_args['orderby'] = 'meta_value_num';
                $query_args['meta_key'] = 'fave_property_price';
                $query_args['order'] = 'DESC';
                break;
                
            case 'featured':
                $query_args['meta_key'] = 'fave_featured';
                $query_args['meta_value'] = '1';
                $query_args['orderby'] = 'meta_value date';
                break;
                
            case 'featured_random':
                $query_args['meta_key'] = 'fave_featured';
                $query_args['meta_value'] = '1';
                $query_args['orderby'] = 'meta_value DESC rand';
                break;
                
            case 'a_date':
                $query_args['orderby'] = 'date';
                $query_args['order'] = 'ASC';
                break;
                
            case 'd_date':
                $query_args['orderby'] = 'date';
                $query_args['order'] = 'DESC';
                break;
                
            case 'featured_first':
            case 'featured_top': // These do the same thing
                $query_args['orderby'] = 'meta_value date';
                $query_args['meta_key'] = 'fave_featured';
                break;
                
            case 'featured_first_random':
                $query_args['meta_key'] = 'fave_featured';
                $query_args['orderby'] = 'meta_value DESC rand';
                break;
                
            case 'random':
                $query_args['orderby'] = 'rand';
                $query_args['order'] = 'DESC';
                break;
                
            default:
                /**
                 * Filter for custom sort options
                 *
                 * @param array $query_args The current query arguments
                 * @param string $sort_by The custom sort option
                 * @return array
                 */
                $query_args = apply_filters('houzez_custom_sort_options', $query_args, $sort_by);
                break;
        }
        
        /**
         * Action after applying sort parameters
         *
         * @param string $sort_by The current sort option
         * @param array $query_args The modified query arguments
         */
        do_action('houzez_after_apply_sort', $sort_by, $query_args);
        
        /**
         * Filter the final query arguments for property sorting
         * 
         * @param array $query_args The modified query arguments
         * @param string $sort_by The current sort option
         * @return array
         */
        return apply_filters('houzez_sort_properties', $query_args, $sort_by);
    }
}


/*-----------------------------------------------------------------------------------*/
// Remove property attachments
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_remove_property_thumbnail', 'houzez_remove_property_thumbnail' );
add_action( 'wp_ajax_nopriv_houzez_remove_property_thumbnail', 'houzez_remove_property_thumbnail' );
if( !function_exists('houzez_remove_property_thumbnail') ) {
    function houzez_remove_property_thumbnail() {

        $nonce = $_POST['removeNonce'];
        $remove_attachment = false;
        if (!wp_verify_nonce($nonce, 'verify_gallery_nonce')) {

            echo json_encode(array(
                'remove_attachment' => false,
                'reason' => esc_html__('Invalid Nonce', 'houzez')
            ));
            wp_die();
        }

        if (isset($_POST['thumb_id']) && isset($_POST['prop_id'])) {
            $thumb_id = intval($_POST['thumb_id']);
            $prop_id = intval($_POST['prop_id']);

            $property_status = get_post_status ( $prop_id );

            if ( $thumb_id > 0 && $prop_id > 0 && $property_status != "draft" ) {
                delete_post_meta($prop_id, 'fave_property_images', $thumb_id);
                $remove_attachment = wp_delete_attachment($thumb_id);
            } elseif ( $thumb_id > 0 && $prop_id > 0 && $property_status == "draft" ) {
                delete_post_meta($prop_id, 'fave_property_images', $thumb_id);
                $remove_attachment = true;
            } elseif ($thumb_id > 0) {
                if( false == wp_delete_attachment( $thumb_id )) {
                    $remove_attachment = false;
                } else {
                    $remove_attachment = true;
                }
            }
        }

        echo json_encode(array(
            'remove_attachment' => $remove_attachment,
        ));
        wp_die();

    }
}

/*-----------------------------------------------------------------------------------*/
// Remove property attachments
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_remove_property_documents', 'houzez_remove_property_documents' );
add_action( 'wp_ajax_nopriv_houzez_remove_property_documents', 'houzez_remove_property_documents' );
if( !function_exists('houzez_remove_property_documents') ) {
    function houzez_remove_property_documents() {

        $nonce = $_POST['removeNonce'];
        $remove_attachment = false;
        if (!wp_verify_nonce($nonce, 'verify_gallery_nonce')) {

            echo json_encode(array(
                'remove_attachment' => false,
                'reason' => esc_html__('Invalid Nonce', 'houzez')
            ));
            wp_die();
        }

        if (isset($_POST['thumb_id']) && isset($_POST['prop_id'])) {
            $thumb_id = intval($_POST['thumb_id']);
            $prop_id = intval($_POST['prop_id']);

            $property_status = get_post_status ( $prop_id );

            if ( $thumb_id > 0 && $prop_id > 0 && $property_status != "draft" ) {
                delete_post_meta($prop_id, 'fave_attachments', $thumb_id);
                $remove_attachment = wp_delete_attachment($thumb_id);
            } elseif ( $thumb_id > 0 && $prop_id > 0 && $property_status == "draft" ) {
                delete_post_meta($prop_id, 'fave_attachments', $thumb_id);
                $remove_attachment = true;
            } elseif ($thumb_id > 0) {
                if( false == wp_delete_attachment( $thumb_id )) {
                    $remove_attachment = false;
                } else {
                    $remove_attachment = true;
                }
            }
        }

        echo json_encode(array(
            'remove_attachment' => $remove_attachment,
        ));
        wp_die();

    }
}

/*-----------------------------------------------------------------------------------*/
/*   Upload property gallery images
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_property_img_upload', 'houzez_property_img_upload' );    // only for logged in user
add_action( 'wp_ajax_nopriv_houzez_property_img_upload', 'houzez_property_img_upload' );
if( !function_exists( 'houzez_property_img_upload' ) ) {
    function houzez_property_img_upload( ) {

        // Check security Nonce
        $verify_nonce = $_REQUEST['verify_nonce'];
        if ( ! wp_verify_nonce( $verify_nonce, 'verify_gallery_nonce' ) ) {
            echo json_encode( array( 'success' => false , 'reason' => 'Invalid nonce!' ) );
            die;
        }

        $submitted_file = $_FILES['property_upload_file'];

        // Security: Validate file type before processing
        $allowed_image_types = array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        );

        $file_type_check = wp_check_filetype($submitted_file['name'], $allowed_image_types);

        if (!$file_type_check['ext'] || !$file_type_check['type']) {
            echo json_encode(array('success' => false, 'reason' => 'Invalid file type. Only JPG, PNG, GIF and WebP images are allowed.'));
            die;
        }

        // Additional security: Verify file content matches extension
        if ($submitted_file['tmp_name'] && file_exists($submitted_file['tmp_name'])) {
            $image_info = @getimagesize($submitted_file['tmp_name']);
            if (!$image_info) {
                echo json_encode(array('success' => false, 'reason' => 'Invalid image file.'));
                die;
            }

            // Verify MIME type from actual file content
            $allowed_mime_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
            if (!in_array($image_info['mime'], $allowed_mime_types)) {
                echo json_encode(array('success' => false, 'reason' => 'File content does not match allowed image types.'));
                die;
            }
        }

        $uploaded_image = wp_handle_upload( $submitted_file, array(
            'test_form' => false,
            'mimes' => $allowed_image_types
        ) );

        if ( isset( $uploaded_image['file'] ) ) {
            $file_name          =   basename( $submitted_file['name'] );
            $file_type          =   wp_check_filetype( $uploaded_image['file'] );

            // Prepare an array of post data for the attachment.
            $attachment_details = array(
                'guid'           => $uploaded_image['url'],
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id      =   wp_insert_attachment( $attachment_details, $uploaded_image['file'] );
            $attach_data    =   wp_generate_attachment_metadata( $attach_id, $uploaded_image['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            $user_id = get_current_user_id();
            $watermark_image_url = get_user_meta($user_id, 'fave_watermark_image', true);

            $thumbnail_url = wp_get_attachment_image_src( $attach_id, 'houzez-item-image-6' );

            $feat_image_url = wp_get_attachment_url( $attach_id );

            $ajax_response = array(
                'success'   => true,
                'url' => $thumbnail_url[0],
                'attachment_id'    => $attach_id,
                'full_image'    => $feat_image_url
            );

            echo json_encode( $ajax_response );
            die;

        } else {
            $ajax_response = array( 'success' => false, 'reason' => 'Image upload failed!' );
            echo json_encode( $ajax_response );
            die;
        }

    }
}

// Utility function to convert URL to local file path
function houzez_get_local_path($url) {
    $parsed_url = parse_url($url);
    $path = $_SERVER['DOCUMENT_ROOT'] . $parsed_url['path'];
    return $path;
}

/*-----------------------------------------------------------------------------------*/
/*   Upload property gallery images
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_property_attachment_upload', 'houzez_property_attachment_upload' );    // only for logged in user
add_action( 'wp_ajax_nopriv_houzez_property_attachment_upload', 'houzez_property_attachment_upload' );
if( !function_exists( 'houzez_property_attachment_upload' ) ) {
    function houzez_property_attachment_upload( ) {

        // Check security Nonce
        $verify_nonce = $_REQUEST['verify_nonce'];
        if ( ! wp_verify_nonce( $verify_nonce, 'verify_gallery_nonce' ) ) {
            echo json_encode( array( 'success' => false , 'reason' => 'Invalid nonce!' ) );
            die;
        }

        $submitted_file = $_FILES['property_attachment_file'];

        // Security: Validate file type before processing
        $allowed_attachment_types = array(
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        );

        $file_type_check = wp_check_filetype($submitted_file['name'], $allowed_attachment_types);

        if (!$file_type_check['ext'] || !$file_type_check['type']) {
            echo json_encode(array('success' => false, 'reason' => 'Invalid file type. Only PDF, DOC, DOCX and image files are allowed.'));
            die;
        }

        // Additional security for image attachments: Verify file content matches extension
        $image_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (in_array(strtolower($file_type_check['ext']), $image_extensions)) {
            if ($submitted_file['tmp_name'] && file_exists($submitted_file['tmp_name'])) {
                $image_info = @getimagesize($submitted_file['tmp_name']);
                if (!$image_info) {
                    echo json_encode(array('success' => false, 'reason' => 'Invalid image file.'));
                    die;
                }

                // Verify MIME type from actual file content
                $allowed_image_mimes = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
                if (!in_array($image_info['mime'], $allowed_image_mimes)) {
                    echo json_encode(array('success' => false, 'reason' => 'File content does not match allowed image types.'));
                    die;
                }
            }
        }

        $uploaded_image = wp_handle_upload( $submitted_file, array(
            'test_form' => false,
            'mimes' => $allowed_attachment_types
        ) );

        if ( isset( $uploaded_image['file'] ) ) {
            $file_name          =   basename( $submitted_file['name'] );
            $file_type          =   wp_check_filetype( $uploaded_image['file'] );

            // Prepare an array of post data for the attachment.
            $attachment_details = array(
                'guid'           => $uploaded_image['url'],
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id      =   wp_insert_attachment( $attachment_details, $uploaded_image['file'] );
            $attach_data    =   wp_generate_attachment_metadata( $attach_id, $uploaded_image['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            $attachment_title = get_the_title($attach_id);
            $attachment_url = wp_get_attachment_url( $attach_id );

            $ajax_response = array(
                'success'   => true,
                'url' => $attachment_url,
                'attachment_id'    => $attach_id,
                'attach_title'    => $attachment_title,
            );

            echo json_encode( $ajax_response );
            die;

        } else {
            $ajax_response = array( 'success' => false, 'reason' => 'File upload failed!' );
            echo json_encode( $ajax_response );
            die;
        }

    }
}


/*-----------------------------------------------------------------------------------*/
/*  Houzez Print Property
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_houzez_create_print', 'houzez_create_print' );
add_action( 'wp_ajax_houzez_create_print', 'houzez_create_print' );

if( !function_exists('houzez_create_print')) {
    function houzez_create_print () {

        if(!isset($_POST['propid'])|| !is_numeric($_POST['propid'])){
            exit();
        }
        global $hide_fields;
        $hide_fields = houzez_option('hide_detail_prop_fields');
        $property_id = intval($_POST['propid']);
    
        print  '<html><head><link href="'.get_stylesheet_uri().'" rel="stylesheet" type="text/css" />';
        print  '<html><head><link href="'.HOUZEZ_CSS_DIR_URI.'bootstrap.min.css" rel="stylesheet" type="text/css" />';
        print  '<html><head><link href="'.HOUZEZ_CSS_DIR_URI.'main.css" rel="stylesheet" type="text/css" />';
        print  '<html><head><link href="'.HOUZEZ_CSS_DIR_URI.'icons.css" rel="stylesheet" type="text/css" />';
        print  '<html><head><link href="'.HOUZEZ_CSS_DIR_URI.'font-awesome.min.css" rel="stylesheet" type="text/css" />';

        if( is_rtl() ) {
            print '<link href="'.HOUZEZ_CSS_DIR_URI.'/rtl.css" rel="stylesheet" type="text/css" />';
            print '<link href="'.HOUZEZ_CSS_DIR_URI.'/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />';
        }
        print '</head>';
        print  '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script>$(window).load(function(){ print(); });</script>';
        print  '<body class="print-page">';

        $print_logo = houzez_option( 'print_page_logo', false, 'url' );

        $print_agent = houzez_option('print_agent');
        $print_description = houzez_option('print_description');
        $print_details = houzez_option('print_details');
        $print_details_additional = houzez_option('print_details_additional');
        $print_features = houzez_option('print_features');
        $print_floorplans = houzez_option('print_floorplans');
        $print_gallery = houzez_option('print_gallery');
        $print_gr_code = houzez_option('print_gr_code');

        $args = array(
            'post_type' => 'property',
            'p' => $property_id,
        );

        $the_query = new WP_Query($args);

        if($the_query->have_posts()): 
            while($the_query->have_posts()): $the_query->the_post(); 
                global $property_features, $energy_class;
                $image_id     = get_post_thumbnail_id( get_the_ID() );
                $full_img     = wp_get_attachment_image_src($image_id, 'full');
                $full_img     = isset($full_img [0]) ? $full_img [0] : '';
                $property_features     = wp_get_post_terms( get_the_ID(), 'property_feature', array("fields" => "all"));
                $energy_class = houzez_get_listing_data('energy_class');
                $floor_plans  = get_post_meta( get_the_ID(), 'floor_plans', true );
                $images_ids  = get_post_meta( get_the_ID(), 'fave_property_images', false );
                $agent_array = houzez20_get_property_agent();
            ?>

            <div class="print-main-wrap">
                <div class="print-wrap">
                    <header class="print-header">
                        <div class="print-logo-wrap py-4 d-flex justify-content-between">
                            <img src="<?php echo esc_url($print_logo); ?>" alt="logo">
                            <div class="primary-text mt-1"><?php bloginfo( 'description' ); ?></div>
                        </div><!-- print-logo-wrap -->
                        
                        <div class="print-title py-4-wrap py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <?php get_template_part('property-details/partials/title'); ?>      
                                    <?php get_template_part('property-details/partials/item-address'); ?>
                                </div>          
                                <?php get_template_part('property-details/partials/item-price'); ?>
                            </div><!-- d-flex -->
                        </div><!-- print-title py-4-wrap -->
                        
                        <?php if( !empty($full_img) ) { ?>
                        <div class="print-banner-wrap">
                            <?php if($print_gr_code != 0) { ?>
                            <div class="qr-code">
                                <img class="img-fluid" src="https://qrcode.tec-it.com/API/QRCode?size=small&dpi=120&data=<?php echo esc_url( get_permalink($property_id) ); ?>" title="<?php echo esc_attr(get_the_title()); ?>" />
                            </div>
                            <?php } ?>
                            <img class="img-fluid" src="<?php echo esc_url( $full_img ); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                        </div><!-- print-banner-wrap -->
                        <?php } ?>
                        
                        <?php 
                        if( $print_agent != 0 && !empty($agent_array)) { ?>
                        <div class="print-agent-info-wrap pb-4">
                            
                            <h2 class="print-title py-4"><?php echo houzez_option('sps_contact_info', 'Contact Information'); ?></h2>
                            
                            <?php 
                            if( isset( $agent_array['agent_info'] ) ) {
                                foreach( $agent_array['agent_info'] as $agent_info ) {  ?>
                                   
                                    <div class="agent-details">
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($agent_info['picture'])) { ?>
                                            <div class="agent-image">
                                                <img class="rounded" src="<?php echo esc_url($agent_info['picture']); ?>" alt="<?php echo esc_attr($agent_info['agent_name']); ?>" width="80" height="80">
                                            </div>
                                            <?php } ?>

                                            <ul class="list-unstyled m-0 ms-3">
                                                <li class="agent-name">
                                                    <i class="houzez-icon icon-single-neutral me-1"></i> <?php echo esc_attr($agent_info['agent_name']); ?>
                                                </li>
                                                <li class="agent-phone-wrap mb-1">
                                                    <?php if(!empty($agent_info['agent_phone'])) { ?>
                                                    <i class="houzez-icon icon-phone me-1"></i> <strong><?php echo esc_attr($agent_info['agent_phone']); ?></strong>
                                                    <?php } ?>

                                                    <?php if(!empty($agent_info['agent_mobile'])) { ?>
                                                    <i class="houzez-icon icon-mobile-phone me-1 ms-3"></i> <strong><?php echo esc_attr($agent_info['agent_mobile']); ?></strong>
                                                    <?php } ?>
                                                </li>

                                                <?php if(!empty($agent_info['agent_email'])) { ?>
                                                <li><i class="houzez-icon icon-envelope me-1"></i> <strong><?php echo esc_attr($agent_info['agent_email']); ?></strong></li>
                                                <?php } ?>
                                            </ul>
                                        </div><!-- d-flex -->
                                    </div><!-- agent-details -->
                                    <br/>
                                <?php
                                }
                            }
                            ?>
                        </div><!-- print-agent-info-wrap -->
                        <?php } ?>

                    </header>  

                    
                    <section class="print-content mb-4">
                        
                        <?php 
                        if( $print_description != 0 ) { ?>

                            <div class="print-section">
                                <h2 class="print-title py-4"><?php echo houzez_option('sps_description', 'Description'); ?></h2>
                                <?php the_content(); ?>       
                            </div>

                        <?php } ?>

                        <?php 
                        if( $print_details != 0 ) { ?>

                            <div class="print-section mb-4">
                                <h2 class="print-title py-4"><?php echo houzez_option('sps_details', 'Details'); ?></h2>
                                <div class="block-content-wrap">
                                    <?php get_template_part('property-details/partials/details'); ?> 
                                </div><!-- block-content-wrap -->
                            </div>

                        <?php } ?>

                        <?php 
                        if( $print_features != 0 && !empty($property_features)) { ?>

                            <div class="print-section mb-4">
                                <h2 class="print-title py-4"><?php echo houzez_option('sps_features', 'Features'); ?></h2>
                                <div class="block-content-wrap">
                                    <?php get_template_part('property-details/partials/features'); ?>  
                                </div><!-- block-content-wrap -->
                            </div>

                        <?php } ?>

                        <?php
                        if( houzez_option('print_energy_class') != 0 && !empty($energy_class) ) { ?>
                            <div class="print-section mb-4">
                                <h2 class="print-title py-4"><?php echo houzez_option('sps_energy_class', 'Energy Class'); ?></h2>
                                <div class="block-content-wrap">
                                    <?php get_template_part('property-details/partials/energy-class'); ?> 
                                </div><!-- block-content-wrap -->
                            </div><!-- print-section -->
                        <?php } ?>

                        <?php 
                        if( !empty( $floor_plans ) && $print_floorplans != 0 ) { ?>

                            <div class="print-section mb-4">
                                <h2 class="print-title py-4"><?php echo houzez_option('sps_floor_plans', 'Floor Plans'); ?></h2>
                                
                                <?php 
                                foreach( $floor_plans as $plan ):
                                    $price_postfix = '';
                                    if( !empty( $plan['fave_plan_price_postfix'] ) ) {
                                        $price_postfix = ' / '.$plan['fave_plan_price_postfix'];
                                    }
                                    $filetype = wp_check_filetype($plan['fave_plan_image']);
                                ?>
                                <div class="floor-plan-wrap mb-4">
                                    <div class="floor-plan-top p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="accordion-title flex-grow-1">
                                                <?php echo esc_attr( $plan['fave_plan_title'] ); ?>
                                            </div><!-- accordion-title -->
                                            <ul class="floor-information list-unstyled list-inline m-0">
                                                <?php if( !empty( $plan['fave_plan_size'] ) ) { ?>
                                                    <li class="list-inline-item">
                                                        <?php esc_html_e( 'Size', 'houzez' ); ?>: 
                                                        <strong> <?php echo esc_attr( $plan['fave_plan_size'] ); ?></strong>
                                                    </li>
                                                <?php } ?>

                                                <?php if( !empty( $plan['fave_plan_rooms'] ) ) { ?>
                                                    <li class="list-inline-item">
                                                        <i class="houzez-icon icon-hotel-double-bed-1 me-1"></i>
                                                        <strong><?php echo esc_attr( $plan['fave_plan_rooms'] ); ?></strong>
                                                    </li>
                                                <?php } ?>

                                                <?php if( !empty( $plan['fave_plan_bathrooms'] ) ) { ?>
                                                    <li class="list-inline-item">
                                                        <i class="houzez-icon icon-bathroom-shower-1 me-1"></i>
                                                        <strong><?php echo esc_attr( $plan['fave_plan_bathrooms'] ); ?></strong>
                                                    </li>
                                                <?php } ?>

                                                <?php if( !empty( $plan['fave_plan_price'] ) ) { ?>
                                                    <li class="list-inline-item">
                                                        <?php esc_html_e( 'Price', 'houzez' ); ?>: 
                                                        <strong><?php echo houzez_get_property_price( $plan['fave_plan_price'] ).$price_postfix; ?></strong>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div><!-- d-flex -->
                                    </div><!-- floor-plan-top -->
                                    
                                    <?php 
                                    if( !empty( $plan['fave_plan_image'] ) ) { ?>
                    
                                        <?php if($filetype['ext'] != 'pdf' ) {?>
                                        <a href="<?php echo esc_url( $plan['fave_plan_image'] ); ?>" target="_blank">
                                            <img class="img-fluid" src="<?php echo esc_url( $plan['fave_plan_image'] ); ?>" alt="image">
                                        </a>
                                        <?php } ?>
                                        
                                    <?php } ?>

                                    <?php
                                    if( !empty( $plan['fave_plan_description'] ) ) { ?>
                                    <div class="floor-plan-description p-4">
                                        <p>
                                            <?php echo wp_kses_post( $plan['fave_plan_description'] ); ?>
                                        </p>
                                    </div><!-- floor-plan-description -->
                                    <?php } ?>

                                </div><!-- floor-plan-wrap -->
                                <?php endforeach; ?>

                            </div>
                        <?php } ?>


                        <?php 
                        if( !empty( $images_ids ) && $print_gallery != 0 ) { ?>
                        <div class="print-section mb-4">
                            <h2 class="print-title py-4"><?php esc_html_e('Images', 'houzez'); ?></h2>
                            <?php 
                            foreach( $images_ids as $img_id ): 
                                $image_url = wp_get_attachment_image_src($img_id, 'full');
                                ?>
                                <div class="print-gallery-image"> 
                                <img src="<?php echo $image_url[0]; ?>" class="img-fluid mb-3">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php } ?>


                    </section>
                </div><!-- print-wrap -->
            </div><!-- print-main-wrap -->

        <?php
            endwhile;
        endif;

        ?>

<?php
        print '</body></html>';
        wp_die();
    }
}

/*-----------------------------------------------------------------------------------*/
// Get Current Area | @return mixed|string|void
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'houzez_get_current_area' ) ) {

    function houzez_get_current_area() {

        if ( isset( $_COOKIE[ "houzez_current_area" ] ) ) {
            $current_area = $_COOKIE[ "houzez_current_area" ];
        }

        if ( empty( $current_area ) ) {
            $current_area = houzez_option('houzez_base_area');
        }

        return $current_area;
    }
}

/*-----------------------------------------------------------------------------------*/
// Ajax Area Switch
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'houzez_switch_area' ) ) {

    function houzez_switch_area() {

        if ( isset( $_POST[ 'switch_to_area' ] ) ):

            $expiry_period = '';

            $switch_to_area = $_POST[ 'switch_to_area' ];

            // expiry time
            $expiry_period = intval( $expiry_period );
            if ( ! $expiry_period ) {
                $expiry_period = 60 * 60;   // one hour
            }
            $expiry = time() + $expiry_period;

            // save cookie
            if ( setcookie( 'houzez_current_area', $switch_to_area, $expiry, '/' ) ) {
                echo json_encode( array(
                    'success' => true
                ) );
            } else {
                echo json_encode( array(
                    'success' => false,
                    'message' => __( "Failed to updated cookie !", 'houzez' )
                ) );
            }

        else:
            echo json_encode( array(
                    'success' => false,
                    'message' => __( "Invalid Request !", 'houzez' )
                )
            );
        endif;

        die;

    }

    add_action( 'wp_ajax_nopriv_houzez_switch_area', 'houzez_switch_area' );
    add_action( 'wp_ajax_houzez_switch_area', 'houzez_switch_area' );
}


if( !function_exists('houzez_get_area_size') ) {
    function houzez_get_area_size( $areaSize ) {
        $prop_size = $areaSize;
        $prop_area_size = '';
        $houzez_base_area = houzez_option('houzez_base_area');

        if( !empty( $prop_size ) ) {
            // Convert string to float
            $prop_size = (float)$prop_size;

            if( isset( $_COOKIE[ "houzez_current_area" ] ) ) {
                if( $_COOKIE[ "houzez_current_area" ] == 'sq_meter' && $houzez_base_area != 'sq_meter'  ) {
                    $prop_size = $prop_size * 0.09290304; //m2 = ft2 x 0.09290304

                } elseif( $_COOKIE[ "houzez_current_area" ] == 'sqft' && $houzez_base_area != 'sqft' ) {
                    $prop_size = $prop_size / 0.09290304; //ft2 = m2 ÷ 0.09290304
                }
            }

            $prop_area_size = esc_attr( round( $prop_size, 0 ) );

        }
        return $prop_area_size;

    }
}

if( !function_exists('houzez_get_size_unit') ) {
    function houzez_get_size_unit( $areaUnit ) {
        $measurement_unit_global = houzez_option('measurement_unit_global');
        $area_switcher_enable = houzez_option('area_switcher_enable');

        if( $area_switcher_enable != 0 ) {
            $prop_size_prefix = houzez_option('houzez_base_area');

            if( isset( $_COOKIE[ "houzez_current_area" ] ) ) {
                $prop_size_prefix =$_COOKIE[ "houzez_current_area" ];
            }

            if( $prop_size_prefix == 'sqft' ) {
                $prop_size_prefix = houzez_option('measurement_unit_sqft_text');
            } elseif( $prop_size_prefix == 'sq_meter' ) {
                $prop_size_prefix = houzez_option('measurement_unit_square_meter_text');
            }

        } else {
            if ($measurement_unit_global == 1) {
                $prop_size_prefix = houzez_option('measurement_unit');

                if( $prop_size_prefix == 'sqft' ) {
                    $prop_size_prefix = houzez_option('measurement_unit_sqft_text');
                } elseif( $prop_size_prefix == 'sq_meter' ) {
                    $prop_size_prefix = houzez_option('measurement_unit_square_meter_text');
                }

            } else {
                $prop_size_prefix = $areaUnit;
            }
        }
        return $prop_size_prefix;
    }
}

if( !function_exists('houzez_autocomplete_search') ) {
    function houzez_autocomplete_search() {

        return;
    }
}

if( !function_exists('houzez_generate_invoice') ):
    function houzez_generate_invoice( $billingFor, $billionType, $packageID, $invoiceDate, $userID, $featured, $upgrade, $paypalTaxID, $paymentMethod, $is_package = 0 ) {

        $total_taxes = 0;
        $price_per_submission = houzez_option('price_listing_submission');
        $price_featured_submission = houzez_option('price_featured_listing_submission');

        $price_per_submission      = floatval( $price_per_submission );
        $price_featured_submission = floatval( $price_featured_submission );
        
        // Get tax percentages for per-listing
        $tax_percentage_per_listing = floatval(houzez_option('tax_percentage_per_listing'));
        $tax_percentage_featured = floatval(houzez_option('tax_percentage_featured'));

        $args = array(
            'post_title'    => 'Invoice ',
            'post_status'   => 'publish',
            'post_type'     => 'houzez_invoice'
        );
        $inserted_post_id =  wp_insert_post( $args );

        if( $billionType != 'one_time' ) {
            $billionType = __( 'Recurring', 'houzez' );
        } else {
            $billionType = __( 'One Time', 'houzez' );
        }

        if( $is_package == 0 ) {
            // Calculate taxes for per-listing
            $tax_per_listing = 0;
            $tax_featured = 0;
            
            if( !empty($tax_percentage_per_listing) && !empty($price_per_submission) ) {
                $tax_per_listing = ($tax_percentage_per_listing / 100) * $price_per_submission;
                $tax_per_listing = round($tax_per_listing, 2);
            }
            
            if( !empty($tax_percentage_featured) && !empty($price_featured_submission) ) {
                $tax_featured = ($tax_percentage_featured / 100) * $price_featured_submission;
                $tax_featured = round($tax_featured, 2);
            }
            
            if( $upgrade == 1 ) {
                $total_price = $price_featured_submission + $tax_featured;
                $total_taxes = $tax_featured;

            } else {
                if( $featured == 1 ) {
                    $total_price = $price_per_submission + $tax_per_listing + $price_featured_submission + $tax_featured;
                    $total_taxes = $tax_per_listing + $tax_featured;
                } else {
                    $total_price = $price_per_submission + $tax_per_listing;
                    $total_taxes = $tax_per_listing;
                }
            }
        } else {
            $pack_price = get_post_meta( $packageID, 'fave_package_price', true);
            $pack_tax = get_post_meta( $packageID, 'fave_package_tax', true );

            if( !empty($pack_tax) && !empty($pack_price) ) {
                $total_taxes = intval($pack_tax)/100 * $pack_price;
                $total_taxes = round($total_taxes, 2);
            }
            
            $total_price = $pack_price + $total_taxes;
        }
        
        $fave_meta = array();

        $fave_meta['invoice_billion_for'] = $billingFor;
        $fave_meta['invoice_billing_type'] = $billionType;
        $fave_meta['invoice_item_id'] = $packageID;
        $fave_meta['invoice_item_price'] = $total_price;
        $fave_meta['invoice_tax'] = $total_taxes;
        $fave_meta['invoice_purchase_date'] = $invoiceDate;
        $fave_meta['invoice_buyer_id'] = $userID;
        $fave_meta['paypal_txn_id'] = $paypalTaxID;
        $fave_meta['invoice_payment_method'] = $paymentMethod;

        update_post_meta( $inserted_post_id, 'HOUZEZ_invoice_buyer', $userID );
        update_post_meta( $inserted_post_id, 'HOUZEZ_invoice_type', $billionType );
        update_post_meta( $inserted_post_id, 'HOUZEZ_invoice_for', $billingFor );
        update_post_meta( $inserted_post_id, 'HOUZEZ_invoice_item_id', $packageID );
        update_post_meta( $inserted_post_id, 'HOUZEZ_invoice_price', $total_price );
        update_post_meta( $inserted_post_id, 'HOUZEZ_invoice_tax', $total_taxes );
        update_post_meta( $inserted_post_id, 'HOUZEZ_invoice_date', $invoiceDate );
        update_post_meta( $inserted_post_id, 'HOUZEZ_paypal_txn_id', $paypalTaxID );
        update_post_meta( $inserted_post_id, 'HOUZEZ_invoice_payment_method', $paymentMethod );

        update_post_meta( $inserted_post_id, '_houzez_invoice_meta', $fave_meta );

        // Update post title
        $update_post = array(
            'ID'         => $inserted_post_id,
            'post_title' => 'Invoice '.$inserted_post_id,
        );
        wp_update_post( $update_post );
        return $inserted_post_id;
    }
endif;


if( !function_exists('houzez_get_agent_info') ) {
    function houzez_get_agent_info( $args, $type ) {
        if( $type == 'for_grid_list' ) {
            return '<a href="'.$args[ 'link' ].'">'.$args[ 'agent_name' ].'</a> ';

        } elseif( $type == 'agent_form' ) {
            $output = '';

            $output .= '<div class="media agent-media">';
                $output .= '<div class="media-left">';
                    $output .= '<input type="checkbox">';
                    $output .= '<a href="'.$args[ 'link' ].'">';
                        $output .= '<img src="'.$args[ 'picture' ].'" alt="'.$args[ 'agent_name' ].'" width="75" height="75">';
                    $output .= '</a>';
                $output .= '</div>';

                $output .= '<div class="media-body">';
                    $output .= '<dl>';
                        if( !empty($args[ 'agent_name' ]) ) {
                            $output .= '<dd><i class="fa fa-user"></i> '.$args[ 'agent_name' ].'</dd>';
                        }
                        if( !empty( $args[ 'agent_mobile' ] ) ) {
                            $output .= '<dd><i class="fa fa-phone"></i><span class="clickToShow">'.esc_attr( $args[ 'agent_mobile' ] ).'</span></dd>';
                        }
                        $output .= '<dd><a href="'.$args[ 'link' ].'" class="view">'.esc_html__('View my listing', 'houzez' ).'</a></dd>';
                    $output .= '</dl>';
                $output .= '</div>';
            $output .= '</div>';

            return $output;
        }
    }
}

if( !function_exists('houzez_get_property_agent') ) {
    function houzez_get_property_agent($prop_id, $type = null) {

        // Validate input
        if (empty($prop_id) || !is_numeric($prop_id)) {
            return array();
        }
        
        $prop_id = intval($prop_id);
        
        // Get display options
        $agent_display_option = get_post_meta($prop_id, 'fave_agent_display_option', true);
        $prop_agent_display = get_post_meta($prop_id, 'fave_agents', true);
        
        // Handle agent info display
        if ($prop_agent_display != '-1' && $agent_display_option == 'agent_info') {
            return houzez_get_agent_data($prop_id, 'agent');
        }
        
        // Handle agency info display
        if ($agent_display_option == 'agency_info') {
            return houzez_get_agent_data($prop_id, 'agency');
        }
        
        // Default to author info
        return houzez_get_author_data();
    }
}

/**
 * Helper function to get agent or agency data
 */
if (!function_exists('houzez_get_agent_data')) {
    function houzez_get_agent_data($prop_id, $type = 'agent') {
        
        $meta_key = ($type === 'agent') ? 'fave_agents' : 'fave_property_agency';
        $ids = get_post_meta($prop_id, $meta_key);
        
        // Filter out invalid IDs and get the last valid one
        $valid_ids = array_filter($ids, function($id) {
            return is_numeric($id) && $id > 0;
        });
        
        if (empty($valid_ids)) {
            return array();
        }
        
        $id = intval(end($valid_ids));
        // WPML Workaround for compsupp-7949
        $id = apply_filters( 'wpml_object_id', $id, 'houzez_agent', TRUE );
        
        // Get all meta data in one call for better performance
        $meta_prefix = ($type === 'agent') ? 'fave_agent_' : 'fave_agency_';
        $meta_fields = array(
            'mobile' => $meta_prefix . 'mobile',
            'email' => $meta_prefix . 'email',
            'whatsapp' => $meta_prefix . 'whatsapp',
            'telegram' => $meta_prefix . 'telegram',
            'line_id' => $meta_prefix . 'line_id'
        );
        
        $agent_data = array();
        foreach ($meta_fields as $key => $meta_key) {
            $agent_data[$key] = get_post_meta($id, $meta_key, true);
        }
        
        // Build the response array
        $result = array(
            'agent_name' => get_the_title($id),
            'agent_mobile' => $agent_data['mobile'],
            'agent_mobile_call' => houzez_clean_phone_number($agent_data['mobile']),
            'agent_whatsapp' => $agent_data['whatsapp'],
            'agent_whatsapp_call' => houzez_clean_phone_number($agent_data['whatsapp']),
            'agent_telegram' => $agent_data['telegram'],
            'agent_lineapp' => $agent_data['line_id'],
            'agent_email' => $agent_data['email'],
            'link' => get_permalink($id),
            'picture' => houzez_get_contact_picture($id, $type)
        );
        
        return $result;
    }
}

/**
 * Helper function to get author data
 */
if (!function_exists('houzez_get_author_data')) {
    function houzez_get_author_data() {
        
        $author_id = get_the_author_meta('ID');
        $mobile = get_the_author_meta('fave_author_mobile');
        $whatsapp = get_the_author_meta('fave_author_whatsapp');
        
        return array(
            'agent_name' => get_the_author(),
            'agent_mobile' => $mobile,
            'agent_mobile_call' => houzez_clean_phone_number($mobile),
            'agent_whatsapp' => $whatsapp,
            'agent_whatsapp_call' => houzez_clean_phone_number($whatsapp),
            'agent_telegram' => get_the_author_meta('fave_author_telegram'),
            'agent_lineapp' => get_the_author_meta('fave_author_line_id'),
            'agent_email' => get_the_author_meta('email'),
            'link' => get_author_posts_url($author_id),
            'picture' => houzez_get_author_picture($author_id)
        );
    }
}

/**
 * Helper function to clean phone numbers
 */
if (!function_exists('houzez_clean_phone_number')) {
    function houzez_clean_phone_number($phone) {
        if (empty($phone)) {
            return '';
        }
        return str_replace(array('(', ')', ' ', '-'), '', $phone);
    }
}

/**
 * Helper function to get contact picture (agent/agency)
 */
if (!function_exists('houzez_get_contact_picture')) {
    function houzez_get_contact_picture($id, $type = 'agent') {
        
        // Try to get featured image first
        $thumb_id = get_post_thumbnail_id($id);
        if ($thumb_id) {
            $thumb_url_array = wp_get_attachment_image_src($thumb_id, array(150, 150), true);
            if (!empty($thumb_url_array[0])) {
                return $thumb_url_array[0];
            }
        }
        
        // Get placeholder based on type
        $placeholder_option = ($type === 'agent') ? 'houzez_agent_placeholder' : 'houzez_agency_placeholder';
        $placeholder_url = houzez_option($placeholder_option, false, 'url');
        
        if (!empty($placeholder_url)) {
            return $placeholder_url;
        }
        
        // Default fallback
        return HOUZEZ_IMAGE . 'profile-avatar.png';
    }
}

/**
 * Helper function to get author picture
 */
if (!function_exists('houzez_get_author_picture')) {
    function houzez_get_author_picture($author_id) {
        
        $custom_picture = get_the_author_meta('fave_author_custom_picture', $author_id);
        
        if (!empty($custom_picture)) {
            return $custom_picture;
        }
        
        // Default fallback
        return HOUZEZ_IMAGE . 'profile-avatar.png';
    }
}

add_action( 'wp_ajax_nopriv_houzez_get_auto_complete_search', 'houzez_get_auto_complete_search' );
add_action( 'wp_ajax_houzez_get_auto_complete_search', 'houzez_get_auto_complete_search' );

if ( !function_exists( 'houzez_get_auto_complete_search' ) ) {

    function houzez_get_auto_complete_search() {
        $current_language = apply_filters( 'wpml_current_language', null );
        global $wpdb;
        $key = $_POST['key'];
        $key = $wpdb->esc_like($key);
        $keyword_field = houzez_option('keyword_field');
        $houzez_local = houzez_get_localization();
        $placeholder_url = houzez_option( 'houzez_placeholder', false, 'url' );
        $response = '';

        if( $keyword_field != 'prop_city_state_county' ) {

            if ( $keyword_field == "prop_title" ) {

                $table = $wpdb->posts;

                $data = $wpdb->get_results( 
                    $wpdb->prepare(
                        "SELECT DISTINCT * FROM {$table} WHERE post_type = %s AND post_status = %s AND (post_title LIKE %s OR post_content LIKE %s)",
                        'property',
                        'publish',
                        '%' . $key . '%',
                        '%' . $key . '%'
                    )
                );


                if ( sizeof( $data ) != 0 ) {

                    $search_url = add_query_arg( 'keyword', $key, houzez_get_search_template_link() );

                    echo '<ul class="list-group" role="listbox">';

                    $new_data = array();

                    foreach ( $data as $post ) {

                        $propID = $post->ID;

                        $post_language = apply_filters( 'wpml_element_language_code', null, array('element_id' => $propID, 'element_type' => 'post'));

                        if ($post_language !== $current_language) {
                            continue;
                        }

                        $new_data [] = $post;
                        
                        $prop_type = houzez_taxonomy_simple_2('property_type', $propID);

                        $prop_img = get_the_post_thumbnail_url( $propID, array ( 40, 40 ) );

                        if ( empty( $prop_img ) ) {
                            $prop_img = houzez_get_image_placeholder_url('thumbnail');
                        }

                        ?>

                        <li class="list-group-item p-2" role="option" data-text="<?php echo $post->post_title; ?>">
                            <div class="d-flex align-items-start">
                                <div class="auto-complete-image-wrap m-1">
                                    <a href="<?php the_permalink( $propID ); ?>">
                                        <img class="img-fluid rounded" src="<?php echo $prop_img; ?>" width="64" height="64" alt="image">
                                    </a>    
                                </div><!-- auto-complete-image-wrap -->
                                <div class="auto-complete-content-wrap ms-3">
                                    <div class="auto-complete-title mb-1">
                                        <a href="<?php the_permalink( $propID ); ?>"><?php echo $post->post_title; ?></a>
                                    </div>
                                    <ul class="item-price-wrap position-relative top-0 start-0 d-flex flex-column gap-2 mb-2" role="list">
                                        <!-- <li class="item-price" role="listitem">
                                            <span class="price-prefix">From </span><span class="price">$9,990</span><span class="price-postfix">/mo</span>
                                        </li> -->
                                        <?php echo houzez_listing_price_for_print($propID); ?>
                                    </ul>
                                    <div class="mb-1">
                                        <ul class="item-amenities item-amenities-with-icons d-flex flex-wrap align-items-center gap-2 mb-2" role="list">
                                            <li class="h-type d-flex w-100" role="listitem">
                                                <span><?php echo $prop_type;?></span>
                                            </li>
                                        </ul>                    
                                    </div>
                                </div><!-- auto-complete-content-wrap -->
                            </div><!-- d-flex -->
                        </li><!-- list-group-item -->
                        <?php
                    }

                    echo '</ul>';

                    echo '<div class="auto-complete-footer d-flex justify-content-between align-items-center" role="contentinfo">';
                        echo '<span class="auto-complete-count"><i class="houzez-icon icon-pin me-1"></i> ' . sizeof( $new_data ) . ' '.$houzez_local['listins_found'].'</span>';
                        echo '<a target="_blank" href="' . $search_url . '" class="search-result-view" role="button">'.$houzez_local['view_all_results'].'</a>';
                    echo '</div>';

                } else {

               ?>
               <ul class="list-group">
                   <li class="list-group-item"> <?php echo $houzez_local['auto_result_not_found']; ?> </li>
               </ul>
               <?php

           }

       } else if ( $keyword_field == "prop_address" ) {

                $posts_table = $wpdb->posts;
                $postmeta_table = $wpdb->postmeta;

                $data = $wpdb->get_results( 
                    $wpdb->prepare(
                            "SELECT DISTINCT post.ID, meta.meta_value FROM {$postmeta_table} AS meta INNER JOIN $posts_table AS post ON meta.post_id=post.ID AND post.post_type='property' and post.post_status='publish' AND meta.meta_value LIKE %s AND ( meta.meta_key='fave_property_map_address' OR meta.meta_key='fave_property_zip' OR meta.meta_key='fave_property_address' OR meta.meta_key='fave_property_id' )",
                            '%' . $key . '%'
                        )
                );

                if ( sizeof( $data ) != 0 ) {

                    echo '<ul class="list-group">';

                    $new_data = array();

                    foreach ( $data as $title ) {

                        $post_language = apply_filters( 'wpml_element_language_code', null, array('element_id' => $title->ID, 'element_type' => 'post'));

                        if ($post_language !== $current_language) {
                            continue;
                        }

                        $new_data [] = $title;
                        ?>
                        
                        <li class="list-group-item p-2" data-text="<?php echo $title->meta_value; ?>">
                            <div class="d-flex align-items-center">
                                <div class="auto-complete-content-wrap flex-fill">
                                    <i class="houzez-icon icon-pin me-1"></i> 
                                    <span><?php echo $title->meta_value; ?></span>
                                </div><!-- auto-complete-content-wrap -->
                            </div><!-- d-flex -->
                        </li>
                        <?php

                    }

                    echo '</ul>';

                } else {

               ?>
               <ul class="list-group">
                   <li class="list-group-item"> <?php echo $houzez_local['auto_result_not_found']; ?> </li>
               </ul>
               <?php

           }

            }

        } else {
            $terms_table = $wpdb->terms;
            $term_taxonomy = $wpdb->term_taxonomy;

            $data = $wpdb->get_results( 
                $wpdb->prepare(
                    "SELECT DISTINCT * FROM {$terms_table} as term INNER JOIN $term_taxonomy AS term_taxonomy ON term.term_id = term_taxonomy.term_id AND term.name LIKE %s AND ( term_taxonomy.taxonomy = %s OR term_taxonomy.taxonomy = %s OR term_taxonomy.taxonomy = %s )",
                    '%' . $key . '%',
                    'property_area',
                    'property_city',
                    'property_state'
                )
            );

            if ( sizeof( $data ) != 0 ) {

                echo '<ul class="list-group" role="list">';

                $new_data = array();

                foreach ( $data as $term ) {
        
                    $term_language = apply_filters( 'wpml_element_language_code', null, array('element_id' => $term->term_id, 'element_type' => 'category'));

                    if ($term_language !== $current_language) {
                        continue;
                    }

                    $new_data [] = $term;
                }

                // Sort the $new_data array based on the taxonomy
                usort($new_data, function($a, $b) {
                    $order = ['property_state' => 1, 'property_city' => 2, 'property_area' => 3];
                    return $order[$a->taxonomy] - $order[$b->taxonomy];
                });

                // Display the sorted terms
                foreach ($new_data as $term) {
                    

                    $taxonomy_img_id = get_term_meta( $term->term_id, 'fave_taxonomy_img', true );

                    $term_type = explode( 'property_', $term->taxonomy );
                    $term_type = $term_type[1];
                    $prop_count = $term->count;

                    if ( empty( $taxonomy_img_id ) ) {
                       $term_img = '<img src="'.$placeholder_url.'" class="img-fluid rounded" width="64" height="64">';
                   } else {
                        $term_img = wp_get_attachment_image( $taxonomy_img_id, array( 64, 64 ), array( "class" => "img-fluid rounded" ) );
                   }

                   if( $term_type == 'state' ) {
                        $term_type = $houzez_local['auto_state'];
                   } else if( $term_type == 'city' ) {
                        $term_type = $houzez_local['auto_city'];
                   } else if( $term_type == 'area' ) {
                        $term_type = $houzez_local['auto_area'];
                   }

                    ?>
                    <li class="list-group-item" role="listitem" data-text="<?php echo $term->name; ?>">
                        <div class="d-flex align-items-center">
                            <div class="auto-complete-image-wrap mx-1">
                                <a href="<?php echo get_term_link( $term ); ?>">
                                    <?php echo $term_img; ?>
                                </a>    
                            </div><!-- auto-complete-image-wrap -->
                            <div class="auto-complete-content-wrap flex-fill ms-3">
                                <div class="auto-complete-title mb-2"><?php echo esc_attr($term->name); ?></div>
                                <ul class="item-amenities">
                                    <li><?php if ( !empty( $term_type ) ) { ?>
                                    <?php echo $term_type; ?>
                                <?php } ?>
                                <?php if ( !empty( $prop_count ) ) : ?>
                                     - <?php echo $prop_count . ' ' . $houzez_local['auto_listings']; ?>
                                <?php endif; ?></li>
                                </ul>
                            </div><!-- auto-complete-content-wrap -->
                            <div class="auto-complete-content-wrap me-3">
                                <a target="_blank" href="<?php echo get_term_link( $term ); ?>" class="search-result-view"><?php echo $houzez_local['auto_view_lists']; ?></a>
                            </div><!-- auto-complete-content-wrap -->
                        </div><!-- d-flex -->
                    </li>
                    <?php

                }

                echo '</ul>';

            } else {

               ?>
               <ul class="list-group">
                   <li class="list-group-item"> <?php echo $houzez_local['auto_result_not_found']; ?> </li>
               </ul>
               <?php

           }

        }

        wp_die();

    }

}

/*-------------------------------------------------------------------------------
*
* Agency ajax pagination data
*-------------------------------------------------------------------------------*/
add_action('wp_ajax_houzez_ajax_agency_filter', 'houzez_ajax_agency_filter');
add_action('wp_ajax_nopriv_houzez_ajax_agency_filter', 'houzez_ajax_agency_filter');

if( !function_exists('houzez_ajax_agency_filter')) {
    function houzez_ajax_agency_filter() {

    }
}


if ( !function_exists( 'houzez_get_agent_info_bottom' ) ) {
    /**
     * @deprecated 2.0.0 Use houzez_render_agent_info() instead
     */
    function houzez_get_agent_info_bottom( $args, $type, $is_single = true ) {

        $view_listing = houzez_option('agent_view_listing');
        $agent_phone_num = houzez_option('agent_phone_num');
        if( empty($args['agent_name']) ) {
            return '';
        }
        if( $type == 'for_grid_list' ) {
            return '<a href="'.$args[ 'link' ].'">'.$args[ 'agent_name' ].'</a> ';

        } elseif( $type == 'agent_form' ) {
            $output = '';

            $output .= '<div class="agent-details">';
                $output .= '<div class="d-flex align-items-center gap-3">';
                    
                    $output .= '<div class="agent-image">';
                        if ( $is_single == false ) :
                            $output .= '<input type="checkbox" checked="checked" class="houzez-hidden multiple-agent-check" name="target_email[]" value="' . $args['agent_email'] . '" >';
                        endif;
                        
                        $output .= '<a href="'.$args[ 'link' ].'">';
                            $output .= '<img class="rounded" src="'.$args[ 'picture' ].'" alt="'.$args[ 'agent_name' ].'" width="80" height="80">';
                            if($args['verified'] == 1) {
                                $output .= '<span class="badge btn-secondary agent-verified-icon"><i class="houzez-icon icon-check-circle-1"></i></span>';
                            }
                        $output .= '</a>';
                    $output .= '</div>';

                    $output .= '<ul class="agent-information list-unstyled d-flex flex-column gap-1">';
                        
                        if ( !empty( $args[ 'agent_name' ] ) ) :
                        $output .= '<li class="agent-name">';
                            $output .= '<i class="houzez-icon icon-single-neutral me-1"></i> '.$args[ 'agent_name' ];
                        $output .= '</li>';
                        endif;


                        $output .= '<li class="agent-phone-wrap d-flex gap-2 align-items-center">';

                            if ( !empty( $args[ 'agent_phone' ] ) && houzez_option('agent_phone_num', 1) ) :
                            $output .= '<i class="houzez-icon icon-phone me-1"></i>';
                            $output .= '<span class="agent-phone '.houzez_get_show_phone().' me-1">';
                                 $output .= '<a href="tel:'.esc_attr( $args[ 'agent_phone_call' ] ).'">'.esc_attr($args[ 'agent_phone' ]).'</a>';
                            $output .= '</span>';
                            endif;

                            if ( !empty( $args[ 'agent_mobile' ] ) && houzez_option('agent_mobile_num', 1) ) :
                            $output .= '<i class="houzez-icon icon-mobile-phone me-1"></i>';
                            $output .= '<span class="agent-phone '.houzez_get_show_phone().' me-1">';
                                 $output .= '<a href="tel:'.esc_attr( $args[ 'agent_mobile_call' ] ).'">'.esc_attr($args[ 'agent_mobile' ]).'</a>';
                            $output .= '</span>';
                            endif;

                            if ( !empty( $args[ 'agent_skype' ] ) && $args[ 'agent_skype' ] != "#" && houzez_option('agent_skype_con', 1) ) :
                            $output .= '<i class="houzez-icon icon-video-meeting-skype me-1"></i>';
                            $output .= '<span>';
                                 $output .= '<a href="skype:'.esc_attr( $args[ 'agent_skype' ] ).'?call">'.esc_attr( $args[ 'agent_skype' ] ).'</a>';
                            $output .= '</span>';
                            endif;

                            if ( !empty( $args[ 'agent_whatsapp' ] ) && houzez_option('agent_whatsapp_num', 1) ) :
                            $output .= '<i class="houzez-icon icon-messaging-whatsapp me-1"></i>';
                            $output .= '<span>';
                                 $output .= '<a target="_blank" href="https://api.whatsapp.com/send?phone='.esc_attr( $args[ 'agent_whatsapp_call' ] ).'&text='.houzez_option('spl_con_interested', "Hello, I am interested in").' ['.get_the_title().'] '.get_permalink().'">'.esc_html__('WhatsApp', 'houzez').'</a>';
                            $output .= '</span>';
                            endif;

                        $output .= '</li>';


                        if( houzez_option('agent_con_social', 1) ) {
                            $output .= '<li class="agent-social-media">';
                                
                                if( !empty( $args[ 'facebook' ] ) ) :
                                $output .= '<span>';
                                    $output .= '<a class="btn-facebook" target="_blank" rel="noopener" href="'.esc_url($args['facebook']).'">';
                                        $output .= '<i class="houzez-icon icon-social-media-facebook me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                                endif;
                                
                                if( !empty( $args[ 'instagram' ] ) ) :
                                $output .= '<span>';
                                    $output .= '<a class="btn-instagram" target="_blank" rel="noopener" href="'.esc_url($args['instagram']).'">';
                                        $output .= '<i class="houzez-icon icon-social-instagram me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                                endif;

                                if( !empty( $args[ 'twitter' ] ) ) :
                                $output .= '<span>';
                                    $output .= '<a class="btn-twitter" target="_blank" rel="noopener" href="'.esc_url($args['twitter']).'">';
                                        $output .= '<i class="houzez-icon icon-x-logo-twitter-logo-2 me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                                endif;

                                if( !empty( $args[ 'linkedin' ] ) ) :
                                $output .= '<span>';
                                    $output .= '<a class="btn-linkedin" target="_blank" rel="noopener" href="'.esc_url($args['linkedin']).'">';
                                        $output .= '<i class="houzez-icon icon-professional-network-linkedin me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                                endif;

                                if( !empty( $args[ 'googleplus' ] ) ) :
                                $output .= '<span>';
                                    $output .= '<a class="btn-google-plus" target="_blank" rel="noopener" href="'.esc_url($args['googleplus']).'">';
                                        $output .= '<i class="houzez-icon icon-social-media-google-plus-1 me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                                endif;

                                if( !empty( $args[ 'youtube' ] ) ) :
                                $output .= '<span>';
                                    $output .= '<a class="btn-youtube" target="_blank" rel="noopener" href="'.esc_url($args['youtube']).'">';
                                       $output .= '<i class="houzez-icon icon-social-video-youtube-clip me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                                endif;

                            $output .= '</li>';
                        }
                    $output .= '</ul>';
                $output .= '</div>';
            $output .= '</div>';


            return $output;

        }

    }
}

if ( !function_exists( 'houzez_get_agent_info_bottom_v2' ) ) {
    /**
     * @deprecated 2.0.0 Use houzez_render_agent_info() instead
     */
    function houzez_get_agent_info_bottom_v2( $args, $type, $is_single = true ) {

        if( empty($args['agent_name']) ) {
            return '';
        }
        ob_start();
        ?>
        <div class="agent-details">
    
            <div class="agent-image">
                <img class="rounded" src="<?php echo esc_url( $args[ 'picture' ] ); ?>" alt="<?php echo esc_attr( $args['agent_name'] ); ?>" width="80" height="80">
                <?php if ( $args['verified'] == 1 ) { ?>
                    <span class="badge btn-secondary agent-verified-icon"><i class="houzez-icon icon-check-circle-1"></i></span>
                <?php } ?>
                <?php if ( $is_single == false ) { ?>
                <input type="checkbox" class="houzez-hidden multiple-agent-check" checked="checked" name="target_email[]" value="<?php echo $args['agent_email']; ?>" >
                <?php } ?>
            </div>

            <ul class="agent-information list-unstyled">
                <li class="agent-name">
                    <?php if( !empty( $args['agent_name'] ) ) { ?>
                        <i class="houzez-icon icon-single-neutral me-1"></i> <?php echo esc_attr( $args['agent_name'] ); ?>
                    <?php } ?>
                </li>

                <li class="agent-phone-wrap clearfix">
        
                    <?php if( !empty( $args['agent_phone'] ) && houzez_option('agent_phone_num', 1) ) { ?>
                        <i class="houzez-icon icon-phone me-1"></i>
                        <span class="agent-phone <?php houzez_show_phone(); ?>">
                            <?php echo esc_attr( $args['agent_phone'] );?>
                        </span>
                    <?php } ?>

                    <?php if( !empty( $args['agent_mobile'] ) && houzez_option('agent_mobile_num', 1) ) { ?>
                        <i class="houzez-icon icon-mobile-phone me-1"></i>
                        <span class="agent-phone <?php houzez_show_phone(); ?>">
                            <?php echo esc_attr( $args['agent_mobile'] );?>
                        </span>
                    <?php } ?>

                    <?php if( !empty( $args['agent_skype'] ) && houzez_option('agent_skype_con', 1) ) { ?>
                        <i class="houzez-icon icon-video-meeting-skype me-1"></i>
                        <span>
                            <a href="skype:<?php esc_attr( $args[ 'agent_skype' ] ); ?>?call"><?php echo esc_attr( $args[ 'agent_skype' ] ); ?></a>
                        </span>
                    <?php } ?>

                    <?php if( !empty( $args['agent_whatsapp'] ) && houzez_option('agent_whatsapp_num', 1) ) { ?>
                        <i class="houzez-icon icon-messaging-whatsapp me-1"></i>
                        <span>
                            <a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo esc_attr( $args[ 'agent_whatsapp_call' ] ); ?>&text=<?php echo houzez_option('spl_con_interested', "Hello, I am interested in").' ['.get_the_title().'] '.get_permalink(); ?> "><?php echo esc_html__('WhatsApp', 'houzez'); ?></a>
                        </span>
                    <?php } ?>

                </li>

                <?php if( houzez_option('agent_con_social', 1) ) { ?>
                <li class="agent-social-media mb-3">
                    <?php if( !empty( $args['facebook'] ) ) { ?>
                        <span><a class="btn-facebook" target="_blank" href="<?php echo esc_url( $args['facebook'] ); ?>"><i class="houzez-icon icon-social-media-facebook me-2"></i></a></span>
                    <?php } ?>

                    <?php if( !empty( $args['twitter'] ) ) { ?>
                        <span><a class="btn-twitter" target="_blank" href="<?php echo esc_url( $args['twitter'] ); ?>"><i class="houzez-icon icon-x-logo-twitter-logo-2 me-2"></i></a></span>
                    <?php } ?>

                    <?php if( !empty( $args['linkedin'] ) ) { ?>
                        <span><a class="btn-linkedin" target="_blank" href="<?php echo esc_url( $args['linkedin'] ); ?>"><i class="houzez-icon icon-professional-network-linkedin me-2"></i></a></span>
                    <?php } ?>

                    <?php if( !empty( $args['googleplus'] ) ) { ?>
                        <span><a class="btn-google-plus" target="_blank" href="<?php echo esc_url( $args['googleplus'] ); ?>"><i class="houzez-icon icon-social-media-google-plus-1 me-2"></i></a></span>
                    <?php } ?>

                    <?php if( !empty( $args['youtube'] ) ) { ?>
                        <span><a class="btn-youtube" target="_blank" href="<?php echo esc_url( $args['youtube'] ); ?>"><i class="houzez-icon icon-social-video-youtube-clip me-2"></i></a></span>
                    <?php } ?>

                    <?php if( !empty( $args['instagram'] ) ) { ?>
                        <span><a class="btn-instagram" target="_blank" href="<?php echo esc_url( $args['instagram'] ); ?>"><i class="houzez-icon icon-social-video-instagram-clip me-2"></i></a></span>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>

            <?php if( houzez_option('agent_view_listing') != 0 ) { ?>
            <a class="btn btn-primary btn-slim" href="<?php echo esc_url($args[ 'link' ]); ?>" target="_blank"><?php echo houzez_option('spl_con_view_listings', 'View listings'); ?></a>
            <?php } ?>
        </div><!-- agent-details -->

        <?php
        $data = ob_get_contents();
        ob_clean();

        return $data;

    }
}

if ( !function_exists( 'houzez_get_agent_info_top' ) ) {
    /**
     * @deprecated 2.0.0 Use houzez_render_agent_info() instead
     */
    function houzez_get_agent_info_top($args, $type, $is_single = true) {
        global $ele_settings;
        $view_listing_link = isset($ele_settings['view_listing']) ? $ele_settings['view_listing'] : 'yes';
        
        $view_listing = houzez_option('agent_view_listing');
        $agent_phone_num = houzez_option('agent_phone_num');

        if( empty($args['agent_name']) ) {
            return '';
        }

        if ($type == 'for_grid_list') {
            return '<a href="' . $args['link'] . '">' . $args['agent_name'] . '</a> ';

        } elseif ($type == 'agent_form') {
            $output = '';

            $output .= '<div class="agent-details" role="region">';
                $output .= '<div class="d-flex align-items-center gap-3">';
                    
                    $output .= '<div class="agent-image" role="img">';
                        
                        if ( $is_single == false ) {
                            $output .= '<input type="checkbox" class="houzez-hidden" checked="checked" class="multiple-agent-check" name="target_email[]" value="' . $args['agent_email'] . '" >';
                        }

                        $output .= '<img class="rounded" src="' . $args['picture'] . '" alt="' . $args['agent_name'] . '" width="70" height="70">';
                        if($args['verified'] == 1) {
                            $output .= '<span class="badge btn-secondary agent-verified-icon"><i class="houzez-icon icon-check-circle-1"></i></span>';
                        }

                    $output .= '</div>';

                    $output .= '<ul class="agent-information list-unstyled mb-0" role="list">';

                        if (!empty($args['agent_name'])) {
                            $output .= '<li class="agent-name" role="listitem">';
                                $output .= '<i class="houzez-icon icon-single-neutral me-1"></i> '.$args['agent_name'];
                            $output .= '</li>';
                        }
                        
                        if ( $is_single == false && !empty($args['agent_mobile'])) {
                            $output .= '<li class="agent-phone agent-phone-hidden" role="listitem">';
                                $output .= '<i class="houzez-icon icon-phone me-1"></i> ' . esc_attr($args['agent_mobile']);
                            $output .= '</li>';
                        }

                        
                        if($view_listing != 0 && $view_listing_link == 'yes') {
                            $output .= '<li class="agent-link" role="listitem">';
                                $output .= '<a href="' . $args['link'] . '">' . houzez_option('spl_con_view_listings', 'View listings') . '</a>';
                            $output .= '</li>';
                        }


                    $output .= '</ul>';
                $output .= '</div>';
            $output .= '</div>';

            return $output;
        }
    }
}

/**
 * Backward compatibility wrapper for the old function name
 * @deprecated 2.0.0 Use houzez20_get_property_agent() instead
 */
if(!function_exists('houzez20_property_contact_form')) {
    function houzez20_property_contact_form($is_top = true, $luxury = false) {
        return houzez20_get_property_agent($is_top, $luxury);
    }
}

if(!function_exists('houzez20_get_property_agent')) {
    function houzez20_get_property_agent($is_top = true, $luxury = false) {
        global $post;
        
        // Validate global post object
        if (empty($post) || !is_object($post)) {
            return array();
        }
        
        $agent_display = houzez_get_listing_data('agent_display_option');
        
        // Early return if no agent display
        if ($agent_display == 'none') {
            return array();
        }
        
        $result = array();
        $listing_agent_info = array();
        $listing_agent = '';
        $is_single_agent = true;
        
        // Process based on agent display type
        switch ($agent_display) {
            case 'agent_info':
                $result = houzez20_process_agents_info($is_top, $luxury, $listing_agent_info, $listing_agent, $is_single_agent);
                break;
                
            case 'agency_info':
                $result = houzez20_process_agency_info($is_top, $luxury, $listing_agent_info, $listing_agent);
                break;
                
            default:
                $result = houzez20_process_author_info($is_top, $luxury, $listing_agent_info, $listing_agent);
                break;
        }
        
        // Build final return array
        $return_array = array(
            'agent_info' => $listing_agent_info,
            'agent_data' => $listing_agent,
            'is_single_agent' => $is_single_agent,
            'agent_type' => $agent_display
        );
        
        // Add primary agent data if available
        if (!empty($result)) {
            $return_array = array_merge($return_array, $result);
        }
        
        return $return_array;
    }
}

/**
 * Process multiple agents information
 */
if (!function_exists('houzez20_process_agents_info')) {
    function houzez20_process_agents_info($is_top, $luxury, &$listing_agent_info, &$listing_agent, &$is_single_agent) {
        
        $agents_ids = houzez_get_listing_data('agents', false);
        
        // Filter and validate agent IDs
        $valid_agent_ids = array_filter(array_unique($agents_ids), function($id) {
            return is_numeric($id) && $id > 0;
        });
        
        if (empty($valid_agent_ids)) {
            return array();
        }
        
        $agents_count = count($valid_agent_ids);
        $is_single_agent = ($agents_count <= 1);
        $primary_agent_data = array();
        
        foreach ($valid_agent_ids as $agent_id) {
            $agent_id = intval($agent_id);
            // WPML Workaround for compsupp-7949
            $agent_id = apply_filters( 'wpml_object_id', $agent_id, 'houzez_agent', TRUE );
            
            // Get agent data using our improved helper
            $agent_data = houzez20_get_contact_data($agent_id, 'agent');
            
            if (empty($agent_data)) {
                continue;
            }
            
            $listing_agent_info[] = $agent_data;
            
            // Generate HTML output
            $listing_agent .= houzez20_generate_agent_html($agent_data, $is_top, $luxury, $is_single_agent, 'agent_form');
            
            // Store first agent as primary
            if (empty($primary_agent_data)) {
                $primary_agent_data = $agent_data;
            }
        }
        
        return houzez20_extract_primary_agent_data($primary_agent_data);
    }
}

/**
 * Process agency information
 */
if (!function_exists('houzez20_process_agency_info')) {
    function houzez20_process_agency_info($is_top, $luxury, &$listing_agent_info, &$listing_agent) {
        
        $agency_id = get_post_meta(get_the_ID(), 'fave_property_agency', true);
        
        if (empty($agency_id) || !is_numeric($agency_id)) {
            return array();
        }
        
        $agency_id = intval($agency_id);
        // WPML Workaround for compsupp-7949
        $agency_id = apply_filters( 'wpml_object_id', $agency_id, 'houzez_agency', TRUE );
        
        // Get agency data using our improved helper
        $agency_data = houzez20_get_contact_data($agency_id, 'agency');
        
        if (empty($agency_data)) {
            return array();
        }
        
        $listing_agent_info[] = $agency_data;
        
        // Generate HTML output
        $listing_agent .= houzez20_generate_agent_html($agency_data, $is_top, $luxury, true, 'agent_form');
        
        return houzez20_extract_primary_agent_data($agency_data);
    }
}

/**
 * Process author information
 */
if (!function_exists('houzez20_process_author_info')) {
    function houzez20_process_author_info($is_top, $luxury, &$listing_agent_info, &$listing_agent) {
        global $post;
        
        $author_id = $post->post_author;
        
        if (empty($author_id)) {
            return array();
        }
        
        // Get author data using our improved helper
        $author_data = houzez20_get_author_contact_data($author_id);
        
        if (empty($author_data)) {
            return array();
        }
        
        $listing_agent_info[] = $author_data;
        
        // Generate HTML output
        $listing_agent .= houzez20_generate_agent_html($author_data, $is_top, $luxury, true, 'agent_form');
        
        return houzez20_extract_primary_agent_data($author_data);
    }
}

/**
 * Get contact data for agent or agency
 */
if (!function_exists('houzez20_get_contact_data')) {
    function houzez20_get_contact_data($contact_id, $type = 'agent') {
        
        if (empty($contact_id) || !is_numeric($contact_id)) {
            return array();
        }
        
        $contact_id = intval($contact_id);
        $prefix = ($type === 'agent') ? 'fave_agent_' : 'fave_agency_';
        
        // Define meta fields to retrieve
        $meta_fields = array(
            'office_num' => $prefix . ($type === 'agent' ? 'office_num' : 'phone'),
            'mobile' => $prefix . 'mobile',
            'email' => $prefix . 'email',
            'whatsapp' => $prefix . 'whatsapp',
            'telegram' => $prefix . 'telegram',
            'line_id' => $prefix . 'line_id',
            'verified' => $prefix . 'verified',
            'linkedin' => $prefix . 'linkedin',
            'skype' => $prefix . 'skype',
            'facebook' => $prefix . 'facebook',
            'twitter' => $prefix . 'twitter',
            'googleplus' => $prefix . 'googleplus',
            'youtube' => $prefix . 'youtube',
            'instagram' => $prefix . 'instagram'
        );
        
        // Add agent-specific fields
        if ($type === 'agent') {
            $meta_fields['position'] = $prefix . 'position';
            $meta_fields['company'] = $prefix . 'company';
            $meta_fields['tax_no'] = $prefix . 'tax_no';
        }
        
        $meta_fields['service_area'] = $prefix . 'service_area';
        $meta_fields['specialties'] = $prefix . 'specialties';
        
        // Retrieve all meta data
        $contact_meta = array();
        foreach ($meta_fields as $key => $meta_key) {
            $contact_meta[$key] = get_post_meta($contact_id, $meta_key, true);
        }
        
        // Get contact picture
        $picture = houzez_get_contact_picture($contact_id, $type);
        
        // Build contact data array
        $contact_data = array(
            'agent_id' => $contact_id,
            'agent_type' => $type . '_info',
            'agent_name' => get_the_title($contact_id),
            'agent_mobile' => $contact_meta['mobile'],
            'agent_mobile_call' => houzez_clean_phone_number($contact_meta['mobile']),
            'agent_phone' => $contact_meta['office_num'],
            'agent_phone_call' => houzez_clean_phone_number($contact_meta['office_num']),
            'agent_email' => $contact_meta['email'],
            'agent_whatsapp' => $contact_meta['whatsapp'],
            'agent_whatsapp_call' => houzez_clean_phone_number($contact_meta['whatsapp']),
            'telegram' => $contact_meta['telegram'],
            'lineapp' => $contact_meta['line_id'],
            'verified' => $contact_meta['verified'],
            'link' => get_permalink($contact_id),
            'picture' => $picture,
            'linkedin' => $contact_meta['linkedin'],
            'agent_skype' => $contact_meta['skype'],
            'facebook' => $contact_meta['facebook'],
            'twitter' => $contact_meta['twitter'],
            'googleplus' => $contact_meta['googleplus'],
            'youtube' => $contact_meta['youtube'],
            'instagram' => $contact_meta['instagram'],
            'agent_service_area' => $contact_meta['service_area'],
            'agent_specialties' => $contact_meta['specialties']
        );
        
        // Add agent-specific fields
        if ($type === 'agent') {
            $contact_data['agent_position'] = $contact_meta['position'];
            $contact_data['agent_company'] = $contact_meta['company'];
            $contact_data['agent_tax_no'] = $contact_meta['tax_no'];
            } else {
            $contact_data['agent_position'] = '';
            $contact_data['agent_company'] = '';
            $contact_data['agent_tax_no'] = '';
        }
        
        return $contact_data;
    }
}

/**
 * Get author contact data
 */
if (!function_exists('houzez20_get_author_contact_data')) {
    function houzez20_get_author_contact_data($author_id) {
        
        if (empty($author_id) || !is_numeric($author_id)) {
            return array();
        }
        
        $author_id = intval($author_id);
        
        // Define author meta fields
        $author_fields = array(
            'display_name', 'fave_author_phone', 'fave_author_mobile', 
            'fave_author_whatsapp', 'email', 'fave_author_telegram', 
            'fave_author_line_id', 'fave_author_linkedin', 'fave_author_skype',
            'fave_author_facebook', 'fave_author_twitter', 'fave_author_googleplus',
            'fave_author_youtube', 'fave_author_instagram', 'fave_author_title',
            'fave_author_company', 'fave_author_service_areas', 'fave_author_specialties',
            'fave_author_tax_no', 'houzez_verification_status'
        );
        
        // Retrieve author meta data
        $author_meta = array();
        foreach ($author_fields as $field) {
            $author_meta[$field] = get_the_author_meta($field, $author_id);
        }
        
        // Check verification status
        $verified = ($author_meta['houzez_verification_status'] === 'approved') ? 1 : 0;
        
        // Get author picture
        $picture = houzez_get_author_picture($author_id);
        
        // Build author data array
        return array(
            'agent_id' => $author_id,
            'agent_type' => 'author_info',
            'agent_name' => $author_meta['display_name'],
            'agent_mobile' => $author_meta['fave_author_mobile'],
            'agent_mobile_call' => houzez_clean_phone_number($author_meta['fave_author_mobile']),
            'agent_phone' => $author_meta['fave_author_phone'],
            'agent_phone_call' => houzez_clean_phone_number($author_meta['fave_author_phone']),
            'agent_email' => $author_meta['email'],
            'agent_whatsapp' => $author_meta['fave_author_whatsapp'],
            'agent_whatsapp_call' => houzez_clean_phone_number($author_meta['fave_author_whatsapp']),
            'telegram' => $author_meta['fave_author_telegram'],
            'lineapp' => $author_meta['fave_author_line_id'],
            'verified' => $verified,
            'link' => get_author_posts_url($author_id),
            'picture' => $picture,
            'linkedin' => $author_meta['fave_author_linkedin'],
            'agent_skype' => $author_meta['fave_author_skype'],
            'facebook' => $author_meta['fave_author_facebook'],
            'twitter' => $author_meta['fave_author_twitter'],
            'googleplus' => $author_meta['fave_author_googleplus'],
            'youtube' => $author_meta['fave_author_youtube'],
            'instagram' => $author_meta['fave_author_instagram'],
            'agent_position' => $author_meta['fave_author_title'],
            'agent_company' => $author_meta['fave_author_company'],
            'agent_service_area' => $author_meta['fave_author_service_areas'],
            'agent_specialties' => $author_meta['fave_author_specialties'],
            'agent_tax_no' => $author_meta['fave_author_tax_no']
        );
    }
}

/**
 * Generate agent HTML output
 */
if (!function_exists('houzez20_generate_agent_html')) {
    function houzez20_generate_agent_html($agent_data, $is_top, $luxury, $is_single_agent, $form_type) {
        
        if (empty($agent_data)) {
            return '';
        }
        
        // Use the new unified renderer
        $layout = $is_top ? 'top' : ($luxury ? 'bottom_v2' : 'bottom');
        return houzez_render_agent_info($agent_data, $form_type, $is_single_agent, $layout);
    }
}

/**
 * Extract primary agent data for return array
 */
if (!function_exists('houzez20_extract_primary_agent_data')) {
    function houzez20_extract_primary_agent_data($agent_data) {
        
        if (empty($agent_data)) {
            return array();
        }
        
        return array(
            'agent_email' => $agent_data['agent_email'] ?? '',
            'agent_name' => $agent_data['agent_name'] ?? '',
            'verified' => $agent_data['verified'] ?? 0,
            'linkedin' => $agent_data['linkedin'] ?? '',
            'agent_phone' => $agent_data['agent_phone'] ?? '',
            'agent_phone_call' => $agent_data['agent_phone_call'] ?? '',
            'agent_mobile' => $agent_data['agent_mobile'] ?? '',
            'agent_mobile_call' => $agent_data['agent_mobile_call'] ?? '',
            'agent_whatsapp' => $agent_data['agent_whatsapp'] ?? '',
            'agent_whatsapp_call' => $agent_data['agent_whatsapp_call'] ?? '',
            'agent_telegram' => $agent_data['telegram'] ?? '',
            'agent_lineapp' => $agent_data['lineapp'] ?? '',
            'picture' => $agent_data['picture'] ?? '',
            'link' => $agent_data['link'] ?? '',
            'agent_id' => $agent_data['agent_id'] ?? 0
        );
    }
}

// houzez_property_clone
add_action( 'wp_ajax_houzez_property_clone', 'houzez_property_clone' );

if ( !function_exists( 'houzez_property_clone' ) ) {
    function houzez_property_clone() {

        // Check if propID is provided
        if ( ! isset( $_POST['propID'] ) ) {
            wp_send_json_error( esc_html__( 'No property ID provided!', 'houzez' ) );
            wp_die();
        }

        // Verify nonce for security
        if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'clone_property_nonce' ) ) {
            wp_send_json_error( esc_html__( 'Security check failed!', 'houzez' ) );
            wp_die();
        }

        global $wpdb;
        $userID = get_current_user_id();
        $new_post_author = $userID;
        $post_id = absint( $_POST['propID'] );

        // Validate post_id
        if ( ! $post_id || get_post_type( $post_id ) !== 'property' ) {
            wp_send_json_error( esc_html__( 'Invalid property ID!', 'houzez' ) );
            wp_die();
        }

        // Get the post author
        $post_author = get_post_field( 'post_author', $post_id );

        // Get agency agents associated with the current user
        $agencyAgentsArray = array();
        $agencyAgents = houzez_get_agency_agents( $userID );
        if ( $agencyAgents ) {
            $agencyAgentsArray = $agencyAgents;
        }

        // Check if the current user has permission to edit the property
        if (
            $post_author != $userID &&
            ! houzez_is_admin() &&
            ! houzez_is_editor() &&
            ! in_array( $post_author, $agencyAgentsArray )
        ) {
            wp_send_json_error( esc_html__( 'You do not have permission to edit this property!', 'houzez' ) );
            wp_die();
        }

        $post = get_post( $post_id );

        if (isset( $post ) && $post != null) {

            /*
             * new post data array
             */
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status'    => $post->ping_status,
                'post_author'    => $new_post_author,
                'post_content'   => $post->post_content,
                'post_excerpt'   => $post->post_excerpt,
                'post_name'      => $post->post_name,
                'post_parent'    => $post->post_parent,
                'post_password'  => $post->post_password,
                'post_status'    => 'draft',
                'post_title'     => $post->post_title,
                'post_type'      => $post->post_type,
                'to_ping'        => $post->to_ping,
                'menu_order'     => $post->menu_order
            );

            /*
             * insert the post by wp_insert_post() function
             */
            $new_post_id = wp_insert_post( $args );

            /*
             * get all current post terms ad set them to the new post draft
             */
            $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
            foreach ($taxonomies as $taxonomy) {
                $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
            }

            /*
             * duplicate all post meta just in two SQL queries
             */
            $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
            if (count($post_meta_infos)!=0) {
                $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                foreach ($post_meta_infos as $meta_info) {
                    $meta_key = $meta_info->meta_key;
                    $meta_value = addslashes($meta_info->meta_value);
                    $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                }
                $sql_query.= implode(" UNION ALL ", $sql_query_sel);
                $wpdb->query($sql_query);
            }

            update_post_meta( $new_post_id, 'fave_featured', 0 );
            update_post_meta( $new_post_id, 'houzez_featured_listing_date', '' );
            update_post_meta( $new_post_id, 'fave_payment_status', 'not_paid' );

            if( houzez_option('auto_property_id', 0) != 0 ) {
                $pattern = houzez_option( 'property_id_pattern' );
                $new_id   = preg_replace( '/{ID}/', $new_post_id, $pattern );
                update_post_meta($new_post_id, 'fave_property_id', $new_id);
            }

            $dashboard_listings = houzez_get_template_link_2('template/user_dashboard_properties.php');
            $dashboard_listings = add_query_arg( 'cloned', 1, $dashboard_listings );

            wp_send_json_success( array(
                'redirect' => esc_url( $dashboard_listings ),
                'message'  => esc_html__( 'Successfully cloned', 'houzez' )
            ) );
            wp_die();

        } else {
            wp_send_json_error( esc_html__( 'Post creation failed, could not find original post:', 'houzez' ) );
            wp_die();
        }

    }
}

// houzez_property_on_hold
add_action( 'wp_ajax_houzez_property_on_hold', 'houzez_property_on_hold' );

if ( !function_exists( 'houzez_property_on_hold' ) ) {
    function houzez_property_on_hold() {

        // Check if propID is provided
        if ( ! isset( $_POST['propID'] ) ) {
            wp_send_json_error( esc_html__( 'No property ID provided!', 'houzez' ) );
            wp_die();
        }

        // Verify nonce for security
        if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'puthold_property_nonce' ) ) {
            wp_send_json_error( esc_html__( 'Security check failed!', 'houzez' ) );
            wp_die();
        }

        global $wpdb;
        $userID = get_current_user_id();
        $post_id = absint( $_POST['propID'] );

        // Validate post_id
        if ( ! $post_id || get_post_type( $post_id ) !== 'property' ) {
            wp_send_json_error( esc_html__( 'Invalid property ID!', 'houzez' ) );
            wp_die();
        }

        // Get the post author
        $post_author = get_post_field( 'post_author', $post_id );

        // Get agency agents associated with the current user
        $agencyAgentsArray = array();
        $agencyAgents = houzez_get_agency_agents( $userID );
        if ( $agencyAgents ) {
            $agencyAgentsArray = $agencyAgents;
        }

        // Check if the current user has permission to edit the property
        if (
            $post_author != $userID &&
            ! houzez_is_admin() &&
            ! houzez_is_editor() &&
            ! in_array( $post_author, $agencyAgentsArray )
        ) {
            wp_send_json_error( esc_html__( 'You do not have permission to edit this property!', 'houzez' ) );
            wp_die();
        }

        $post_status = get_post_status( $post_id );

        if($post_status == 'publish') { 
            $post = array(
                'ID'            => $post_id,
                'post_status'   => 'on_hold'
            );
        } elseif ($post_status == 'on_hold') {
            $post = array(
                'ID'            => $post_id,
                'post_status'   => 'publish'
            );
        } else {
            wp_send_json_error( esc_html__( 'Invalid post status!', 'houzez' ) );
            wp_die();
        }

        $updated_post_id = wp_update_post( $post );

        if ( is_wp_error( $updated_post_id ) ) {
            wp_send_json_error( esc_html__( 'Failed to update property status!', 'houzez' ) );
            wp_die();
        } else {
            wp_send_json_success( esc_html__( 'Property status updated successfully!', 'houzez' ) );
            wp_die();
        }

    }
}


// AJAX action registration.
add_action( 'wp_ajax_houzez_property_mark_sold', 'houzez_property_mark_sold' );

if ( ! function_exists( 'houzez_property_mark_sold' ) ) {
    function houzez_property_mark_sold() {
        // Check if the property ID is provided.
        if ( ! isset( $_POST['propID'] ) ) {
            wp_send_json_error( esc_html__( 'No property ID provided!', 'houzez' ) );
        }

        // Verify nonce for security.
        if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'sold_property_nonce' ) ) {
            wp_send_json_error( esc_html__( 'Security check failed!', 'houzez' ) );
        }

        $user_id = get_current_user_id();
        $prop_id = absint( $_POST['propID'] );

        // Process the "mark sold" action.
        $result = houzez_process_property_mark_sold( $prop_id, $user_id );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'msg' => $result->get_error_message() ) );
        } else {
            wp_send_json_success( $result );
        }
    }
}

/**
 * Process marking a property as sold or unsold.
 *
 * @param int $prop_id Property ID.
 * @param int $user_id Current user ID.
 * @return array|WP_Error Result array on success, WP_Error on failure.
 */
if( ! function_exists('houzez_process_property_mark_sold') ) {
    function houzez_process_property_mark_sold( $prop_id, $user_id ) {
        // Sanitize and validate the property ID.
        $prop_id = absint( $prop_id );
        if ( ! $prop_id || 'property' !== get_post_type( $prop_id ) ) {
            return new WP_Error( 'invalid_property', esc_html__( 'Invalid property ID!', 'houzez' ) );
        }

        // Get the property author.
        $post_author = get_post_field( 'post_author', $prop_id );

        // Retrieve the agency agents associated with the current user.
        $agency_agents = houzez_get_agency_agents( $user_id );
        if ( ! is_array( $agency_agents ) ) {
            $agency_agents = array();
        }

        // Check if the current user has permission to edit this property.
        if ( $post_author != $user_id && ! houzez_is_admin() && ! houzez_is_editor() && ! in_array( $post_author, $agency_agents, true ) ) {
            return new WP_Error( 'permission_denied', esc_html__( 'You do not have permission to edit this property!', 'houzez' ) );
        }

        // Get current post status.
        $post_status = get_post_status( $prop_id );
        $post_update = array( 'ID' => $prop_id );

        if ( 'publish' === $post_status ) {
            // Mark property as sold.
            $post_update['post_status'] = 'houzez_sold';

            // Optionally update the property status taxonomy.
            $mark_sold_status = houzez_option( 'mark_sold_status' );
            if ( '' !== $mark_sold_status ) {
                $mark_sold_status = intval( $mark_sold_status );
                wp_set_object_terms( $prop_id, $mark_sold_status, 'property_status' );
            }
        } elseif ( 'houzez_sold' === $post_status ) {
            // Revert sold property back to published.
            $post_update['post_status'] = 'publish';
        } else {
            return new WP_Error( 'invalid_status', esc_html__( 'Invalid post status!', 'houzez' ) );
        }

        $updated_post_id = wp_update_post( $post_update );
        if ( is_wp_error( $updated_post_id ) ) {
            return new WP_Error( 'update_failed', esc_html__( 'Failed to update property status!', 'houzez' ) );
        }

        return array(
            'success' => true,
            'message'     => esc_html__( 'Property status updated successfully!', 'houzez' ),
        );
    }
}


/*-----------------------------------------------------------------------------------*/
/*  Houzez Invoice Print Property - deprecated since v3.5.0
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_houzez_create_invoice_print', 'houzez_create_invoice_print' );
add_action( 'wp_ajax_houzez_create_invoice_print', 'houzez_create_invoice_print' );

if ( !function_exists( 'houzez_create_invoice_print' ) ) {
    function houzez_create_invoice_print() {

        if(!isset($_POST['invoice_id'])|| !is_numeric($_POST['invoice_id'])){
            exit();
        }

        $houzez_local = houzez_get_localization();
        $invoice_id = intval($_POST['invoice_id']);
        $the_post= get_post( $invoice_id );

        if( $the_post->post_type != 'houzez_invoice' || $the_post->post_status != 'publish' ) {
            exit();
        }

        print  '<html><head><link href="'.get_stylesheet_uri().'" rel="stylesheet" type="text/css" />';
        print  '<html><head><link href="'.get_template_directory_uri().'/css/bootstrap.min.css" rel="stylesheet" type="text/css" />';
        print  '<html><head><link href="'.get_template_directory_uri().'/css/main.css" rel="stylesheet" type="text/css" />';

        if( is_rtl() ) {
            print '<link href="'.get_template_directory_uri().'/css/rtl.css" rel="stylesheet" type="text/css" />';
            print '<link href="'.get_template_directory_uri().'/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />';
        }
        print '</head>';
        print  '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script>$(window).load(function(){ print(); });</script>';
        print  '<body class="print-page">';

        // Get user ID from the invoice post meta or similar
        $user_id_from_invoice = get_post_meta($invoice_id, 'HOUZEZ_invoice_buyer', true);

        // Get user info by user ID
        $user_info = get_userdata($user_id_from_invoice);

        // Check if user exists
        if ($user_info) {
            $userID = $user_info->ID;
            $user_login = $user_info->user_login;
            $user_email = $user_info->user_email;
            $first_name = $user_info->first_name;
            $last_name = $user_info->last_name;
        } 

        $user_address = get_user_meta( $userID, 'fave_author_address', true);
        if( !empty($first_name) && !empty($last_name) ) {
            $fullname = $first_name.' '.$last_name;
        } else {
            $fullname = $user_info->display_name;
        }
        $invoice_id = $_REQUEST['invoice_id'];
        $post = get_post( $invoice_id );
        $invoice_data = houzez_get_invoice_meta( $invoice_id );

        $publish_date = $post->post_date;
        $publish_date = date_i18n( get_option('date_format'), strtotime( $publish_date ) );
        $invoice_logo = houzez_option( 'invoice_logo', false, 'url' );
        $invoice_company_name = houzez_option( 'invoice_company_name' );
        $invoice_address = houzez_option( 'invoice_address' );
        $invoice_phone = houzez_option( 'invoice_phone' );
        $invoice_additional_info = houzez_option( 'invoice_additional_info' );
        $invoice_thankyou = houzez_option( 'invoice_thankyou' );
        ?>
        <div class="print-main-wrap">
            <div class="print-wrap">
                <div class="invoice-wrap">
                    <div class="row">
                        <div class="col-md-9 col-sm-12">
                            <div class="invoice-logo mb-3">
                                <div class="logo">
                                    <?php if( !empty($invoice_logo) ) { ?>
                                        <img src="<?php echo esc_url($invoice_logo); ?>" alt="logo">
                                    <?php } ?>
                                </div>
                            </div>
                        </div><!-- col-md-9 col-sm-12 -->
                        <div class="col-md-3 col-sm-12">
                            <div class="invoice-date mb-3">
                                <ul class="list-unstyled">
                                    <li>
                                        <strong><?php esc_html_e('Invoice', 'houzez'); ?>:</strong> 
                                        <?php echo esc_attr($invoice_id); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e('Date', 'houzez'); ?>:</strong> 
                                        <?php echo $publish_date; ?>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- col-md-3 col-sm-12 -->
                    </div><!-- row -->

                    <div class="invoice-spacer mb-5"></div>
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <ul class="list-unstyled">
                                <li><strong><?php esc_html_e('To:', 'houzez'); ?>:</strong></li>
                                <li><?php echo esc_attr($fullname); ?></li>
                                <?php if( !empty($user_address)) { ?>
                                <li><?php echo esc_attr($user_address); ?></li>
                                <?php } ?>

                                <li><?php esc_html_e('Email:', 'houzez'); ?> <?php echo esc_attr($user_email);?></li>
                            </ul>
                        </div><!-- col-md-6 col-sm-12 -->
                        <div class="col-md-6 col-sm-12">
                            <ul class="list-unstyled">
                                
                                <?php if( !empty($invoice_company_name) ) { ?>
                                <li> 
                                    <strong> <?php echo esc_attr($invoice_company_name); ?>:</strong>
                                </li>
                                <?php } ?>

                                <?php if( !empty($invoice_address) ) { ?>
                                <li><?php echo ($invoice_address); ?></li>
                                <?php } ?>

                                <?php if( !empty($invoice_phone) ) { ?>
                                <li><?php esc_html_e('Phone', 'houzez'); ?>: <?php echo esc_attr($invoice_phone); ?></li>
                                <?php } ?>
                            </ul>

                        </div><!-- col-md-6 col-sm-12 -->
                    </div><!-- row -->

                    <div class="invoice-spacer mb-5"></div>

                    <div class="invoce-content">
                        <ul class="list-unstyled">
                            <li>
                                <strong><?php echo $houzez_local['billing_for']; ?></strong> 
                                <span>
                                    <?php
                                    if( $invoice_data['invoice_billion_for'] != 'package' && $invoice_data['invoice_billion_for'] != 'Package' ) {
                                        echo esc_html($invoice_data['invoice_billion_for']);
                                    } else {
                                        echo esc_html__('Membership Plan', 'houzez').' '. get_the_title( get_post_meta( $invoice_id, 'HOUZEZ_invoice_item_id', true) );
                                    }
                                    ?>
                                </span>
                            </li>

                            <li>
                                <strong><?php echo $houzez_local['billing_type']; ?></strong> 
                                <span><?php echo esc_html( $invoice_data['invoice_billing_type'] ); ?></span>
                            </li>

                            <li>
                                <strong><?php echo $houzez_local['payment_method']; ?></strong> 
                                <span>
                                    <?php if( $invoice_data['invoice_payment_method'] == 'Direct Bank Transfer' ) {
                                        echo $houzez_local['bank_transfer'];
                                    } else {
                                        echo $invoice_data['invoice_payment_method'];
                                    } ?>
                                </span>
                            </li>

                            <li>
                                <strong><?php echo $houzez_local['invoice_price']; ?>:</strong> 
                                <span><?php echo houzez_get_invoice_price( $invoice_data['invoice_item_price'] )?></span>
                            </li>
                        </ul>
                    </div><!-- invoce-content -->

                    <div class="invoice-spacer mb-5"></div>
                    
                    <?php if( !empty($invoice_additional_info) || !empty($invoice_thankyou) ) { ?>
        
                        <?php if( !empty($invoice_additional_info)) { ?>
                        <div class="invoce-information">
                            <p><strong><?php echo esc_html__('Additional Information:', 'houzez'); ?>:</strong></p>
                            <p><?php echo $invoice_additional_info; ?> </p>
                        </div><!-- invoce-information -->
                        <?php } ?>
                    
                    <div class="invoice-spacer mb-5"></div>

                    <p><strong><?php echo $invoice_thankyou; ?></strong></p>
                    <?php } ?>

                </div><!-- invoice-wrap -->


            </div><!-- print-wrap -->
        </div><!-- print-main-wrap -->
        
        <?php

        print '</body></html>';
        wp_die();
    }
}


/* --------------------------------------------------------------------------
** Property delete ajax - deprecated from v3.5.0
** --------------------------------------------------------------------------- */
// Add action for AJAX delete property
add_action( 'wp_ajax_houzez_delete_property', 'houzez_delete_property' );

if ( ! function_exists( 'houzez_delete_property' ) ) {

    function houzez_delete_property() {
        
        // Verify nonce for security
        if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'delete_my_property_nonce' ) ) {
            wp_send_json_error( esc_html__( 'Security check failed!', 'houzez' ) );
            wp_die();
        }

        // Check if prop_id is provided
        if ( ! isset( $_POST['prop_id'] ) ) {
            wp_send_json_error( esc_html__( 'No Property ID found', 'houzez' ) );
            wp_die();
        }

        $propID = absint( $_POST['prop_id'] );

        if ( ! $propID ) {
            wp_send_json_error( esc_html__( 'Invalid Property ID', 'houzez' ) );
            wp_die();
        }

        // Check if the post exists and is of the correct post type
        $post = get_post( $propID );
        if ( ! $post || $post->post_type !== 'property' ) {
            wp_send_json_error( esc_html__( 'Property not found', 'houzez' ) );
            wp_die();
        }

        $userID = get_current_user_id();

        // Get the package user ID
        $packageUserId = $userID;
        $agent_agency_id = houzez_get_agent_agency_id( $userID );
        if ( $agent_agency_id ) {
            $packageUserId = $agent_agency_id;
        }

        // Get agency agents associated with the user
        $agencyAgentsArray = array();
        $agencyAgents = houzez_get_agency_agents( $userID );
        if ( $agencyAgents ) {
            $agencyAgentsArray = $agencyAgents;
        }

        // Get the post author
        $post_author = $post->post_author;

        // Check if the user has permission to delete the property
        if ( $post_author == $userID || houzez_is_admin() || houzez_is_editor() || in_array( $post_author, $agencyAgentsArray ) ) {

            // Delete property attachments if post status is not 'draft'
            if ( get_post_status( $propID ) != 'draft' ) {
                houzez_delete_property_attachments_frontend( $propID );
            }

            // Delete the post
            $deleted = wp_delete_post( $propID, true ); // true to force delete, bypass trash

            if ( ! $deleted ) {
                wp_send_json_error( esc_html__( 'Failed to delete property', 'houzez' ) );
                wp_die();
            }

            // Increment package listings count
            houzez_plusone_package_listings( $packageUserId );

            // Build dashboard listings URL
            $dashboard_listings = houzez_get_template_link_2( 'template/user_dashboard_properties.php' );
            $dashboard_listings = add_query_arg( 'deleted', 1, $dashboard_listings );

            wp_send_json_success( array(
                'redirect' => esc_url( $dashboard_listings ),
                'message'  => esc_html__( 'Property Deleted', 'houzez' )
            ) );
            wp_die();

        } else {
            wp_send_json_error( esc_html__( 'Permission denied', 'houzez' ) );
            wp_die();
        }

    }

}


/**
 * AJAX handler for deleting one or more properties,
 * and crediting the appropriate package owner (user or agency).
 */
add_action( 'wp_ajax_houzez_delete_properties', 'houzez_delete_properties' );

if ( ! function_exists( 'houzez_delete_properties' ) ) {
    function houzez_delete_properties() {
        // 1) Security check
        $nonce = isset( $_POST['security'] ) ? sanitize_text_field( $_POST['security'] ) : '';
        if ( ! wp_verify_nonce( $nonce, 'delete_properties_nonce' ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Security check failed!', 'houzez' ) ] );
        }

        // 2) Collect & sanitize IDs
        if ( empty( $_POST['property_ids'] ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'No properties selected for deletion.', 'houzez' ) ] );
        }
        $ids = $_POST['property_ids'];
        if ( ! is_array( $ids ) ) {
            $ids = [ $ids ];
        }
        $ids = array_map( 'absint', $ids );

        // 3) Prepare user context
        $current_user     = get_current_user_id();
        $agency_agents    = houzez_get_agency_agents( $current_user ) ?: [];
        $deleted_count    = 0;
        $errors           = [];

        // 4) Loop & delete each
        foreach ( $ids as $prop_id ) {
            $post = get_post( $prop_id );

            if ( ! $post || $post->post_type !== 'property' ) {
                $errors[] = sprintf( esc_html__( 'Property ID %d not found.', 'houzez' ), $prop_id );
                continue;
            }

            $author = (int) $post->post_author;
            $can_delete = 
                $author === $current_user ||
                houzez_is_admin() ||
                houzez_is_editor() ||
                in_array( $author, $agency_agents );

            if ( ! $can_delete ) {
                $errors[] = sprintf( esc_html__( 'No permission to delete property ID %d.', 'houzez' ), $prop_id );
                continue;
            }

            // if not draft, remove frontend attachments
            if ( get_post_status( $prop_id ) !== 'draft' ) {
                houzez_delete_property_attachments_frontend( $prop_id );
            }

            // delete the post
            if ( wp_delete_post( $prop_id, true ) ) {
                $deleted_count++;
            } else {
                $errors[] = sprintf( esc_html__( 'Failed to delete property ID %d.', 'houzez' ), $prop_id );
            }
        }

        // 5) Build response
        if ( $deleted_count > 0 ) {
            // Determine package owner (user vs agency)
            $packageUserId   = $current_user;
            $agent_agency_id = houzez_get_agent_agency_id( $current_user );
            if ( $agent_agency_id ) {
                $packageUserId = $agent_agency_id;
            }

            // Credit listings back for each deletion
            for ( $i = 0; $i < $deleted_count; $i++ ) {
                houzez_plusone_package_listings( $packageUserId );
            }

            // Success message
            $message = sprintf(
                _n( '%d property deleted.', '%d properties deleted.', $deleted_count, 'houzez' ),
                $deleted_count
            );
            if ( $errors ) {
                $message .= ' ' . implode( ' ', $errors );
            }

            wp_send_json_success( [ 'message' => $message ] );
        } else {
            // Nothing deleted
            $err_msg = $errors ? implode( ' ', $errors ) : esc_html__( 'No properties were deleted.', 'houzez' );
            wp_send_json_error( [ 'message' => $err_msg ] );
        }
    }
}


/* --------------------------------------------------------------------------
** Property load more - Deprecated since v3.5.0
** --------------------------------------------------------------------------- 
*/
if ( !function_exists( 'houzez_loadmore_properties' ) ) {
    function houzez_loadmore_properties() {
        return Houzez_Data_Source::loadmore_properties();
    }
}


if(!function_exists('houzez_get_custom_add_listing_field')) {
    function houzez_get_custom_add_listing_field($key) {

        if(class_exists('Houzez_Fields_Builder')) {

            $field_array = Houzez_Fields_Builder::get_field_by_slug($key);
            $field_title = houzez_wpml_translate_single_string($field_array['label']);
            $placeholder = houzez_wpml_translate_single_string($field_array['placeholder']);

            $field_name = $field_array['field_id'];
            $field_type = $field_array['type'];
            $field_options = $field_array['fvalues'];

            $selected = '';
            if (!houzez_edit_property()) {
                $selected = 'selected=selected';
            }

            $data_value = '';
            if (houzez_edit_property()) {
                global $prop_meta_data;
                $data_value = isset( $prop_meta_data[ 'fave_'.$key ] ) ? ( ( 'checkbox_list' == $field_type || 'radio' == $field_type ) || 'multiselect' == $field_type ? $prop_meta_data[ 'fave_'.$key ] : $prop_meta_data[ 'fave_'.$key ][0] ) : '';
            }


            if($field_type == 'select' ) { ?>

                <div class="form-group">
                    <label for="<?php echo esc_attr($field_name); ?>">
                        <?php echo $field_title.houzez_required_field($field_name); ?>
                    </label>

                    <select name="<?php echo esc_attr($field_name);?>" data-size="5" class="selectpicker <?php houzez_required_field_2($field_name); ?> form-control bs-select-hidden" title="<?php echo esc_attr($placeholder); ?>" data-live-search="false">
                        
                        <option <?php echo esc_attr($selected); ?> value=""><?php echo esc_attr($placeholder); ?> </option>
                        <?php
                        $options = unserialize($field_options);
                        
                        foreach ($options as $key => $val) {
                            $val = houzez_wpml_translate_single_string($val);
                            
                            $selected_val = houzez_get_field_meta($field_name);
                            $key = trim($key);

                            echo '<option '.selected($selected_val, $key, false).' value="'.esc_attr($key).'">'.esc_attr($val).'</option>';
                        }
                        ?>

                    </select><!-- selectpicker -->
                </div>

            <?php
            } else if($field_type == 'multiselect' ) { ?>

                <div class="form-group">
                    <label for="<?php echo esc_attr($field_name); ?>">
                        <?php echo $field_title.houzez_required_field($field_name); ?>
                    </label>

                    <select name="<?php echo esc_attr($field_name).'[]'; ?>" data-size="5" data-actions-box="true" class="selectpicker <?php houzez_required_field_2($field_name); ?> form-control bs-select-hidden" title="<?php echo esc_attr($placeholder); ?>" data-live-search="false" data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-count-selected-text="{0}" multiple>
                        
                        <?php
                        $options = unserialize($field_options);
                        
                        foreach ($options as $key => $val) {
                            $val = houzez_wpml_translate_single_string($val);
                            $key = trim($key);
                            $selected = ( houzez_edit_property() && ! empty( $data_value ) && in_array( $key, $data_value ) ) ? 'selected' : '';

                            echo '<option '.esc_attr($selected).' value="'.esc_attr($key).'">'.esc_attr($val).'</option>';
                        }
                        ?>

                    </select><!-- selectpicker -->
                </div>

            <?php
            } else if( $field_type == 'checkbox_list' ) { ?>

                <div class="form-group">
                    <label for="<?php echo esc_attr($field_name); ?>">
                        <?php echo $field_title.houzez_required_field($field_name); ?>
                    </label>
                    <div class="features-list houzez-custom-field">
                        <?php
                        $options    = unserialize( $field_options );
                        $options    = explode( ',', $options );
                        $options    = array_filter( array_map( 'trim', $options ) );
                        $checkboxes = array_combine( $options, $options );

                        foreach ($checkboxes as $checkbox) {

                            $checked = ( houzez_edit_property() && ! empty( $data_value ) && in_array( $checkbox, $data_value ) ) ? 'checked' : '';
                            $checkbox_title = houzez_wpml_translate_single_string($checkbox);
                            echo '<label class="control control--checkbox">';
                                echo '<input type="checkbox" '.esc_attr($checked).' name="'.esc_attr($field_name).'[]" value="'.esc_attr($checkbox).'">'.esc_attr($checkbox_title);
                                echo '<span class="control__indicator"></span>';
                            echo '</label>';

                        }
                        ?>
                    </div><!-- features-list -->
                </div>

            <?php
            } else if( $field_type == 'radio' ) { ?>

                <div class="form-group">
                    <label for="<?php echo esc_attr($field_name); ?>">
                        <?php echo $field_title.houzez_required_field($field_name); ?>
                    </label>
                    <div class="features-list houzez-custom-field">
                        <?php
                        $options    = unserialize( $field_options );
                        $options    = explode( ',', $options );
                        $options    = array_filter( array_map( 'trim', $options ) );
                        $radios     = array_combine( $options, $options );

                        echo '<label class="control control--radio">';
                            echo '<input type="radio" name="'.esc_attr($field_name).'" value="">'.esc_html__('None', 'houzez');
                            echo '<span class="control__indicator"></span>';
                        echo '</label>';

                        foreach ($radios as $radio) {

                            $radio_checked = ( houzez_edit_property() && ! empty( $data_value ) && in_array( $radio, $data_value ) ) ? 'checked' : '';

                            $radio_title = houzez_wpml_translate_single_string($radio);
                            echo '<label class="control control--radio">';
                                echo '<input type="radio" '.esc_attr($radio_checked).' name="'.esc_attr($field_name).'" value="'.esc_attr($radio).'">'.esc_attr($radio_title);
                                echo '<span class="control__indicator"></span>';
                            echo '</label>';

                        }
                        ?>
                    </div><!-- features-list -->
                </div>

            <?php
            } else if( $field_type == 'number' ) { ?>

                <div class="form-group">
                    <label for="<?php echo esc_attr($field_name); ?>">
                        <?php echo $field_title.houzez_required_field($field_name); ?>
                    </label>
                    <input name="<?php echo esc_attr($field_name);?>" <?php houzez_required_field_2($field_name); ?> type="number" class="form-control" value="<?php
                    if (houzez_edit_property()) {
                        houzez_field_meta($field_name);
                    } ?>" placeholder="<?php echo esc_attr($placeholder);?>">
                </div>

            <?php
            } else if( $field_type == 'textarea' ) { ?>

                <div class="form-group">
                    <label for="<?php echo esc_attr($field_name); ?>">
                        <?php echo $field_title.houzez_required_field($field_name); ?>
                    </label>
                    <textarea class="form-control" name="<?php echo esc_attr($field_name);?>" placeholder="<?php echo esc_attr($placeholder);?>" <?php houzez_required_field_2($field_name); ?>><?php
                    if (houzez_edit_property()) {
                        houzez_field_meta($field_name);
                    } ?></textarea>
                </div>

            <?php
            } else if( $field_type == 'url' ) { ?>

                <div class="form-group">
                    <label for="<?php echo esc_attr($field_name); ?>">
                        <?php echo $field_title.houzez_required_field($field_name); ?>
                    </label>

                    <input name="<?php echo esc_attr($field_name);?>" <?php houzez_required_field_2($field_name); ?> type="url" class="form-control" value="<?php
                    if (houzez_edit_property()) {
                        houzez_field_meta($field_name);
                    } ?>" placeholder="<?php echo esc_attr($placeholder);?>">
                </div>

            <?php
            } else { ?>

                <div class="form-group">
                    <label for="<?php echo esc_attr($field_name); ?>">
                        <?php echo $field_title.houzez_required_field($field_name); ?>
                    </label>

                    <input name="<?php echo esc_attr($field_name);?>" <?php houzez_required_field_2($field_name); ?> type="text" class="form-control" value="<?php
                    if (houzez_edit_property()) {
                        houzez_field_meta($field_name);
                    } ?>" placeholder="<?php echo esc_attr($placeholder);?>">
                </div>

            <?php
            } 

        }
    }
}

add_action('wp_ajax_load_lightbox_content', 'houzez_listing_model');
add_action('wp_ajax_nopriv_load_lightbox_content', 'houzez_listing_model');

if( !function_exists('houzez_listing_model')) {
    function houzez_listing_model() {
        $listing_id = isset($_POST['listing_id']) ? $_POST['listing_id'] : '';

        if(empty($listing_id)) {
            echo esc_html__('Nothing found', 'houzez');
            return;
        }
        

        $lightbox_logo = houzez_option( 'lightbox_logo', false, 'url' );

        $userID      =   get_current_user_id();
        $fav_option = 'houzez_favorites-'.$userID;
        $fav_option = get_option( $fav_option );
        $icon = $key = '';
        if( !empty($fav_option) ) {
            $key = array_search($listing_id, $fav_option);
        }
        if( $key != false || $key != '' ) {
            $icon = 'text-danger';
        }
    
        $prop_id = houzez_get_listing_data_by_id('property_id', $listing_id);
        $prop_size = houzez_get_listing_data_by_id('property_size', $listing_id);
        $land_area = houzez_get_listing_data_by_id('property_land', $listing_id);
        $bedrooms = houzez_get_listing_data_by_id('property_bedrooms', $listing_id);
        $rooms = houzez_get_listing_data_by_id('property_rooms', $listing_id);
        $bathrooms = houzez_get_listing_data_by_id('property_bathrooms', $listing_id);
        $year_built = houzez_get_listing_data_by_id('property_year', $listing_id);
        $garage = houzez_get_listing_data_by_id('property_garage', $listing_id);
        $property_type = houzez_taxonomy_simple_2('property_type', $listing_id);
        $garage_size = houzez_get_listing_data_by_id('property_garage_size', $listing_id);

        $term_status = wp_get_post_terms( $listing_id, 'property_status', array("fields" => "all"));
        $term_label = wp_get_post_terms( $listing_id, 'property_label', array("fields" => "all"));

        $size = 'houzez-gallery';
        $properties_images = rwmb_meta( 'fave_property_images', 'type=plupload_image&size='.$size, $listing_id );

        $token = wp_generate_password(5, false, false);

    ?>
    <div class="modal-header">
        <div class="d-flex flex-fill me-2 align-items-center">
            <div class="lightbox-logo flex-grow-1">
                <?php if(!empty($lightbox_logo)) { ?>
                <img class="img-fluid" src="<?php echo esc_url($lightbox_logo); ?>" alt="logo">
                <?php } ?>
            </div><!-- lightbox-logo -->
            <div class="lightbox-tools">
                <ul class="list-inline">
                    <?php if( houzez_option('disable_favorite') != 0 ) { ?>
                    <li class="list-inline-item btn-favorite">
                        <a class="add-favorite-js" data-listid="<?php echo intval($listing_id)?>" href="#" role="button"><i class="houzez-icon icon-love-it me-2 <?php echo esc_attr($icon); ?>" aria-hidden="true"></i></a>
                    </li>
                    <?php } ?>
                </ul>
            </div><!-- lightbox-tools -->
        </div><!-- d-flex -->
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div><!-- modal-header -->

    <div class="modal-body clearfix">

        <div class="lightbox-gallery-wrap">
            <a class="btn-expand">
                <i class="houzez-icon icon-expand-3"></i>
            </a>
            
            <?php  if( !empty($properties_images) && count($properties_images)) { ?>
            <div class="lightbox-gallery">
                <div id="preview-js-<?php echo esc_attr($token); ?>" class="lightbox-slider">
                    
                    <?php
                    $lightbox_caption = houzez_option('lightbox_caption', 0); 
                    foreach( $properties_images as $prop_image_id => $prop_image_meta ) {
                        // Skip if attachment doesn't exist
                        if ( ! get_post_status( $prop_image_id ) ) {
                            continue;
                        }
                        $output = '';
                        $output .= '<div role="listitem">';
                            $output .= '<img class="img-fluid" src="'.esc_url( $prop_image_meta['full_url'] ).'" alt="'.esc_attr($prop_image_meta['alt']).'" title="'.esc_attr($prop_image_meta['title']).'">';

                            if( !empty($prop_image_meta['caption']) && $lightbox_caption != 0 ) {
                                $output .= '<span class="hz-image-caption">'.esc_attr($prop_image_meta['caption']).'</span>';
                            }

                        $output .= '</div>';

                        echo $output;
                    }
                    ?>
                    
                </div>
            </div><!-- lightbox-gallery -->
            <?php 
            } else { 
                $featured_image_url = houzez_get_image_url('full', $listing_id);
                echo '<div>
                    <img class="img-fluid" src="'.esc_url($featured_image_url[0]).'">
                </div>';
            } ?>

        </div><!-- lightbox-gallery-wrap -->


        <div class="lightbox-content-wrap lightbox-form-wrap">
        
            <div class="labels-wrap d-flex align-items-center gap-1" role="group"> 
                <?php 
                if( !empty($term_status) ) {
                    foreach( $term_status as $status ) {
                        $status_id = $status->term_id;
                        $status_name = $status->name;
                        echo '<a href="'.get_term_link($status_id).'" class="label-status label status-color-'.intval($status_id).'">
                                '.esc_attr($status_name).'
                            </a>';
                    }
                }

                if( !empty($term_label) ) {
                    foreach( $term_label as $label ) {
                        $label_id = $label->term_id;
                        $label_name = $label->name;
                        echo '<a href="'.get_term_link($label_id).'" class="label label-color-'.intval($label_id).'">
                                '.esc_attr($label_name).'
                            </a>';
                    }
                }
                ?>       
            </div>
            
            <h2 class="item-title mb-2">
                <a href="<?php echo esc_url(get_permalink($listing_id)); ?>"><?php echo get_the_title($listing_id); ?></a>
            </h2><!-- item-title -->

            <?php 
            $address_composer = houzez_option('listing_address_composer');
            $enabled_data = isset($address_composer['enabled']) ? $address_composer['enabled'] : [];
            $temp_array = array();

            if ($enabled_data) {
                unset($enabled_data['placebo']);
                foreach ($enabled_data as $key=>$value) {

                    if( $key == 'address' ) {
                        $map_address = houzez_get_listing_data_by_id('property_map_address', $listing_id);

                        if( $map_address != '' ) {
                            $temp_array[] = $map_address;
                        }

                    } else if( $key == 'streat-address' ) {
                        $property_address = houzez_get_listing_data_by_id('property_address', $listing_id);

                        if( $property_address != '' ) {
                            $temp_array[] = $property_address;
                        }

                    } else if( $key == 'country' ) {
                        $country = houzez_taxonomy_simple_2('property_country', $listing_id);

                        if( $country != '' ) {
                            $temp_array[] = $country;
                        }

                    } else if( $key == 'state' ) {
                        $state = houzez_taxonomy_simple_2('property_state', $listing_id);

                        if( $state != '' ) {
                            $temp_array[] = $state;
                        }

                    } else if( $key == 'city' ) {
                        $city = houzez_taxonomy_simple_2('property_city', $listing_id);

                        if( $city != '' ) {
                            $temp_array[] = $city;
                        }

                    } else if( $key == 'area' ) {
                        $area = houzez_taxonomy_simple_2('property_area', $listing_id);

                        if( $area != '' ) {
                            $temp_array[] = $area;
                        }

                    }
                }

                $address = join( ", ", $temp_array );

                if( ! empty( $address ) ) {
                    echo '<address class="item-address mb-2">';
                        echo '<i class="houzez-icon icon-pin me-1" aria-hidden="true"></i>';
                        echo '<span role="text">' . esc_html($address) . '</span>';
                    echo '</address>';
                }
            }
            ?>
            
            <ul class="item-price-wrap">
                <?php echo houzez_listing_price_for_print($listing_id); ?>
            </ul>

            <p><?php echo houzez_get_excerpt(23, $listing_id); ?></p>

            <div class="property-overview-data" role="list">
				<div class="row row-cols-3 g-4" role="list">
                <?php
                $listing_data_composer = houzez_option('preview_data_composer');
                $data_composer = $listing_data_composer['enabled'];

                $meta_type = houzez_option('preview_meta_type');

                $bd_output = $b_output = $id_output = $garage_output = $area_size_output = $land_output = $year_output = $icon = $icon_bt = $icon_prop_id = $icon_garage = $icon_areasize = $icon_land = $icon_year = $cus_output = $cus_icon = '';
                $i = 0;
                if ($data_composer) {
                    unset($data_composer['placebo']);
                    foreach ($data_composer as $key=>$value) { $i ++;

                        $listing_area_size = houzez_get_listing_area_size( $listing_id );
                        $listing_size_unit = houzez_get_listing_size_unit( $listing_id );

                        $listing_land_size = houzez_get_land_area_size( $listing_id );
                        $listing_land_unit = houzez_get_land_size_unit( $listing_id );

                        if( $key == 'bed' && $bedrooms != '' ) {
                            $bd_output = '<div class="col" role="listitem">';
                            $bd_output .= '<ul class="list-unstyled mb-0">';
                                $bd_output .= '<li class="property-overview-item d-flex align-items-center">';
                                    
                                    if(houzez_option('icons_type') == 'font-awesome') {
                                        $icon .= '<i class="'.houzez_option('fa_bed').' me-2"></i>';

                                    } elseif (houzez_option('icons_type') == 'custom') {
                                        $cus_icon = houzez_option('bed');
                                        if(!empty($cus_icon['url'])) {
                                            $icon .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($cus_icon['title']).'">';
                                        }
                                    } else {
                                        $icon .= '<i class="houzez-icon icon-hotel-double-bed-1 me-2"></i>';
                                    }

                                    if( $meta_type != 'text' ) {
                                        $bd_output .= $icon;
                                    }
                                    
                                    $bd_output .= '<strong>'.esc_attr($bedrooms).'</strong>';
                                    
                                $bd_output .= '</li>';

                                if( $meta_type != 'icons' ) {
                                    $prop_bed_label = ($bedrooms > 1 ) ? houzez_option('glc_bedrooms', 'Bedrooms') : houzez_option('glc_bedroom', 'Bedroom');
                                    $bd_output .= '<li class="h-beds font-size-13">'.esc_attr($prop_bed_label).'</li>';
                                }

                            $bd_output .= '</ul>';
                            $bd_output .= '</div>';

                            if(!empty($bd_output)) {
                                echo $bd_output;
                            }

                        } else if( $key == 'room' && $rooms != '' ) {
                            $room_icon = '';
                            $rooms_output = '<div class="col" role="listitem">';    
                            $rooms_output .= '<ul class="list-unstyled mb-0">';
                                $rooms_output .= '<li class="property-overview-item d-flex align-items-center">';
                                    
                                    if(houzez_option('icons_type') == 'font-awesome') {
                                        $room_icon .= '<i class="'.houzez_option('fa_room').' me-2"></i>';

                                    } elseif (houzez_option('icons_type') == 'custom') {
                                        $cus_icon = houzez_option('room');
                                        if(!empty($cus_icon['url'])) {
                                            $room_icon .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($cus_icon['title']).'">';
                                        }
                                    } else {
                                        $room_icon .= '<i class="houzez-icon icon-hotel-double-bed-1 me-2"></i>';
                                    }

                                    if( $meta_type != 'text' ) {
                                        $rooms_output .= $room_icon;
                                    }
                                    
                                    $rooms_output .= '<strong>'.esc_attr($rooms).'</strong>';
                                    
                                $rooms_output .= '</li>';

                                if( $meta_type != 'icons' ) {
                                    $prop_room_label = ($rooms > 1 ) ? houzez_option('glc_rooms', 'Rooms') : houzez_option('glc_room', 'Room');
                                    $rooms_output .= '<li class="h-beds font-size-13">'.esc_attr($prop_room_label).'</li>';
                                }

                            $rooms_output .= '</ul>';
                            $rooms_output .= '</div>';

                            if(!empty($rooms_output)) {
                                echo $rooms_output;
                            }

                        } elseif( $key == 'bath' && $bathrooms != "" ) {
                            $b_output = '<div class="col" role="listitem">';
                            $b_output .= '<ul class="list-unstyled mb-0">';
                                $b_output .= '<li class="property-overview-item d-flex align-items-center">';
                                    
                                    if(houzez_option('icons_type') == 'font-awesome') {
                                        $icon_bt .= '<i class="'.houzez_option('fa_bath').' me-2"></i>';

                                    } elseif (houzez_option('icons_type') == 'custom') {
                                        $cus_icon = houzez_option('bath');
                                        if(!empty($cus_icon['url'])) {
                                            $icon_bt .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($cus_icon['title']).'">';
                                        }
                                    } else {
                                        $icon_bt .= '<i class="houzez-icon icon-bathroom-shower-1 me-2"></i>';
                                    }

                                    if( $meta_type != 'text' ) {
                                        $b_output .= $icon_bt;
                                    }
                                    
                                    $b_output .= '<strong>'.esc_attr($bathrooms).'</strong>';
                                    
                                $b_output .= '</li>';

                                if( $meta_type != 'icons' ) {
                                    $prop_bath_label = ($bathrooms > 1 ) ? houzez_option('glc_bathrooms', 'Bathrooms') : houzez_option('glc_bathroom', 'Bathroom');
                                    $b_output .= '<li class="h-baths font-size-13">'.esc_attr($prop_bath_label).'</li>';
                                }

                            $b_output .= '</ul>';
                            $b_output .= '</div>';
                            if(!empty($b_output)) {
                                echo $b_output;
                            }

                        } elseif( $key == 'property-id' && $prop_id != "" ) {
                            $id_output = '<div class="col" role="listitem">';
                            $id_output .= '<ul class="list-unstyled mb-0">';
                                $id_output .= '<li class="property-overview-item d-flex align-items-center">';
                                    
                                    if(houzez_option('icons_type') == 'font-awesome') {
                                        $icon_prop_id .= '<i class="'.houzez_option('fa_property-id').' me-2"></i>';

                                    } elseif (houzez_option('icons_type') == 'custom') {
                                        $cus_icon = houzez_option('property-id');
                                        if(!empty($cus_icon['url'])) {
                                            $icon_prop_id .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($cus_icon['title']).'">';
                                        }
                                    } else {
                                        $icon_prop_id .= '<i class="houzez-icon icon-tags me-2"></i>';
                                    }

                                    if( $meta_type != 'text' ) {
                                        $id_output .= $icon_prop_id;
                                    }
                                    
                                    $id_output .= '<strong>'.esc_attr($prop_id).'</strong>';
                                    
                                $id_output .= '</li>';

                                if( $meta_type != 'icons' ) {
                                    $prop_id_label = houzez_option('glc_listing_id', 'Listing ID');
                                    $id_output .= '<li class="h-property-id font-size-13">'.esc_attr($prop_id_label).'</li>';
                                }

                            $id_output .= '</ul>';
                            $id_output .= '</div>';     
                            if(!empty($id_output)) {
                                echo $id_output;
                            }

                        } elseif( $key == 'garage' && $garage != "" ) {
                            $garage_output = '<div class="col" role="listitem">';
                            $garage_output .= '<ul class="list-unstyled mb-0">';
                                $garage_output .= '<li class="property-overview-item d-flex align-items-center">';
                                    
                                    if(houzez_option('icons_type') == 'font-awesome') {
                                        $icon_garage .= '<i class="'.houzez_option('fa_garage').' me-2"></i>';

                                    } elseif (houzez_option('icons_type') == 'custom') {
                                        $cus_icon = houzez_option('garage');
                                        if(!empty($cus_icon['url'])) {
                                            $icon_garage .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($cus_icon['title']).'">';
                                        }
                                    } else {
                                        $icon_garage .= '<i class="houzez-icon icon-car-1 me-2"></i>';
                                    }

                                    if( $meta_type != 'text' ) {
                                        $garage_output .= $icon_garage;
                                    }
                                    
                                    $garage_output .= '<strong>'.esc_attr($garage).'</strong>';
                                    
                                $garage_output .= '</li>';

                                if( $meta_type != 'icons' ) {
                                    $prop_garage_label = ($garage > 1 ) ? houzez_option('glc_garages', 'Garages') : houzez_option('glc_garage', 'Garage');
                                    $garage_output .= '<li class="h-garage font-size-13">'.esc_attr($prop_garage_label).'</li>';
                                }

                            $garage_output .= '</ul>';
                            $garage_output .= '</div>';
                            if(!empty($garage_output)) {
                                echo $garage_output;
                            }

                        } elseif( $key == 'area-size' && $listing_area_size != "" ) {
                            $area_size_output = '<div class="col" role="listitem">';
                            $area_size_output .= '<ul class="list-unstyled mb-0">';
                                $area_size_output .= '<li class="property-overview-item d-flex align-items-center">';
                                    
                                    if(houzez_option('icons_type') == 'font-awesome') {
                                        $icon_areasize .= '<i class="'.houzez_option('fa_area-size').' me-2"></i>';

                                    } elseif (houzez_option('icons_type') == 'custom') {
                                        $cus_icon = houzez_option('area-size');
                                        if(!empty($cus_icon['url'])) {
                                            $icon_areasize .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($cus_icon['title']).'">';
                                        }
                                    } else {
                                        $icon_areasize .= '<i class="houzez-icon icon-ruler-triangle me-2"></i>';
                                    }

                                    if( $meta_type != 'text' ) {
                                        $area_size_output .= $icon_areasize;
                                    }
                                    
                                    $area_size_output .= '<strong>'.esc_attr($listing_area_size).'</strong>';
                                    
                                $area_size_output .= '</li>';

                                if( $meta_type != 'icons' ) {
                                    $area_size_output .= '<li class="h-area font-size-13">'.$listing_size_unit.'</li>';
                                }

                            $area_size_output .= '</ul>';
                            $area_size_output .= '</div>';
                            if(!empty($area_size_output)) {
                                echo $area_size_output;
                            }

                        } elseif( $key == 'land-area' && $listing_land_size != "" ) {
                            $land_output = '<div class="col" role="listitem">';
                            $land_output .= '<ul class="list-unstyled mb-0">';
                                $land_output .= '<li class="property-overview-item d-flex align-items-center">';
                                    
                                    if(houzez_option('icons_type') == 'font-awesome') {
                                        $icon_land .= '<i class="'.houzez_option('fa_land-area').' me-2"></i>';

                                    } elseif (houzez_option('icons_type') == 'custom') {
                                        $cus_icon = houzez_option('land-area');
                                        if(!empty($cus_icon['url'])) {
                                            $icon_land .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($cus_icon['title']).'">';
                                        }
                                    } else {
                                        $icon_land .= '<i class="houzez-icon icon-real-estate-dimensions-map me-2"></i>';
                                    }

                                    if( $meta_type != 'text' ) {
                                        $land_output .= $icon_land;
                                    }
                                    
                                    $land_output .= '<strong>'.esc_attr($listing_land_size).'</strong>';
                                    
                                $land_output .= '</li>';

                                if( $meta_type != 'icons' ) {
                                    $land_output .= '<li class="h-land-area font-size-13">'.$listing_land_unit.'</li>';
                                }

                            $land_output .= '</ul>';
                            $land_output .= '</div>';
                            if(!empty($listing_land_size)) {
                                echo $land_output;
                            }

                        }  elseif( $key == 'year-built' && $year_built != "" ) {
                            $year_output = '<div class="col" role="listitem">';
                            $year_output .= '<ul class="list-unstyled mb-0">';
                                $year_output .= '<li class="property-overview-item d-flex align-items-center">';
                                    
                                    if(houzez_option('icons_type') == 'font-awesome') {
                                        $icon_year .= '<i class="'.houzez_option('fa_year-built').' me-2"></i>';

                                    } elseif (houzez_option('icons_type') == 'custom') {
                                        $cus_icon = houzez_option('year-built');
                                        if(!empty($cus_icon['url'])) {
                                            $icon_year .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($cus_icon['title']).'">';
                                        }
                                    } else {
                                        $icon_year .= '<i class="houzez-icon icon-attachment me-2"></i>';
                                    }

                                    if( $meta_type != 'text' ) {
                                        $year_output .= $icon_year;
                                    }
                                    
                                    $year_output .= '<strong>'.esc_attr($year_built).'</strong>';
                                    
                                $year_output .= '</li>';

                                if( $meta_type != 'icons' ) {
                                    $year_output .= '<li class="h-year-built font-size-13">'.houzez_option('glc_year_built', 'Year Built').'</li>';
                                }

                            $year_output .= '</ul>';
                            $year_output .= '</div>';
                            if(!empty($year_built)) {
                                echo $year_output;
                            }

                        } else {
                            
                            $cus_output = '<div class="col" role="listitem">';
                            $custom_icon = '';
                            $cus_data = houzez_get_listing_data_by_id($key, $listing_id);

                            $cus_output .= '<ul class="list-unstyled mb-0">';
                            $cus_output .= '<li class="property-overview-item d-flex align-items-center">';
                                
                                if(houzez_option('icons_type') == 'font-awesome') {
                                    $custom_icon .= '<i class="'.houzez_option('fa_'.$key).' me-2"></i>';

                                } elseif (houzez_option('icons_type') == 'custom') {
                                    $cus_icon = houzez_option($key);

                                    if(!empty($cus_icon['url'])) {
                                        $alt = isset($cus_icon['title']) ? $cus_icon['title'] : '';
                                        $custom_icon .= '<img class="img-fluid me-2" src="'.esc_url($cus_icon['url']).'" width="16" height="16" alt="'.esc_attr($alt).'">';
                                    }
                                } 

                                if( $meta_type != 'text' ) {
                                    $cus_output .= $custom_icon;
                                }
                                
                                $cus_output .= '<strong>'.esc_attr($cus_data).'</strong>';
                                
                            $cus_output .= '</li>';

                            if( $meta_type != 'icons' ) {
                                $cus_output .= '<li class="h-'.$key.' font-size-13">'.esc_attr($value).'</li>';
                            }

                            $cus_output .= '</ul>';
                            $cus_output .= '</div>';

                            if(!empty($cus_data)) {
                                echo $cus_output;
                            }

                        } // end else
                    if($i == 6)
                        break;
                    }
                }
                ?>
                </div>
            </div>
            
            <a class="btn btn-primary btn-item" href="<?php echo esc_url(get_permalink($listing_id)); ?>">
                <?php echo houzez_option('glc_detail_btn', 'Details'); ?>
            </a><!-- btn-item -->

        </div><!-- lightbox-content-wrap -->
    </div><!-- modal-body -->
    <div class="modal-footer">
        
    </div><!-- modal-footer -->

    <?php
    wp_die();
    }
}


if ( ! function_exists( 'houzez_taxonomy_pagination' ) ) {
    /**
     * Update Taxonomy Pagination according to theme option
     *
     * @param $query
     */
    function houzez_taxonomy_pagination( $query ) {
        if ( is_tax( 'property_type' ) || is_tax( 'property_status' ) || is_tax( 'property_label' ) || is_tax( 'property_city' ) || is_tax( 'property_feature' ) || is_tax( 'property_country' ) || is_tax( 'property_state' ) || is_tax( 'property_area' ) || is_post_type_archive('property') ) {
            if ( $query->is_main_query() ) {
                $taxonomy_num_posts = houzez_option('taxonomy_num_posts');
                $number_of_prop = intval($taxonomy_num_posts);
                if(!$number_of_prop){
                    $number_of_prop = 9;
                }
                $query->set( 'posts_per_page', $number_of_prop );
            }
        }
    }

    add_action( 'pre_get_posts', 'houzez_taxonomy_pagination' );
}

/*================================================================================
*   Properties ajax tabs
*=================================================================================*/

if ( ! function_exists( 'houzez_properties_ajax_tab_content' ) ) {
    function houzez_properties_ajax_tab_content() {

        if ( ! empty( $_POST['settings'] ) ) {
            $settings = $_POST['settings']; //houzez_clean_v2( $_POST['settings'] );
            
            $html = houzez_listings_tab( $settings );

            echo json_encode( $html );
            die();
        }
    }

    add_action( 'wp_ajax_houzez_get_properties_tab_content', 'houzez_properties_ajax_tab_content' );
    add_action( 'wp_ajax_nopriv_houzez_get_properties_tab_content', 'houzez_properties_ajax_tab_content' );
}

if( ! function_exists('houzez_listings_tab') ) {

    function houzez_listings_tab( $settings ) {
        
        $grid_style = $settings['grid_style'];
        $check_is_ajax = houzez_check_is_ajax();

        if ( $check_is_ajax ) {
            ob_start();
        }
        ?>

        <?php if ( ! $check_is_ajax ) : ?>
        <div class="houzez-tab-content">
        <?php endif; ?>

            <?php 

            $property_type = $property_status = $property_label = $property_country = $property_state = $property_city = $property_area = $properties_by_agents = $properties_by_agencies = '';

                if(!empty($settings['property_type'])) {
                    $property_type = implode (",", $settings['property_type']);
                }

                if(!empty($settings['property_status'])) {
                    $property_status = implode (",", $settings['property_status']);
                }

                if(!empty($settings['property_label'])) {
                    $property_label = implode (",", $settings['property_label']);
                }

                if(!empty($settings['property_country'])) {
                    $property_country = implode (",", $settings['property_country']);
                }

                if(!empty($settings['property_state'])) {
                    $property_state = implode (",", $settings['property_state']);
                }

                if(!empty($settings['property_city'])) {
                    $property_city = implode (",", $settings['property_city']);
                }

                if(!empty($settings['property_area'])) {
                    $property_area = implode (",", $settings['property_area']);
                }

                if( !empty($settings['properties_by_agents']) ) {
                    $properties_by_agents = implode (",", $settings['properties_by_agents']);
                }

                if( !empty($settings['properties_by_agencies']) ) {
                    $properties_by_agencies = implode (",", $settings['properties_by_agencies']);
                }

                $listing_thumb_size = $settings['listing_thumb_size'] ?? '';
                if ($listing_thumb_size === 'houzez-item-image-1' || $listing_thumb_size === 'global' ) {
                    $listing_thumb_size = '';
                }

                
                $args['module_type'] =  $settings['module_type'];
                $args['houzez_user_role'] =  $settings['houzez_user_role'];
                $args['featured_prop'] =  $settings['featured_prop'];
                $args['posts_limit'] =  $settings['posts_limit'];
                $args['sort_by'] =  $settings['sort_by'];
                $args['offset'] =  $settings['offset'];
                $args['post_status'] =  $settings['post_status'];
                $args['pagination_type'] =  $settings['pagination_type'];
                $args['hide_button'] =  isset($settings['hide_button']) && $settings['hide_button'] === 'none' ? false : true;
                $args['hide_author_date'] =  isset($settings['hide_author_date']) && $settings['hide_author_date'] === 'none' ? false : true;

                $args['property_type']   =  $property_type;
                $args['property_status']   =  $property_status;
                $args['property_label']   =  $property_label;
                $args['property_country']   =  $property_country;
                $args['property_state']   =  $property_state;
                $args['property_city']   =  $property_city;
                $args['property_area']   =  $property_area;
                $args['thumb_size'] = $listing_thumb_size;
                $args['properties_by_agents'] = $properties_by_agents;
                $args['properties_by_agencies'] = $properties_by_agencies;
                $args['min_price'] = $settings['min_price'];
                $args['max_price'] = $settings['max_price'];

                $args['min_beds'] = $settings['min_beds'] ?? '';
                $args['max_beds'] = $settings['max_beds'] ?? '';
                $args['min_baths'] = $settings['min_baths'] ?? '';
                $args['max_baths'] = $settings['max_baths'] ?? '';

                if( $grid_style == 'cards-v1' ) {
                    echo houzez_get_property_cards($args, $settings['module_type'], 'v1');
                } elseif( $grid_style == 'cards-v2' ) {
                    echo houzez_get_property_cards($args, $settings['module_type'], 'v2');
                } elseif( $grid_style == 'cards-v3' ) {
                    echo houzez_get_property_cards($args, $settings['module_type'], 'v3');
                } elseif( $grid_style == 'cards-v5' ) {
                    echo houzez_get_property_cards($args, $settings['module_type'], 'v5');
                } elseif( $grid_style == 'cards-v6' ) {
                    echo houzez_get_property_cards($args, $settings['module_type'], 'v6');
                } elseif( $grid_style == 'cards-v7' ) {
                    echo houzez_get_property_cards($args, $settings['module_type'], 'v7');
                } elseif( $grid_style == 'cards-v8' ) {
                    echo houzez_get_property_cards($args, $settings['module_type'], 'v8');
                }
                ?>

        <?php if ( ! $check_is_ajax ) : ?>
        </div>
        <?php endif; ?>

        <?php
        if ( $check_is_ajax ) {
            return [
                'html' => ob_get_clean(),
            ];
        }
    }
}

if( !function_exists('houzez_build_features_array') ) {
    function houzez_build_features_array() {

        $all_features = array();
        $terms = get_terms( array(
            'taxonomy' => 'property_feature',
            'hide_empty' => false,
            'parent'=> 0

        ));

        foreach( $terms as $key => $term ) {

            $temp_array = array();

            $child_terms = get_terms( array(
                'taxonomy'   => 'property_feature',
                'hide_empty' => false,
                'parent'     => $term->term_id
            ));

            $children = array();
            $children_id = array();

            if( is_array($child_terms) ) {
                foreach( $child_terms as $child_key => $child_term ) {
                    $children[] = $child_term->name;
                    $children_id[] = $child_term->term_id;
                }
            }

            $temp_array['term_id']   = $term->term_id;
            $temp_array['name']      = $term->name;
            $temp_array['childs']    = $children;
            $temp_array['child_ids'] = $children_id;

            $all_features[] = $temp_array;
        }

        return $all_features;
    }
}

if( !function_exists('houzez_feature_output') ) {
    function houzez_feature_output( $term_name, $term_id, $property_features, $column_class = 'col-xl-4 col-lg-6 col-md-6 col-sm-12' ) {

        $feature_icon = '';
        $return_html = '';

        $icon_type  = get_term_meta($term_id, 'fave_feature_icon_type', true);
        $icon_class = get_term_meta($term_id, 'fave_prop_features_icon', true);
        $img_icon   = get_term_meta($term_id, 'fave_feature_img_icon', true);
        $term_link = get_term_link($term_id, 'property_feature');
    
        if($icon_type == 'custom') {
            $icon_url = wp_get_attachment_url( $img_icon );
            if(!empty($icon_url)) {
                $feature_icon = '<img src="'.esc_url($icon_url).'" class="hz-fte-img me-2">';
            }
        } else {
            if(!empty($icon_class))
            $feature_icon = '<i class="'.$icon_class.' me-2"></i>';
        }

        // Check if the feature is selected by matching both term_id and name
        $feature_selected = false;
        if (is_array($property_features)) {
            foreach ($property_features as $feature) {
                if ($feature->term_id === $term_id && $feature->name === $term_name) {
                    $feature_selected = true;
                    break;
                }
            }
        }

        if ($feature_selected) {
            if( !empty($feature_icon) ) {
                $return_html = '<li class="'.$column_class.'" role="listitem">'.$feature_icon.'<a href="' . esc_url($term_link) . '">' . esc_attr($term_name) . '</a></li>';
            } else {
                $return_html = '<li class="'.$column_class.'" role="listitem"><i class="houzez-icon icon-check-circle-1 me-2"></i><a href="' . esc_url($term_link) . '">' . esc_attr($term_name) . '</a></li>';
            }
        }

        return $return_html;
    }
}

if( !function_exists('houzez_feature_submit_output') ) {
    function houzez_feature_submit_output( $term_name, $term_id, $features_terms_id ) {

        $return_html = '';

        $return_html .= '<div class="col-md-3 col-sm-6 col-6">';
        $return_html .= '<label class="control control--checkbox" role="checkbox" aria-checked="false">';
        if ( in_array( $term_id, $features_terms_id ) ) {
            $return_html .= '<input type="checkbox" name="prop_features[]" value="' . esc_attr( $term_id ) . '" checked />';
            $return_html .= '<span class="control__indicator" aria-hidden="true"></span>';
            $return_html .= '<span class="control__label">'.esc_attr( $term_name ).'</span>';
        } else {
            $return_html .= '<input type="checkbox" name="prop_features[]" value="' . esc_attr( $term_id ) . '" />';
            $return_html .= '<span class="control__indicator" aria-hidden="true"></span>';
            $return_html .= '<span class="control__label">'.esc_attr( $term_name ).'</span>';
        }
        $return_html .= '</label>';
        $return_html .= '</div>';
        

        return $return_html;
    }
}

if( ! function_exists( 'houzez_get_leaf_terms' ) ) {
    function houzez_get_leaf_terms($taxonomy) {
        // Get all terms in the taxonomy
        $all_terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ]);

        // Initialize an array to store the "leaf" terms
        $leaf_terms = [];

        foreach ($all_terms as $term) {
            // Check if the term has children
            $children = get_terms([
                'taxonomy'   => $taxonomy,
                'parent'     => $term->term_id,
                'hide_empty' => true,
            ]);

            // If the term does not have children, add it to the leaf terms array
            if (empty($children)) {
                $leaf_terms[] = $term->name;
            }
        }

        return $leaf_terms;
    }

}

if( !function_exists('houzez_get_similar_properties') ) {
    function houzez_get_similar_properties($property_id = null, $criteria = array(), $count = 3, $sort_by = 'd_date') {
        if (!$property_id) {
            $property_id = get_the_ID();
        }

        $properties_args = array(
            'post_type'           => 'property',
            'posts_per_page'      => intval($count),
            'post__not_in'        => array($property_id),
            'post_parent__not_in' => array($property_id),
            'post_status'         => 'publish'
        );

        if (!empty($criteria) && is_array($criteria)) {
            $similar_taxonomies_count = count($criteria);
            $tax_query = array();

            for ($i = 0; $i < $similar_taxonomies_count; $i++) {
                $similar_terms = get_the_terms($property_id, $criteria[$i]);
                if (!empty($similar_terms) && is_array($similar_terms)) {
                    $terms_array = array();
                    foreach ($similar_terms as $property_term) {
                        $terms_array[] = $property_term->term_id;
                    }
                    $tax_query[] = array(
                        'taxonomy' => $criteria[$i],
                        'field'    => 'id',
                        'terms'    => $terms_array,
                    );
                }
            }

            $tax_count = count($tax_query);
            if ($tax_count > 1) {
                $tax_query['relation'] = 'AND';
            }
            if ($tax_count > 0) {
                $properties_args['tax_query'] = $tax_query;
            }
        }

        // Sort properties based on the provided sort parameter
        if ($sort_by == 'a_title') {
            $properties_args['orderby'] = 'title';
            $properties_args['order'] = 'ASC';
        } else if ($sort_by == 'd_title') {
            $properties_args['orderby'] = 'title';
            $properties_args['order'] = 'DESC';
        } else if ($sort_by == 'a_price') {
            $properties_args['orderby'] = 'meta_value_num';
            $properties_args['meta_key'] = 'fave_property_price';
            $properties_args['order'] = 'ASC';
        } else if ($sort_by == 'd_price') {
            $properties_args['orderby'] = 'meta_value_num';
            $properties_args['meta_key'] = 'fave_property_price';
            $properties_args['order'] = 'DESC';
        } else if ($sort_by == 'a_date') {
            $properties_args['orderby'] = 'date';
            $properties_args['order'] = 'ASC';
        } else if ($sort_by == 'd_date') {
            $properties_args['orderby'] = 'date';
            $properties_args['order'] = 'DESC';
        } else if ($sort_by == 'featured_first') {
            $properties_args['orderby'] = 'meta_value date';
            $properties_args['meta_key'] = 'fave_featured';
        } else if ($sort_by == 'featured_first_random') {
            $properties_args['orderby'] = 'meta_value DESC rand';
            $properties_args['meta_key'] = 'fave_featured';
        } else if ($sort_by == 'random') {
            $properties_args['orderby'] = 'rand date';
        }

        return new WP_Query($properties_args);
    }
}

if( !function_exists('houzez_apply_display_agent_information_filter') ) {
    function houzez_apply_display_agent_information_filter( $agent_info ) {
        // Add any additional filtering logic you want to apply
        return $agent_info;
    }
}

/**
 * Unified Agent Info Renderer - Replaces the three separate functions
 * 
 * @param array $args Agent data array
 * @param string $type Display type ('for_grid_list', 'agent_form')
 * @param bool $is_single Whether this is a single agent
 * @param string $layout Layout variant ('top', 'bottom', 'bottom_v2')
 * @return string Generated HTML
 */
if (!function_exists('houzez_render_agent_info')) {
    function houzez_render_agent_info($args, $type = 'agent_form', $is_single = true, $layout = 'bottom') {
        
        // Validate input
        if (empty($args) || !is_array($args) || empty($args['agent_name'])) {
            return '';
        }
        
        // Handle grid/list view
        if ($type === 'for_grid_list') {
            return sprintf(
                '<a href="%s">%s</a> ',
                esc_url($args['link']),
                esc_html($args['agent_name'])
            );
        }
        
        // Handle agent form view with unified implementation
        if ($type === 'agent_form') {
            return houzez_render_agent_form_layout($args, $is_single, $layout);
        }
        
        return '';
    }
}

/**
 * Render agent form layout - unified implementation for all variants
 * 
 * @param array $args Agent data array
 * @param bool $is_single Whether this is a single agent
 * @param string $layout Layout variant ('top', 'bottom', 'bottom_v2')
 * @return string Generated HTML
 */
if (!function_exists('houzez_render_agent_form_layout')) {
    function houzez_render_agent_form_layout($args, $is_single, $layout) {
        
        // Get theme options
        $view_listing = houzez_option('agent_view_listing');
        $agent_phone_num = houzez_option('agent_phone_num');
        $show_phone = houzez_get_show_phone();
        
        // Initialize output
        $output = '';
        
        // Common agent data with validation
        $agent_name = !empty($args['agent_name']) ? esc_html($args['agent_name']) : '';
        $agent_link = !empty($args['link']) ? esc_url($args['link']) : '';
        $agent_picture = !empty($args['picture']) ? esc_url($args['picture']) : '';
        $agent_email = !empty($args['agent_email']) ? esc_attr($args['agent_email']) : '';
        $verified = !empty($args['verified']) ? (int)$args['verified'] : 0;
        
        // Phone numbers with validation
        $agent_phone = !empty($args['agent_phone']) ? esc_attr($args['agent_phone']) : '';
        $agent_phone_call = !empty($args['agent_phone_call']) ? esc_attr($args['agent_phone_call']) : '';
        $agent_mobile = !empty($args['agent_mobile']) ? esc_attr($args['agent_mobile']) : '';
        $agent_mobile_call = !empty($args['agent_mobile_call']) ? esc_attr($args['agent_mobile_call']) : '';
        $agent_skype = !empty($args['agent_skype']) ? esc_attr($args['agent_skype']) : '';
        $agent_whatsapp = !empty($args['agent_whatsapp']) ? esc_attr($args['agent_whatsapp']) : '';
        $agent_whatsapp_call = !empty($args['agent_whatsapp_call']) ? esc_attr($args['agent_whatsapp_call']) : '';
        
        // Social media links with validation
        $facebook = !empty($args['facebook']) ? esc_url($args['facebook']) : '';
        $instagram = !empty($args['instagram']) ? esc_url($args['instagram']) : '';
        $twitter = !empty($args['twitter']) ? esc_url($args['twitter']) : '';
        $linkedin = !empty($args['linkedin']) ? esc_url($args['linkedin']) : '';
        $googleplus = !empty($args['googleplus']) ? esc_url($args['googleplus']) : '';
        $youtube = !empty($args['youtube']) ? esc_url($args['youtube']) : '';
        
        // Generate layout-specific HTML
        switch ($layout) {
            case 'top':
                $output = houzez_render_agent_top_layout($agent_name, $agent_link, $agent_picture, $agent_mobile, $verified, $view_listing, $is_single, $agent_email);
                break;
                
            case 'bottom_v2':
                $output = houzez_render_agent_bottom_v2_layout($args, $is_single, $view_listing);
                break;
                
            case 'bottom':
            default:
                $output = houzez_render_agent_bottom_layout($args, $is_single, $view_listing, $show_phone);
                break;
        }
        
        return $output;
    }
}

/**
 * Render agent top layout
 */
if (!function_exists('houzez_render_agent_top_layout')) {
    function houzez_render_agent_top_layout($agent_name, $agent_link, $agent_picture, $agent_mobile, $verified, $view_listing, $is_single, $agent_email) {
        global $ele_settings;
        $view_listing_link = isset($ele_settings['view_listing']) ? $ele_settings['view_listing'] : 'yes';
        
        $output = '';
        $output .= '<div class="agent-details" role="region">';
            $output .= '<div class="d-flex align-items-center gap-3">';
                
                $output .= '<div class="agent-image" role="img">';
                    
                    if (!$is_single) {
                        $output .= '<input type="checkbox" class="houzez-hidden" checked="checked" class="multiple-agent-check" name="target_email[]" value="' . $agent_email . '" >';
                    }

                    $output .= '<img class="rounded" src="' . $agent_picture . '" alt="' . $agent_name . '" width="70" height="70">';
                    if ($verified == 1) {
                        $output .= '<span class="badge btn-secondary agent-verified-icon"><i class="houzez-icon icon-check-circle-1"></i></span>';
                    }

                $output .= '</div>';

                $output .= '<ul class="agent-information list-unstyled mb-0" role="list">';

                    if (!empty($agent_name)) {
                        $output .= '<li class="agent-name" role="listitem">';
                            $output .= '<i class="houzez-icon icon-single-neutral me-1"></i> ' . $agent_name;
                        $output .= '</li>';
                    }
                    
                    if (!$is_single && !empty($agent_mobile)) {
                        $output .= '<li class="agent-phone agent-phone-hidden" role="listitem">';
                            $output .= '<i class="houzez-icon icon-phone me-1"></i> ' . $agent_mobile;
                        $output .= '</li>';
                    }

                    if ($view_listing != 0 && $view_listing_link == 'yes') {
                        $output .= '<li class="agent-link" role="listitem">';
                            $output .= '<a href="' . $agent_link . '">' . houzez_option('spl_con_view_listings', 'View listings') . '</a>';
                        $output .= '</li>';
                    }

                $output .= '</ul>';
            $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
}

/**
 * Render agent bottom v2 layout
 */
if (!function_exists('houzez_render_agent_bottom_v2_layout')) {
    function houzez_render_agent_bottom_v2_layout($args, $is_single, $view_listing) {
        
        // Extract and validate data
        $agent_name = !empty($args['agent_name']) ? esc_html($args['agent_name']) : '';
        $agent_picture = !empty($args['picture']) ? esc_url($args['picture']) : '';
        $agent_link = !empty($args['link']) ? esc_url($args['link']) : '';
        $agent_email = !empty($args['agent_email']) ? esc_attr($args['agent_email']) : '';
        $verified = !empty($args['verified']) ? (int)$args['verified'] : 0;
        
        $agent_phone = !empty($args['agent_phone']) ? esc_attr($args['agent_phone']) : '';
        $agent_mobile = !empty($args['agent_mobile']) ? esc_attr($args['agent_mobile']) : '';
        $agent_skype = !empty($args['agent_skype']) ? esc_attr($args['agent_skype']) : '';
        $agent_whatsapp = !empty($args['agent_whatsapp']) ? esc_attr($args['agent_whatsapp']) : '';
        $agent_whatsapp_call = !empty($args['agent_whatsapp_call']) ? esc_attr($args['agent_whatsapp_call']) : '';
        
        // Social media links
        $facebook = !empty($args['facebook']) ? esc_url($args['facebook']) : '';
        $instagram = !empty($args['instagram']) ? esc_url($args['instagram']) : '';
        $twitter = !empty($args['twitter']) ? esc_url($args['twitter']) : '';
        $linkedin = !empty($args['linkedin']) ? esc_url($args['linkedin']) : '';
        $googleplus = !empty($args['googleplus']) ? esc_url($args['googleplus']) : '';
        $youtube = !empty($args['youtube']) ? esc_url($args['youtube']) : '';
        
        ob_start();
        ?>
        <div class="agent-details">
    
            <div class="agent-image">
                <img class="rounded" src="<?php echo $agent_picture; ?>" alt="<?php echo $agent_name; ?>" width="80" height="80">
                <?php if ($verified == 1) { ?>
                    <span class="badge btn-secondary agent-verified-icon"><i class="houzez-icon icon-check-circle-1"></i></span>
                <?php } ?>
                <?php if (!$is_single) { ?>
                <input type="checkbox" class="houzez-hidden multiple-agent-check" checked="checked" name="target_email[]" value="<?php echo $agent_email; ?>" >
                <?php } ?>
            </div>

            <ul class="agent-information list-unstyled">
                <li class="agent-name">
                    <?php if (!empty($agent_name)) { ?>
                        <i class="houzez-icon icon-single-neutral me-1"></i> <?php echo $agent_name; ?>
                    <?php } ?>
                </li>

                <li class="agent-phone-wrap clearfix">
        
                    <?php if (!empty($agent_phone) && houzez_option('agent_phone_num', 1)) { ?>
                        <i class="houzez-icon icon-phone me-1"></i>
                        <span class="agent-phone <?php houzez_show_phone(); ?>">
                            <?php echo $agent_phone; ?>
                        </span>
                    <?php } ?>

                    <?php if (!empty($agent_mobile) && houzez_option('agent_mobile_num', 1)) { ?>
                        <i class="houzez-icon icon-mobile-phone me-1"></i>
                        <span class="agent-phone <?php houzez_show_phone(); ?>">
                            <?php echo $agent_mobile; ?>
                        </span>
                    <?php } ?>

                    <?php if (!empty($agent_skype) && houzez_option('agent_skype_con', 1)) { ?>
                        <i class="houzez-icon icon-video-meeting-skype me-1"></i>
                        <span>
                            <a href="skype:<?php echo $agent_skype; ?>?call"><?php echo $agent_skype; ?></a>
                        </span>
                    <?php } ?>

                    <?php if (!empty($agent_whatsapp) && houzez_option('agent_whatsapp_num', 1)) { ?>
                        <i class="houzez-icon icon-messaging-whatsapp me-1"></i>
                        <span>
                            <a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo $agent_whatsapp_call; ?>&text=<?php echo houzez_option('spl_con_interested', "Hello, I am interested in").' ['.get_the_title().'] '.get_permalink(); ?> "><?php echo esc_html__('WhatsApp', 'houzez'); ?></a>
                        </span>
                    <?php } ?>

                </li>

                <?php if (houzez_option('agent_con_social', 1)) { ?>
                <li class="agent-social-media mb-3">
                    <?php if (!empty($facebook)) { ?>
                        <span><a class="btn-facebook" target="_blank" rel="noopener" href="<?php echo $facebook; ?>"><i class="houzez-icon icon-social-media-facebook me-2"></i></a></span>
                    <?php } ?>

                    <?php if (!empty($twitter)) { ?>
                        <span><a class="btn-twitter" target="_blank" rel="noopener" href="<?php echo $twitter; ?>"><i class="houzez-icon icon-x-logo-twitter-logo-2 me-2"></i></a></span>
                    <?php } ?>

                    <?php if (!empty($linkedin)) { ?>
                        <span><a class="btn-linkedin" target="_blank" rel="noopener" href="<?php echo $linkedin; ?>"><i class="houzez-icon icon-professional-network-linkedin me-2"></i></a></span>
                    <?php } ?>

                    <?php if (!empty($googleplus)) { ?>
                        <span><a class="btn-google-plus" target="_blank" rel="noopener" href="<?php echo $googleplus; ?>"><i class="houzez-icon icon-social-media-google-plus-1 me-2"></i></a></span>
                    <?php } ?>

                    <?php if (!empty($youtube)) { ?>
                        <span><a class="btn-youtube" target="_blank" rel="noopener" href="<?php echo $youtube; ?>"><i class="houzez-icon icon-social-video-youtube-clip me-2"></i></a></span>
                    <?php } ?>

                    <?php if (!empty($instagram)) { ?>
                        <span><a class="btn-instagram" target="_blank" rel="noopener" href="<?php echo $instagram; ?>"><i class="houzez-icon icon-social-video-instagram-clip me-2"></i></a></span>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>

            <?php if (houzez_option('agent_view_listing') != 0) { ?>
            <a class="btn btn-primary btn-slim" href="<?php echo $agent_link; ?>" target="_blank"><?php echo houzez_option('spl_con_view_listings', 'View listings'); ?></a>
            <?php } ?>
        </div><!-- agent-details -->

        <?php
        return ob_get_clean();
    }
}

/**
 * Render agent bottom layout
 */
if (!function_exists('houzez_render_agent_bottom_layout')) {
    function houzez_render_agent_bottom_layout($args, $is_single, $view_listing, $show_phone) {
        
        // Extract and validate data
        $agent_name = !empty($args['agent_name']) ? esc_html($args['agent_name']) : '';
        $agent_picture = !empty($args['picture']) ? esc_url($args['picture']) : '';
        $agent_link = !empty($args['link']) ? esc_url($args['link']) : '';
        $agent_email = !empty($args['agent_email']) ? esc_attr($args['agent_email']) : '';
        $verified = !empty($args['verified']) ? (int)$args['verified'] : 0;
        
        $agent_phone = !empty($args['agent_phone']) ? esc_attr($args['agent_phone']) : '';
        $agent_phone_call = !empty($args['agent_phone_call']) ? esc_attr($args['agent_phone_call']) : '';
        $agent_mobile = !empty($args['agent_mobile']) ? esc_attr($args['agent_mobile']) : '';
        $agent_mobile_call = !empty($args['agent_mobile_call']) ? esc_attr($args['agent_mobile_call']) : '';
        $agent_skype = !empty($args['agent_skype']) ? esc_attr($args['agent_skype']) : '';
        $agent_whatsapp = !empty($args['agent_whatsapp']) ? esc_attr($args['agent_whatsapp']) : '';
        $agent_whatsapp_call = !empty($args['agent_whatsapp_call']) ? esc_attr($args['agent_whatsapp_call']) : '';
        
        // Social media links
        $facebook = !empty($args['facebook']) ? esc_url($args['facebook']) : '';
        $instagram = !empty($args['instagram']) ? esc_url($args['instagram']) : '';
        $twitter = !empty($args['twitter']) ? esc_url($args['twitter']) : '';
        $linkedin = !empty($args['linkedin']) ? esc_url($args['linkedin']) : '';
        $googleplus = !empty($args['googleplus']) ? esc_url($args['googleplus']) : '';
        $youtube = !empty($args['youtube']) ? esc_url($args['youtube']) : '';
        
        $output = '';
        $output .= '<div class="agent-details">';
            $output .= '<div class="d-flex align-items-center gap-3">';
                
                $output .= '<div class="agent-image">';
                    if (!$is_single) {
                        $output .= '<input type="checkbox" checked="checked" class="houzez-hidden multiple-agent-check" name="target_email[]" value="' . $agent_email . '" >';
                    }
                    
                    $output .= '<a href="' . $agent_link . '">';
                        $output .= '<img class="rounded" src="' . $agent_picture . '" alt="' . $agent_name . '" width="80" height="80">';
                        if ($verified == 1) {
                            $output .= '<span class="badge btn-secondary agent-verified-icon"><i class="houzez-icon icon-check-circle-1"></i></span>';
                        }
                    $output .= '</a>';
                $output .= '</div>';

                $output .= '<ul class="agent-information list-unstyled d-flex flex-column gap-1">';
                    
                    if (!empty($agent_name)) {
                        $output .= '<li class="agent-name">';
                            $output .= '<i class="houzez-icon icon-single-neutral me-1"></i> ' . $agent_name;
                        $output .= '</li>';
                    }

                    $output .= '<li class="agent-phone-wrap d-flex gap-2 align-items-center">';

                        if (!empty($agent_phone) && houzez_option('agent_phone_num', 1)) {
                            $output .= '<i class="houzez-icon icon-phone me-1"></i>';
                            $output .= '<span class="agent-phone ' . $show_phone . ' me-1">';
                                $output .= '<a href="tel:' . $agent_phone_call . '">' . $agent_phone . '</a>';
                            $output .= '</span>';
                        }

                        if (!empty($agent_mobile) && houzez_option('agent_mobile_num', 1)) {
                            $output .= '<i class="houzez-icon icon-mobile-phone me-1"></i>';
                            $output .= '<span class="agent-phone ' . $show_phone . ' me-1">';
                                $output .= '<a href="tel:' . $agent_mobile_call . '">' . $agent_mobile . '</a>';
                            $output .= '</span>';
                        }

                        if (!empty($agent_skype) && $agent_skype != "#" && houzez_option('agent_skype_con', 1)) {
                            $output .= '<i class="houzez-icon icon-video-meeting-skype me-1"></i>';
                            $output .= '<span>';
                                $output .= '<a href="skype:' . $agent_skype . '?call">' . $agent_skype . '</a>';
                            $output .= '</span>';
                        }

                        if (!empty($agent_whatsapp) && houzez_option('agent_whatsapp_num', 1)) {
                            $output .= '<i class="houzez-icon icon-messaging-whatsapp me-1"></i>';
                            $output .= '<span>';
                                $output .= '<a target="_blank" href="https://api.whatsapp.com/send?phone=' . $agent_whatsapp_call . '&text=' . houzez_option('spl_con_interested', "Hello, I am interested in") . ' [' . get_the_title() . '] ' . get_permalink() . '">' . esc_html__('WhatsApp', 'houzez') . '</a>';
                            $output .= '</span>';
                        }

                    $output .= '</li>';

                    if (houzez_option('agent_con_social', 1)) {
                        $output .= '<li class="agent-social-media">';
                            
                            if (!empty($facebook)) {
                                $output .= '<span>';
                                    $output .= '<a class="btn-facebook" target="_blank" rel="noopener" href="' . $facebook . '">';
                                        $output .= '<i class="houzez-icon icon-social-media-facebook me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                            }
                            
                            if (!empty($instagram)) {
                                $output .= '<span>';
                                    $output .= '<a class="btn-instagram" target="_blank" rel="noopener" href="' . $instagram . '">';
                                        $output .= '<i class="houzez-icon icon-social-instagram me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                            }

                            if (!empty($twitter)) {
                                $output .= '<span>';
                                    $output .= '<a class="btn-twitter" target="_blank" rel="noopener" href="' . $twitter . '">';
                                        $output .= '<i class="houzez-icon icon-x-logo-twitter-logo-2 me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                            }

                            if (!empty($linkedin)) {
                                $output .= '<span>';
                                    $output .= '<a class="btn-linkedin" target="_blank" rel="noopener" href="' . $linkedin . '">';
                                        $output .= '<i class="houzez-icon icon-professional-network-linkedin me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                            }

                            if (!empty($googleplus)) {
                                $output .= '<span>';
                                    $output .= '<a class="btn-google-plus" target="_blank" rel="noopener" href="' . $googleplus . '">';
                                        $output .= '<i class="houzez-icon icon-social-media-google-plus-1 me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                            }

                            if (!empty($youtube)) {
                                $output .= '<span>';
                                    $output .= '<a class="btn-youtube" target="_blank" rel="noopener" href="' . $youtube . '">';
                                       $output .= '<i class="houzez-icon icon-social-video-youtube-clip me-2"></i>';
                                    $output .= '</a>';
                                $output .= '</span>';
                            }

                        $output .= '</li>';
                    }
                $output .= '</ul>';
            $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}

/**
 * Automatically add excerpt/more tag to content if enabled
 * 
 * @param string $content The content to process
 * @param string $context The context (property, agent, agency)
 * @return array Array with processed content parts or original content
 */
if( ! function_exists('houzez_auto_excerpt_content') ) {
    function houzez_auto_excerpt_content($content, $context = 'property') {

        // Check if auto excerpt is enabled
        if( !houzez_option('enable_auto_excerpt', 0) ) {
            return array('has_more' => false, 'content' => $content);
        }

        // Check for existing more tag
        if( ($more_pos = strpos($content, '<!--more-->')) !== false ) {
            return array(
                'has_more' => true,
                'content_before' => substr($content, 0, $more_pos),
                'content_after' => substr($content, $more_pos + 11)
            );
        }

        // Get excerpt length with minimum enforcement
        $excerpt_length = max(20, intval(houzez_option('excerpt_length', 75)));

        // Quick check if content needs excerpting
        $word_count = str_word_count(wp_strip_all_tags($content));
        if( $word_count <= $excerpt_length ) {
            return array('has_more' => false, 'content' => $content);
        }

        // Find best breaking point using simple regex approach
        $break_points = array(
            '</p>' => 1.0,      // Best: paragraph end
            '</ul>' => 0.9,     // Good: list end
            '</ol>' => 0.9,     // Good: list end
            '</div>' => 0.8,    // OK: div end
            '</blockquote>' => 0.7
        );

        $best_pos = false;
        $best_score = -1;
        $current_word_count = 0;

        // Look for natural break points near our target length
        foreach( $break_points as $tag => $score ) {
            $search_pos = 0;
            while( ($pos = strpos($content, $tag, $search_pos)) !== false ) {
                $excerpt = substr($content, 0, $pos + strlen($tag));
                $current_word_count = str_word_count(wp_strip_all_tags($excerpt));

                // Check if this position is viable
                if( $current_word_count >= $excerpt_length * 0.8 &&
                    $current_word_count <= $excerpt_length * 1.5 ) {

                    // Check we're not breaking a list
                    $open_lists = substr_count($excerpt, '<ul') + substr_count($excerpt, '<ol');
                    $closed_lists = substr_count($excerpt, '</ul>') + substr_count($excerpt, '</ol>');

                    if( $open_lists === $closed_lists ) {
                        // Calculate position score based on proximity to target
                        $distance_penalty = abs($current_word_count - $excerpt_length) / $excerpt_length;
                        $position_score = $score - ($distance_penalty * 0.3);

                        if( $position_score > $best_score ) {
                            $best_score = $position_score;
                            $best_pos = $pos + strlen($tag);
                        }
                    }
                }

                // Stop searching if we've gone too far
                if( $current_word_count > $excerpt_length * 2 ) break;

                $search_pos = $pos + 1;
            }
        }

        // If we found a good break point, use it
        if( $best_pos !== false ) {
            $content_before = substr($content, 0, $best_pos);
            $content_after = substr($content, $best_pos);

            // Verify we have content after the break
            if( !empty(trim(wp_strip_all_tags($content_after))) ) {
                return array(
                    'has_more' => true,
                    'content_before' => $content_before,
                    'content_after' => $content_after
                );
            }
        }

        // Fallback: If content has lists, try to keep them intact
        if( strpos($content, '<ul') !== false || strpos($content, '<ol') !== false ) {
            // Find first list end
            $ul_end = strpos($content, '</ul>');
            $ol_end = strpos($content, '</ol>');

            $list_end = false;
            if( $ul_end !== false && $ol_end !== false ) {
                $list_end = min($ul_end, $ol_end) + 5;
            } elseif( $ul_end !== false ) {
                $list_end = $ul_end + 5;
            } elseif( $ol_end !== false ) {
                $list_end = $ol_end + 5;
            }

            if( $list_end !== false ) {
                $excerpt = substr($content, 0, $list_end);
                $wc = str_word_count(wp_strip_all_tags($excerpt));

                // Use list end if it's not too far from target
                if( $wc <= $excerpt_length * 2.5 ) {
                    $content_after = substr($content, $list_end);
                    if( !empty(trim(wp_strip_all_tags($content_after))) ) {
                        return array(
                            'has_more' => true,
                            'content_before' => $excerpt,
                            'content_after' => $content_after
                        );
                    }
                }
            }
        }

        // Ultimate fallback: show all content rather than break it badly
        return array('has_more' => false, 'content' => $content);
    }
}

// Removed DOMNodeIterator class and houzez_find_excerpt_cut_position function
// as they are no longer needed with the simplified approach

/**
 * Safely decode saved search data with backward compatibility
 * Prevents PHP Object Injection by disallowing object unserialization
 *
 * @param string $data Base64 encoded data (either JSON or serialized)
 * @return array Decoded search data or empty array on failure
 * @since 4.1.6
 */
if (!function_exists('houzez_decode_search_data')) {
    function houzez_decode_search_data($data) {
        if (empty($data)) {
            return array();
        }

        // Try base64 + JSON first (new format)
        $decoded = @json_decode(base64_decode($data), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Fallback for old data: base64 + serialize
        // IMPORTANT: allowed_classes => false prevents object injection
        $decoded = base64_decode($data);
        if ($decoded !== false) {
            // Only allow arrays and basic types, no PHP objects
            $result = @unserialize($decoded, array('allowed_classes' => false));
            if ($result !== false) {
                return $result;
            }
        }

        // If all decoding fails, return empty array
        return array();
    }
}