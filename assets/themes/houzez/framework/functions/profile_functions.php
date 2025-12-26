<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 03/10/15
 * Time: 7:57 PM
 */

/*-----------------------------------------------------------------------------------*/
/*   Get Profile Picture
/*-----------------------------------------------------------------------------------*/
if(!function_exists('houzez_get_profile_pic')) {
    function houzez_get_profile_pic($user_id = null) {

        if(empty($user_id)) {
            $user_id = get_the_author_meta( 'ID' );
        }
        
        $author_picture_id   = get_the_author_meta( 'fave_author_picture_id' , $user_id );
        $user_custom_picture = get_the_author_meta( 'fave_author_custom_picture', $user_id );

        if( !empty( $author_picture_id ) ) {
            $author_picture_id = intval( $author_picture_id );
            if ( $author_picture_id ) {
                $img = wp_get_attachment_image_src( $author_picture_id, 'large' );
                if( $img ) {
                    $user_custom_picture = $img[0];
                } else {
                    $user_custom_picture = '';
                }
            }
        }

        if($user_custom_picture == '' ) {
            $user_custom_picture = HOUZEZ_IMAGE. 'profile-avatar.png';
        }

        return $user_custom_picture;
    }
}


/*-----------------------------------------------------------------------------------*/
/*   Upload picture for user profile using ajax
/*-----------------------------------------------------------------------------------*/
if( !function_exists( 'houzez_user_picture_upload' ) ) {
    function houzez_user_picture_upload( ) {

        // Ensure the user is authenticated
        if (!is_user_logged_in()) {
            echo json_encode(array('success' => false, 'msg' => esc_html__('You must be logged in to change the password.', 'houzez')));
            die();
        }

        // Get the current user ID
        $currentUserID = get_current_user_id();

        // Determine the user ID to update
        if ((current_user_can('administrator') || current_user_can('houzez_agency')) && isset($_REQUEST['user_id']) && is_numeric($_REQUEST['user_id'])) {
            $user_id = intval($_REQUEST['user_id']); // Use the posted user_id
        } else {
            $user_id = $currentUserID; // Fallback to the current user's ID
        }

        // Check if the current user has the 'houzez_agency' role and is trying to change another user's password
        if (current_user_can('houzez_agency') && $user_id !== $currentUserID) {
            $agency_id = get_user_meta($user_id, 'fave_agent_agency', true);
            if ($agency_id != $currentUserID) {
                echo json_encode(array('success' => false, 'msg' => esc_html__('This agent does not belong to your agency.', 'houzez')));
                die();
            }
        }

        $verify_nonce = $_REQUEST['verify_nonce'];
        if ( ! wp_verify_nonce( $verify_nonce, 'houzez_upload_nonce' ) ) {
            echo json_encode( array( 'success' => false , 'reason' => 'Invalid request' ) );
            die;
        }

        $author_picture_id   = get_the_author_meta( 'fave_author_picture_id' , $user_id );

        if( $author_picture_id ) {
            wp_delete_attachment($author_picture_id, true);
        }

        $houzez_user_image = $_FILES['houzez_file_data_name'];
        $houzez_wp_handle_upload = wp_handle_upload( $houzez_user_image, array( 'test_form' => false ) );

        if ( isset( $houzez_wp_handle_upload['file'] ) ) {
            $file_name  = basename( $houzez_user_image['name'] );
            $file_type  = wp_check_filetype( $houzez_wp_handle_upload['file'] );

            $uploaded_image_details = array(
                'guid'           => $houzez_wp_handle_upload['url'],
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $profile_attach_id      =   wp_insert_attachment( $uploaded_image_details, $houzez_wp_handle_upload['file'] );
            $profile_attach_data    =   wp_generate_attachment_metadata( $profile_attach_id, $houzez_wp_handle_upload['file'] );
            wp_update_attachment_metadata( $profile_attach_id, $profile_attach_data );

            $thumbnail_url = wp_get_attachment_image_src( $profile_attach_id, 'large' );
            houzez_save_user_photo($user_id, $profile_attach_id, $thumbnail_url);

            echo json_encode( array(
                'success'   => true,
                'url' => $thumbnail_url[0],
                'attachment_id'    => $profile_attach_id
            ));
            die;

        } else {
            echo json_encode( array( 'success' => false, 'reason' => 'Profile Photo upload failed!' ) );
            die;
        }

    }
}
add_action( 'wp_ajax_houzez_user_picture_upload', 'houzez_user_picture_upload' );    // only for logged in user

if( !function_exists('houzez_save_user_photo')) {
    function houzez_save_user_photo($user_id, $pic_id, $thumbnail_url) {
        
        update_user_meta( $user_id, 'fave_author_picture_id', $pic_id );
        update_user_meta( $user_id, 'fave_author_custom_picture', $thumbnail_url[0] );

        $user_agent_id = get_the_author_meta('fave_author_agent_id', $user_id);
        $user_agency_id = get_the_author_meta('fave_author_agency_id', $user_id);
        
        if( !empty($user_agent_id) && houzez_is_agent($user_id) ) {
            update_post_meta( $user_agent_id, '_thumbnail_id', $pic_id );
        }
        
        if( !empty($user_agency_id) && houzez_is_agency($user_id) ) {
            update_post_meta( $user_agency_id, '_thumbnail_id', $pic_id );
        }

    }
}



/**
 * Process user profile update.
 *
 * @param array $data Form/request data.
 * @param bool  $validate_nonce Whether to validate nonce (set false for REST API).
 * @return array|WP_Error Returns an array with a success message or WP_Error on failure.
 */
if( ! function_exists('houzez_process_update_profile') ) {
    function houzez_process_update_profile( $data, $validate_nonce = true ) {

        // If user can update other profiles, use provided user_id.
        if ( ( current_user_can( 'administrator' ) || current_user_can( 'houzez_agency' ) ) 
             && isset( $data['user_id'] ) && is_numeric( $data['user_id'] ) ) {

            $userID = intval( $data['user_id'] );
            $current_user = get_userdata( $userID );
            if ( ! $current_user ) {
                return new WP_Error( 'user_not_found', esc_html__( 'User not found or invalid user.', 'houzez' ) );
            }
            // If current user is a houzez_agency, ensure the target user belongs to the same agency.
            if ( current_user_can( 'houzez_agency' ) ) {
                $agency_id = get_user_meta( $userID, 'fave_agent_agency', true );
                if ( $agency_id != get_current_user_id() ) {
                    return new WP_Error( 'invalid_agency', esc_html__( 'This user does not belong to your agency.', 'houzez' ) );
                }
            }
        } else {
            $current_user = wp_get_current_user();
            $userID = get_current_user_id();
        }

        // Validate nonce if required.
        if ( $validate_nonce ) {
            if ( empty( $data['houzez-security-profile'] ) || ! wp_verify_nonce( $data['houzez-security-profile'], 'houzez_profile_ajax_nonce' ) ) {
                return new WP_Error( 'invalid_nonce', esc_html__( 'Security check failed!', 'houzez' ) );
            }
        }

        // Initialize variables. (Most fields are updated via user meta.)
        $firstname      = ! empty( $data['firstname'] )    ? sanitize_text_field( $data['firstname'] )    : '';
        $lastname       = ! empty( $data['lastname'] )     ? sanitize_text_field( $data['lastname'] )     : '';
        $gdpr_agreement = ! empty( $data['gdpr_agreement'] ) ? sanitize_text_field( $data['gdpr_agreement'] ) : '';
        $userlangs      = ! empty( $data['userlangs'] )      ? sanitize_text_field( $data['userlangs'] )      : '';
        $display_name   = ! empty( $data['display_name'] )   ? sanitize_text_field( $data['display_name'] )   : '';

        // For company: if provided use it; if not and an agency is set, use the agency title.
        $user_company   = '';
        if ( ! empty( $data['user_company'] ) ) {
            $agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );
            $user_company = ( ! empty( $agency_id ) ) ? get_the_title( $agency_id ) : sanitize_text_field( $data['user_company'] );
            update_user_meta( $userID, 'fave_author_company', $user_company );
        } else {
            $agency_id = get_user_meta( $userID, 'fave_author_agency_id', true );
            if ( ! empty( $agency_id ) ) {
                update_user_meta( $userID, 'fave_author_company', get_the_title( $agency_id ) );
            } else {
                delete_user_meta( $userID, 'fave_author_company' );
            }
        }

        // Update basic fields.
        if ( $firstname ) {
            update_user_meta( $userID, 'first_name', $firstname );
        } else {
            delete_user_meta( $userID, 'first_name' );
        }

        if ( $lastname ) {
            update_user_meta( $userID, 'last_name', $lastname );
        } else {
            delete_user_meta( $userID, 'last_name' );
        }

        if ( $gdpr_agreement ) {
            update_user_meta( $userID, 'gdpr_agreement', $gdpr_agreement );
        } else {
            delete_user_meta( $userID, 'gdpr_agreement' );
        }

        if ( $userlangs ) {
            update_user_meta( $userID, 'fave_author_language', $userlangs );
        } else {
            delete_user_meta( $userID, 'fave_author_language' );
        }

        // Update Title.
        if ( ! empty( $data['title'] ) ) {
            $title = sanitize_text_field( $data['title'] );
            update_user_meta( $userID, 'fave_author_title', $title );
        } else {
            delete_user_meta( $userID, 'fave_author_title' );
        }

        // Update About/Bio.
        if ( ! empty( $data['bio'] ) ) {
            $about = wp_kses_post( wpautop( wptexturize( $data['bio'] ) ) );
            update_user_meta( $userID, 'description', $about );
        } else {
            delete_user_meta( $userID, 'description' );
        }

        // Update Phone and Fax.
        if ( ! empty( $data['userphone'] ) ) {
            $userphone = sanitize_text_field( $data['userphone'] );
            update_user_meta( $userID, 'fave_author_phone', $userphone );
        } else {
            delete_user_meta( $userID, 'fave_author_phone' );
        }
        if ( ! empty( $data['fax_number'] ) ) {
            $fax_number = sanitize_text_field( $data['fax_number'] );
            update_user_meta( $userID, 'fave_author_fax', $fax_number );
        } else {
            delete_user_meta( $userID, 'fave_author_fax' );
        }

        // Update Service Areas and Specialties.
        if ( ! empty( $data['service_areas'] ) ) {
            $service_areas = sanitize_text_field( $data['service_areas'] );
            update_user_meta( $userID, 'fave_author_service_areas', $service_areas );
        } else {
            delete_user_meta( $userID, 'fave_author_service_areas' );
        }
        if ( ! empty( $data['specialties'] ) ) {
            $specialties = sanitize_text_field( $data['specialties'] );
            update_user_meta( $userID, 'fave_author_specialties', $specialties );
        } else {
            delete_user_meta( $userID, 'fave_author_specialties' );
        }

        // Update Mobile, WhatsApp and Line ID.
        if ( ! empty( $data['usermobile'] ) ) {
            $usermobile = sanitize_text_field( $data['usermobile'] );
            update_user_meta( $userID, 'fave_author_mobile', $usermobile );
        } else {
            delete_user_meta( $userID, 'fave_author_mobile' );
        }
        if ( ! empty( $data['whatsapp'] ) ) {
            $whatsapp = sanitize_text_field( $data['whatsapp'] );
            update_user_meta( $userID, 'fave_author_whatsapp', $whatsapp );
        } else {
            delete_user_meta( $userID, 'fave_author_whatsapp' );
        }
        if ( ! empty( $data['line_id'] ) ) {
            $line_id = sanitize_text_field( $data['line_id'] );
            update_user_meta( $userID, 'fave_author_line_id', $line_id );
        } else {
            delete_user_meta( $userID, 'fave_author_line_id' );
        }

        // Update Social Profiles.
        $social_fields = array(
            'telegram'    => 'fave_author_telegram',
            'userskype'   => 'fave_author_skype',
            'facebook'    => 'fave_author_facebook',
            'twitter'     => 'fave_author_twitter',
            'linkedin'    => 'fave_author_linkedin',
            'instagram'   => 'fave_author_instagram',
            'pinterest'   => 'fave_author_pinterest',
            'youtube'     => 'fave_author_youtube',
            'tiktok'      => 'fave_author_tiktok',
            'zillow'      => 'fave_author_zillow',
            'realtor_com' => 'fave_author_realtor_com',
            'vimeo'       => 'fave_author_vimeo',
            'googleplus'  => 'fave_author_googleplus',
        );
        foreach ( $social_fields as $field_key => $meta_key ) {
            if ( ! empty( $data[ $field_key ] ) ) {
                $value = sanitize_text_field( $data[ $field_key ] );
                update_user_meta( $userID, $meta_key, $value );
            } else {
                delete_user_meta( $userID, $meta_key );
            }
        }

        // Update Website and Display Name.
        if ( ! empty( $data['website'] ) || ! empty( $data['website_url'] ) ) {
            
            $website = isset($data['website'] ) ? sanitize_text_field( $data['website'] ) : sanitize_text_field($data['website_url']);
            wp_update_user( array( 'ID' => $userID, 'user_url' => $website ) );
        } else {
            wp_update_user( array( 'ID' => $userID, 'user_url' => '' ) );
        }
        if ( $display_name ) {
            wp_update_user( array( 'ID' => $userID, 'display_name' => $display_name ) );
        }

        // For agency role.
        if ( ! empty( $data['license'] ) ) {
            $license = sanitize_text_field( $data['license'] );
            update_user_meta( $userID, 'fave_author_license', $license );
        } else {
            delete_user_meta( $userID, 'fave_author_license' );
        }
        if ( ! empty( $data['tax_number'] ) ) {
            $tax_number = sanitize_text_field( $data['tax_number'] );
            update_user_meta( $userID, 'fave_author_tax_no', $tax_number );
        } else {
            delete_user_meta( $userID, 'fave_author_tax_no' );
        }
        if ( ! empty( $data['user_address'] ) ) {
            $user_address = sanitize_text_field( $data['user_address'] );
            update_user_meta( $userID, 'fave_author_address', $user_address );
        } else {
            delete_user_meta( $userID, 'fave_author_address' );
        }
        if ( ! empty( $data['user_location'] ) ) {
            $user_location = sanitize_text_field( $data['user_location'] );
            update_user_meta( $userID, 'fave_author_google_location', $user_location );
        } else {
            delete_user_meta( $userID, 'fave_author_google_location' );
        }
        if ( ! empty( $data['latitude'] ) ) {
            $latitude = sanitize_text_field( $data['latitude'] );
            update_user_meta( $userID, 'fave_author_google_latitude', $latitude );
        } else {
            delete_user_meta( $userID, 'fave_author_google_latitude' );
        }
        if ( ! empty( $data['longitude'] ) ) {
            $longitude = sanitize_text_field( $data['longitude'] );
            update_user_meta( $userID, 'fave_author_google_longitude', $longitude );
        } else {
            delete_user_meta( $userID, 'fave_author_google_longitude' );
        }

        // Update email.
        if ( ! empty( $data['useremail'] ) ) {
            $useremail = sanitize_email( $data['useremail'] );
            $useremail = is_email( $useremail );
            if ( ! $useremail ) {
                return new WP_Error( 'invalid_email', esc_html__( 'The Email you entered is not valid. Please try again.', 'houzez' ) );
            } else {
                $email_exists = email_exists( $useremail );
                if ( $email_exists && ( $email_exists != $userID ) ) {
                    return new WP_Error( 'email_exists', esc_html__( 'This Email is already used by another user. Please try a different one.', 'houzez' ) );
                } else {
                    $result = wp_update_user( array( 'ID' => $userID, 'user_email' => $useremail ) );
                    if ( is_wp_error( $result ) ) {
                        return new WP_Error( 'email_update_failed', $result->get_error_message() );
                    }
                }
            }

            // Optionally update profile picture & related data.
            $profile_pic_id = ! empty( $data['profile-pic-id'] ) ? intval( $data['profile-pic-id'] ) : 0;
            // $profile_pic could be handled as needed (e.g. via media upload), not shown here.

            // For users with agent or agency roles, update additional data via dedicated functions.
            $current_user_roles = (array) $current_user->roles;
            $agent_id = get_user_meta( $userID, 'fave_author_agent_id', true );
            if ( in_array( 'houzez_agent', $current_user_roles, true ) ) {
                houzez_update_user_agent(
                    $agent_id, $firstname, $lastname, ( isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : '' ),
                    ( isset( $data['bio'] ) ? wp_kses_post( wpautop( wptexturize( $data['bio'] ) ) ) : '' ),
                    ( isset( $data['userphone'] ) ? sanitize_text_field( $data['userphone'] ) : '' ),
                    ( isset( $data['usermobile'] ) ? sanitize_text_field( $data['usermobile'] ) : '' ),
                    ( isset( $data['whatsapp'] ) ? sanitize_text_field( $data['whatsapp'] ) : '' ),
                    ( isset( $data['userskype'] ) ? sanitize_text_field( $data['userskype'] ) : '' ),
                    ( isset( $data['facebook'] ) ? sanitize_text_field( $data['facebook'] ) : '' ),
                    ( isset( $data['twitter'] ) ? sanitize_text_field( $data['twitter'] ) : '' ),
                    ( isset( $data['linkedin'] ) ? sanitize_text_field( $data['linkedin'] ) : '' ),
                    ( isset( $data['instagram'] ) ? sanitize_text_field( $data['instagram'] ) : '' ),
                    ( isset( $data['pinterest'] ) ? sanitize_text_field( $data['pinterest'] ) : '' ),
                    ( isset( $data['youtube'] ) ? sanitize_text_field( $data['youtube'] ) : '' ),
                    ( isset( $data['vimeo'] ) ? sanitize_text_field( $data['vimeo'] ) : '' ),
                    ( isset( $data['googleplus'] ) ? sanitize_text_field( $data['googleplus'] ) : '' ),
                    '', // $profile_pic (if available)
                    $profile_pic_id,
                    ( isset( $data['website'] ) ? sanitize_text_field( $data['website'] ) : '' ),
                    $useremail,
                    ( isset( $data['license'] ) ? sanitize_text_field( $data['license'] ) : '' ),
                    ( isset( $data['tax_number'] ) ? sanitize_text_field( $data['tax_number'] ) : '' ),
                    ( isset( $data['fax_number'] ) ? sanitize_text_field( $data['fax_number'] ) : '' ),
                    $userlangs,
                    ( isset( $data['user_address'] ) ? sanitize_text_field( $data['user_address'] ) : '' ),
                    $user_company,
                    ( isset( $data['service_areas'] ) ? sanitize_text_field( $data['service_areas'] ) : '' ),
                    ( isset( $data['specialties'] ) ? sanitize_text_field( $data['specialties'] ) : '' ),
                    ( isset( $data['tiktok'] ) ? sanitize_text_field( $data['tiktok'] ) : '' ),
                    ( isset( $data['telegram'] ) ? sanitize_text_field( $data['telegram'] ) : '' ),
                    ( isset( $data['line_id'] ) ? sanitize_text_field( $data['line_id'] ) : '' ),
                    ( isset( $data['zillow'] ) ? sanitize_text_field( $data['zillow'] ) : '' ),
                    ( isset( $data['realtor_com'] ) ? sanitize_text_field( $data['realtor_com'] ) : '' )
                );
            } elseif ( in_array( 'houzez_agency', $current_user_roles, true ) ) {
                houzez_update_user_agency(
                    $agency_id, $firstname, $lastname, ( isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : '' ),
                    ( isset( $data['bio'] ) ? wp_kses_post( wpautop( wptexturize( $data['bio'] ) ) ) : '' ),
                    ( isset( $data['userphone'] ) ? sanitize_text_field( $data['userphone'] ) : '' ),
                    ( isset( $data['usermobile'] ) ? sanitize_text_field( $data['usermobile'] ) : '' ),
                    ( isset( $data['whatsapp'] ) ? sanitize_text_field( $data['whatsapp'] ) : '' ),
                    ( isset( $data['userskype'] ) ? sanitize_text_field( $data['userskype'] ) : '' ),
                    ( isset( $data['facebook'] ) ? sanitize_text_field( $data['facebook'] ) : '' ),
                    ( isset( $data['twitter'] ) ? sanitize_text_field( $data['twitter'] ) : '' ),
                    ( isset( $data['linkedin'] ) ? sanitize_text_field( $data['linkedin'] ) : '' ),
                    ( isset( $data['instagram'] ) ? sanitize_text_field( $data['instagram'] ) : '' ),
                    ( isset( $data['pinterest'] ) ? sanitize_text_field( $data['pinterest'] ) : '' ),
                    ( isset( $data['youtube'] ) ? sanitize_text_field( $data['youtube'] ) : '' ),
                    ( isset( $data['vimeo'] ) ? sanitize_text_field( $data['vimeo'] ) : '' ),
                    ( isset( $data['googleplus'] ) ? sanitize_text_field( $data['googleplus'] ) : '' ),
                    '', // $profile_pic (if available)
                    $profile_pic_id,
                    ( isset( $data['website'] ) ? sanitize_text_field( $data['website'] ) : '' ),
                    $useremail,
                    ( isset( $data['license'] ) ? sanitize_text_field( $data['license'] ) : '' ),
                    ( isset( $data['tax_number'] ) ? sanitize_text_field( $data['tax_number'] ) : '' ),
                    ( isset( $data['user_address'] ) ? sanitize_text_field( $data['user_address'] ) : '' ),
                    ( isset( $data['user_location'] ) ? sanitize_text_field( $data['user_location'] ) : '' ),
                    ( isset( $data['latitude'] ) ? sanitize_text_field( $data['latitude'] ) : '' ),
                    ( isset( $data['longitude'] ) ? sanitize_text_field( $data['longitude'] ) : '' ),
                    ( isset( $data['fax_number'] ) ? sanitize_text_field( $data['fax_number'] ) : '' ),
                    $userlangs,
                    ( isset( $data['tiktok'] ) ? sanitize_text_field( $data['tiktok'] ) : '' ),
                    ( isset( $data['telegram'] ) ? sanitize_text_field( $data['telegram'] ) : '' ),
                    ( isset( $data['line_id'] ) ? sanitize_text_field( $data['line_id'] ) : '' ),
                    ( isset( $data['service_areas'] ) ? sanitize_text_field( $data['service_areas'] ) : '' ),
                    ( isset( $data['specialties'] ) ? sanitize_text_field( $data['specialties'] ) : '' ),
                    ( isset( $data['zillow'] ) ? sanitize_text_field( $data['zillow'] ) : '' ),
                    ( isset( $data['realtor_com'] ) ? sanitize_text_field( $data['realtor_com'] ) : '' )
                );
            }
        }

        return array( 'success' => true, 'message' => esc_html__( 'Profile updated', 'houzez' ) );
    }
}

/* ------------------------------------------------------------------------------
* Ajax Update Profile function
/------------------------------------------------------------------------------ */
add_action( 'wp_ajax_houzez_ajax_update_profile', 'houzez_ajax_update_profile' );
if ( ! function_exists( 'houzez_ajax_update_profile' ) ) {
    function houzez_ajax_update_profile() {
        $data   = $_POST;
        $result = houzez_process_update_profile( $data, true );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        } else {
            wp_send_json_success( $result );
        }
    }
}


/* ------------------------------------------------------------------------------
* Update agency user
/------------------------------------------------------------------------------ */
if( !function_exists('houzez_update_user_agency') ) {
    function houzez_update_user_agency ( $agency_id, $firstname, $lastname, $title, $about, $userphone, $usermobile, $whatsapp, $userskype, $facebook, $twitter, $linkedin, $instagram, $pinterest, $youtube, $vimeo, $googleplus, $profile_pic, $profile_pic_id, $website, $useremail, $license, $tax_number, $user_address, $user_location, $latitude, $longitude, $fax_number, $userlangs, $tiktok, $telegram, $line_id, $service_areas = '', $specialties = '', $zillow = '', $realtor_com = '' ) {

        $args = array(
            'ID' => $agency_id,
            'post_title' => $title,
            'post_content' => $about
        );
        $post_id = wp_update_post($args);

        update_post_meta( $agency_id, 'fave_agency_licenses', $license );
        update_post_meta( $agency_id, 'fave_agency_tax_no', $tax_number );
        update_post_meta( $agency_id, 'fave_agency_fax', $fax_number );
        update_post_meta( $agency_id, 'fave_agency_facebook', $facebook );
        update_post_meta( $agency_id, 'fave_agency_linkedin', $linkedin );
        update_post_meta( $agency_id, 'fave_agency_twitter', $twitter );
        update_post_meta( $agency_id, 'fave_agency_pinterest', $pinterest );
        update_post_meta( $agency_id, 'fave_agency_instagram', $instagram );
        update_post_meta( $agency_id, 'fave_agency_youtube', $youtube );
        update_post_meta( $agency_id, 'fave_agency_tiktok', $tiktok );
        update_post_meta( $agency_id, 'fave_agency_telegram', $telegram );
        update_post_meta( $agency_id, 'fave_agency_zillow', $zillow );
        update_post_meta( $agency_id, 'fave_agency_realtor_com', $realtor_com );
        update_post_meta( $agency_id, 'fave_agency_vimeo', $vimeo );
        update_post_meta( $agency_id, 'fave_agency_web', $website );
        update_post_meta( $agency_id, 'fave_agency_googleplus', $googleplus );
        update_post_meta( $agency_id, 'fave_agency_phone', $userphone );
        update_post_meta( $agency_id, 'fave_agency_mobile', $usermobile );
        update_post_meta( $agency_id, 'fave_agency_whatsapp', $whatsapp );
        update_post_meta( $agency_id, 'fave_agency_line_id', $line_id );
        update_post_meta( $agency_id, 'fave_agency_address', $user_address );
        update_post_meta( $agency_id, 'fave_agency_map_address', $user_location );
        update_post_meta( $agency_id, 'fave_agency_location', $latitude.','.$longitude );
        update_post_meta( $agency_id, 'fave_agency_email', $useremail );
        update_post_meta( $agency_id, 'fave_agency_language', $userlangs );
        //update_post_meta( $agency_id, '_thumbnail_id', $profile_pic_id );
        update_post_meta( $agency_id, 'fave_agency_service_area', $service_areas );
        update_post_meta( $agency_id, 'fave_agency_specialties', $specialties );

    }
}

/* ------------------------------------------------------------------------------
* Update agent user
/------------------------------------------------------------------------------ */
if( !function_exists('houzez_update_user_agent') ) {
    function houzez_update_user_agent ( $agent_id, $firstname, $lastname, $title, $about, $userphone, $usermobile, $whatsapp, $userskype, $facebook, $twitter, $linkedin, $instagram, $pinterest, $youtube, $vimeo, $googleplus, $profile_pic, $profile_pic_id, $website, $useremail, $license, $tax_number, $fax_number, $userlangs, $user_address, $user_company, $service_areas, $specialties, $tiktok, $telegram, $line_id, $zillow, $realtor_com ) {


        if( !empty( $firstname ) || !empty( $lastname ) ) {
            $agr = array(
                'ID' => $agent_id,
                'post_title' => $firstname.' '.$lastname,
                'post_content' => $about
            );
            $post_id = wp_update_post($agr);
        } else {
            $agr = array(
                'ID' => $agent_id,
                'post_content' => $about
            );
            $post_id = wp_update_post($agr);
        }

        
        update_post_meta( $agent_id, 'fave_agent_license', $license );
        update_post_meta( $agent_id, 'fave_agent_tax_no', $tax_number );
        update_post_meta( $agent_id, 'fave_agent_facebook', $facebook );
        update_post_meta( $agent_id, 'fave_agent_linkedin', $linkedin );
        update_post_meta( $agent_id, 'fave_agent_twitter', $twitter );
        update_post_meta( $agent_id, 'fave_agent_pinterest', $pinterest );
        update_post_meta( $agent_id, 'fave_agent_instagram', $instagram );
        update_post_meta( $agent_id, 'fave_agent_youtube', $youtube );
        update_post_meta( $agent_id, 'fave_agent_tiktok', $tiktok );
        update_post_meta( $agent_id, 'fave_agent_telegram', $telegram );
        update_post_meta( $agent_id, 'fave_agent_vimeo', $vimeo );
        update_post_meta( $agent_id, 'fave_agent_zillow', $zillow );
        update_post_meta( $agent_id, 'fave_agent_realtor_com', $realtor_com );
        update_post_meta( $agent_id, 'fave_agent_website', $website );
        update_post_meta( $agent_id, 'fave_agent_googleplus', $googleplus );
        update_post_meta( $agent_id, 'fave_agent_office_num', $userphone );
        update_post_meta( $agent_id, 'fave_agent_fax', $fax_number );
        update_post_meta( $agent_id, 'fave_agent_mobile', $usermobile );
        update_post_meta( $agent_id, 'fave_agent_whatsapp', $whatsapp );
        update_post_meta( $agent_id, 'fave_agent_line_id', $line_id );
        update_post_meta( $agent_id, 'fave_agent_skype', $userskype );
        update_post_meta( $agent_id, 'fave_agent_position', $title );
        update_post_meta( $agent_id, 'fave_agent_des', $about );
        update_post_meta( $agent_id, 'fave_agent_email', $useremail );
        update_post_meta( $agent_id, 'fave_agent_language', $userlangs );
        update_post_meta( $agent_id, 'fave_agent_address', $user_address );
        update_post_meta( $agent_id, 'fave_agent_company', $user_company );
        update_post_meta( $agent_id, 'fave_agent_service_area', $service_areas );
        update_post_meta( $agent_id, 'fave_agent_specialties', $specialties );
        //update_post_meta( $agent_id, '_thumbnail_id', $profile_pic_id );

    }
}

/* ------------------------------------------------------------------------------
* Update agency user agent
/------------------------------------------------------------------------------ */
if( !function_exists('houzez_update_agency_user_agent') ) {
    function houzez_update_agency_user_agent($agency_user_agent_id, $firstname, $lastname, $useremail)
    {
        if (!empty($firstname) || !empty($lastname)) {
            $agr = array(
                'ID' => $agency_user_agent_id,
                'post_title' => $firstname . ' ' . $lastname
            );
            $post_id = wp_update_post($agr);
        }
        update_post_meta( $post_id, 'fave_agent_email', $useremail );
    }
}

/* ------------------------------------------------------------------------------
* Ajax Reset Password function
/------------------------------------------------------------------------------ */
add_action('wp_ajax_houzez_ajax_password_reset', 'houzez_ajax_password_reset');

if (!function_exists('houzez_ajax_password_reset')) {
    function houzez_ajax_password_reset() {
        // Ensure the user is authenticated
        if (!is_user_logged_in()) {
            echo json_encode(array('success' => false, 'msg' => esc_html__('You must be logged in to change the password.', 'houzez')));
            die();
        }

        // Get the current user ID
        $currentUserID = get_current_user_id();

        // Determine the user ID to update
        if ((current_user_can('administrator') || current_user_can('houzez_agency')) && isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
            $userID = intval($_POST['user_id']); // Use the posted user_id
        } else {
            $userID = $currentUserID; // Fallback to the current user's ID
        }

        // Check if the current user has the 'houzez_agency' role and is trying to change another user's password
        if (current_user_can('houzez_agency') && $userID !== $currentUserID) {
            $agency_id = get_user_meta($userID, 'fave_agent_agency', true);
            if ($agency_id != $currentUserID) {
                echo json_encode(array('success' => false, 'msg' => esc_html__('This agent does not belong to your agency.', 'houzez')));
                die();
            }
        }

        // Sanitize and validate password inputs
        $allowed_html = array();
        $newpass = isset($_POST['newpass']) ? wp_kses($_POST['newpass'], $allowed_html) : '';
        $confirmpass = isset($_POST['confirmpass']) ? wp_kses($_POST['confirmpass'], $allowed_html) : '';

        if (empty($newpass) || empty($confirmpass)) {
            echo json_encode(array('success' => false, 'msg' => esc_html__('New password or confirm password is blank', 'houzez')));
            die();
        }

        if ($newpass !== $confirmpass) {
            echo json_encode(array('success' => false, 'msg' => esc_html__('Passwords do not match', 'houzez')));
            die();
        }

        // Verify the nonce to protect against CSRF
        check_ajax_referer('houzez_pass_ajax_nonce', 'houzez-security-pass');

        // Update the user's password
        $user = get_user_by('id', $userID);
        if ($user) {
            wp_set_password($newpass, $userID);
            echo json_encode(array('success' => true, 'msg' => esc_html__('Password updated successfully.', 'houzez')));
        } else {
            echo json_encode(array('success' => false, 'msg' => esc_html__('Failed to update password. User not found.', 'houzez')));
        }

        die();
    }
}


/*-----------------------------------------------------------------------------------*/
/*   Get uploaded file url
/*-----------------------------------------------------------------------------------*/
if( !function_exists( 'houzez_uploaded_image_url' ) ) {
    function houzez_uploaded_image_url( $attachment_data ) {
        $houzez_wp_upload_dir     =   wp_upload_dir();
        $upload_file_path_array   =   explode( '/', $attachment_data['file'] );
        $upload_file_path_array   =   array_slice( $upload_file_path_array, 0, count( $upload_file_path_array ) - 1 );
        $uploaded_image_dir       =   implode( '/', $upload_file_path_array );
        $houzez_thumbnail     =   null;
        if ( isset( $attachment_data['sizes']['houzez-image350_350'] ) ) {
            $houzez_thumbnail     =   $attachment_data['sizes']['houzez-image350_350']['file'];
        } else {
            $houzez_thumbnail     =   $attachment_data['sizes']['thumbnail']['file'];
        }
        return $houzez_wp_upload_dir['baseurl'] . '/' . $uploaded_image_dir . '/' . $houzez_thumbnail ;
    }
}


/* ------------------------------------------------------------------------------
/  User Profile Link
/ ------------------------------------------------------------------------------ */
if( !function_exists('houzez_get_dashboard_profile_link') ):
    function houzez_get_dashboard_profile_link(){
        $get_pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'template/user_dashboard_profile.php'
        ));

        if( $get_pages ){
            $get_dash_link = get_permalink( $get_pages[0]->ID);
        }else{
            $get_dash_link = home_url();
        }

        return $get_dash_link;
    }
endif; // end   houzez_get_dashboard_profile_link

/* ------------------------------------------------------------------------------
/  Update User Profile on register
/ ------------------------------------------------------------------------------ */
if( !function_exists('houzez_update_profile') ):

    function houzez_update_profile( $userID ) {

    }
endif; // end houzez_update_profile

/*-----------------------------------------------------------------------------------*/
/*  Houzez Delete Account
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_delete_account', 'houzez_delete_account' );

if ( !function_exists( 'houzez_delete_account' ) ) :

    function houzez_delete_account() {

        if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
            $userID = intval($_POST['user_id']); // Sanitize the input
        } else {
            $userID  = get_current_user_id();
        }

        $agent_id = get_user_meta($userID, 'fave_author_agent_id', true);
        $agency_id = get_user_meta($userID, 'fave_author_agency_id', true);

        wp_delete_user( $userID );

        if( !empty( $agent_id ) ) {
            houzez_delete_user_agent($agent_id);
        }
        if( !empty( $agency_id ) ) {
            houzez_delete_user_agency($agency_id);
        }
        
        houzez_delete_user_searches($userID);

        echo json_encode( array( 'success' => true, 'msg' => esc_html__('success', 'houzez') ) );
        wp_die();
    }

endif;

/*-----------------------------------------------------------------------------------*/
/* Delete Profile Picture
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_delete_profile_pic', 'houzez_delete_profile_pic' );

if ( !function_exists( 'houzez_delete_profile_pic' ) ) :

    function houzez_delete_profile_pic() {

        // Ensure the user is authenticated
        if (!is_user_logged_in()) {
            echo json_encode(array('success' => false, 'msg' => esc_html__('You must be logged in to change the password.', 'houzez')));
            die();
        }

        // Get the current user ID
        $currentUserID = get_current_user_id();

        // Determine the user ID to update
        if ((current_user_can('administrator') || current_user_can('houzez_agency')) && isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
            $user_id = intval($_POST['user_id']); // Use the posted user_id
        } else {
            $user_id = $currentUserID; // Fallback to the current user's ID
        }

        // Check if the current user has the 'houzez_agency' role and is trying to change another user's password
        if (current_user_can('houzez_agency') && $user_id !== $currentUserID) {
            $agency_id = get_user_meta($user_id, 'fave_agent_agency', true);
            if ($agency_id != $currentUserID) {
                echo json_encode(array('success' => false, 'msg' => esc_html__('This agent does not belong to your agency.', 'houzez')));
                die();
            }
        }

        $picture_id = isset($_POST['picture_id']) ? $_POST['picture_id'] : '';

        delete_user_meta( $user_id, 'fave_author_picture_id' );
        delete_user_meta( $user_id, 'fave_author_custom_picture' );

        if( ! empty($picture_id) ) {
            wp_delete_attachment($picture_id, true);
        }

        echo json_encode( array( 'success' => true, 'msg' => esc_html__('success', 'houzez') ) );
        wp_die();
    }

endif;

if(!function_exists('houzez_delete_user_agent')) {
    function houzez_delete_user_agent($agent_id) {
        if( $agent_id ) {
            wp_delete_post( $agent_id );
        }
        return true;
    }
}

if(!function_exists('houzez_delete_user_agency')) {
    function houzez_delete_user_agency($agency_id) {
        if( $agency_id ) {
            wp_delete_post( $agency_id );
            return true;
        }
    }
}

if(!function_exists('houzez_delete_user_searches')) {
    function houzez_delete_user_searches($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'houzez_search';
        $wpdb->query( 
            $wpdb->prepare("DELETE FROM $table_name WHERE auther_id = %d", $user_id)
        );
        return true;
    }
}

add_action( 'delete_user', 'houzez_delete_user_admin' );
if(!function_exists('houzez_delete_user_admin')) {
    function houzez_delete_user_admin($user_id) {
        $agent_id = get_user_meta($user_id, 'fave_author_agent_id', true);
        $agency_id = get_user_meta($user_id, 'fave_author_agency_id', true);
        $user_facebook_id = get_user_meta($user_id, 'houzez_user_facebook_id', true);

        delete_option( 'houzez_user_facebook_id_'.$user_facebook_id );
        delete_option( 'houzez_user_facebook_info_'.$user_facebook_id );

        houzez_delete_user_agent($agent_id);
        houzez_delete_user_agency($agency_id);
        houzez_delete_user_searches($user_id);
    }
}

/*-----------------------------------------------------------------------------------*/
/*  Houzez Delete agency agent Account
/*-----------------------------------------------------------------------------------*/
// add_action( 'wp_ajax_houzez_delete_agency_agent', 'houzez_delete_agency_agent' );

// if ( !function_exists( 'houzez_delete_agency_agent' ) ) :

//     function houzez_delete_agency_agent() {

//         check_ajax_referer( 'agent_delete_nonce', 'agent_delete_security' );

//         $userID = get_current_user_id();

//         $agent_id = $_POST['agent_id'];
//         $agent_parent = get_user_meta($agent_id, 'fave_agent_agency', true);
//         $agent_cpt_id = get_user_meta($agent_id, 'fave_author_agent_id', true);
//         $agency_id = get_user_meta($agent_id, 'fave_author_agency_id', true);
        
//         if( $userID == $agent_parent ) {
//             wp_delete_user( $agent_id );
//         }

//         if( !empty($agent_cpt_id) ) {
//             wp_delete_post( $agent_cpt_id, true );
//         }

//         if( !empty( $agent_id ) ) {
//             houzez_delete_user_agent($agent_id);
//         }
        
//         houzez_delete_user_searches($agent_id);

//         echo json_encode( array( 'success' => true, 'msg' => esc_html__('success', 'houzez') ) );
//         wp_die();
//     }

// endif;

/**
 * AJAX handler for deleting one or more properties,
 * and crediting the appropriate package owner (user or agency).
 */
add_action( 'wp_ajax_houzez_delete_agents', 'houzez_delete_agents' );

if ( ! function_exists( 'houzez_delete_agents' ) ) {
    function houzez_delete_agents() {
        // 1) Security check
        $nonce = isset( $_POST['security'] ) ? sanitize_text_field( $_POST['security'] ) : '';
        if ( ! wp_verify_nonce( $nonce, 'delete_agents_nonce' ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Security check failed!', 'houzez' ) ] );
        }

        // 2) Collect & sanitize IDs
        if ( empty( $_POST['agent_ids'] ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'No agents selected for deletion.', 'houzez' ) ] );
        }
        $ids = $_POST['agent_ids'];
        if ( ! is_array( $ids ) ) {
            $ids = [ $ids ];
        }
        $ids = array_map( 'absint', $ids );

        // 3) Prepare user context
        $current_user     = get_current_user_id();
        $deleted_count    = 0;
        $errors           = [];


        // 4) Loop & delete each
        foreach ( $ids as $agent_id ) {
            $agent_parent = get_user_meta($agent_id, 'fave_agent_agency', true);
            $agent_cpt_id = get_user_meta($agent_id, 'fave_author_agent_id', true);
            $agency_id = get_user_meta($agent_id, 'fave_author_agency_id', true);

            // Check if current user is the parent agency
            if( $current_user == $agent_parent ) {
                if( wp_delete_user( $agent_id ) ) {
                    $deleted_count++;
                } else {
                    $errors[] = sprintf( esc_html__( 'Failed to delete user ID %d.', 'houzez' ), $agent_id );
                    continue;
                }
            } else {
                $errors[] = sprintf( esc_html__( 'No permission to delete agent ID %d.', 'houzez' ), $agent_id );
                continue;
            }
            
            // Delete agent CPT if exists
            if( !empty($agent_cpt_id) ) {
                wp_delete_post( $agent_cpt_id, true );
            }
            
            // Clean up agent data
            if( !empty( $agent_id ) ) {
                houzez_delete_user_agent($agent_id);
            }
            
            // Delete saved searches
            houzez_delete_user_searches($agent_id);
        }

        // 5) Build response
        if ( $deleted_count > 0 ) {
            // Success message
            $message = sprintf(
                _n( '%d agent deleted successfully.', '%d agents deleted successfully.', $deleted_count, 'houzez' ),
                $deleted_count
            );
            
            if ( !empty($errors) ) {
                $message .= ' ' . esc_html__('With errors:', 'houzez') . ' ' . implode( ' ', $errors );
            }

            wp_send_json_success( [ 
                'message' => $message,
                'deleted' => $deleted_count
            ] );
        } else {
            // Nothing deleted
            $err_msg = !empty($errors) ? implode( ' ', $errors ) : esc_html__( 'No agents were deleted.', 'houzez' );
            wp_send_json_error( [ 'message' => $err_msg ] );
        }
    }
}

add_action( 'wp_ajax_houzez_change_user_currency', 'houzez_change_user_currency' );
if(!function_exists('houzez_change_user_currency')) {
    function houzez_change_user_currency() {

        if ( is_user_logged_in() && isset( $_POST['currency'] ) ) {

            global $current_user;
            wp_get_current_user();
            $userID = $current_user->ID;

            update_user_meta( $userID, 'fave_author_currency', $_POST['currency']);

            $ajax_response = array('success' => true, 'reason' => esc_html__('Currency updated!', 'houzez'));

            echo json_encode($ajax_response);

            wp_die();
        }
    }
}

add_action( 'wp_ajax_houzez_change_user_role', 'houzez_change_user_role' );
if ( !function_exists( 'houzez_change_user_role' ) ) :
    function houzez_change_user_role()
    {

        check_ajax_referer( 'houzez_role_pass_ajax_nonce', 'houzez-role-security-pass' );

        $ajax_response = array();
        $user_roles = Array ( 'houzez_agency', 'houzez_agent', 'houzez_buyer', 'houzez_seller', 'houzez_owner', 'houzez_manager' );

        if ( is_user_logged_in() && isset( $_POST['role'] ) && in_array( $_POST['role'], $user_roles ) ) {

            global $current_user;
            wp_get_current_user();
            $userID = $current_user->ID;
            $username = $current_user->user_login;
            $user_email = $current_user->user_email;
            $role = $_POST['role'];
            $current_author_meta = get_user_meta( $userID );
            $authorAgentID = $current_author_meta['fave_author_agent_id'][0];
            $authorAgencyID = $current_author_meta['fave_author_agency_id'][0];

            $user_as_agent = houzez_option('user_as_agent');

            $user_id = wp_update_user( Array ( 'ID' => $userID, 'role' => $role ) );

            if ( is_wp_error( $user_id ) ) {

                $ajax_response = array('success' => false, 'reason' => esc_html__('Role not updated!', 'houzez'));

            } else {

                $ajax_response = array('success' => true, 'reason' => esc_html__('Role updated!', 'houzez'));

                if( $user_as_agent == "yes" && ($role == 'houzez_agent' || $role == 'houzez_agency') ) {
                    if( $role == 'houzez_agency' ) {
                        wp_delete_post( $authorAgentID, true );
                        houzez_register_as_agency($username, $user_email, $userID);
                        update_user_meta( $userID, 'fave_author_agent_id', '');
                        
                    }elseif( $role == 'houzez_agent' ) {
                        wp_delete_post( $authorAgencyID, true );
                        houzez_register_as_agent($username, $user_email, $userID);
                        update_user_meta( $userID, 'fave_author_agency_id', '');
                    }
                } else {
                    wp_delete_post( $authorAgentID, true );
                    wp_delete_post( $authorAgencyID, true );
                    update_user_meta( $userID, 'fave_author_agent_id', '');
                    update_user_meta( $userID, 'fave_author_agency_id', '');
                }
            }

        } else {

            $ajax_response = array('success' => false, 'reason' => esc_html__('Role not updated!', 'houzez'));

        }

        echo json_encode($ajax_response);

        wp_die();
    }
endif;

/* -----------------------------------------------------------------------------------------------------------
 *  Handle role changes from WordPress admin (Edit User screen)
 *  Replicates the frontend role change functionality for admin-initiated changes
 -------------------------------------------------------------------------------------------------------------*/
if( !function_exists('houzez_admin_role_change_handler') ) :
    function houzez_admin_role_change_handler( $user_id, $new_role, $old_roles ) {

        // Check if user_as_agent option is enabled
        $user_as_agent = houzez_option('user_as_agent');

        // Define allowed roles that trigger agent/agency post management
        $agent_agency_roles = array('houzez_agency', 'houzez_agent');
        $all_houzez_roles = array('houzez_agency', 'houzez_agent', 'houzez_buyer', 'houzez_seller', 'houzez_owner', 'houzez_manager');

        // Only proceed if the new role is a Houzez role
        if ( !in_array( $new_role, $all_houzez_roles ) ) {
            return;
        }

        // Get user data
        $user = get_userdata( $user_id );
        if ( !$user ) {
            return;
        }

        $username = $user->user_login;
        $user_email = $user->user_email;

        // Get existing agent/agency post IDs
        $authorAgentID = get_user_meta( $user_id, 'fave_author_agent_id', true );
        $authorAgencyID = get_user_meta( $user_id, 'fave_author_agency_id', true );

        // Determine old role (first role from old_roles array)
        $old_role = !empty($old_roles) ? $old_roles[0] : '';

        // If role hasn't actually changed, do nothing
        if ( $old_role === $new_role ) {
            return;
        }

        // Apply the same logic as frontend role switcher
        if( $user_as_agent == "yes" && in_array( $new_role, $agent_agency_roles ) ) {

            // Changing TO agency role
            if( $new_role == 'houzez_agency' ) {
                // Delete existing agent post if it exists
                if ( !empty($authorAgentID) ) {
                    wp_delete_post( $authorAgentID, true );
                }

                // Create agency post only if it doesn't already exist
                if ( empty($authorAgencyID) ) {
                    houzez_register_as_agency($username, $user_email, $user_id);
                }

                // Clear agent meta
                update_user_meta( $user_id, 'fave_author_agent_id', '');
            }

            // Changing TO agent role
            elseif( $new_role == 'houzez_agent' ) {
                // Delete existing agency post if it exists
                if ( !empty($authorAgencyID) ) {
                    wp_delete_post( $authorAgencyID, true );
                }

                // Create agent post only if it doesn't already exist
                if ( empty($authorAgentID) ) {
                    houzez_register_as_agent($username, $user_email, $user_id);
                }

                // Clear agency meta
                update_user_meta( $user_id, 'fave_author_agency_id', '');
            }

        } else {
            // Changing to non-agent/agency role (buyer, seller, owner, manager, etc.)
            // Delete both agent and agency posts if they exist
            if ( !empty($authorAgentID) ) {
                wp_delete_post( $authorAgentID, true );
            }
            if ( !empty($authorAgencyID) ) {
                wp_delete_post( $authorAgencyID, true );
            }

            // Clear both meta fields
            update_user_meta( $user_id, 'fave_author_agent_id', '');
            update_user_meta( $user_id, 'fave_author_agency_id', '');
        }
    }
endif;

// Hook into set_user_role action (fires when admin changes user role)
add_action( 'set_user_role', 'houzez_admin_role_change_handler', 10, 3 );

add_filter( 'random_user_name', 'random_user_name', 10, 1 );

if( !function_exists('random_user_name') ) {
    function random_user_name($username)
    {

        $user_name = $username . rand(3, 5);

        if (username_exists($user_name)) :

            apply_filters( 'random_user_name', $username );

        else :

            return $user_name;

        endif;
    }
}

/* -----------------------------------------------------------------------------------------------------------
 *  Forgot PassWord function
 -------------------------------------------------------------------------------------------------------------*/

$reset_password_link = houzez_get_template_link_2( 'template/reset_password.php' );

if ( !empty( $reset_password_link ) ) :

    add_action( 'login_form_rp', 'redirect_to_custom_password_reset' );
    add_action( 'login_form_resetpass', 'redirect_to_custom_password_reset' );

endif;

if ( !function_exists( 'redirect_to_custom_password_reset' ) ) :

    function redirect_to_custom_password_reset() {

        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) :

            $reset_password_link = houzez_get_template_link_2( 'template/reset_password.php' );

            // Verify key / login combo
            $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );

            if ( ! $user || is_wp_error( $user ) ) :

                if ( $user && $user->get_error_code() === 'expired_key' ) :

                    wp_redirect( home_url( $reset_password_link. '?login=expiredkey' ) );

                else :

                    wp_redirect( home_url( $reset_password_link. '?login=invalidkey' ) );

                endif;

                exit;

            endif;


            $redirect_url = add_query_arg( 
                array(
                    'login' => esc_attr( $_REQUEST['login'] ), 
                    'key' => esc_attr( $_REQUEST['key'] ),
                ),
                $reset_password_link 
            );

            wp_redirect( $redirect_url );

            exit;

        endif;

    }

endif;

add_action( 'wp_ajax_nopriv_houzez_reset_password', 'houzez_reset_password' );
add_action( 'wp_ajax_houzez_reset_password', 'houzez_reset_password' );

if( !function_exists('houzez_reset_password') ) {
    function houzez_reset_password() {
        $allowed_html   = array();

        $newpass        = wp_kses( $_POST['password'], $allowed_html );
        $confirmpass    = wp_kses( $_POST['confirm_pass'], $allowed_html );
        $rq_login   = wp_kses( $_POST['rq_login'], $allowed_html );
        $rp_key   = wp_kses( $_POST['rp_key'], $allowed_html );

        $user = check_password_reset_key( $rp_key, $rq_login );

        if ( ! $user || is_wp_error( $user ) ) {

            if ($user && $user->get_error_code() === 'expired_key') {
                echo json_encode(array('success' => false, 'msg' => esc_html__('Reset password Session key expired.', 'houzez')));
                die();
            } else {
                echo json_encode(array('success' => false, 'msg' => esc_html__('Invalid password reset Key', 'houzez')));
                die();
            }
        }

        if( $newpass == '' || $confirmpass == '' ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('New password or confirm password is blank', 'houzez') ) );
            die();
        }
        if( $newpass != $confirmpass ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Passwords do not match', 'houzez') ) );
            die();
        }

        reset_password( $user, $newpass );
        echo json_encode( array( 'success' => true, 'msg' => esc_html__('Password reset successfully, you can login now.', 'houzez') ) );
        die();
    }
}


/* -----------------------------------------------------------------------------------------------------------
 *  Update profile
 -------------------------------------------------------------------------------------------------------------*/
add_action( 'profile_update', 'houzez_profile_update', 10, 2 );
if( !function_exists('houzez_profile_update') ) {
    function houzez_profile_update($user_id, $old_user_data)
    {
        $user_agent_id = get_the_author_meta('fave_author_agent_id', $user_id);

        $user_agency_id = get_the_author_meta('fave_author_agency_id', $user_id);
        $roles = get_the_author_meta('roles', $user_id);

        if ( in_array('houzez_agent', (array)$roles ) || in_array('houzez_agency', (array)$roles ) ) {
            $email = get_the_author_meta('email', $user_id);
            $website = get_the_author_meta('url', $user_id);
            $first_name = get_the_author_meta('first_name', $user_id);
            $last_name = get_the_author_meta('last_name', $user_id);
            $description = get_the_author_meta('description', $user_id);
            $fave_author_title = get_the_author_meta('fave_author_title', $user_id);
            $fave_author_company = get_the_author_meta('fave_author_company', $user_id);
            $fave_author_phone = get_the_author_meta('fave_author_phone', $user_id);
            $fave_author_fax = get_the_author_meta('fave_author_fax', $user_id);
            $fave_author_mobile = get_the_author_meta('fave_author_mobile', $user_id);
            $fave_author_whatsapp = get_the_author_meta('fave_author_whatsapp', $user_id);
            $fave_author_line_id = get_the_author_meta('fave_author_line_id', $user_id);
            $fave_author_skype = get_the_author_meta('fave_author_skype', $user_id);
            $fave_author_custom_picture = get_the_author_meta('fave_author_custom_picture', $user_id);
            $fave_author_facebook = get_the_author_meta('fave_author_facebook', $user_id);
            $fave_author_linkedin = get_the_author_meta('fave_author_linkedin', $user_id);
            $fave_author_twitter = get_the_author_meta('fave_author_twitter', $user_id);
            $fave_author_pinterest = get_the_author_meta('fave_author_pinterest', $user_id);
            $fave_author_instagram = get_the_author_meta('fave_author_instagram', $user_id);
            $fave_author_youtube = get_the_author_meta('fave_author_youtube', $user_id);
            $fave_author_tiktok = get_the_author_meta('fave_author_tiktok', $user_id);
            $fave_author_telegram = get_the_author_meta('fave_author_telegram', $user_id);
            $fave_author_vimeo = get_the_author_meta('fave_author_vimeo', $user_id);
            $fave_author_zillow = get_the_author_meta('fave_author_zillow', $user_id);
            $fave_author_realtor_com = get_the_author_meta('fave_author_realtor_com', $user_id);
            $fave_author_googleplus = get_the_author_meta('fave_author_googleplus', $user_id);
            $fave_author_language = get_the_author_meta('fave_author_language', $user_id);
            $fave_author_tax_no = get_the_author_meta('fave_author_tax_no', $user_id);
            $fave_author_license = get_the_author_meta('fave_author_license', $user_id);
        
            $fave_author_service_areas = get_the_author_meta('fave_author_service_areas', $user_id);
            $fave_author_specialties = get_the_author_meta('fave_author_specialties', $user_id);

            $agent_featured_iamge = houzez_get_image_id($fave_author_custom_picture);
            $fave_author_picture_id = get_the_author_meta('fave_author_picture_id', $user_id);

            if( empty( $fave_author_picture_id ) ) {
                $fave_author_picture_id = $agent_featured_iamge;
            }



            if ( in_array('houzez_agent', (array)$roles ) ) {
                if (!empty($user_agent_id)) {
                    if( !empty($first_name) || !empty($last_name) ) {

                        $my_post = array(
                            'ID' => $user_agent_id,
                            'post_title' => $first_name.' '.$last_name
                        );
                        wp_update_post($my_post);
                    }

                    update_post_meta($user_agent_id, 'houzez_user_meta_id', $user_id);  // used when agent custom post type updated
                    update_post_meta($user_agent_id, 'fave_agent_des', $description);
                    update_post_meta($user_agent_id, 'fave_agent_position', $fave_author_title);
                    update_post_meta($user_agent_id, 'fave_agent_mobile', $fave_author_mobile);
                    update_post_meta($user_agent_id, 'fave_agent_whatsapp', $fave_author_whatsapp);
                    update_post_meta($user_agent_id, 'fave_agent_line_id', $fave_author_line_id);
                    update_post_meta($user_agent_id, 'fave_agent_office_num', $fave_author_phone);
                    update_post_meta($user_agent_id, 'fave_agent_fax', $fave_author_fax);
                    update_post_meta($user_agent_id, 'fave_agent_skype', $fave_author_skype);
                    update_post_meta($user_agent_id, 'fave_agent_website', $website);
                    update_post_meta($user_agent_id, 'fave_agent_language', $fave_author_language);
                    update_post_meta($user_agent_id, 'fave_agent_tax_no', $fave_author_tax_no);
                    update_post_meta($user_agent_id, 'fave_agent_licenses', $fave_author_license);
                    update_post_meta($user_agent_id, '_thumbnail_id', $fave_author_picture_id);

                    update_post_meta($user_agent_id, 'fave_agent_facebook', $fave_author_facebook);
                    update_post_meta($user_agent_id, 'fave_agent_linkedin', $fave_author_linkedin);
                    update_post_meta($user_agent_id, 'fave_agent_twitter', $fave_author_twitter);
                    update_post_meta($user_agent_id, 'fave_agent_googleplus', $fave_author_googleplus);
                    update_post_meta($user_agent_id, 'fave_agent_youtube', $fave_author_youtube);
                    update_post_meta($user_agent_id, 'fave_agent_tiktok', $fave_author_tiktok);
                    update_post_meta($user_agent_id, 'fave_agent_telegram', $fave_author_telegram);
                    update_post_meta($user_agent_id, 'fave_agent_instagram', $fave_author_instagram);
                    update_post_meta($user_agent_id, 'fave_agent_pinterest', $fave_author_pinterest);
                    update_post_meta($user_agent_id, 'fave_agent_vimeo', $fave_author_vimeo);
                    update_post_meta($user_agent_id, 'fave_agent_zillow', $fave_author_zillow);
                    update_post_meta($user_agent_id, 'fave_agent_realtor_com', $fave_author_realtor_com);
                    update_post_meta($user_agent_id, 'fave_agent_email', $email);

                    update_post_meta($user_agent_id, 'fave_agent_service_area', $fave_author_service_areas);
                    update_post_meta($user_agent_id, 'fave_agent_specialties', $fave_author_specialties);

                    $agency_id = get_user_meta($user_id, 'fave_author_agency_id', true);
                    if( !empty($agency_id) ) {
                        $fave_author_company = get_the_title($agency_id);
                    }
                    update_post_meta($user_agent_id, 'fave_agent_company', $fave_author_company);

                }
            } elseif ( in_array('houzez_agency', (array)$roles ) ) {
                if (!empty($user_agency_id)) {

                    if( !empty($first_name) || !empty($last_name) ) {

                        $my_post = array(
                            'ID' => $user_agency_id,
                            'post_title' => $first_name.' '.$last_name
                        );
                        wp_update_post($my_post);
                    }
                    update_post_meta($user_agency_id, 'houzez_user_meta_id', $user_id);  // used when agent custom post type updated

                    update_post_meta($user_agency_id, 'fave_agency_mobile', $fave_author_mobile);
                    update_post_meta($user_agent_id,  'fave_agency_whatsapp', $fave_author_whatsapp);
                    update_post_meta($user_agent_id,  'fave_agency_line_id', $fave_author_line_id);
                    update_post_meta($user_agency_id, 'fave_agency_phone', $fave_author_phone);
                    update_post_meta($user_agency_id, 'fave_agency_fax', $fave_author_fax);
                    update_post_meta($user_agency_id, 'fave_agency_language', $fave_author_language);
                    update_post_meta($user_agency_id, 'fave_agency_tax_no', $fave_author_tax_no);
                    update_post_meta($user_agency_id, 'fave_agency_licenses', $fave_author_license);
                    update_post_meta($user_agency_id, 'fave_agency_web', $website);
                    update_post_meta($user_agency_id, 'fave_agency_email', $email);
                    update_post_meta($user_agency_id, '_thumbnail_id', $fave_author_picture_id);

                    update_post_meta($user_agency_id, 'fave_agency_facebook', $fave_author_facebook);
                    update_post_meta($user_agency_id, 'fave_agency_linkedin', $fave_author_linkedin);
                    update_post_meta($user_agency_id, 'fave_agency_twitter', $fave_author_twitter);
                    update_post_meta($user_agency_id, 'fave_agency_googleplus', $fave_author_googleplus);
                    update_post_meta($user_agency_id, 'fave_agency_youtube', $fave_author_youtube);
                    update_post_meta($user_agency_id, 'fave_agency_tiktok', $fave_author_tiktok);
                    update_post_meta($user_agency_id, 'fave_agency_telegram', $fave_author_telegram);
                    update_post_meta($user_agency_id, 'fave_agency_instagram', $fave_author_instagram);
                    update_post_meta($user_agency_id, 'fave_agency_pinterest', $fave_author_pinterest);
                    update_post_meta($user_agency_id, 'fave_agency_vimeo', $fave_author_vimeo);
                    update_post_meta($user_agency_id, 'fave_agency_zillow', $fave_author_zillow);
                    update_post_meta($user_agency_id, 'fave_agency_realtor_com', $fave_author_realtor_com);

                    update_post_meta($user_agency_id, 'fave_agency_service_area', $fave_author_service_areas);
                    update_post_meta($user_agency_id, 'fave_agency_specialties', $fave_author_specialties);
                }
            }
        } // End roles if
    }
}



/**
 * Show custom user profile fields
 * @param  obj $user The user object.
 * @return void
 */
if( !function_exists('houzez_custom_user_profile_fields')) {
    function houzez_custom_user_profile_fields($user) {

        if ( in_array('houzez_agent', (array)$user->roles ) ) {
            $information_title = esc_html__('Agent Profile Info', 'houzez');
            $title = esc_html__('Title/Position', 'houzez');

        } elseif ( in_array('houzez_agency', (array)$user->roles ) ) {
            $information_title = esc_html__('Agency Profile Info', 'houzez');
            $title = esc_html__('Agency Name', 'houzez');

        } elseif ( in_array('author', (array)$user->roles ) ) {
            $information_title = esc_html__('Author Profile Info', 'houzez');
            $title = esc_html__('Title/Position', 'houzez');
        } else {
            $information_title = esc_html__('Profile Info', 'houzez');
            $title = esc_html__('Title/Position', 'houzez');
        }
    ?>
        <h2><?php echo $information_title; ?></h2>
        <table class="form-table">
            <input type="hidden" name="houzez_role" value="<?php echo esc_attr($user->roles[0]); ?>">
            <tbody>
                <tr class="user-fave_author_title-wrap">
                    <th><label for="fave_author_title"><?php echo $title; ?></label></th>
                    <td><input type="text" name="fave_author_title" id="fave_author_title" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_title', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>

                <?php if ( !in_array('houzez_agency', (array)$user->roles ) ) { ?>
                <tr class="user-fave_author_company-wrap">
                    <th><label for="fave_author_company"><?php echo esc_html__('Company Name', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_company" id="fave_author_company" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_company', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <?php } ?>

                <tr class="user-fave_author_language-wrap">
                    <th><label for="fave_author_language"><?php echo esc_html__('Language', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_language" id="fave_author_language" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_language', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_license-wrap">
                    <th><label for="fave_author_license"><?php echo esc_html__('License', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_license" id="fave_author_license" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_license', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_tax_no-wrap">
                    <th><label for="fave_author_tax_no"><?php echo esc_html__('Tax Number', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_tax_no" id="fave_author_tax_no" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_tax_no', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_phone-wrap">
                    <th><label for="fave_author_phone"><?php echo esc_html__('Phone', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_phone" id="fave_author_phone" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_phone', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_fax-wrap">
                    <th><label for="fave_author_fax"><?php echo esc_html__('Fax Number', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_fax" id="fave_author_fax" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_fax', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_mobile-wrap">
                    <th><label for="fave_author_mobile"><?php echo esc_html__('Mobile', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_mobile" id="fave_author_mobile" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_mobile', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_whatsapp-wrap">
                    <th><label for="fave_author_whatsapp"><?php echo esc_html__('WhatsApp', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_whatsapp" id="fave_author_whatsapp" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_whatsapp', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_line_id-wrap">
                    <th><label for="fave_author_line_id"><?php echo esc_html__('Line ID', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_line_id" id="fave_author_line_id" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_line_id', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_telegram-wrap">
                    <th><label for="fave_author_telegram"><?php echo esc_html__('Telegram Username', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_telegram" id="fave_author_telegram" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_telegram', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_skype-wrap">
                    <th><label for="fave_author_skype"><?php echo esc_html__('Skype', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_skype" id="fave_author_skype" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_skype', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_custom_picture-wrap">
                    <th><label for="fave_author_custom_picture"><?php echo esc_html__('Picture Url', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_custom_picture" id="fave_author_custom_picture" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_custom_picture', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_agency_id-wrap">
                    <th><label for="fave_author_agency_id"><?php echo esc_html__('Agency ID', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_agency_id" id="fave_author_agency_id" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_agency_id', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_agent_id-wrap">
                    <th><label for="fave_author_agent_id"><?php echo esc_html__('User Agent ID', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_agent_id" id="fave_author_agent_id" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_agent_id', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_agent_id-wrap">
                    <th><label for="fave_author_agent_id"><?php echo esc_html__('Currency', 'houzez'); ?></label></th>
                    <td><input placeholder="<?php echo esc_html__('Enter currency shortcode', 'houzez'); ?>" type="text" name="fave_author_currency" id="fave_author_currency" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_currency', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>

                <tr class="user-fave_author_agent_id-wrap">
                    <th><label for="fave_author_agent_id"><?php echo esc_html__('Service Areas', 'houzez'); ?></label></th>
                    <td><input placeholder="<?php echo esc_html__('Enter your service areas', 'houzez'); ?>" type="text" name="fave_author_service_areas" id="fave_author_service_areas" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_service_areas', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>

                <tr class="user-fave_author_agent_id-wrap">
                    <th><label for="fave_author_agent_id"><?php echo esc_html__('Specialties', 'houzez'); ?></label></th>
                    <td><input placeholder="<?php echo esc_html__('Enter your specialties', 'houzez'); ?>" type="text" name="fave_author_specialties" id="fave_author_specialties" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_specialties', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_agent_id-wrap">
                    <th><label for="fave_author_agent_id"><?php echo esc_html__('Address', 'houzez'); ?></label></th>
                    <td><input placeholder="<?php echo esc_html__('Enter your address', 'houzez'); ?>" type="text" name="fave_author_address" id="fave_author_address" value="<?php echo esc_attr( get_the_author_meta( 'fave_author_address', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
            </tbody>
        </table>

        <h2><?php echo esc_html__('Package Info', 'houzez'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr class="user-package_id-wrap">
                    <th><label for="package_id"><?php echo esc_html__('Package Id', 'houzez'); ?></label></th>
                    <td><input type="text" name="package_id" id="package_id" value="<?php echo esc_attr( get_the_author_meta( 'package_id', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-package_activation-wrap">
                    <th><label for="package_activation"><?php echo esc_html__('Package Activation', 'houzez'); ?></label></th>
                    <td><input type="text" name="package_activation" id="package_activation" value="<?php echo esc_attr( get_the_author_meta( 'package_activation', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-package_listings-wrap">
                    <th><label for="package_listings"><?php echo esc_html__('Listings available', 'houzez'); ?></label></th>
                    <td><input type="text" name="package_listings" id="package_listings" value="<?php echo esc_attr( get_the_author_meta( 'package_listings', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-package_featured_listings-wrap">
                    <th><label for="package_featured_listings"><?php echo esc_html__('Featured Listings available', 'houzez'); ?></label></th>
                    <td><input type="text" name="package_featured_listings" id="package_featured_listings" value="<?php echo esc_attr( get_the_author_meta( 'package_featured_listings', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_paypal_profile-wrap">
                    <th><label for="fave_paypal_profile"><?php echo esc_html__('Paypal Recuring Profile', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_paypal_profile" id="fave_paypal_profile" value="<?php echo esc_attr( get_the_author_meta( 'fave_paypal_profile', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_stripe_user_profile-wrap">
                    <th><label for="fave_stripe_user_profile"><?php echo esc_html__('Stripe Consumer Profile', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_stripe_user_profile" id="fave_stripe_user_profile" value="<?php echo esc_attr( get_the_author_meta( 'fave_stripe_user_profile', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
            </tbody>
        </table>

        <!-- <h2><?php echo esc_html__('Watermark Settings', 'houzez'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="watermark_image"><?php esc_html_e("Watermark Image", "houzez"); ?></label></th>
                    <td>
                        <input type="text" name="fave_watermark_image" id="fave_watermark_image" value="<?php echo esc_attr(get_the_author_meta('fave_watermark_image', $user->ID)); ?>" class="regular-text" /><br />
                        <span class="description"><?php esc_html_e("Please enter your watermark image URL.", "houzez"); ?></span>
                    </td>
                </tr>
            </tbody>
        </table> -->

        <h2><?php echo esc_html__('Social Info', 'houzez'); ?></h2>
        <table class="form-table">
            <tbody>
                <tr class="user-fave_author_facebook-wrap">
                    <th><label for="fave_author_facebook"><?php echo esc_html__('Facebook', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_facebook" id="fave_author_facebook" value="<?php echo esc_url( get_the_author_meta( 'fave_author_facebook', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_linkedin-wrap">
                    <th><label for="fave_author_linkedin"><?php echo esc_html__('LinkedIn', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_linkedin" id="fave_author_linkedin" value="<?php echo esc_url( get_the_author_meta( 'fave_author_linkedin', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_twitter-wrap">
                    <th><label for="fave_author_twitter"><?php echo esc_html__('Twitter', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_twitter" id="fave_author_twitter" value="<?php echo esc_url( get_the_author_meta( 'fave_author_twitter', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_pinterest-wrap">
                    <th><label for="fave_author_pinterest"><?php echo esc_html__('Pinterest', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_pinterest" id="fave_author_pinterest" value="<?php echo esc_url( get_the_author_meta( 'fave_author_pinterest', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_instagram-wrap">
                    <th><label for="fave_author_instagram"><?php echo esc_html__('Instagram', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_instagram" id="fave_author_instagram" value="<?php echo esc_url( get_the_author_meta( 'fave_author_instagram', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_youtube-wrap">
                    <th><label for="fave_author_youtube"><?php echo esc_html__('Youtube', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_youtube" id="fave_author_youtube" value="<?php echo esc_url( get_the_author_meta( 'fave_author_youtube', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                
                <tr class="user-fave_author_tiktok-wrap">
                    <th><label for="fave_author_tiktok"><?php echo esc_html__('TikTok', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_tiktok" id="fave_author_tiktok" value="<?php echo esc_url( get_the_author_meta( 'fave_author_tiktok', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_vimeo-wrap">
                    <th><label for="fave_author_vimeo"><?php echo esc_html__('Vimeo', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_vimeo" id="fave_author_vimeo" value="<?php echo esc_url( get_the_author_meta( 'fave_author_vimeo', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_zillow-wrap">
                    <th><label for="fave_author_zillow"><?php echo esc_html__('Zillow', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_zillow" id="fave_author_zillow" value="<?php echo esc_url( get_the_author_meta( 'fave_author_zillow', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_realtor_com-wrap">
                    <th><label for="fave_author_realtor_com"><?php echo esc_html__('Realtor.com', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_realtor_com" id="fave_author_realtor_com" value="<?php echo esc_url( get_the_author_meta( 'fave_author_realtor_com', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
                <tr class="user-fave_author_googleplus-wrap">
                    <th><label for="fave_author_googleplus"><?php echo esc_html__('Google', 'houzez'); ?></label></th>
                    <td><input type="text" name="fave_author_googleplus" id="fave_author_googleplus" value="<?php echo esc_url( get_the_author_meta( 'fave_author_googleplus', $user->ID ) ); ?>" class="regular-text"></td>
                </tr>
            </tbody>
        </table>

    <?php
    }
}
add_action('show_user_profile', 'houzez_custom_user_profile_fields');
add_action('edit_user_profile', 'houzez_custom_user_profile_fields');


if( !function_exists('houzez_update_extra_profile_fields') ) {
    function houzez_update_extra_profile_fields($user_id)
    {
        
        // Check for the current user's permissions
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        /*
         * Agent and agency Info
        --------------------------------------------------------------------------------*/
        update_user_meta($user_id, 'fave_author_title', $_POST['fave_author_title']);
        update_user_meta($user_id, 'fave_author_agent_id', $_POST['fave_author_agent_id']);
        update_user_meta($user_id, 'fave_author_tax_no', $_POST['fave_author_tax_no']);
        update_user_meta($user_id, 'fave_author_license', $_POST['fave_author_license']);
        update_user_meta($user_id, 'fave_author_agency_id', $_POST['fave_author_agency_id']);
        update_user_meta($user_id, 'fave_author_language', $_POST['fave_author_language']);

        $agency_id = get_user_meta($user_id, 'fave_author_agency_id', true);
        $user_company =  isset($_POST['fave_author_company']) ? $_POST['fave_author_company'] : '';
        if( !empty($agency_id) ) {
            $user_company = get_the_title($agency_id);
        }
        update_user_meta($user_id, 'fave_author_company', $user_company);

        /*
         * Common Info
        --------------------------------------------------------------------------------*/
        update_user_meta($user_id, 'fave_author_phone', $_POST['fave_author_phone']);
        update_user_meta($user_id, 'fave_author_fax', $_POST['fave_author_fax']);
        update_user_meta($user_id, 'fave_author_mobile', $_POST['fave_author_mobile']);
        update_user_meta($user_id, 'fave_author_whatsapp', $_POST['fave_author_whatsapp']);
        update_user_meta($user_id, 'fave_author_line_id', $_POST['fave_author_line_id']);
        update_user_meta($user_id, 'fave_author_telegram', $_POST['fave_author_telegram']);
        update_user_meta($user_id, 'fave_author_skype', $_POST['fave_author_skype']);
        update_user_meta($user_id, 'fave_author_currency', $_POST['fave_author_currency']);
        update_user_meta($user_id, 'fave_author_custom_picture', $_POST['fave_author_custom_picture']);
        update_user_meta($user_id, 'fave_author_service_areas', $_POST['fave_author_service_areas']);
        update_user_meta($user_id, 'fave_author_specialties', $_POST['fave_author_specialties']);
        update_user_meta($user_id, 'fave_author_address', $_POST['fave_author_address']);


        /*
         * Package Info
        --------------------------------------------------------------------------------*/
        update_user_meta($user_id, 'package_id', $_POST['package_id']);
        update_user_meta($user_id, 'package_activation', $_POST['package_activation']);
        update_user_meta($user_id, 'package_listings', $_POST['package_listings']);
        update_user_meta($user_id, 'package_featured_listings', $_POST['package_featured_listings']);
        update_user_meta($user_id, 'fave_paypal_profile', $_POST['fave_paypal_profile']);
        update_user_meta($user_id, 'fave_stripe_user_profile', $_POST['fave_stripe_user_profile']);


        /*
         * Social Info
        --------------------------------------------------------------------------------*/
        update_user_meta($user_id, 'fave_author_facebook', $_POST['fave_author_facebook']);
        update_user_meta($user_id, 'fave_author_linkedin', $_POST['fave_author_linkedin']);
        update_user_meta($user_id, 'fave_author_twitter', $_POST['fave_author_twitter']);
        update_user_meta($user_id, 'fave_author_pinterest', $_POST['fave_author_pinterest']);
        update_user_meta($user_id, 'fave_author_instagram', $_POST['fave_author_instagram']);
        update_user_meta($user_id, 'fave_author_youtube', $_POST['fave_author_youtube']);
        update_user_meta($user_id, 'fave_author_tiktok', $_POST['fave_author_tiktok']);
        update_user_meta($user_id, 'fave_author_vimeo', $_POST['fave_author_vimeo']);
        update_user_meta($user_id, 'fave_author_zillow', $_POST['fave_author_zillow']);
        update_user_meta($user_id, 'fave_author_realtor_com', $_POST['fave_author_realtor_com']);
        update_user_meta($user_id, 'fave_author_googleplus', $_POST['fave_author_googleplus']);

        /*
         * Image watermark
        --------------------------------------------------------------------------------*/
        //update_user_meta($user_id, 'fave_watermark_image', $_POST['fave_watermark_image']);

    }
}
add_action('edit_user_profile_update', 'houzez_update_extra_profile_fields');
add_action('personal_options_update', 'houzez_update_extra_profile_fields');


if( !function_exists('houzez_registration_save')) {
    function houzez_registration_save($user_id)
    {

        $user_role = houzez_user_role_by_user_id($user_id);
        $user_login = isset($_POST['user_login']) ? $_POST['user_login'] : '';
        $email = $_POST['email'];
        $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
        $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
        $usermane = $first_name . ' ' . $last_name;

        if ($user_role == 'houzez_agent') {
           houzez_register_as_agent($usermane, $email, $user_id);
        } else if ($user_role == 'houzez_agency') {
            houzez_register_as_agency($usermane, $email, $user_id);
        }
    }

    //add_action('user_register', 'houzez_registration_save', 10, 1);
}

if(!function_exists('houzez_gdrf_data_request')) {
    function houzez_gdrf_data_request() {
        $errors      = array();
        $gdpr_data_type       = isset($_POST['gdpr_data_type']) ? $_POST['gdpr_data_type'] : '';
        $gdpr_data_type       = sanitize_key( $gdpr_data_type );
        $gdrf_data_email      = sanitize_email( $_POST['gdrf_data_email'] );
        $gdrf_data_nonce      = esc_html( filter_input( INPUT_POST, 'gdrf_data_nonce', FILTER_SANITIZE_STRING ) );

        if ( ! empty( $gdrf_data_email ) ) {
            if ( ! wp_verify_nonce( $gdrf_data_nonce, 'houzez_gdrf_nonce' ) ) {
                $errors[] = esc_html__( 'Security check failed, please refresh page and try again.', 'houzez' );
            } else {
                if ( ! is_email( $gdrf_data_email ) ) {
                    $errors[] = esc_html__( 'Email address is not valid.', 'houzez' );
                }
                
                if ( ! in_array( $gdpr_data_type, array( 'export_personal_data', 'remove_personal_data' ), true ) ) {
                    $errors[] = esc_html__( 'Please select request type.', 'houzez' );
                }
            }
        } else {
            $errors[] = esc_html__( 'Fields are required', 'houzez' );
        }

        if ( empty( $errors ) ) {
            $request_id = wp_create_user_request( $gdrf_data_email, $gdpr_data_type );
            if ( is_wp_error( $request_id ) ) {
                wp_send_json_error( $request_id->get_error_message() );
            } elseif ( ! $request_id ) {
                wp_send_json_error( esc_html__( 'Unable to initiate confirmation request. Please contact the administrator.', 'houzez' ) );
            } else {
                $send_request = wp_send_user_request( $request_id );
                wp_send_json_success( esc_html__('Confirmation request initiated successfully.', 'houzez'));
            }
        } else {
            wp_send_json_error($errors);
        }
        die();


    }
}

add_action( 'wp_ajax_houzez_gdrf_data_request', 'houzez_gdrf_data_request' );


if( ! function_exists('houzez_get_agency_agents') ) {

    function houzez_get_agency_agents($agency_id) {
        $args = array(
            'role'    => 'houzez_agent',
            'meta_key' => 'fave_agent_agency',
            'meta_value' => $agency_id,
            'fields' => 'ID' // Retrieve only the user IDs
        );

        $users = get_users($args);

        if (empty($users)) {
            return false;
        } else {
            return $users; // Creates a comma-separated string of user IDs
        }
    }
}

if( ! function_exists('houzez_user_posts_count') ) {
    function houzez_user_posts_count( $post_status = 'any', $mine = false, $post_type = 'property' ) {
        $userID = get_current_user_id();

        // Common arguments for both queries
        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => -1, // Set to -1 to fetch all records
            'post_status'    => $post_status,
            'fields'         => 'ids', // Fetch only the IDs for performance
        ];

        if( houzez_is_admin() || houzez_is_editor() ) {
            
            if( $mine ) {
                $args['author'] = $userID; 
            }

        } else if( houzez_is_agency() ) {
            
            if( $mine ) {
                $args['author'] = $userID; 
            } else {
                $agents = houzez_get_agency_agents($userID);
                if( $agents ) {
                    if (!in_array($userID, $agents)) {
                        $agents[] = $userID;
                    }
                    $args['author__in'] = $agents;
                } else {
                    $args['author'] = $userID;
                }
            }
        } else {
            $args['author'] = $userID; 
        }

        // Query for counting all records
        $query = new WP_Query($args);
        $total_records = $query->found_posts; // Total count of all records

        return $total_records;

    }
}

// Helper function to get user properties count by date range
if (!function_exists('houzez_get_user_properties_count_by_date')) {
    function houzez_get_user_properties_count_by_date($post_status, $start_date, $end_date) {
        $user_id = get_current_user_id();
        
        $args = array(
            'post_type' => 'property',
            'post_status' => $post_status,
            'author' => $user_id,
            'date_query' => array(
                array(
                    'after' => $start_date,
                    'before' => $end_date,
                    'inclusive' => true,
                ),
            ),
            'fields' => 'ids'
        );
        
        $query = new WP_Query($args);
        $count = $query->found_posts;
        wp_reset_postdata();
        
        return $count;
    }
}

if( ! function_exists('houzez_user_invoices_count') ) {
    function houzez_user_invoices_count( $post_status = 'any', $mine = false, $post_type = 'houzez_invoice' ) {
        $userID = get_current_user_id();

        // Common arguments for both queries
        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => -1, // Set to -1 to fetch all records
            'post_status'    => $post_status,
            'fields'         => 'ids', // Fetch only the IDs for performance
        ];

        if( $mine ) {
            $args['author'] = $userID; 
        }

        // Query for counting all records
        $query = new WP_Query($args);
        $total_records = $query->found_posts; // Total count of all records

        return $total_records;

    }
}

if (!function_exists('houzez_get_users_who_can_post')) {
    function houzez_get_users_who_can_post() {
        $userID = get_current_user_id();

        $query_args = array(
            'fields' => array('ID', 'display_name'),
            'role__in' => array('administrator','houzez_agent', 'houzez_agency', 'houzez_manager', 'houzez_owner', 'houzez_seller', 'author', 'editor', 'contributor')
        );

        if (houzez_is_agency()) {
            $query_args['meta_key'] = 'fave_agent_agency';
            $query_args['meta_value'] = $userID;
            $query_args['meta_compare'] = '=';
        }

        $user_query = new WP_User_Query($query_args);
        $users = $user_query->get_results();

        // Check if current user is in the list, if not add them
        $user_ids = wp_list_pluck($users, 'ID');
        if (!in_array($userID, $user_ids)) {
            $users[] = get_userdata($userID);
        }

        return (!empty($users)) ? $users : false;
    }
}


if (!function_exists('houzez_get_user')) {
    function houzez_get_user( $user_id ) {
        $userID = get_current_user_id();

        $query_args = array(
            'fields' => 'all_with_meta',
            'role__in' => array('administrator','houzez_agent', 'houzez_agency', 'houzez_manager', 'houzez_owner', 'houzez_seller', 'author', 'editor', 'contributor')
        );

        if (houzez_is_agency()) {
            $query_args['meta_key'] = 'fave_agent_agency';
            $query_args['meta_value'] = $userID;
            $query_args['meta_compare'] = '=';
        }

        $user_query = new WP_User_Query($query_args);
        $users = $user_query->get_results();

        return (!empty($users)) ? $users : false;
    }
}

if ( ! function_exists('houzez_can_manage') ) {
    function houzez_can_manage() {
        if( houzez_is_admin() || houzez_is_editor() ) {
            return true;
        } else if( houzez_is_agency() ) { // if is agency and property id belong to agency agent then return true can manage their own agents
            $property_id = isset($_GET['edit_property']) ? sanitize_text_field($_GET['edit_property']) : false;
            $author_id = get_post_field('post_author', $property_id);
            $agency_id = get_user_meta( $author_id, 'fave_agent_agency', true );
            if( $agency_id == get_current_user_id() ) {
                return true;
            }
        }
        return false;
    }
}

if( !function_exists('houzez_check_role') ) {
    function houzez_check_role() {
        $current_user = wp_get_current_user();
        //houzez_agent, subscriber, author, houzez_buyer, houzez_owner, houzez_seller, houzez_manager, houzez_agency
        $use_houzez_roles = 1;

        if( $use_houzez_roles != 0 ) {
            if (in_array('houzez_buyer', (array)$current_user->roles) || in_array('subscriber', (array)$current_user->roles)) {
                return false;
            }
            return true;
        }
        return true;
    }
}

if (!function_exists('houzez_has_role')) {
    function houzez_has_role($role) {
        $current_user = wp_get_current_user();

        return in_array($role, (array)$current_user->roles);
    }
}


if( !function_exists('houzez_is_admin') ) {
    function houzez_is_admin() {
        $current_user = wp_get_current_user();

        return in_array('administrator', (array)$current_user->roles);
    }
}

if( !function_exists('houzez_is_manager') ) {
    function houzez_is_manager() {
        $current_user = wp_get_current_user();

        return in_array('houzez_manager', (array)$current_user->roles);
    }
}

if (!function_exists('houzez_is_editor')) {
    function houzez_is_editor() {
        $current_user = wp_get_current_user();

        return in_array('houzez_manager', (array)$current_user->roles) || in_array('editor', (array)$current_user->roles);
    }
}


if( !function_exists('houzez_is_agency') ) {
    function houzez_is_agency( $user_id = null ) {
        // If a user ID is provided, get the user data for the given user ID; otherwise, get the current user.
        if (!empty($user_id)) {
            $user_data = get_userdata($user_id);
        } else {
            $user_data = wp_get_current_user();
        }

        // Check if the user data was successfully retrieved and the user has the 'houzez_agency' role.
        if ($user_data) {
            return in_array('houzez_agency', (array)$user_data->roles);
        }

        return false;
    }
}


if( !function_exists('houzez_is_agent') ) {
    function houzez_is_agent( $user_id = null ) {
        // If a user ID is provided, get the user data for the given user ID; otherwise, get the current user.
        if (!empty($user_id)) {
            $user_data = get_userdata($user_id);
        } else {
            $user_data = wp_get_current_user();
        }

        // Check if the user data was successfully retrieved and the user has the 'houzez_agent' role.
        if ($user_data) {
            return in_array('houzez_agent', (array)$user_data->roles);
        }

        return false;
    }
}


if( !function_exists('houzez_is_owner') ) {
    function houzez_is_owner() {
        $current_user = wp_get_current_user();
        
        return in_array('houzez_owner', (array)$current_user->roles);
    }
}

if( !function_exists('houzez_is_buyer') ) {
    function houzez_is_buyer() {
        $current_user = wp_get_current_user();
        
        if (in_array('houzez_buyer', (array)$current_user->roles) || in_array('subscriber', (array)$current_user->roles)) {
            return true;
        }
        return false;
    }
}

if( !function_exists('houzez_not_buyer') ) {
    function houzez_not_buyer() {
        $current_user = wp_get_current_user();
        //houzez_agent, subscriber, author, houzez_buyer, houzez_owner
        if (in_array('houzez_buyer', (array)$current_user->roles) ) {
            return false;
        }
        return true;
    }
}

if( ! function_exists('houzez_get_property_authors_list') ) {
    function houzez_get_property_authors_list() {
        $property_id = isset($_GET['edit_property']) ? sanitize_text_field($_GET['edit_property']) : false;
        $author_id = get_current_user_id();
        $output = '';
        if( $property_id ) {
            $author_id = get_post_field('post_author', $property_id);
        }

        $users_can_post = houzez_get_users_who_can_post();
        if( $users_can_post ) {
            foreach ($users_can_post as $user) {
                $output .= '<option '.selected( $author_id, $user->ID, false ).' value="'.esc_attr($user->ID).'">'.esc_attr($user->display_name).'</option>';
            }
        }
        return $output;
    }
}

if( ! function_exists('houzez_property_authors_list') ) {
    function houzez_property_authors_list() {
        echo houzez_get_property_authors_list();
    }
}

if( ! function_exists('houzez_get_agent_agency_id') ) {
    function houzez_get_agent_agency_id($agent_userId) {
        $agency_id = get_user_meta( $agent_userId, 'fave_agent_agency', true );

        $canUseAgencyPackage = get_user_meta( $agency_id, 'houzez_is_agent_can_use_agency_package', true );
        if( $agency_id && $canUseAgencyPackage == 'yes') {
            return $agency_id;
        }
        return false;
    }
}

if( ! function_exists('houzez_can_agent_user_agency_package') ) {
    function houzez_can_agent_user_agency_package($userId) {
        
        $canUseAgencyPackage = get_user_meta( $userId, 'houzez_is_agent_can_use_agency_package', true );
        if( $canUseAgencyPackage == 'yes') {
            return true;
        }
        return false;
    }
}

add_action( 'wp_ajax_houzez_user_package_permission', 'houzez_user_package_permission' );
if ( !function_exists( 'houzez_user_package_permission' ) ) :
    function houzez_user_package_permission() {

        $ajax_response = array();

        if ( is_user_logged_in() && isset( $_POST['agency_allow_package'] ) ) {
            
            $userID = get_current_user_id();

            $current_listings =  get_user_meta( $userID, 'package_listings', true );
            $package_id =  get_user_meta( $userID, 'package_id', true );
            $unlimited_listings =  get_post_meta( $package_id, 'fave_unlimited_listings', true );

            $total_posted_listing = houzez_get_agency_agents_total_listings($userID);

            if( $total_posted_listing <=  $current_listings || $unlimited_listings ) {
                update_user_meta( $userID, 'houzez_is_agent_can_use_agency_package', $_POST['agency_allow_package'] );
                houzez_plusone_package_listings($userID);

                $ajax_response = array('success' => true, 'reason' => '');
            } else {
                $ajax_response = array('success' => false, 'reason' => esc_html__('Request failed because your available package listings are less then your agents posted listings ('.$total_posted_listing.')', 'houzez') );
            }

        }
        echo json_encode($ajax_response);
        wp_die();
    }
endif;

