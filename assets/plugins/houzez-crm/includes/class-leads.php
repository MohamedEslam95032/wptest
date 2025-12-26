<?php
if ( ! class_exists( 'Houzez_Leads' ) ) {

	class Houzez_Leads {

		
		public function __construct() {
			add_action( 'wp_ajax_houzez_crm_add_lead', array( $this, 'add_lead' ) );
			add_action( 'wp_ajax_get_single_lead', array( $this, 'get_single_lead' ) );
			add_action( 'wp_ajax_houzez_delete_lead', array( $this, 'delete_lead') );
			add_action( 'wp_ajax_bulk_delete_leads', array( $this, 'bulk_delete_leads') );
			add_action( 'wp_ajax_houzez_crm_export_leads', array( $this, 'export_leads_ajax_handler') );
			add_action( 'wp_ajax_houzez_crm_upload_csv', array( $this, 'crm_upload_csv_handler') );
			add_action( 'wp_ajax_delete_leads_csv_file', array( $this, 'delete_leads_csv_file') );
			add_action( 'wp_ajax_get_leads_csv_headers', array( $this, 'houzez_crm_get_csv_headers') );
			add_action( 'wp_ajax_houzez_crm_process_field_mapping', array( $this, 'houzez_crm_process_field_mapping') );
		}

		public function delete_leads_csv_file() {
		    // Verify nonce for security
		    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'delete_leads_csv_file_nonce')) {
		        wp_send_json_error('Nonce verification failed.');
		        return;
		    }

		    // Check if file_name is set in POST request
		    if (isset($_POST['file_name'])) {
		        $file_name = sanitize_file_name($_POST['file_name']);

		        // Get the current user ID
		        $current_user_id = get_current_user_id();

		        // Retrieve the existing array of files for the current user
		        $uploaded_files = get_user_meta($current_user_id, 'houzez_crm_leads_uploaded_csvs', true);

		        // Get the custom upload directory
		        $upload_dir = wp_upload_dir();
		        $custom_upload_dir = $upload_dir['basedir'] . '/houzez-crm';
		        $file_path = $custom_upload_dir . '/' . $file_name; // Full path to the file

		        // Ensure the file path is within the houzez-crm directory and prevent path traversal
		        if (strpos(realpath($file_path), realpath($custom_upload_dir)) === 0) {
		            // Ensure the file is in the user's uploaded files
		            if (!empty($uploaded_files) && is_array($uploaded_files)) {
		                $file_belongs_to_user = false;

		                foreach ($uploaded_files as $key => $file_data) {
		                    if ($file_data['name'] == $file_name) {
		                        $file_belongs_to_user = true;

		                        // Delete the file record from the database
		                        unset($uploaded_files[$key]);
		                        update_user_meta($current_user_id, 'houzez_crm_leads_uploaded_csvs', array_values($uploaded_files));
		                        break;
		                    }
		                }

		                // Only delete the file if it belongs to the user and the path is valid
		                if ($file_belongs_to_user && file_exists($file_path)) {
		                    unlink($file_path);
		                    wp_send_json_success('File deleted successfully.');
		                } else {
		                    wp_send_json_error('File does not belong to you or does not exist.');
		                }
		            } else {
		                wp_send_json_error('No files found for this user.');
		            }
		        } else {
		            wp_send_json_error('Invalid file path.');
		        }
		    } else {
		        wp_send_json_error('No file specified.');
		    }
		}




		public function crm_upload_csv_handler() {

		    // Check if the user has the capability to upload files
		    $current_user = wp_get_current_user();
		    $allowed_roles = array('houzez_agent', 'houzez_agency');
		    
		    if (!current_user_can('upload_files') && !array_intersect($allowed_roles, $current_user->roles)) {
		    	$permissions = esc_html__('You do not have permission to upload files.', 'houzez-crm');
		        wp_send_json_error($permissions);
		        wp_die();
		    }

		    // Verify the nonce
		    if (!isset($_POST['houzez_crm_leads_nonce_field']) || !wp_verify_nonce($_POST['houzez_crm_leads_nonce_field'], 'houzez_crm_leads_upload_nonce')) {
		    	$nonce = esc_html__('Nonce verification failed.', 'houzez-crm');
		        wp_send_json_error($nonce);
		        wp_die();
		    }

		    // Handle file upload
		    if (!function_exists('wp_handle_upload')) {
		        require_once(ABSPATH . 'wp-admin/includes/file.php');
		    }

		    add_filter('upload_dir', array($this, 'custom_upload_directory'));

		    $uploadedfile = $_FILES['csv_import'];
		    $upload_overrides = array('test_form' => false);
		    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

		    remove_filter('upload_dir', array($this, 'custom_upload_directory'));

		    if ($movefile && !isset($movefile['error'])) {
		        // File is uploaded successfully. Now, save the file path in user meta.
		        $current_user_id = get_current_user_id();
		        if ($current_user_id) {
		            // Retrieve the existing array of files (if it exists)
		            $existing_files = get_user_meta($current_user_id, 'houzez_crm_leads_uploaded_csvs', true);
		            if (empty($existing_files) || !is_array($existing_files)) {
		                $existing_files = array();
		            }

		            // get file name
		            $filename = basename($movefile['file']);

		            // Prepare the file data with name and upload date
				    $file_data = array(
				        'name' => $filename,
				        'upload_date' => current_time('mysql') // Get the current WordPress time in MySQL format
				    );

				    // Add the new file data to the array
				    $existing_files[$filename] = $file_data;

		            // Save the updated array of files in user meta
		            update_user_meta($current_user_id, 'houzez_crm_leads_uploaded_csvs', $existing_files);

		            $dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
					$import_link = add_query_arg( array('hpage' => 'import-leads', 'import' => 1 ), $dashboard_crm );

		            $msg = array( 
					    'message' => esc_html__('File is uploaded successfully. Redirecting...', 'houzez-crm'), 
					    'redirect_to' => $import_link,
					);
		            wp_send_json_success($msg);
		            wp_die();
		        } else {
		            // Handle the case where there is no logged-in user
		            $msg = esc_html__("You don't have permission to upload file", 'houzez-crm');
		            wp_send_json_error($msg);
		            wp_die();
		        }
		    } else {
		        // Handle error
		        $msg = esc_html__("Error: ".$movefile['error'], 'houzez-crm');
	            wp_send_json_error($msg);
	            wp_die();
		    }

		    wp_die(); // Always end with wp_die() in AJAX handlers.
		}


		public function custom_upload_directory($dir) {
		    return array(
		        'path'   => $dir['basedir'] . '/houzez-crm',
		        'url'    => $dir['baseurl'] . '/houzez-crm',
		        'subdir' => '/houzez-crm',
		    ) + $dir;
		}


		public function houzez_crm_get_csv_headers() {
		    // Verify nonce and user permission

		    $file_name = isset($_POST['file_name']) ? sanitize_file_name($_POST['file_name']) : '';

		    if( empty($file_name) ) {
		    	wp_send_json_error(esc_html__('File not found.', 'houzez-crm'));
		    }

		    $current_user_id = get_current_user_id();
		    $upload_dir = wp_upload_dir();
		    $file_path = trailingslashit($upload_dir['basedir']) . 'houzez-crm/' . $file_name;

		    // Check if file exists and then get headers
		    if (file_exists($file_path)) {
		        $headers = $this->get_csv_headers($file_path);
		        wp_send_json_success($headers);
		    } else {
		        wp_send_json_error(esc_html__('File not found.', 'houzez-crm'));
		    }

		    wp_die();
		}

		public function get_csv_headers($file_path) {
		    if (($handle = fopen($file_path, 'r')) !== false) {
		        if (($data = fgetcsv($handle, 1000, ',')) !== false) {
		            fclose($handle);
		            return $data; // Returns an array of headers
		        }
		        fclose($handle);
		    }
		    return array();
		}

		public function houzez_crm_process_field_mapping() {
		    // Verify nonce and check user capability

		    $selected_csv_file = isset($_POST['selected_csv_file']) ? sanitize_file_name($_POST['selected_csv_file']) : '';
		    $field_mapping = isset($_POST['field_mapping']) ? $_POST['field_mapping'] : array();

		    $dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
		    $leads_link = add_query_arg( array('hpage' => 'leads' ), $dashboard_crm );

		    $upload_dir = wp_upload_dir();
		    $file_path = trailingslashit($upload_dir['basedir']) . 'houzez-crm/' . $selected_csv_file;

		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';

		    // Sanitize and validate field mappings, exclude empty mappings
		    $valid_field_mapping = array();
		    foreach ($field_mapping as $db_field => $csv_header) {
		        // Check if the mapping is a non-empty string
		        if (!empty($csv_header) && is_string($csv_header) && trim($csv_header) !== '') {
		            $valid_field_mapping[$db_field] = sanitize_text_field($csv_header);
		        }
		    }

		    if( empty( $valid_field_mapping ) ) {
		    	wp_send_json_error(esc_html__('Please map at least one field.', 'houzez-crm'));
		    }

		    // Get current user ID
		    $current_user_id = get_current_user_id();

		    if (file_exists($file_path)) {
		        if (($handle = fopen($file_path, 'r')) !== false) {
		            // Fetch CSV headers
		            $csv_headers = fgetcsv($handle, 1000, ',');

		            // Create an associative array mapping CSV headers to their index
		            $header_index = array_flip($csv_headers);

		            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
			            $insert_data = array();

			            // Process only valid mappings
			            foreach ($valid_field_mapping as $db_field => $csv_header) {
			                $insert_data[$db_field] = sanitize_text_field($data[$header_index[$csv_header]]);
			            }

			            // Add the current user ID to the insert data
		                $insert_data['user_id'] = $current_user_id;

			            // Insert data into the database
		                $wpdb->insert($table_name, $insert_data);

			        }
		            fclose($handle);
		        }
		    } else {
		        wp_send_json_error('File not found.');
		    }

		    $return = array(
		    	'message' => esc_html__('Data imported successfully.', 'houzez-crm'),
		    	'redirect_to' => $leads_link,
		    );

		    wp_send_json_success($return);

		    wp_die();
		}

		public function export_leads_ajax_handler() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';
		    $current_user_id = get_current_user_id();

		    // Fields to include in the CSV
		    $fields = [
		        'prefix', 'first_name', 'last_name', 'display_name', 'email', 
		        'mobile', 'home_phone', 'work_phone', 'address', 'city', 'state', 
		        'country', 'zipcode', 'type', 'source', 'source_link', 
		        'twitter_url', 'linkedin_url', 'facebook_url', 'private_note', 'message',
		    ];

		    // Create the query string with the specified fields
		    $query = "SELECT " . implode(', ', $fields) . " FROM {$table_name} WHERE user_id = $current_user_id";
		    $results = $wpdb->get_results($query, ARRAY_A);

		    // Set the headers to output a CSV
		    header("Content-Type: text/csv");
		    header("Content-Disposition: attachment; filename=leads.csv");
		    header("Pragma: no-cache");
		    header("Expires: 0");

		    // Open the output stream
		    $output = fopen('php://output', 'w');

		    // Headings for the CSV
		    $Headings = [
			    esc_html__('Prefix', 'houzez-crm'), 
			    esc_html__('First Name', 'houzez-crm'), 
			    esc_html__('Last Name', 'houzez-crm'), 
			    esc_html__('Full Name', 'houzez-crm'), 
			    esc_html__('Email', 'houzez-crm'), 
			    esc_html__('Mobile', 'houzez-crm'), 
			    esc_html__('Home Phone', 'houzez-crm'), 
			    esc_html__('Work Phone', 'houzez-crm'), 
			    esc_html__('Address', 'houzez-crm'), 
			    esc_html__('City', 'houzez-crm'), 
			    esc_html__('County / State', 'houzez-crm'), 
			    esc_html__('Country', 'houzez-crm'), 
			    esc_html__('Postal Code / Zip', 'houzez-crm'), 
			    esc_html__('Type', 'houzez-crm'), 
			    esc_html__('Source', 'houzez-crm'), 
			    esc_html__('Source Link', 'houzez-crm'), 
			    esc_html__('Twitter', 'houzez-crm'), 
			    esc_html__('Linkedin', 'houzez-crm'), 
			    esc_html__('Facebook', 'houzez-crm'), 
			    esc_html__('Private Note', 'houzez-crm'), 
			    esc_html__('Message', 'houzez-crm')
			];

		    // Add the headings to the CSV
		    fputcsv($output, $Headings);

		    // Add rows to CSV
		    foreach ($results as $row) {
		        // Map the results to the fields array to maintain the correct order
		        $filtered_row = array_map(function($field) use ($row) {
		            return $row[$field] ?? '';
		        }, $fields);

		        // Output the row to the CSV
		        fputcsv($output, $filtered_row);
		    }

		    // Close the output stream
		    fclose($output);
		    exit;
		}


		public function export_leads_ajax_handler_old() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';

		    // Fields to include in the CSV (excluding certain fields)
		    $fields = [
		        'prefix', 'first_name', 'last_name', 'display_name', 'email', 
		        'mobile', 'home_phone', 'work_phone', 'address', 'city', 'state', 
		        'country', 'zipcode', 'type', 'source', 'source_link', 
		        'twitter_url', 'linkedin_url', 'facebook_url', 'private_note', 'message',
		    ];

		    $Headings = [
			    esc_html__('Prefix', 'houzez-crm'), 
			    esc_html__('First Name', 'houzez-crm'), 
			    esc_html__('Last Name', 'houzez-crm'), 
			    esc_html__('Full Name', 'houzez-crm'), 
			    esc_html__('Email', 'houzez-crm'), 
			    esc_html__('Mobile', 'houzez-crm'), 
			    esc_html__('Home Phone', 'houzez-crm'), 
			    esc_html__('Work Phone', 'houzez-crm'), 
			    esc_html__('Address', 'houzez-crm'), 
			    esc_html__('City', 'houzez-crm'), 
			    esc_html__('County / State', 'houzez-crm'), 
			    esc_html__('Country', 'houzez-crm'), 
			    esc_html__('Postal Code / Zip', 'houzez-crm'), 
			    esc_html__('Type', 'houzez-crm'), 
			    esc_html__('Source', 'houzez-crm'), 
			    esc_html__('Source Link', 'houzez-crm'), 
			    esc_html__('Twitter', 'houzez-crm'), 
			    esc_html__('Linkedin', 'houzez-crm'), 
			    esc_html__('Facebook', 'houzez-crm'), 
			    esc_html__('Private Note', 'houzez-crm'), 
			    esc_html__('Message', 'houzez-crm')
			];


		    // Create the query string with the specified fields
		    $query = "SELECT " . implode(', ', $fields) . " FROM {$table_name}";
		    $results = $wpdb->get_results($query, ARRAY_A);

		    // Generate CSV content
		    $csv_output = " ," . implode(',', $Headings) . "\n"; // CSV header with numbering

		    // Add rows to CSV
		    $row_number = 1; // Initialize row counter
		    foreach ($results as $row) {
		        // Filter $row to only include the specified fields and apply esc_csv to each value
		        $filtered_row = array_map([$this, 'esc_csv'], array_intersect_key($row, array_flip($fields)));
		        $csv_output .= $row_number . ',"' . implode('","', $filtered_row) . "\"\n";
		        $row_number++; // Increment the row counter
		    }

		    // Output headers for downloading
		    header("Content-Type: text/csv");
		    header("Content-Disposition: attachment; filename=leads.csv");
		    header("Pragma: no-cache");
		    header("Expires: 0");

		    echo $csv_output;
		    exit;
		}

		// Helper function to escape CSV values
		public function esc_csv($value) {
		    $value = str_replace('"', '""', $value); // Escape double quotes
		    return $value;
		}

		public function add_lead() {

			$lead_id = $this->lead_exist();
			$email = sanitize_email( $_POST['email'] );
			$prefix = sanitize_text_field( $_POST['prefix'] );
			$first_name = sanitize_text_field( $_POST['first_name'] );
			$name = sanitize_text_field( $_POST['name'] );

			if(empty($prefix)) {
				echo json_encode( array( 'success' => false, 'msg' => esc_html__('Please select title!', 'houzez-crm') ) );
	            wp_die();
			}

			if(empty($name)) {
				echo json_encode( array( 'success' => false, 'msg' => esc_html__('Please enter your full name!', 'houzez-crm') ) );
	            wp_die();
			}

			if( !is_email( $email ) ) {
	            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid email address.', 'houzez-crm') ) );
	            wp_die();
	        }

	        if(isset($_POST['lead_id']) && !empty($_POST['lead_id'])) {
	        	$lead_id = intval($_POST['lead_id']);
	        	$lead_id = $this->update_lead($lead_id);

				echo json_encode( array(
	                'success' => true,
	                'msg' => esc_html__("Lead Successfully updated!", 'houzez-crm')
	            ));
	            wp_die();

	        } else {

	        	//if( empty($lead_id) ) {
					$lead_id = $this->save_lead();

					echo json_encode( array(
		                'success' => true,
		                'msg' => esc_html__("Lead Successfully added!", 'houzez-crm')
		            ));

				/*} else {
					echo json_encode( array(
		                'success' => false,
		                'msg' => esc_html__("Email already exist, try different email address", 'houzez-crm')
		            ));
				}*/
	        }
            wp_die();
		}

		public function lead_exist() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';
		    
		    $email = '';
		    if ( isset( $_POST['email'] ) ) {
		        $email = sanitize_email( $_POST['email'] );
		    }

		    if(empty($email)) {
		        return false;
		    }

		    $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE email = %s", $email);

		    $result = $wpdb->get_row( $sql, OBJECT );

		    if( is_object( $result ) && ! empty( $result ) ) {
		        return $result->lead_id;
		    }
		    return '';
		}


		public function get_single_lead() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';
		    
		    // Verify nonce for security
		    if ( !isset( $_POST['security'] ) || !wp_verify_nonce( $_POST['security'], 'get_lead_nonce' ) ) {
		        echo json_encode( 
		            array( 
		                'success' => false, 
		                'msg' => esc_html__('Security check failed!', 'houzez-crm') 
		            ) 
		        );
		        wp_die();
		    }
		    
		    // Check user capabilities - require at least 'edit_posts' capability
		    if ( !current_user_can('edit_posts') ) {
		        echo json_encode( 
		            array( 
		                'success' => false, 
		                'msg' => esc_html__('You do not have permission to access this resource.', 'houzez-crm') 
		            ) 
		        );
		        wp_die();
		    }
		    
		    $lead_id = '';
		    if ( isset( $_POST['lead_id'] ) ) {
		        $lead_id = intval( $_POST['lead_id'] );
		    }

		    if(empty($lead_id)) {
		        echo json_encode( 
		            array( 
		                'success' => false, 
		                'msg' => esc_html__('Something went wrong!', 'houzez-crm') 
		            ) 
		        );
		        wp_die();
		    }
		    
		    $current_user_id = get_current_user_id();
		    
		    // Build query with ownership check unless user is admin
		    if ( current_user_can('manage_options') ) {
		        // Admin can access all leads
		        $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE lead_id = %d", $lead_id);
		    } else {
		        // Regular users can only access their own leads
		        $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE lead_id = %d AND user_id = %d", $lead_id, $current_user_id);
		    }

		    $result = $wpdb->get_row( $sql, OBJECT );

		    if( is_object( $result ) && ! empty( $result ) ) {
		        echo json_encode( 
		            array( 
		                'success' => true, 
		                'data' => $result 
		            ) 
		        );
		        wp_die();
		    } else {
		        echo json_encode( 
		            array( 
		                'success' => false, 
		                'msg' => esc_html__('Lead not found or you do not have permission to access it.', 'houzez-crm') 
		            ) 
		        );
		        wp_die();
		    }
		}


		public function save_lead() {

			global $wpdb;
			$user_id = $message = '';

			$lead_title = '';
			if ( isset( $_POST['name'] ) ) {
				$lead_title = sanitize_text_field( $_POST['name'] );
			}

			$first_name = '';
			if ( isset( $_POST['first_name'] ) ) {
				$first_name = sanitize_text_field( $_POST['first_name'] );
			}

			$prefix = '';
			if ( isset( $_POST['prefix'] ) ) {
				$prefix = sanitize_text_field( $_POST['prefix'] );
			}

			$last_name = '';
			if ( isset( $_POST['last_name'] ) ) {
				$last_name = sanitize_text_field( $_POST['last_name'] );
			}

			if(empty($lead_title)) {
				$lead_title = $first_name.' '.$last_name;
			}

			$mobile = '';
			if ( isset( $_POST['mobile'] ) ) {
				$mobile = sanitize_text_field( $_POST['mobile'] );
			}

			if( isset($_POST['is_schedule_form']) && $_POST['is_schedule_form'] == 'yes') {
				$mobile = sanitize_text_field( $_POST['phone'] );
			}

			$home_phone = '';
			if ( isset( $_POST['home_phone'] ) ) {
				$home_phone = sanitize_text_field( $_POST['home_phone'] );
			}


			$work_phone = '';
			if ( isset( $_POST['work_phone'] ) ) {
				$work_phone = sanitize_text_field( $_POST['work_phone'] );
			}

			$user_type = '';
			if ( isset( $_POST['user_type'] ) ) {
				$user_type = sanitize_text_field( $_POST['user_type'] );
				$user_type = houzez_crm_get_form_user_type($user_type);
				
				// Remove slashes and properly decode HTML entities
				$user_type = stripslashes($user_type);
				$user_type = str_replace('&nbsp;', '', $user_type);
				$user_type = html_entity_decode($user_type, ENT_QUOTES);
				
				// Ensure no additional slashes are added when saving
				$user_type = wp_unslash($user_type);
			}

			$email = '';
			if ( isset( $_POST['email'] ) ) {
				$email = sanitize_email( $_POST['email'] );
			}

			$address = '';
			if ( isset( $_POST['address'] ) ) {
				$address = sanitize_text_field( $_POST['address'] );
			}

			$country = '';
			if ( isset( $_POST['country'] ) ) {
				$country = sanitize_text_field( $_POST['country'] );
			}

			$city = '';
			if ( isset( $_POST['city'] ) ) {
				$city = sanitize_text_field( $_POST['city'] );
			}

			$state = '';
			if ( isset( $_POST['state'] ) ) {
				$state = sanitize_text_field( $_POST['state'] );
			}

			$zip = '';
			if ( isset( $_POST['zip'] ) ) {
				$zip = sanitize_text_field( $_POST['zip'] );
			}

			$source = '';
			if ( isset( $_POST['source'] ) ) {
				$source = sanitize_text_field( $_POST['source'] );
			}

			$source_link = '';
			if ( isset( $_POST['source_link'] ) ) {
				$source_link = esc_url( $_POST['source_link'] );
			}

			if( isset($_POST['property_permalink']) ) {
				$source_link = esc_url($_POST['property_permalink']);
			}

			$agent_id = '';
			if ( isset( $_POST['agent_id'] ) ) {
				$agent_id = sanitize_text_field( $_POST['agent_id'] );
			}

			$agent_type = '';
			if ( isset( $_POST['agent_type'] ) ) {
				$agent_type = sanitize_text_field( $_POST['agent_type'] );
			}

			$facebook = '';
			if ( isset( $_POST['facebook'] ) ) {
				$facebook = sanitize_text_field( $_POST['facebook'] );
			}

			$twitter = '';
			if ( isset( $_POST['twitter'] ) ) {
				$twitter = sanitize_text_field( $_POST['twitter'] );
			}

			$linkedin = '';
			if ( isset( $_POST['linkedin'] ) ) {
				$linkedin = sanitize_text_field( $_POST['linkedin'] );
			}

			$private_note = '';
			if ( isset( $_POST['private_note'] ) ) {
				$private_note = sanitize_textarea_field( $_POST['private_note'] );
			}

			$listing_id = '';
			if ( isset( $_POST['listing_id'] ) ) {
				$listing_id = intval( $_POST['listing_id'] );
			}

			if(!empty($listing_id)) {
				$user_id = get_post_field( 'post_author', $listing_id );
			}

			if(isset($_POST['realtor_page']) && $_POST['realtor_page'] == 'yes') {
				if($agent_type == 'author_info') {
					$user_id = $agent_id;
				} else {
					$user_id = get_post_meta( $agent_id, 'houzez_user_meta_id', true );
				}
			} 

			$message = isset( $_POST['message'] ) ? sanitize_textarea_field($_POST['message']) : '';

			if( (isset($_POST['houzez_contact_form']) && $_POST['houzez_contact_form'] == 'yes') || (isset($_POST['is_estimation']) && $_POST['is_estimation'] == 'yes') || empty($user_id) ) {

				$adminData = get_user_by( 'email', get_option( 'admin_email' ) );
				$user_id = $adminData->ID;
			}

			if( isset($_POST['dashboard_lead']) && $_POST['dashboard_lead'] == 'yes' ) {
				$user_id = get_current_user_id();
			}

            $leads_table        = $wpdb->prefix . 'houzez_crm_leads';
	        $data = array(
	        	'user_id'       => $user_id,
                'prefix'        => $prefix,
                'display_name'  => $lead_title,
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'email'         => $email,
                'mobile'        => $mobile,
                'home_phone'    => $home_phone,
                'work_phone'    => $work_phone,
                'address'       => $address,
                'city'          => $city,
                'state'         => $state,
                'country'       => $country,
                'zipcode'       => $zip,
                'type'          => $user_type,
                'status'        => '',
                'source'        => $source,
                'source_link'        => $source_link,
                'enquiry_to'        => $agent_id,
                'enquiry_user_type' => $agent_type,
                'twitter_url'   => $twitter,
                'linkedin_url'  => $linkedin,
                'facebook_url'  => $facebook,
                'private_note'  => $private_note,
                'message'  => $message
            );

            $format = array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            );

            $wpdb->insert($leads_table, $data, $format);
            $inserted_id = $wpdb->insert_id;
            return $inserted_id;

		}

		public function update_lead($lead_id) {

			global $wpdb;

			$lead_title = '';
			if ( isset( $_POST['name'] ) ) {
				$lead_title = sanitize_text_field( $_POST['name'] );
			}

			$first_name = '';
			if ( isset( $_POST['first_name'] ) ) {
				$first_name = sanitize_text_field( $_POST['first_name'] );
			}

			$prefix = '';
			if ( isset( $_POST['prefix'] ) ) {
				$prefix = sanitize_text_field( $_POST['prefix'] );
			}

			$last_name = '';
			if ( isset( $_POST['last_name'] ) ) {
				$last_name = sanitize_text_field( $_POST['last_name'] );
			}

			if(empty($lead_title)) {
				$lead_title = $first_name.' '.$last_name;
			}

			$mobile = '';
			if ( isset( $_POST['mobile'] ) ) {
				$mobile = sanitize_text_field( $_POST['mobile'] );
			}

			$home_phone = '';
			if ( isset( $_POST['home_phone'] ) ) {
				$home_phone = sanitize_text_field( $_POST['home_phone'] );
			}

			$work_phone = '';
			if ( isset( $_POST['work_phone'] ) ) {
				$work_phone = sanitize_text_field( $_POST['work_phone'] );
			}

			$user_type = '';
			if ( isset( $_POST['user_type'] ) ) {
				$user_type = sanitize_text_field( $_POST['user_type'] );
			}

			$email = '';
			if ( isset( $_POST['email'] ) ) {
				$email = sanitize_email( $_POST['email'] );
			}

			$address = '';
			if ( isset( $_POST['address'] ) ) {
				$address = sanitize_text_field( $_POST['address'] );
			}

			$country = '';
			if ( isset( $_POST['country'] ) ) {
				$country = sanitize_text_field( $_POST['country'] );
			}

			$city = '';
			if ( isset( $_POST['city'] ) ) {
				$city = sanitize_text_field( $_POST['city'] );
			}

			$state = '';
			if ( isset( $_POST['state'] ) ) {
				$state = sanitize_text_field( $_POST['state'] );
			}

			$zip = '';
			if ( isset( $_POST['zip'] ) ) {
				$zip = sanitize_text_field( $_POST['zip'] );
			}

			$source = '';
			if ( isset( $_POST['source'] ) ) {
				$source = sanitize_text_field( $_POST['source'] );
			}

			/*$source_link = '';
			if ( isset( $_POST['source_link'] ) ) {
				$source_link = esc_url( $_POST['source_link'] );
			}*/

			$agent_id = '';
			if ( isset( $_POST['agent_id'] ) ) {
				$agent_id = sanitize_text_field( $_POST['agent_id'] );
			}

			$agent_type = '';
			if ( isset( $_POST['agent_type'] ) ) {
				$agent_type = sanitize_text_field( $_POST['agent_type'] );
			}

			$facebook = '';
			if ( isset( $_POST['facebook'] ) ) {
				$facebook = sanitize_text_field( $_POST['facebook'] );
			}

			$twitter = '';
			if ( isset( $_POST['twitter'] ) ) {
				$twitter = sanitize_text_field( $_POST['twitter'] );
			}

			$linkedin = '';
			if ( isset( $_POST['linkedin'] ) ) {
				$linkedin = sanitize_text_field( $_POST['linkedin'] );
			}

			$private_note = '';
			if ( isset( $_POST['private_note'] ) ) {
				$private_note = sanitize_textarea_field( $_POST['private_note'] );
			}

            $leads_table        = $wpdb->prefix . 'houzez_crm_leads';
	        $data = array(
                'prefix'        => $prefix,
                'display_name'  => $lead_title,
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'email'         => $email,
                'mobile'        => $mobile,
                'home_phone'    => $home_phone,
                'work_phone'    => $work_phone,
                'address'       => $address,
                'city'          => $city,
                'state'         => $state,
                'country'       => $country,
                'zipcode'       => $zip,
                'type'          => $user_type,
                'status'        => '',
                'source'        => $source,
                'enquiry_to'        => $agent_id,
                'enquiry_user_type' => $agent_type,
                'twitter_url'   => $twitter,
                'linkedin_url'  => $linkedin,
                'facebook_url'  => $facebook,
                'private_note'  => $private_note
            );

            $format = array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
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
            	'lead_id' => $lead_id
            );

            $where_format = array(
            	'%d'
            );

            $updated = $wpdb->update( $leads_table, $data, $where, $format, $where_format );

            if ( false === $updated ) {
			    return false;
			} else {
			    return true;
			}

		}

		public static function get_leads() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';

		    $items_per_page = isset($_GET['records']) ? intval($_GET['records']) : 10;
		    $page = isset($_GET['cpage']) ? abs((int) $_GET['cpage']) : 1;
		    $offset = ($page * $items_per_page) - $items_per_page;

		    $current_user_id = get_current_user_id();

		    // Retrieving the search keyword
		    $keyword = isset($_GET['keyword']) ? sanitize_text_field(trim($_GET['keyword'])) : '';

		    // Basic query
		    $query = "SELECT * FROM {$table_name} WHERE user_id = %d";

		    // If keyword is present, modify the query to include search condition
		    if (!empty($keyword)) {
		        $query .= $wpdb->prepare(" AND (mobile LIKE '%%%s%%' OR email LIKE '%%%s%%' OR first_name LIKE '%%%s%%' OR last_name LIKE '%%%s%%')", $keyword, $keyword, $keyword, $keyword);
		    }

		    $total_query = "SELECT COUNT(1) FROM ({$query}) AS combined_table"; // no need for prepare here
		    $total = $wpdb->get_var($wpdb->prepare($total_query, $current_user_id));

		    $results_query = $wpdb->prepare($query . ' ORDER BY lead_id DESC LIMIT %d, %d', $current_user_id, $offset, $items_per_page);
		    $results = $wpdb->get_results($results_query, OBJECT);

		    $return_array['data'] = array(
		        'results' => $results,
		        'total_records' => $total,
		        'items_per_page' => $items_per_page,
		        'page' => $page,
		    );

		    return $return_array;
		}

		public static function get_all_leads() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';
		    $current_user_id = get_current_user_id();
		    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id= %d", $current_user_id);
		    $results = $wpdb->get_results($sql, OBJECT);
		    return $results;
		}

		public static function get_lead($lead_id) {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';
		    $current_user_id = get_current_user_id();
		    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE lead_id = %d AND user_id = %d", $lead_id, $current_user_id);
		    $result = $wpdb->get_row($sql, OBJECT);
		    if (is_object($result) && !empty($result)) {
		        return $result;
		    }
		    return '';
		}

        public static function get_lead_viewed_listings() {
		    global $wpdb;

		    // Sanitize and validate the 'lead-id' input
		    $lead_id = isset($_GET['lead-id']) ? intval($_GET['lead-id']) : 0;

		    if(empty($lead_id)) {
		        return '';
		    }

		    $lead = self::get_lead($lead_id);

		    if(empty($lead->email)) {
		        return '';
		    }

		    $email = sanitize_email($lead->email);

		    if(empty($email)) {
		        return '';
		    }

		    $user = get_user_by('email', $email);

		    if(empty($user)) {
		        return '';
		    }

		    $user_id = intval($user->ID);

		    $table_name = $wpdb->prefix . 'houzez_crm_viewed_listings';

		    // Sanitize and validate the 'records' and 'cpage' inputs
		    $items_per_page = isset($_GET['records']) ? intval($_GET['records']) : 10;
		    $page = isset($_GET['cpage']) ? abs(intval($_GET['cpage'])) : 1;
		    $offset = ($page * $items_per_page) - $items_per_page;

		    // Secure the SQL query
		    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d ORDER BY id DESC LIMIT %d, %d", $user_id, $offset, $items_per_page);
		    $total_query = $wpdb->prepare("SELECT COUNT(1) FROM $table_name WHERE user_id = %d", $user_id);
		    $total = $wpdb->get_var($total_query);
		    $results = $wpdb->get_results($query, OBJECT);

		    $return_array['data'] = array(
		        'results' => $results,
		        'total_records' => $total,
		        'items_per_page' => $items_per_page,
		        'page' => $page,
		    );

		    return $return_array;
		}


        public static function get_lead_saved_searches() {
		    global $wpdb;

		    // Sanitize and validate the 'lead-id' input
		    $lead_id = isset($_GET['lead-id']) ? intval($_GET['lead-id']) : 0;

		    if(empty($lead_id)) {
		        return '';
		    }

		    $lead = self::get_lead($lead_id);

		    if(empty($lead->email)) {
		        return '';
		    }

		    $email = sanitize_email($lead->email);

		    if(empty($email)) {
		        return '';
		    }

		    $user = get_user_by('email', $email);

		    if(empty($user)) {
		        return '';
		    }

		    $user_id = intval($user->ID);

		    $table_name = $wpdb->prefix . 'houzez_search';

		    // Sanitize and validate the 'records' and 'cpage' inputs
		    $items_per_page = isset($_GET['records']) ? intval($_GET['records']) : 10;
		    $page = isset($_GET['cpage']) ? abs(intval($_GET['cpage'])) : 1;
		    $offset = ($page * $items_per_page) - $items_per_page;

		    // Secure the SQL query
		    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE auther_id = %d ORDER BY id DESC LIMIT %d, %d", $user_id, $offset, $items_per_page);
		    $total_query = $wpdb->prepare("SELECT COUNT(1) FROM $table_name WHERE auther_id = %d", $user_id);
		    $total = $wpdb->get_var($total_query);
		    $results = $wpdb->get_results($query, OBJECT);

		    $return_array['data'] = array(
		        'results' => $results,
		        'total_records' => $total,
		        'items_per_page' => $items_per_page,
		        'page' => $page,
		    );

		    return $return_array;
		}


		public function delete_lead() {
			global $wpdb;
            $table_name = $wpdb->prefix . 'houzez_crm_leads';

            $user_id = get_current_user_id();

			$nonce = $_POST['security'];
	        if ( ! wp_verify_nonce( $nonce, 'delete_lead_nonce' ) ) {
	            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'Security check failed!', 'houzez-crm' ) );
	            echo json_encode( $ajax_response );
	            die;
	        }

	        if ( !isset( $_POST['lead_id'] ) ) {
	            $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'No lead id found', 'houzez-crm' ) );
	            echo json_encode( $ajax_response );
	            die;
	        }
	        $lead_id = $_POST['lead_id'];

	        $where = array(
            	'lead_id' => $lead_id
            );

            $where_format = array(
            	'%d'
            );

	        
	        $deleted = $wpdb->query( 
				$wpdb->prepare( 
					"DELETE FROM {$table_name}
					 WHERE lead_id = %d AND user_id = %d
					",
				        $lead_id,
				        $user_id
			        )
			);

	        if( $deleted ) {
		        $ajax_response = array( 'success' => true , 'reason' => '' );
		    } else {
		    	$ajax_response = array( 'success' => false , 'reason' => esc_html__("You don't have rights to perform this action", 'houzez-crm') );
		    }
            echo json_encode( $ajax_response );
            die;
		}

		public function bulk_delete_leads() {
		    global $wpdb;

		    $user_id = get_current_user_id();
		    $table_name = $wpdb->prefix . 'houzez_crm_leads';

		    if ( !isset( $_POST['ids'] ) ) {
		        $ajax_response = array( 'success' => false , 'reason' => esc_html__( 'No Item Selected', 'houzez-crm' ) );
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

		    $query = $wpdb->prepare("DELETE FROM {$table_name} WHERE lead_id IN ($placeholders) AND user_id = %d", ...$query_data);
		    $deleted = $wpdb->query($query);

		    if( $deleted ) {
		        $ajax_response = array( 'success' => true , 'reason' => '' );
		    } else {
		        $ajax_response = array( 'success' => false , 'reason' => esc_html__("You don't have rights to perform this action", 'houzez-crm') );
		    }
		    echo json_encode( $ajax_response );
		    die;
		}

		public static function get_leads_stats() {

            $stats = array();
            $args = array('user_id' => get_current_user_id());

            $stats['leads_count'] = self::get_leads_Count($args);
            

            return $stats;
        }

		public static function get_leads_Count( $args = array() ) {
            $return = array();
            $user_id = isset( $args['user_id'] ) ? $args['user_id'] : false;
            
            $return['lastday'] = self::get_leads_insights( [ 'user_id' => $user_id, 'time' => 'lastday' ] );
            $return['lasttwo'] = self::get_leads_insights( [ 'user_id' => $user_id, 'time' => 'lasttwo' ] );
            $return['lastweek'] = self::get_leads_insights( [ 'user_id' => $user_id, 'time' => 'lastweek' ] );
            $return['last2week'] = self::get_leads_insights( [ 'user_id' => $user_id, 'time' => 'last2week' ] );
            $return['lastmonth'] = self::get_leads_insights( [ 'user_id' => $user_id, 'time' => 'lastmonth' ] );
            $return['last2month'] = self::get_leads_insights( [ 'user_id' => $user_id, 'time' => 'last2month' ] );
            
            return $return;
        }

		public static function get_leads_insights( $args = array() ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'houzez_crm_leads';
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

	} // end Houzez_Leads

	new Houzez_Leads();
}