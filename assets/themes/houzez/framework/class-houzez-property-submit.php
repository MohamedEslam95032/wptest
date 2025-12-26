<?php
class Houzez_Property_Submit {

    /**
     * Initialize hooks.
     */
    public static function init() {
        add_filter('houzez_submit_listing', [__CLASS__, 'submitListing']);
        add_action('wp_ajax_save_as_draft', [__CLASS__, 'saveAsDraft']);
    }

    /**
     * Main method to handle property submission or update.
     *
     * @param array $new_property The property data array.
     * @return int The property ID on success.
     */
    public static function submitListing($new_property) {
        $user_id = get_current_user_id();
        $post_author = $user_id;
        $user_id_package = $user_id;
        $listings_admin_approved    = houzez_option('listings_admin_approved');
        $edit_listings_admin_approved = houzez_option('edit_listings_admin_approved');
        $enable_paid_submission     = houzez_option('enable_paid_submission');
        $user_submit_has_no_membership = 'no';

        // Check if verification is required
        $is_verification_required = self::is_user_verification_required($user_id);
        if ($is_verification_required) {
            return 0; // Return zero to indicate failure
        }

        // Use agency id if available.
        $agent_agency_id = houzez_get_agent_agency_id($user_id);
        if ($agent_agency_id) {
            $user_id_package = $agent_agency_id;
        }

        // Update title and description.
        $new_property = self::updateTitleAndDescription($new_property);

        // Allow overriding post author.
        if (!empty($_POST['property_author'])) {
            $post_author = sanitize_text_field($_POST['property_author']);
        }
        $new_property['post_author'] = $post_author;

        // Determine submission action.
        $submission_action = isset($_POST['action']) ? $_POST['action'] : '';
        $property_id = 0;

        if ($submission_action === 'add_property') {
            if (houzez_is_admin()) {
                $new_property['post_status'] = 'publish';
            } else {
                // If admin approval is not required and submission is free/membership.
                if ($listings_admin_approved !== 'yes' &&
                    in_array($enable_paid_submission, ['no', 'free_paid_listing', 'membership'], true)) {

                    $user_submit_has_no_membership = isset($_POST['user_submit_has_no_membership']) ? $_POST['user_submit_has_no_membership'] : 'no';
                    $new_property['post_status'] = ($user_submit_has_no_membership === 'yes') ? 'draft' : 'publish';
                } else {
                    // When admin approval is required.
                    $new_property['post_status'] = ($user_submit_has_no_membership === 'yes' && $enable_paid_submission === 'membership')
                        ? 'draft' : 'pending';
                }
            }
            $new_property = apply_filters('houzez_before_submit_property', $new_property);
            $property_id = wp_insert_post($new_property);

            if ($property_id > 0 && $enable_paid_submission === 'membership') {
                houzez_update_package_listings($user_id_package);
            }
        } elseif ($submission_action === 'update_property') {
            // Use fallback: prefer 'property_id' if provided, otherwise use 'property_id'.
		    $new_property['ID'] = intval( self::getPostValueWithFallback('id', 'prop_id', 'absint') );
		    $prop_status = self::determineUpdateStatus($new_property['ID'], $listings_admin_approved, $edit_listings_admin_approved, $enable_paid_submission, $user_id_package);
		    $new_property['post_status'] = $prop_status;
		    $new_property = apply_filters('houzez_before_update_property', $new_property);
		    $property_id = wp_update_post($new_property);
        } else {
            return 0; // No valid action provided.
        }

        // Update meta if property insertion/update succeeded.
        if ($property_id > 0) {
            self::updateMetaFields($property_id, $user_id, $user_submit_has_no_membership);
            self::updateCustomFieldsMeta($property_id);
            self::updatePropertyImagesAndAttachments($property_id, $submission_action);
            self::updatePropertyTaxonomies($property_id);
            self::updateLocationMeta($property_id);
            self::updateAdditionalMeta($property_id);
            self::updateAgentOrAgencyMeta($property_id, $user_id);

            // Trigger actions and webhooks.
            if ($submission_action === 'add_property') {
                do_action('houzez_after_property_submit', $property_id);
                if (houzez_option('add_new_property') == 1) {
                    houzez_webhook_post($_POST, 'houzez_add_new_property');
                }
                update_post_meta($property_id, 'fave_mortgage_cal', 0);
                
            } elseif ($submission_action === 'update_property') {
                do_action('houzez_after_property_update', $property_id);
                if (houzez_option('add_new_property') == 1) {
                    houzez_webhook_post($_POST, 'houzez_update_property');
                }
            }
        }

        return $property_id;
    }

    public static function saveAsDraft() {
        $user_id = get_current_user_id();
        
        // Check if verification is required
        $is_verification_required = self::is_user_verification_required($user_id);
        if ($is_verification_required) {
            $verification_message = esc_html__('Your account must be verified before you can save property as draft. Please complete the verification process.', 'houzez');
            echo json_encode(array('success' => false, 'msg' => $verification_message));
            wp_die();
        }
        
        $new_property = array(
            'post_type' => 'property'
        );

        $submission_action = isset($_POST['action']) ? $_POST['action'] : '';

        $property_id = 0;
        $new_property['post_status'] = 'draft';
        $new_property['post_author'] = $user_id;
        // Allow overriding post author.
        if (!empty($_POST['property_author'])) {
            $new_property['post_author'] = sanitize_text_field($_POST['property_author']);
        }

        // Update title and description.
        $new_property = self::updateTitleAndDescription($new_property);

        if( isset($_POST['draft_property_id']) && !empty( $_POST['draft_property_id'] ) ) {
            $new_property['ID'] = $_POST['draft_property_id'];
            $property_id = wp_update_post( $new_property );
        } else {
            $property_id = wp_insert_post( $new_property );
        }

        if( $property_id > 0 ) {
            self::updateMetaFields($property_id, $user_id);
            self::updateCustomFieldsMeta($property_id);
            self::updatePropertyImagesAndAttachments($property_id, $submission_action);
            self::updatePropertyTaxonomies($property_id);
            self::updateLocationMeta($property_id);
            self::updateAdditionalMeta($property_id);
            self::updateAgentOrAgencyMeta($property_id, $user_id);
        }

        echo json_encode( array( 'success' => true, 'property_id' => $property_id, 'msg' => esc_html__('Successfull', 'houzez') ) );
        wp_die();
        
    }

    /**
	 * Update the title and description of the property.
	 *
	 * @param array $property
	 * @return array
	 */
	protected static function updateTitleAndDescription(array $property) {
	    // Title: Use 'property_title' if set, otherwise fall back to 'prop_title'.
	    $title = self::getPostValueWithFallback('property_title', 'prop_title', 'sanitize_text_field');
	    if (null !== $title) {
	        $property['post_title'] = $title;
	    }

	    // Description: Use 'property_description' if set, otherwise fall back to 'prop_des'.
	    $description = self::getPostValueWithFallback('property_description', 'prop_des', function($value) {
	        return wp_kses_post(wpautop(wptexturize($value)));
	    });
	    if (null !== $description) {
	        $property['post_content'] = $description;
	    }
	    return $property;
	}


    /**
     * Determine the post status for property update.
     *
     * @param int    $property_id The property ID to update
     * @param string $listings_admin_approved Whether new listings require admin approval ('yes'/'no')
     * @param string $edit_listings_admin_approved Whether edited listings require admin approval ('yes'/'no')
     * @param string $enable_paid_submission Payment submission type ('no', 'free_paid_listing', 'membership', etc.)
     * @param int    $user_id_package User ID for package/membership checks
     * @return string The determined post status ('publish', 'pending', 'draft')
     */
    protected static function determineUpdateStatus($property_id, $listings_admin_approved, $edit_listings_admin_approved, $enable_paid_submission, $user_id_package) {
        
        // Validate property exists
        $current_post_status = get_post_status($property_id);
        if (!$current_post_status) {
            return 'draft'; // Default fallback for invalid property
        }

        // Admins and editors can maintain current status
        if (houzez_is_admin() || houzez_is_editor()) {
            return $current_post_status;
        }
        
        // Handle draft properties being updated to published/pending
        if ($current_post_status === 'draft') {
            // Handle membership package update as side effect
            if ($enable_paid_submission === 'membership') {
                self::handleMembershipPackageUpdate($user_id_package);
            }
            
            // Determine if draft should be published or pending
            $should_auto_publish = ($listings_admin_approved !== 'yes' && 
                                  in_array($enable_paid_submission, ['no', 'free_paid_listing', 'membership'], true));
            return $should_auto_publish ? 'publish' : 'pending';
        }
        
        // Check if edits require admin approval
        if ($edit_listings_admin_approved === 'yes') {
            return 'pending';
        }
        
        // If admin approval is not required for edits, keep the existing status
        // but still check membership requirements
        if ($enable_paid_submission === 'membership' && !houzez_user_has_membership($user_id_package)) {
            return 'draft';
        }

        // Keep existing status when edit approval is not required
        return $current_post_status;
    }

    /**
     * Handle membership package update side effect.
     * Separated from status determination for better separation of concerns.
     *
     * @param int $user_id_package User ID for package updates
     */
    protected static function handleMembershipPackageUpdate($user_id_package) {
        if (function_exists('houzez_update_package_listings')) {
            houzez_update_package_listings($user_id_package);
        }
    }

    /**
     * Backward compatibility helper method.
     *
     * Checks for a new POST key first and falls back to a legacy key if needed.
     * If the legacy key is used and WP_DEBUG is enabled, a deprecation warning is triggered.
     *
     * @param string   $new_key           The new parameter name.
     * @param string   $legacy_key        The legacy parameter name.
     * @param callable $sanitize_callback The function to sanitize the input.
     * @return mixed|null The sanitized value or null if neither is set.
     */
    protected static function getPostValueWithFallback($new_key, $legacy_key, $sanitize_callback = 'sanitize_text_field') {
        if (isset($_POST[$new_key])) {
            return call_user_func($sanitize_callback, $_POST[$new_key]);
        } elseif (isset($_POST[$legacy_key])) {
            // if (defined('WP_DEBUG') && WP_DEBUG) {
            //     trigger_error("Deprecated: Use '$new_key' instead of '$legacy_key'.", E_USER_DEPRECATED);
            // }
            return call_user_func($sanitize_callback, $_POST[$legacy_key]);
        }
        return null;
    }

    /**
     * Update custom fields (meta data) for a property.
     *
     * @param int    $property_id
     * @param int    $user_id
     * @param string $user_submit_has_no_membership
     */
    protected static function updateMetaFields($property_id, $user_id, $user_submit_has_no_membership = null) {
        // Mapping for fields that are renamed from legacy to new keys.
        $fields_mapping = [
            'property_bathrooms'        => 'prop_baths',
            'property_bedrooms'     => 'prop_beds',
            'property_rooms'        => 'prop_rooms',
            'property_size'         => 'prop_size',
            'property_size_prefix'  => 'prop_size_prefix',
            'property_land'    		=> 'prop_land_area',
            'property_land_postfix' => 'prop_land_area_prefix', // legacy key: prop_land_area_prefix; new meta will be fave_property_land_postfix (or postfix as needed)
            'property_garage'       => 'prop_garage',
            'property_garage_size'  => 'prop_garage_size',
            'property_year'         => 'prop_year_built',
            'property_price'        => 'prop_price',
            'property_price_placeholder' => 'prop_price_placeholder',
            'property_price_prefix' => 'prop_price_prefix',
            'property_price_postfix' => 'prop_label',
            'property_sec_price' => 'prop_sec_price',
        ];

        foreach ($fields_mapping as $new_key => $legacy_key) {
            $value = self::getPostValueWithFallback($new_key, $legacy_key);
            if (null !== $value) {
                update_post_meta($property_id, 'fave_' . $new_key, $value);
            }
        }

        // Price placeholder.
        update_post_meta($property_id, 'fave_show_price_placeholder', 0);
        if (isset($_POST['show_price_placeholder'])) {
            $placeholder = ($_POST['show_price_placeholder'] === 'on') ? 1 : $_POST['show_price_placeholder'];
            update_post_meta($property_id, 'fave_show_price_placeholder', sanitize_text_field($placeholder));
        }
        
        // Currency meta.
        if (isset($_POST['currency'])) {
            $currency = sanitize_text_field($_POST['currency']);
            update_post_meta($property_id, 'fave_currency', $currency);
            if (class_exists('Houzez_Currencies')) {
                $currencies = Houzez_Currencies::get_property_currency_2($property_id, $currency);
                update_post_meta($property_id, 'fave_currency_info', $currencies);
            }
        }

        // Additional features.
	    if (isset($_POST['additional_features'])) {
	        $additional_features = $_POST['additional_features'];
            if (gettype($additional_features) == 'string') {
                $additional_features = json_decode($additional_features, true);
            }
	        if (!empty($additional_features)) {
	            update_post_meta($property_id, 'additional_features', $additional_features);
	            update_post_meta($property_id, 'fave_additional_features_enable', 'enable');
	        }
	    } else {
	        update_post_meta($property_id, 'additional_features', '');
	    }

	    // Floor Plans.
	    if (isset($_POST['floorPlans_enable'])) {
	        $floorPlans_enable = $_POST['floorPlans_enable'];
	        if (!empty($floorPlans_enable)) {
	            update_post_meta($property_id, 'fave_floor_plans_enable', $floorPlans_enable);
	        }
	    }
	    if (isset($_POST['floor_plans'])) {
	        $floor_plans_post = $_POST['floor_plans'];
            if (gettype($floor_plans_post) == 'string') {
                $floor_plans_post = json_decode($floor_plans_post, true);
            }
	        if (!empty($floor_plans_post)) {
	            update_post_meta($property_id, 'floor_plans', $floor_plans_post);
	        }
	    } else {
	        update_post_meta($property_id, 'floor_plans', '');
	    }

	    // Multi-units / Sub-properties.
	    if (isset($_POST['multiUnits'])) {
	        $multiUnits_enable = $_POST['multiUnits'];
	        if (!empty($multiUnits_enable)) {
	            update_post_meta($property_id, 'fave_multiunit_plans_enable', $multiUnits_enable);
	        }
	    }
	    if (isset($_POST['fave_multi_units'])) {
	        $fave_multi_units = $_POST['fave_multi_units'];
	        if (!empty($fave_multi_units)) {
	            update_post_meta($property_id, 'fave_multi_units', $fave_multi_units);
	        }
	    } else {
	        update_post_meta($property_id, 'fave_multi_units', '');
	    }

	    $featured = self::getPostValueWithFallback('property_featured', 'prop_featured', 'absint');
		if (null !== $featured) {
		    update_post_meta($property_id, 'fave_featured', $featured);
		}

		if( isset( $_POST['virtual_tour'] ) ) {
            update_post_meta( $property_id, 'fave_virtual_tour', $_POST['virtual_tour'] );
        }


	    // Logged in to view.
	    if (isset($_POST['login-required'])) {
	        $logged_in = intval($_POST['login-required']);
	        update_post_meta($property_id, 'fave_loggedintoview', $logged_in);
	    }

        // Membership flags.
        if ($user_submit_has_no_membership === 'yes') {
            update_user_meta($user_id, 'user_submit_has_no_membership', $property_id);
            update_user_meta($user_id, 'user_submitted_without_membership', 'yes');
        }
    }

    /**
     * Update images and attachments.
     *
     * @param int    $property_id
     * @param string $submission_action
     */
    protected static function updatePropertyImagesAndAttachments($property_id, $submission_action) {
        $property_video_image = "";
        if ($submission_action === "update_property") {
            $property_video_image_id = get_post_meta($property_id, 'fave_video_image', true);
            if (!empty($property_video_image_id)) {
                $video_src = wp_get_attachment_image_src($property_video_image_id, 'houzez-property-detail-gallery');
                if ($video_src) {
                    $property_video_image = $video_src[0];
                }
            }
            // Remove old image-related meta.
            delete_post_meta($property_id, 'fave_property_images');
            delete_post_meta($property_id, 'fave_attachments');
            delete_post_meta($property_id, 'fave_agents');
            delete_post_meta($property_id, 'fave_property_agency');
            delete_post_meta($property_id, '_thumbnail_id');
        }

        // Property Video Url
        $video_url = self::getPostValueWithFallback('property_video_url', 'prop_video_url');
        if (null !== $video_url) {
        	update_post_meta( $property_id, 'fave_video_url', $video_url );
		}

        // Update property images.
        $image_ids = [];
        if (isset($_POST['property_image_ids']) && is_array($_POST['property_image_ids'])) {
            $image_ids = array_map('intval', $_POST['property_image_ids']);
        } elseif (isset($_POST['propperty_image_ids']) && is_array($_POST['propperty_image_ids'])) {
            $image_ids = array_map('intval', $_POST['propperty_image_ids']);
        }
        
        if (!empty($image_ids)) {
            foreach ($image_ids as $img_id) {
                add_post_meta($property_id, 'fave_property_images', $img_id);
                wp_update_post([
                    'ID'          => $img_id,
                    'post_parent' => $property_id
                ]);
            }
            // Set featured image.
            if (isset($_POST['featured_image_id'])) {
			    $featured_id = intval($_POST['featured_image_id']);
			    if (in_array($featured_id, $image_ids, true)) {
			        update_post_meta($property_id, '_thumbnail_id', $featured_id);
			        $video_url = self::getPostValueWithFallback('property_video_url', 'prop_video_url');
			        if (empty($property_video_image) && !empty($video_url)) {
			            update_post_meta($property_id, 'fave_video_image', $featured_id);
			        }
			    }
			} elseif (!empty($image_ids)) {
			    update_post_meta($property_id, '_thumbnail_id', $image_ids[0]);
			    $video_url = self::getPostValueWithFallback('property_video_url', 'prop_video_url');
			    if (empty($property_video_image) && !empty($video_url)) {
			        update_post_meta($property_id, 'fave_video_image', $image_ids[0]);
			    }
			}
        }

        // Update attachments.
        $attachment_ids = [];
        if (isset($_POST['property_attachment_ids']) && is_array($_POST['property_attachment_ids'])) {
            $attachment_ids = $_POST['property_attachment_ids'];
        } elseif (isset($_POST['propperty_attachment_ids']) && is_array($_POST['propperty_attachment_ids'])) {
            $attachment_ids = $_POST['propperty_attachment_ids'];
        }
        
        if (!empty($attachment_ids)) {
            foreach ($attachment_ids as $attachment_id) {
                add_post_meta($property_id, 'fave_attachments', intval($attachment_id));
            }
        }
    }

    /**
	 * Update property taxonomies (types, status, labels, features).
	 *
	 * @param int $property_id
	 */
	protected static function updatePropertyTaxonomies($property_id) {
	    // Property type.
	    $types = [];
        if (isset($_POST['property_type'])) {
            $values = (array) $_POST['property_type'];
        } elseif (isset($_POST['prop_type'])) {
            $values = (array) $_POST['prop_type'];
        } else {
            $values = [];
        }

        foreach ($values as $value) {
            if ( is_numeric($value) ) {
                $types[] = intval($value);
            } else {
                $types[] = sanitize_text_field($value);
            }
        }

        // If $types is not empty and the first value is not -1, set the taxonomy;
        if ( ! empty( $types ) && reset( $types ) != -1 ) {
            wp_set_object_terms( $property_id, $types, 'property_type' );
        } else {
            wp_set_object_terms( $property_id, '', 'property_type' );
        }


	    // Property status.
        $status = [];
        if (isset($_POST['property_status'])) {
            $values = (array) $_POST['property_status'];
        } elseif (isset($_POST['prop_status'])) {
            $values = (array) $_POST['prop_status'];
        } else {
            $values = [];
        }
        foreach ($values as $value) {
            if (is_numeric($value)) {
                $status[] = intval($value);
            } else {
                $status[] = sanitize_text_field($value);
            }
        }
        if (!empty($status) && reset($status) != -1) {
            wp_set_object_terms($property_id, $status, 'property_status');
        } else {
            wp_set_object_terms($property_id, '', 'property_status');
        }

        // Property label.
        $labels = [];
        if (isset($_POST['property_labels'])) {
            $values = (array) $_POST['property_labels'];
        } elseif (isset($_POST['prop_labels'])) {
            $values = (array) $_POST['prop_labels'];
        } else {
            $values = [];
        }
        foreach ($values as $value) {
            if (is_numeric($value)) {
                $labels[] = intval($value);
            } else {
                $labels[] = sanitize_text_field($value);
            }
        }
        if (!empty($labels)) {
            wp_set_object_terms($property_id, $labels, 'property_label');
        } else {
            wp_set_object_terms($property_id, '', 'property_label');
        }

        // Features.
        $features = [];
        if (isset($_POST['property_features'])) {
            $values = (array) $_POST['property_features'];
        } elseif (isset($_POST['prop_features'])) {
            $values = (array) $_POST['prop_features'];
        } else {
            $values = [];
        }
        foreach ($values as $value) {
            if (is_numeric($value)) {
                $features[] = intval($value);
            } else {
                $features[] = sanitize_text_field($value);
            }
        }
        if (!empty($features)) {
            wp_set_object_terms($property_id, $features, 'property_feature');
        }

	    // Allow further customization or additional processing through a hook.
	    do_action('houzez_after_update_property_taxonomies', $property_id);
	}


    /**
     * Update location-related meta and taxonomies.
     *
     * @param int $property_id
     */
    protected static function updateLocationMeta($property_id) {
        // Country.
        if (isset($_POST['country'])) {
            $country = sanitize_text_field($_POST['country']);
            wp_set_object_terms($property_id, $country, 'property_country');
        } else {
            $default_country = houzez_option('default_country');
            wp_set_object_terms($property_id, $default_country, 'property_country');
        }

        if (isset($_POST['postal_code'])) {
            update_post_meta($property_id, 'fave_property_zip', sanitize_text_field($_POST['postal_code']));
        }

        // City.
        if ( isset($_POST['property_city']) || isset($_POST['locality']) ) {
		    $city = self::getPostValueWithFallback('property_city', 'locality', 'sanitize_text_field');
		    $city_id = wp_set_object_terms($property_id, $city, 'property_city');
		    if ( ! empty( $city_id ) && ( isset($_POST['property_state']) || isset($_POST['administrative_area_level_1']) ) ) {
		        $parent_state = self::getPostValueWithFallback('property_state', 'administrative_area_level_1', 'sanitize_text_field');
		        update_option('_houzez_property_city_' . $city_id[0], [
		            'parent_state' => $parent_state
		        ]);
		    }
		}


        // Area.
        if ( isset($_POST['property_area']) || isset($_POST['neighborhood']) ) {
		    $area = self::getPostValueWithFallback('property_area', 'neighborhood', 'sanitize_text_field');
		    $area_id = wp_set_object_terms($property_id, $area, 'property_area');
		    if ( ! empty( $area_id ) && ( isset($_POST['property_city']) || isset($_POST['locality']) ) ) {
		        $parent_city = self::getPostValueWithFallback('property_city', 'locality', 'sanitize_text_field');
		        update_option('_houzez_property_area_' . $area_id[0], [
		            'parent_city' => $parent_city
		        ]);
		    }
		}

        // State.
        if ( isset($_POST['property_state']) || isset($_POST['administrative_area_level_1']) ) {
		    $state = self::getPostValueWithFallback('property_state', 'administrative_area_level_1', 'sanitize_text_field');
		    $state_id = wp_set_object_terms($property_id, $state, 'property_state');
		    $country_short = isset($_POST['country']) ? strtoupper(sanitize_text_field($_POST['country'])) : '';
		    if (!empty($state_id)) {
		        update_option('_houzez_property_state_' . $state_id[0], [
		            'parent_country' => $country_short
		        ]);
		    }
		}

		// Address meta.
        if (isset($_POST['property_map_address'])) {
            $address = sanitize_text_field($_POST['property_map_address']);
            update_post_meta($property_id, 'fave_property_map_address', $address);
            update_post_meta($property_id, 'fave_property_address', $address);
        }

        if ((isset($_POST['latitude']) || isset($_POST['lat'])) && (isset($_POST['longitude']) || isset($_POST['lng']))) {
		    $latitude = self::getPostValueWithFallback('latitude', 'lat', 'sanitize_text_field');
		    $longitude = self::getPostValueWithFallback('longitude', 'lng', 'sanitize_text_field');

		    update_post_meta($property_id, 'houzez_geolocation_lat', $latitude);
		    update_post_meta($property_id, 'houzez_geolocation_long', $longitude);
		    update_post_meta($property_id, 'fave_property_location', $latitude . ',' . $longitude);
		    update_post_meta($property_id, 'fave_property_map', '1');

		    $street_view = self::getPostValueWithFallback('property_google_street_view', 'prop_google_street_view', 'sanitize_text_field');
		    update_post_meta($property_id, 'fave_property_map_street_view', $street_view);
		}

    }

    /**
     * Update additional meta like attachments, energy info, payment, etc.
     *
     * @param int $property_id
     */
    protected static function updateAdditionalMeta($property_id) {
        // Property ID: auto-generate if option enabled.
        $auto_property_id = houzez_option('auto_property_id');
        if ($auto_property_id != 1) {
            if (isset($_POST['property_id'])) {
                update_post_meta($property_id, 'fave_property_id', sanitize_text_field($_POST['property_id']));
            }
        } else {
            update_post_meta($property_id, 'fave_property_id', $property_id);
        }

        // Private note and disclaimer.
        if (isset($_POST['private_note'])) {
            update_post_meta($property_id, 'fave_private_note', wp_kses_post($_POST['private_note']));
        }
        if (isset($_POST['property_disclaimer'])) {
            update_post_meta($property_id, 'fave_property_disclaimer', wp_kses_post($_POST['property_disclaimer']));
        }

        // Energy related meta.
        $energy_fields = [
            'energy_class'                => 'fave_energy_class',
            'energy_global_index'         => 'fave_energy_global_index',
            'renewable_energy_global_index' => 'fave_renewable_energy_global_index',
            'energy_performance'          => 'fave_energy_performance',
            'epc_current_rating'          => 'fave_epc_current_rating',
            'epc_potential_rating'        => 'fave_epc_potential_rating',
            'ghg_emissions_index'        => 'fave_ghg_emissions_index',
            'diagnostic_date'        => 'fave_diagnostic_date',
            'ghg_emissions_class'        => 'fave_ghg_emissions_class'
        ];
        foreach ($energy_fields as $post_key => $meta_key) {
            if (isset($_POST[$post_key])) {
                update_post_meta($property_id, $meta_key, sanitize_text_field($_POST[$post_key]));
            }
        }

        // Payment status.
        $payment = self::getPostValueWithFallback('property_payment', 'prop_payment', 'sanitize_text_field');
		if (null !== $payment) {
		    update_post_meta($property_id, 'fave_payment_status', $payment);
		}

    }

    /**
	 * Update agent/agency related meta.
	 *
	 * @param int $property_id
	 * @param int $user_id
	 */
	protected static function updateAgentOrAgencyMeta($property_id, $user_id) {
	    // Use fallback: prefer 'property_contact_display' over 'fave_agent_display_option'
	    $display_option = self::getPostValueWithFallback('property_contact_display', 'fave_agent_display_option', 'sanitize_text_field');

	    if ($display_option) {
	        if ($display_option === 'agent_info') {
	            // For agents, prefer 'property_agent' over 'fave_agents'
	            $agents = [];
	            if (isset($_POST['property_agent'])) {
	                $agents = (array) $_POST['property_agent'];
	            } elseif (isset($_POST['fave_agents'])) {
	                $agents = (array) $_POST['fave_agents'];
	            }
	            foreach ($agents as $agent) {
	                add_post_meta($property_id, 'fave_agents', intval($agent));
	            }
	            update_post_meta($property_id, 'fave_agent_display_option', $display_option);
	            if (houzez_is_agency()) {
	                $agency_id = get_user_meta($user_id, 'fave_author_agency_id', true);
	                if (!empty($agency_id)) {
	                    update_post_meta($property_id, 'fave_property_agency', $agency_id);
	                }
	            }
	        } elseif ($display_option === 'agency_info') {
	            // For agency info, prefer 'property_agency' over 'fave_property_agency'
	            $agency_ids = [];
	            if (isset($_POST['property_agency'])) {
	                $agency_ids = (array) $_POST['property_agency'];
	            } elseif (isset($_POST['fave_property_agency'])) {
	                $agency_ids = (array) $_POST['fave_property_agency'];
	            }
	            if (houzez_is_agency()) {
	                $agency_id = get_user_meta($user_id, 'fave_author_agency_id', true);
	                if (!empty($agency_id)) {
	                    update_post_meta($property_id, 'fave_property_agency', $agency_id);
	                    update_post_meta($property_id, 'fave_agent_display_option', $display_option);
	                } else {
	                    update_post_meta($property_id, 'fave_agent_display_option', 'author_info');
	                }
	            } else {
	                foreach ($agency_ids as $agency) {
	                    add_post_meta($property_id, 'fave_property_agency', intval($agency));
	                }
	                update_post_meta($property_id, 'fave_agent_display_option', $display_option);
	            }
	        } else {
	            update_post_meta($property_id, 'fave_agent_display_option', $display_option);
	        }
	    } else {
	        // Fallback if no POST value provided for display option.
	        if (houzez_is_agency()) {
	            $agency_id = get_user_meta($user_id, 'fave_author_agency_id', true);
	            if (!empty($agency_id)) {
	                update_post_meta($property_id, 'fave_agent_display_option', 'agency_info');
	                update_post_meta($property_id, 'fave_property_agency', $agency_id);
	            } else {
	                update_post_meta($property_id, 'fave_agent_display_option', 'author_info');
	            }
	        } elseif (houzez_is_agent()) {
	            $agent_id = get_user_meta($user_id, 'fave_author_agent_id', true);
	            if (!empty($agent_id)) {
	                update_post_meta($property_id, 'fave_agent_display_option', 'agent_info');
	                update_post_meta($property_id, 'fave_agents', $agent_id);
	            } else {
	                update_post_meta($property_id, 'fave_agent_display_option', 'author_info');
	            }
	        } else {
	            update_post_meta($property_id, 'fave_agent_display_option', 'author_info');
	        }
	    }
	}

	/**
	 * Update custom fields meta using Houzez_Fields_Builder.
	 *
	 * @param int $property_id The property post ID.
	 */
	protected static function updateCustomFieldsMeta($property_id) {
	    if ( class_exists('Houzez_Fields_Builder') ) {
	        $fields_array = Houzez_Fields_Builder::get_form_fields();
	        if ( ! empty( $fields_array ) ) {
	            foreach ( $fields_array as $field ) {
	                $field_name = $field->field_id;
	                $field_type = $field->type;
	                
	                if ( isset( $_POST[$field_name] ) && ! empty( $_POST[$field_name] ) ) {
	                    // For multi-value fields (checkbox_list or multiselect).
	                    if ( in_array( $field_type, [ 'checkbox_list', 'multiselect' ] ) ) {
	                        delete_post_meta( $property_id, 'fave_' . $field_name );
	                        foreach ( $_POST[$field_name] as $field_value ) {
	                            add_post_meta( $property_id, 'fave_' . $field_name, sanitize_text_field( $field_value ) );
	                        }
	                    } else {
	                        update_post_meta( $property_id, 'fave_' . $field_name, sanitize_text_field( $_POST[$field_name] ) );
	                    }
	                } else {
	                    delete_post_meta( $property_id, 'fave_' . $field_name );
	                }
	            }
	        }
	    }
	}

    public static function is_user_verification_required($user_id) {
        // Check if user verification is required for new property submission
        $enable_user_verification = houzez_option('enable_user_verification', 0);
        $verification_required = houzez_option('verification_required_for_property', 0);
        
        if ($enable_user_verification && $verification_required) {
            // Skip verification check for admin, editors, and exempt roles
            if (!houzez_is_admin() && !houzez_is_editor() && !self::is_exempt_from_verification($user_id)) {
                // Check if user is verified
                $verification_status = get_user_meta($user_id, 'houzez_verification_status', true);
                if ($verification_status !== 'approved') {
                    return true; // Yes, verification is required for this user
                }
            }
        }
        return false; // No verification required
    }

    /**
     * Check if user is exempt from verification requirements
     *
     * @param int $user_id The user ID to check
     * @return bool True if exempt, false otherwise
     */
    protected static function is_exempt_from_verification($user_id) {
        // Get user
        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }
        
        // Get exempt roles
        $exempt_roles = houzez_option('exempt_roles_verification', array('administrator'));
        
        // Check if user has any exempt role
        foreach ($exempt_roles as $role) {
            if (in_array($role, (array)$user->roles)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Update property from a draft status.
     * Publishes a draft property based on admin approval settings.
     *
     * @param int $property_id The property ID to update from draft
     * @return bool True on success, false on failure
     */
    public static function updatePropertyFromDraft($property_id) {
        // Validate property exists and is actually a draft
        $current_status = get_post_status($property_id);
        if (!$current_status || $current_status !== 'draft') {
            return false;
        }
        
        // Determine new status based on admin approval settings
        $listings_admin_approved = houzez_option('listings_admin_approved');
        $new_status = ($listings_admin_approved !== 'yes') ? 'publish' : 'pending';
        
        // Update the property status
        $result = wp_update_post([
            'ID'         => $property_id,
            'post_type'  => 'property',
            'post_status'=> $new_status
        ]);
        
        return $result !== 0; // wp_update_post returns 0 on failure
    }
}

// Initialize the class and add the necessary hooks.
Houzez_Property_Submit::init();