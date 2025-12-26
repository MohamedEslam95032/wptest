<?php
if ( ! class_exists( 'Houzez_Enquiry' ) ) {

	class Houzez_Enquiry {

		
		public function __construct() {
			add_action( 'wp_ajax_crm_add_new_enquiry', array( $this, 'add_enquiry' ) );
			add_action( 'wp_ajax_get_single_enquiry', array( $this, 'get_single_enquiry' ) );
			add_action( 'wp_ajax_houzez_delete_enquiry', array( $this, 'delete_enquiry') );
			add_action( 'wp_ajax_houzez_match_listing_email', array( $this, 'send_match_listing_email') );
			add_action( 'wp_ajax_crm_export_inquiries', array( $this, 'crm_export_inquiries') );
			
		}

		public function crm_export_inquiries() {
		    global $wpdb;
		    $enquiries_table = $wpdb->prefix . 'houzez_crm_enquiries';
		    $leads_table = $wpdb->prefix . 'houzez_crm_leads';
		    $current_user_id = get_current_user_id();

		    // Build the JOIN query with user_id condition
		    $query = $wpdb->prepare(
		        "SELECT e.*, l.display_name, l.prefix, l.email, l.mobile 
		        FROM {$enquiries_table} e 
		        LEFT JOIN {$leads_table} l ON e.lead_id = l.lead_id 
		        WHERE e.user_id = %d AND l.user_id = %d 
		        GROUP BY e.enquiry_id",
		        $current_user_id, $current_user_id
		    );
		    $results = $wpdb->get_results($query, ARRAY_A);

		    // Output headers to force download
		    header('Content-Type: text/csv');
		    header('Content-Disposition: attachment; filename="inquiries_export.csv"');

		    // Open the file stream
		    $output = fopen('php://output', 'w');

		    // Headings for the CSV
		    $Headings = [
		        'Contact', 'Email', 'Mobile', 'Inquiry Type', 'Property Type', 'Property Status', 'Price', 'Bedrooms', 
		        'Bathrooms', 'Area Size', 'Location', 'Message', 'Private Note'
		    ];

		    // Add the headings to the CSV
		    fputcsv($output, $Headings);

		    // Iterate over the results and add them to the CSV
		    foreach ($results as $row) {
		        // Unserialize the enquiry_meta field
		        $meta = maybe_unserialize($row['enquiry_meta']);
		        
		        // Extract the necessary data
		        $propertyType = $meta['property_type']['name'] ?? '';
		        $propertyStatus = $meta['property_status']['name'] ?? '';
		        
		        // Extract and format the necessary data
		        $price = $meta['min_price'] ?? '';
		        $price .= (!empty($meta['max_price']) && $meta['max_price'] != $meta['min_price']) ? ' - ' . $meta['max_price'] : '';

		        $bedrooms = $meta['min_beds'] ?? '';
		        $bedrooms .= (!empty($meta['max_beds']) && $meta['max_beds'] != $meta['min_beds']) ? ' - ' . $meta['max_beds'] : '';

		        $bathrooms = $meta['min_baths'] ?? '';
		        $bathrooms .= (!empty($meta['max_baths']) && $meta['max_baths'] != $meta['min_baths']) ? ' - ' . $meta['max_baths'] : '';

		        $areaSize = $meta['min_area'] ?? '';
		        $areaSize .= (!empty($meta['max_area']) && $meta['max_area'] != $meta['min_area']) ? ' - ' . $meta['max_area'] : '';

		        $location = implode(', ', array_filter([
		            $meta['area']['name'] ?? '',
		            $meta['state']['name'] ?? '',
		            $meta['country']['name'] ?? ''
		        ]));

		        // Add the row to the CSV
		        fputcsv($output, [
		            $row['display_name'] ?? 'N/A', // Contact
		            $row['email'] ?? '-', // Contact
		            $row['mobile'] ?? '-', // Contact
		            $row['enquiry_type'] ?? '',
		            $propertyType,
		            $propertyStatus,
		            $price,
		            $bedrooms,
		            $bathrooms,
		            $areaSize,
		            $location,
		            $row['message'] ?? '',
		            $row['private_note'] ?? ''
		        ]);
		    }

		    // Close the file stream
		    fclose($output);
		    exit;
		}


		public function add_enquiry() {
			$lead_id = sanitize_text_field( $_POST['lead_id'] );
			$enquiry_type = sanitize_text_field( $_POST['enquiry_type'] );
			$property_type = sanitize_text_field( $_POST['e_meta']['property_type'] );
			$property_status = sanitize_text_field( $_POST['e_meta']['property_status'] );
			$property_label = sanitize_text_field( $_POST['e_meta']['property_label'] );

			if(empty($lead_id)) {
				echo json_encode( array( 'success' => false, 'msg' => esc_html__('Enter a valid contact', 'houzez-crm') ) );
	            wp_die();
			}

			if(empty($property_type)) {
				echo json_encode( array( 'success' => false, 'msg' => esc_html__('Select property type', 'houzez-crm') ) );
	            wp_die();
			}

			if(isset($_POST['enquiry_id']) && !empty($_POST['enquiry_id'])) {
				$enquiry_id = intval($_POST['enquiry_id']);
	        	$enquiry_id = $this->update_enquiry($enquiry_id);

				echo json_encode( array(
	                'success' => true,
	                'msg' => esc_html__("Successfully updated!", 'houzez-crm')
	            ));
	            wp_die();

	        } else {

				$save_enquiry = $this->save_enquiry();
				if($save_enquiry) {
					echo json_encode( array( 'success' => true, 'msg' => esc_html__('Successfully added!', 'houzez-crm') ) );
			            wp_die();
				}
			}
		}

		public function save_enquiry($lead_id = "") {

			global $wpdb;

			$listing_id = 0;
			if ( isset( $_POST['listing_id'] ) ) {
				$listing_id = intval( $_POST['listing_id'] );
			}

			$negotiator = '';
			if ( isset( $_POST['negotiator'] ) ) {
				$negotiator = sanitize_text_field( $_POST['negotiator'] );
			}

			$source = '';
			if ( isset( $_POST['source'] ) ) {
				$source = sanitize_text_field( $_POST['source'] );
			}

			$status = '';
			if ( isset( $_POST['status'] ) ) {
				$status = sanitize_text_field( $_POST['status'] );
			}

			$agent_id = '';
			if ( isset( $_POST['agent_id'] ) ) {
				$agent_id = sanitize_text_field( $_POST['agent_id'] );
			}

			$agent_type = '';
			if ( isset( $_POST['agent_type'] ) ) {
				$agent_type = sanitize_text_field( $_POST['agent_type'] );
			}

			$private_note = '';
			if ( isset( $_POST['private_note'] ) ) {
				$private_note = sanitize_textarea_field( $_POST['private_note'] );
			}

			$enquiry_type = '';
			if ( isset( $_POST['enquiry_type'] ) ) {
				$enquiry_type = sanitize_text_field( $_POST['enquiry_type'] );
			}

			$message = '';
			if ( isset( $_POST['message'] ) ) {
				$message = sanitize_textarea_field( $_POST['message'] );
			}

			if(!empty($listing_id)) {
				$enquiry_meta = $this->get_property_info($listing_id);
				$enquiry_meta = maybe_serialize($enquiry_meta);

			} else if( isset($_POST['is_estimation']) && $_POST['is_estimation'] == 'yes' ) {
				$meta = isset($_POST['e_meta']) ? $_POST['e_meta'] : '';
				$enquiry_meta = $this->prepare_estimation_meta($meta);
				$enquiry_meta = maybe_serialize($enquiry_meta);

			} else {
				$lead_id = intval( $_POST['lead_id'] );
				$meta = isset($_POST['e_meta']) ? $_POST['e_meta'] : '';
				$enquiry_meta = $this->prepare_property_meta($meta);
				$enquiry_meta = maybe_serialize($enquiry_meta);
			}

			if(!empty($listing_id)) {
				$user_id = get_post_field( 'post_author', $listing_id );
			}

			if( (isset($_POST['houzez_contact_form']) && $_POST['houzez_contact_form'] == 'yes') || (isset($_POST['is_estimation']) && $_POST['is_estimation'] == 'yes') || empty($user_id) ) {

				$adminData = get_user_by( 'email', get_option( 'admin_email' ) );
				$user_id = $adminData->ID;
				$agent_id = $adminData->ID;
				$agent_type = 'author_info';
			}

			if( isset($_POST['action']) && $_POST['action'] == 'crm_add_new_enquiry' ) {
				$user_id = get_current_user_id();
			}
		

            $data_table        = $wpdb->prefix . 'houzez_crm_enquiries';
	        $data = array(
	        	'user_id'       	=> $user_id,
                'lead_id'           => $lead_id,
                'listing_id'  		=> $listing_id,
                'negotiator'    	=> $negotiator,
                'source'     		=> $source,
                'status'         	=> $status,
                'enquiry_to'        => $agent_id,
                'enquiry_user_type' => $agent_type,
                'message'    		=> $message,
                'enquiry_type'    	=> $enquiry_type,
                'enquiry_meta'    	=> $enquiry_meta,
                'private_note'    	=> $private_note
            );

            $format = array(
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            );

            $inserted_id = $wpdb->insert($data_table, $data, $format);
            return $inserted_id;

		}

		public function update_enquiry($enquiry_id) {

			global $wpdb;

			$listing_id = 0;
			if ( isset( $_POST['listing_id'] ) ) {
				$listing_id = intval( $_POST['listing_id'] );
			}

			$negotiator = '';
			if ( isset( $_POST['negotiator'] ) ) {
				$negotiator = sanitize_text_field( $_POST['negotiator'] );
			}

			$source = '';
			if ( isset( $_POST['source'] ) ) {
				$source = sanitize_text_field( $_POST['source'] );
			}

			$status = '';
			if ( isset( $_POST['status'] ) ) {
				$status = sanitize_text_field( $_POST['status'] );
			}

			$agent_id = '';
			if ( isset( $_POST['agent_id'] ) ) {
				$agent_id = sanitize_text_field( $_POST['agent_id'] );
			}

			$agent_type = '';
			if ( isset( $_POST['agent_type'] ) ) {
				$agent_type = sanitize_text_field( $_POST['agent_type'] );
			}

			$private_note = '';
			if ( isset( $_POST['private_note'] ) ) {
				$private_note = sanitize_text_field( $_POST['private_note'] );
			}

			$enquiry_type = '';
			if ( isset( $_POST['enquiry_type'] ) ) {
				$enquiry_type = sanitize_text_field( $_POST['enquiry_type'] );
			}

			$message = '';
			if ( isset( $_POST['message'] ) ) {
				$message = sanitize_textarea_field( $_POST['message'] );
			}

			if(!empty($listing_id)) {
				$enquiry_meta = $this->get_property_info($listing_id);
				$enquiry_meta = maybe_serialize($enquiry_meta);
			} else {
				$lead_id = intval( $_POST['lead_id'] );
				$meta = $_POST['e_meta'];
				$enquiry_meta = $this->prepare_property_meta($meta);
				$enquiry_meta = maybe_serialize($enquiry_meta);
			}
		

            $data_table        = $wpdb->prefix . 'houzez_crm_enquiries';
	        $data = array(
                'lead_id'           => $lead_id,
                'listing_id'  		=> $listing_id,
                'negotiator'    	=> $negotiator,
                'source'     		=> $source,
                'status'         	=> $status,
                'enquiry_to'        => $agent_id,
                'enquiry_user_type' => $agent_type,
                'message'    		=> $message,
                'enquiry_type'    	=> $enquiry_type,
                'enquiry_meta'    	=> $enquiry_meta,
                'private_note'    	=> $private_note
            );

            $format = array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            );

            $where = array(
            	'enquiry_id' => $enquiry_id
            );

            $where_format = array(
            	'%d'
            );

            $updated = $wpdb->update( $data_table, $data, $where, $format, $where_format );

            if ( false === $updated ) {
			    return false;
			} else {
			    return true;
			}

		}

		public static function get_enquires() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_enquiries';

		    $andwhere = '';
		    if(isset($_GET['lead-id']) && !empty($_GET['lead-id'])) {
		        $lead_id = intval($_GET['lead-id']);
		        $andwhere .= $wpdb->prepare(' AND lead_id = %d ', $lead_id);
		    }

		    if(isset($_GET['keyword']) && !empty($_GET['keyword'])) {
		        $keyword = sanitize_text_field($_GET['keyword']);
		        $andwhere .= $wpdb->prepare(' AND (enquiry_type LIKE %s)', "%$keyword%");
		    }

		    $items_per_page = isset($_GET['records']) ? intval($_GET['records']) : 10;
		    $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
		    $offset = ($page * $items_per_page) - $items_per_page;
		    $user_id = get_current_user_id();

		    $query = $wpdb->prepare('SELECT * FROM '.$table_name.' WHERE user_id = %d '.$andwhere, $user_id);
		    $total_query = "SELECT COUNT(1) FROM ({$query}) AS combined_table";
		    $total = $wpdb->get_var($total_query);

		    $results = $wpdb->get_results($wpdb->prepare($query.' ORDER BY enquiry_id DESC LIMIT %d, %d', $offset, $items_per_page), OBJECT);

			$return_array['data'] = array(
		        'results' => $results,
		        'total_records' => $total,
		        'items_per_page' => $items_per_page,
		        'page' => $page,
		    );

		    return $return_array;
		}



		public function get_property_info($listing_id) {
			
			$enquiry_meta = array();

		    // Check if the listing ID is an integer before proceeding
		    if(empty($listing_id) || !is_int($listing_id)) {
		        return $enquiry_meta;
		    }

		    $listing_id = absint($listing_id);  // Make sure it's a positive integer


			$enquiry_meta['property_type'] = $this->crm_taxonomy( 'property_type', $listing_id );
			$enquiry_meta['property_status'] = $this->crm_taxonomy( 'property_status', $listing_id );
			$enquiry_meta['property_label'] = $this->crm_taxonomy( 'property_label', $listing_id );

			$enquiry_meta['country'] = $this->crm_taxonomy( 'property_country', $listing_id );
			$enquiry_meta['state'] = $this->crm_taxonomy( 'property_state', $listing_id );
			$enquiry_meta['city'] = $this->crm_taxonomy( 'property_city', $listing_id );
			$enquiry_meta['area'] = $this->crm_taxonomy( 'property_area', $listing_id );

			$enquiry_meta['min_beds'] = get_post_meta( $listing_id, 'fave_property_bedrooms', true );
			$enquiry_meta['max_beds'] = get_post_meta( $listing_id, 'fave_property_bedrooms', true );

			$enquiry_meta['min_baths'] = get_post_meta( $listing_id, 'fave_property_bathrooms', true );
			$enquiry_meta['max_baths'] = get_post_meta( $listing_id, 'fave_property_bathrooms', true );
			$enquiry_meta['min_price'] = get_post_meta( $listing_id, 'fave_property_price', true );
			$enquiry_meta['max_price'] = get_post_meta( $listing_id, 'fave_property_price', true );

			$enquiry_meta['min_area'] = get_post_meta( $listing_id, 'fave_property_size', true );
			$enquiry_meta['max_area'] = get_post_meta( $listing_id, 'fave_property_size', true );
			$enquiry_meta['zipcode'] = get_post_meta( $listing_id, 'fave_property_zip', true );
			$enquiry_meta['streat_address'] = get_post_meta( $listing_id, 'fave_property_address', true );

			return $enquiry_meta;
		}

		public function prepare_property_meta($meta) {
			$enquiry_meta = array();

			$enquiry_meta['property_type'] = hcrm_get_term_by( 'slug', sanitize_text_field($meta['property_type']), 'property_type');
			$enquiry_meta['property_status'] = hcrm_get_term_by( 'slug', sanitize_text_field($meta['property_status']), 'property_status');
			$enquiry_meta['property_label'] = hcrm_get_term_by( 'slug', sanitize_text_field($meta['property_label']), 'property_label');

			$enquiry_meta['country'] = hcrm_get_term_by( 'slug', sanitize_text_field($meta['country']), 'property_country');
			$enquiry_meta['state'] = hcrm_get_term_by( 'slug', sanitize_text_field($meta['state']), 'property_state');
			$enquiry_meta['city'] = hcrm_get_term_by( 'slug', sanitize_text_field($meta['city']), 'property_city');
			$enquiry_meta['area'] = hcrm_get_term_by( 'slug', sanitize_text_field($meta['area']), 'property_area');

			$enquiry_meta['min_beds'] = sanitize_text_field($meta['min-beds']);
			$enquiry_meta['max_beds'] = sanitize_text_field($meta['max-beds']);

			$enquiry_meta['min_baths'] = sanitize_text_field($meta['min-baths']);
			$enquiry_meta['max_baths'] = sanitize_text_field($meta['max-baths']);

			$enquiry_meta['min_price'] = sanitize_text_field($meta['min-price']);
			$enquiry_meta['max_price'] = sanitize_text_field($meta['max-price']);

			$enquiry_meta['min_area'] = sanitize_text_field($meta['min-area']);
			$enquiry_meta['max_area'] = sanitize_text_field($meta['max-area']);
			$enquiry_meta['zipcode'] = sanitize_text_field($meta['zipcode']);
			$enquiry_meta['streat_address'] = sanitize_text_field($meta['streat_address']);

			return $enquiry_meta;

		}

		public function prepare_estimation_meta($meta) {
			$enquiry_meta = array();
			$beds = isset($meta['beds']) ? $meta['beds'] : '';
			$max_beds = isset($meta['max-beds']) ? $meta['max-beds'] : '';
			$baths = isset($meta['baths']) ? $meta['baths'] : '';
			$max_baths = isset($meta['max-baths']) ? $meta['max-baths'] : '';
			$price = isset($meta['price']) ? $meta['price'] : '';
			$max_price = isset($meta['max-price']) ? $meta['max-price'] : '';
			$area_size = isset($meta['area-size']) ? $meta['area-size'] : '';
			$max_area_size = isset($meta['max-area-size']) ? $meta['max-area-size'] : '';
			$zipcode = isset($meta['zipcode']) ? $meta['zipcode'] : '';
			$streat_address = isset($meta['streat_address']) ? $meta['streat_address'] : '';

			$property_type = isset($meta['property_type']) ? sanitize_text_field($meta['property_type']) : '';
			$property_status = isset($meta['property_status']) ? sanitize_text_field($meta['property_status']) : '';
			$property_label = isset($meta['property_label']) ? sanitize_text_field($meta['property_label']) : '';

			$country = isset($meta['country']) ? sanitize_text_field($meta['country']) : '';
			$state = isset($meta['state']) ? sanitize_text_field($meta['state']) : '';
			$city = isset($meta['city']) ? sanitize_text_field($meta['city']) : '';
			$area = isset($meta['area']) ? sanitize_text_field($meta['area']) : '';

			$enquiry_meta['property_type'] = hcrm_get_term_by( 'slug', $property_type, 'property_type');
			$enquiry_meta['country'] = hcrm_get_term_by( 'slug', $country, 'property_country');
			$enquiry_meta['state'] = hcrm_get_term_by( 'slug', $state, 'property_state');
			$enquiry_meta['city'] = hcrm_get_term_by( 'slug', $city, 'property_city');
			$enquiry_meta['area'] = hcrm_get_term_by( 'slug', $area, 'property_area');

			$enquiry_meta['min_beds'] = sanitize_text_field($beds);
			$enquiry_meta['max_beds'] = sanitize_text_field($max_beds);

			$enquiry_meta['min_baths'] = sanitize_text_field($baths);
			$enquiry_meta['max_baths'] = sanitize_text_field($max_baths);

			$enquiry_meta['min_price'] = sanitize_text_field($price);
			$enquiry_meta['max_price'] = sanitize_text_field($max_price);

			$enquiry_meta['min_area'] = sanitize_text_field($area_size);
			$enquiry_meta['max_area'] = sanitize_text_field($max_area_size);
			$enquiry_meta['zipcode'] = sanitize_text_field($zipcode);
			$enquiry_meta['streat_address'] = sanitize_text_field($streat_address);

			return $enquiry_meta;

		}

		public function crm_taxonomy( $tax_name, $propID ) {
			$data = array();
	        $terms = get_the_terms( $propID, $tax_name );
			if ( $terms && ! is_wp_error( $terms ) ) {
			    // get the first term
			    $term = array_shift( $terms );
			    $data['name'] = $term->name;
			    $data['slug'] = $term->slug;
			}

			return $data;
	    }

	    public static function get_enquiry($enquiry_id) {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_enquiries';

		    // Make sure the ID is a positive integer
		    $enquiry_id = absint($enquiry_id);

		    $user_id = get_current_user_id();

		    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE enquiry_id = %d AND user_id = %d", $enquiry_id, $user_id);

		    $result = $wpdb->get_row($sql, OBJECT);

		    if( is_object( $result ) && ! empty( $result ) ) {
		        return $result;
		    }

		    return '';
		}


		public function get_single_enquiry() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_enquiries';
		    
		    $enquiry_id = '';
		    if ( isset( $_POST['enquiry_id'] ) ) {
		        $enquiry_id = intval( $_POST['enquiry_id'] );
		    }

		    if(empty($enquiry_id)) {
		        echo json_encode( 
		            array( 
		                'success' => false, 
		                'msg' => esc_html__('Something went wrong!', 'houzez-crm') 
		            ) 
		        );
		        wp_die();
		    }

		    $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE enquiry_id = %d", $enquiry_id);

		    $result = $wpdb->get_row( $sql, OBJECT );

		    $meta = maybe_unserialize($result->enquiry_meta);
		    
		    if( is_object( $result ) && ! empty( $result ) ) {
		        echo json_encode( 
		            array( 
		                'success' => true, 
		                'data' => $result,
		                'meta' => $meta
		            ) 
		        );
		        wp_die();
		    }
		    return '';
		}

		

		public function delete_enquiry() {
		    global $wpdb;

		    $user_id = get_current_user_id();
		    $table_name = $wpdb->prefix . 'houzez_crm_enquiries';

		    if ( !isset( $_POST['ids'] ) ) {
		        $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'No enquiry selected', 'houzez-crm' ) );
		        echo json_encode( $ajax_response );
		        die;
		    }
		    $ids = $_POST['ids'];

		    // Ensure each id is an integer
		    $ids_array = explode(',', $ids);
		    $ids_array = array_map('intval', $ids_array);

		    // Create placeholders for each ID
		    $placeholders = implode(',', array_fill(0, count($ids_array), '%d'));

		    // Merge ids_array with user_id for the preparation
		    $query_data = array_merge($ids_array, array($user_id));

		    $query = $wpdb->prepare("DELETE FROM {$table_name} WHERE enquiry_id IN ($placeholders) AND user_id = %d", ...$query_data);
		    $deleted = $wpdb->query($query);

		    if( $deleted ) {
		        $ajax_response = array( 'success' => true , 'reason' => '' );
		    } else {
		        $ajax_response = array( 'success' => false , 'reason' => esc_html__("You don't have rights to perform this action", 'houzez-crm') );
		    }
		    echo json_encode( $ajax_response );
		    die;
		}

		public function send_match_listing_email() {
			$current_user = wp_get_current_user();
			$from_email = $current_user->user_email;
			$display_name = $current_user->display_name;

			$listing_ids = sanitize_text_field($_POST['ids']);
			
			$target_email = $_POST['email_to'] ?? '';
			$target_email = is_email($target_email);

			$subject = sprintf( esc_html__('Matched Listing Email from %s', 'houzez-crm'), get_bloginfo('name') );

	        $body = esc_html__("We found these listings against your inquiry", 'houzez-crm')." <br/>";

	        $listing_ids = explode(',', $listing_ids);

	        $i = 0;
	        foreach ($listing_ids as $id) { $i++;
	        	$body .= $i.') <a href="'.get_permalink($id).'">'.get_the_title($id).'</a>'. "<br/>";
	        }

	        
	        $headers = array();
	        $headers[] = 'From: '.$display_name.' <'.$from_email.'>';
	        $headers[] = 'Content-Type: text/html; charset=UTF-8';

			if ( wp_mail( $target_email, $subject, $body, $headers ) ) {
	            echo json_encode( array(
	                'success' => true,
	                'msg' => esc_html__("Email Sent Successfully!", 'houzez-crm')
	            ));
	        } else {
	            echo json_encode(array(
	                    'success' => false,
	                    'msg' => esc_html__("Server Error: Make sure Email function working on your server!", 'houzez-crm')
	                )
	            );
	        }
			wp_die();

		}

		public static function get_inquiries_stats() {

            $stats = array();
            $args = array('user_id' => get_current_user_id());

            $stats['enquiries_count'] = self::get_enquiries_Count($args);
            

            return $stats;
        }

		public static function get_enquiries_Count( $args = array() ) {
            $return = array();
            $user_id = isset( $args['user_id'] ) ? $args['user_id'] : false;
            
            $return['lastday'] = self::get_enquiries_insights( [ 'user_id' => $user_id, 'time' => 'lastday' ] );
            $return['lasttwo'] = self::get_enquiries_insights( [ 'user_id' => $user_id, 'time' => 'lasttwo' ] );
            $return['lastweek'] = self::get_enquiries_insights( [ 'user_id' => $user_id, 'time' => 'lastweek' ] );
            $return['last2week'] = self::get_enquiries_insights( [ 'user_id' => $user_id, 'time' => 'last2week' ] );
            $return['lastmonth'] = self::get_enquiries_insights( [ 'user_id' => $user_id, 'time' => 'lastmonth' ] );
            $return['last2month'] = self::get_enquiries_insights( [ 'user_id' => $user_id, 'time' => 'last2month' ] );
            
            return $return;
        }

		public static function get_enquiries_insights( $args = array() ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'houzez_crm_enquiries';
            $query = array();

            $DateTimeZone = wp_timezone();//new DateTimeZone( '+02:30' );
            $DateTime = new DateTime('now', $DateTimeZone);

            $args = wp_parse_args( $args, [
                'user_id' => false,
                'time' => false,
            ] );

            $query[] = "SELECT COUNT( {$table_name}.lead_id ) AS count";

            $query[] = "FROM {$table_name}";
            $query[] = "WHERE user_id =".$args['user_id'];

            if ( !empty( $args['time'] ) && in_array( $args['time'], ['lastday', 'lasttwo', 'lastweek', 'last2week', 'lastmonth', 'last2month', 'lasthalfyear', 'lastyear'] ) ) {

                $time_token = [ 'lastday' => '-1 day', 'lasttwo' => '-2 day', 'lastweek' => '-7 days', 'last2week' => '-14 days', 'lastmonth' => '-30 days', 'last2month' => '-60 days', 'lasthalfyear' => '-182 days', 'lastyear' => '-365 days' ];

                $modifiedTime = $DateTime->modify( $time_token[ $args['time'] ] )->format('Y-m-d H:i:s');

                $query[] = sprintf(
                    " AND {$table_name}.time >= '%s' ", $modifiedTime
                );
            }

            $query = join( "\n", $query );

            $results = $wpdb->get_row( $query, OBJECT );

            return is_object( $results ) && ! empty( $results->count ) ? (int) $results->count : 0;
        }


	} // end Houzez_Enquiry

	new Houzez_Enquiry();
}