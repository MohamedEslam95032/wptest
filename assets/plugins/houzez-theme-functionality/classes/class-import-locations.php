<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Import_Locations {
    
    private static $_instance = null;
    private static $batch_size = 100; // Process 100 records at a time
    private static $max_execution_time = 25; // Maximum execution time per batch (seconds)

    public function __construct() {
        add_action( 'init', array( $this, 'setup' ) );
    }

    public static function instance() {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    }

    public function setup() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__ , 'enqueue_scripts' ) );
        add_action( 'wp_ajax_get_locations_csv_headers', array( __CLASS__ , 'get_locations_csv_headers' ) );
        add_action( 'wp_ajax_locations_process_field_mapping', array( __CLASS__ , 'locations_process_field_mapping' ) );
        add_action( 'wp_ajax_locations_batch_import', array( __CLASS__ , 'locations_batch_import' ) );
        add_action( 'wp_ajax_get_csv_total_rows', array( __CLASS__ , 'get_csv_total_rows' ) );
    }

    /**
     * Convert URL to proper file path using WordPress functions
     * SECURITY: Only allows files within WordPress uploads directory
     */
    private static function url_to_path($url) {
        // Sanitize URL
        $url = esc_url_raw($url);
        
        // Get upload directory info
        $upload_dir = wp_upload_dir();
        $uploads_url = $upload_dir['baseurl'];
        $uploads_path = $upload_dir['basedir'];
        
        // SECURITY: Only allow files within uploads directory
        if (strpos($url, $uploads_url) !== 0) {
            return false; // URL must be within uploads directory
        }
        
        // Replace uploads URL with uploads path
        $file_path = str_replace($uploads_url, $uploads_path, $url);
        
        // SECURITY: Verify the resolved path is still within uploads directory
        $real_uploads_path = realpath($uploads_path);
        $real_file_path = realpath(dirname($file_path)) . '/' . basename($file_path);
        
        if ($real_uploads_path === false || strpos($real_file_path, $real_uploads_path) !== 0) {
            return false; // Path traversal attempt detected
        }
        
        // SECURITY: Only allow CSV files
        $allowed_extensions = array('csv');
        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_extensions)) {
            return false; // Invalid file type
        }
        
        return $file_path;
    }

    /**
     * Get total number of rows in CSV file (excluding header)
     */
    public static function get_csv_total_rows() {
        // SECURITY: Check user permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        // SECURITY: Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'houzez_import_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $file_url = isset($_POST['file_name']) ? sanitize_url($_POST['file_name']) : '';
        $file_path = self::url_to_path($file_url);

        if (!$file_path || !file_exists($file_path)) {
            wp_send_json_error('File not found or access denied');
            return;
        }

        $total_rows = 0;
        if (($handle = fopen($file_path, 'r')) !== false) {
            // Skip header row
            fgetcsv($handle, 1000, ',');
            
            // Count data rows
            while (fgetcsv($handle, 1000, ',') !== false) {
                $total_rows++;
            }
            fclose($handle);
        }

        wp_send_json_success($total_rows);
    }

    /**
     * Initial processing to validate and prepare for batch import
     */
    public static function locations_process_field_mapping() {
        // SECURITY: Check user permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        // SECURITY: Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'houzez_import_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $selected_csv_file = isset($_POST['selected_csv_file']) ? sanitize_url($_POST['selected_csv_file']) : '';
        $field_mapping = isset($_POST['field_mapping']) ? $_POST['field_mapping'] : array();
        $file_path = self::url_to_path($selected_csv_file);

        // Sanitize and validate field mappings
        $valid_field_mapping = array();
        
        if (is_array($field_mapping)) {
            foreach ($field_mapping as $db_field => $csv_header) {
                $clean_db_field = sanitize_key($db_field);
                $clean_csv_header = sanitize_text_field($csv_header);
                
                if (!empty($clean_csv_header) && trim($clean_csv_header) !== '') {
                    $valid_field_mapping[$clean_db_field] = $clean_csv_header;
                }
            }
        }

        if (empty($valid_field_mapping)) {
            wp_send_json_error(esc_html__('Please map at least one field.', 'houzez-theme-functionality'));
            return;
        }

        // Validate file exists
        if (!$file_path || !file_exists($file_path)) {
            wp_send_json_error(esc_html__('File not found or access denied.', 'houzez-theme-functionality'));
            return;
        }

        // Get total rows for progress tracking
        $total_rows = 0;
        if (($handle = fopen($file_path, 'r')) !== false) {
            // Skip header
            fgetcsv($handle, 1000, ',');
            while (fgetcsv($handle, 1000, ',') !== false) {
                $total_rows++;
            }
            fclose($handle);
        }

        // Store import session data
        $import_session = array(
            'file_path' => $file_path,
            'field_mapping' => $valid_field_mapping,
            'total_rows' => $total_rows,
            'processed_rows' => 0,
            'successful_imports' => 0,
            'errors' => array(),
            'start_time' => current_time('timestamp'),
            'status' => 'ready'
        );

        update_option('houzez_locations_import_session', $import_session);

        wp_send_json_success(array(
            'total_rows' => $total_rows,
            'message' => sprintf(esc_html__('Ready to import %d records. Starting batch import...', 'houzez-theme-functionality'), $total_rows)
        ));
    }

    /**
     * Process import in batches
     */
    public static function locations_batch_import() {
        // SECURITY: Check user permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        // SECURITY: Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'houzez_import_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $batch_number = isset($_POST['batch']) ? intval($_POST['batch']) : 0;
        $import_session = get_option('houzez_locations_import_session', array());

        if (empty($import_session) || $import_session['status'] === 'completed') {
            wp_send_json_error(esc_html__('No active import session found.', 'houzez-theme-functionality'));
            return;
        }

        // Set execution time limit
        set_time_limit(self::$max_execution_time + 5);
        $start_time = microtime(true);

        $file_path = $import_session['file_path'];
        $field_mapping = $import_session['field_mapping'];
        $processed_rows = $import_session['processed_rows'];
        $successful_imports = $import_session['successful_imports'];
        $errors = $import_session['errors'];

        if (!file_exists($file_path)) {
            wp_send_json_error(esc_html__('Import file not found.', 'houzez-theme-functionality'));
            return;
        }

        $batch_processed = 0;
        $batch_successful = 0;
        $batch_errors = array();

        if (($handle = fopen($file_path, 'r')) !== false) {
            // Get CSV headers
            $csv_headers = fgetcsv($handle, 1000, ',');
            $header_index = array_flip($csv_headers);

            // Skip to the current position
            $current_row = 0;
            while ($current_row < $processed_rows && fgetcsv($handle, 1000, ',') !== false) {
                $current_row++;
            }

            // Process batch
            while (($data = fgetcsv($handle, 1000, ',')) !== false && 
                   $batch_processed < self::$batch_size &&
                   (microtime(true) - $start_time) < self::$max_execution_time) {
                
                $temp_data = array();
                
                // Extract mapped data
                foreach ($field_mapping as $db_field => $csv_header) {
                    if (isset($header_index[$csv_header]) && isset($data[$header_index[$csv_header]])) {
                        $temp_data[$db_field] = sanitize_text_field(trim($data[$header_index[$csv_header]]));
                    }
                }

                $country_name = $temp_data['country'] ?? '';
                $state_name = $temp_data['state'] ?? '';
                $city_name = $temp_data['city'] ?? '';
                $area_name = $temp_data['area'] ?? '';

                // Skip empty rows
                if (empty($country_name) && empty($state_name) && empty($city_name) && empty($area_name)) {
                    $batch_processed++;
                    continue;
                }

                $import_results = self::insert_or_update_locations($area_name, $city_name, $state_name, $country_name);

                if ($import_results['success']) {
                    $batch_successful++;
                } else {
                    $batch_errors = array_merge($batch_errors, $import_results['errors']);
                }

                $batch_processed++;
            }

            fclose($handle);
        }

        // Update session data
        $import_session['processed_rows'] += $batch_processed;
        $import_session['successful_imports'] += $batch_successful;
        $import_session['errors'] = array_merge($errors, $batch_errors);

        $is_complete = $import_session['processed_rows'] >= $import_session['total_rows'];
        
        if ($is_complete) {
            $import_session['status'] = 'completed';
            $import_session['end_time'] = current_time('timestamp');
        }

        update_option('houzez_locations_import_session', $import_session);

        // Prepare response
        $response_data = array(
            'batch_processed' => $batch_processed,
            'batch_successful' => $batch_successful,
            'batch_errors' => count($batch_errors),
            'total_processed' => $import_session['processed_rows'],
            'total_successful' => $import_session['successful_imports'],
            'total_errors' => count($import_session['errors']),
            'total_rows' => $import_session['total_rows'],
            'progress_percentage' => round(($import_session['processed_rows'] / $import_session['total_rows']) * 100, 2),
            'is_complete' => $is_complete,
            'error_messages' => array_slice($batch_errors, 0, 5) // Show only first 5 errors per batch
        );

        if ($is_complete) {
            $duration = $import_session['end_time'] - $import_session['start_time'];
            $response_data['completion_message'] = sprintf(
                esc_html__('Import completed! Processed %d records in %d seconds. %d successful, %d errors.', 'houzez-theme-functionality'),
                $import_session['total_rows'],
                $duration,
                $import_session['successful_imports'],
                count($import_session['errors'])
            );
            
            // Clean up session after completion
            delete_option('houzez_locations_import_session');
        }

        wp_send_json_success($response_data);
    }

    /**
     * Enhanced location insertion with better error handling and duplicate detection
     */
    public static function insert_or_update_locations($area_name, $city_name, $state_name, $country_name) {
        $errors = array();
        $country_slug = $state_slug = $city_slug = '';

        try {
            // Process Country
            if (!empty($country_name)) {
                $country_name = trim($country_name);
                $country_term = term_exists($country_name, 'property_country');
                
                if (!$country_term) {
                    $country_term = wp_insert_term($country_name, 'property_country');
                }
                
                if (is_wp_error($country_term)) {
                    $errors[] = sprintf('Country "%s": %s', $country_name, implode('; ', $country_term->get_error_messages()));
                } else {
                    $country_term_id = is_array($country_term) ? $country_term['term_id'] : $country_term;
                    $country_term_data = get_term($country_term_id, 'property_country');
                    if (!is_wp_error($country_term_data)) {
                        $country_slug = $country_term_data->slug;
                    }
                }
            }

            // Process State
            if (!empty($state_name)) {
                $state_name = trim($state_name);
                $state_term = term_exists($state_name, 'property_state');
                
                if (!$state_term) {
                    $state_term = wp_insert_term($state_name, 'property_state');
                }
                
                if (is_wp_error($state_term)) {
                    $errors[] = sprintf('State "%s": %s', $state_name, implode('; ', $state_term->get_error_messages()));
                } else {
                    $state_term_id = is_array($state_term) ? $state_term['term_id'] : $state_term;
                    $state_term_data = get_term($state_term_id, 'property_state');
                    if (!is_wp_error($state_term_data)) {
                        $state_slug = $state_term_data->slug;
                        // Set parent country relationship
                        if (!empty($country_slug)) {
                            update_option('_houzez_property_state_' . $state_term_id, array('parent_country' => $country_slug));
                        }
                    }
                }
            }

            // Process City
            if (!empty($city_name)) {
                $city_name = trim($city_name);
                $city_term = term_exists($city_name, 'property_city');
                
                if (!$city_term) {
                    $city_term = wp_insert_term($city_name, 'property_city');
                }
                
                if (is_wp_error($city_term)) {
                    $errors[] = sprintf('City "%s": %s', $city_name, implode('; ', $city_term->get_error_messages()));
                } else {
                    $city_term_id = is_array($city_term) ? $city_term['term_id'] : $city_term;
                    $city_term_data = get_term($city_term_id, 'property_city');
                    if (!is_wp_error($city_term_data)) {
                        $city_slug = $city_term_data->slug;
                        // Set parent state relationship
                        if (!empty($state_slug)) {
                            update_option('_houzez_property_city_' . $city_term_id, array('parent_state' => $state_slug));
                        }
                    }
                }
            }

            // Process Area
            if (!empty($area_name)) {
                $area_name = trim($area_name);
                $area_term = term_exists($area_name, 'property_area');
                
                if (!$area_term) {
                    $area_term = wp_insert_term($area_name, 'property_area');
                }
                
                if (is_wp_error($area_term)) {
                    $errors[] = sprintf('Area "%s": %s', $area_name, implode('; ', $area_term->get_error_messages()));
                } else {
                    $area_term_id = is_array($area_term) ? $area_term['term_id'] : $area_term;
                    // Set parent city relationship
                    if (!empty($city_slug)) {
                        update_option('_houzez_property_area_' . $area_term_id, array('parent_city' => $city_slug));
                    }
                }
            }

        } catch (Exception $e) {
            $errors[] = 'Unexpected error: ' . $e->getMessage();
        }

        return array(
            'success' => empty($errors),
            'errors' => $errors
        );
    }

    public static function get_locations_csv_headers() {
        // SECURITY: Check user permissions - only admins can import locations
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        // SECURITY: Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'houzez_import_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $file_url = isset($_POST['file_name']) ? sanitize_url($_POST['file_name']) : '';
        $file_path = self::url_to_path($file_url);

        if ($file_path && file_exists($file_path)) {
            $headers = self::get_csv_headers($file_path);
            if (!empty($headers)) {
                wp_send_json_success($headers);
            } else {
                wp_send_json_error('Unable to read CSV headers. Please check file format.');
            }
        } else {
            wp_send_json_error('File not found or access denied');
        }
    }

    public static function get_csv_headers($file_path) {
        if (($handle = fopen($file_path, 'r')) !== false) {
            if (($data = fgetcsv($handle, 1000, ',')) !== false) {
                fclose($handle);
                return array_map('trim', $data); // Trim whitespace from headers
            }
            fclose($handle);
        }
        return array();
    }

    public static function enqueue_scripts($hook) {
        if (isset($_GET['page']) && $_GET['page'] == 'import_locations') {
            wp_enqueue_media();
            
            // Enqueue CSS for import progress
            wp_enqueue_style('houzez-locations-import-css', HOUZEZ_PLUGIN_URL . 'assets/admin/css/style.css', array(), '1.0.0', 'all');
            
            // Add localized script variables for progress tracking
            wp_localize_script('jquery', 'houzez_import_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('houzez_import_nonce'),
                'batch_size' => self::$batch_size,
                'texts' => array(
                    'importing' => esc_html__('Importing...', 'houzez-theme-functionality'),
                    'processing_batch' => esc_html__('Processing batch', 'houzez-theme-functionality'),
                    'import_complete' => esc_html__('Import Complete!', 'houzez-theme-functionality'),
                    'import_error' => esc_html__('Import Error', 'houzez-theme-functionality'),
                    'records_processed' => esc_html__('records processed', 'houzez-theme-functionality'),
                    'successful' => esc_html__('successful', 'houzez-theme-functionality'),
                    'errors' => esc_html__('errors', 'houzez-theme-functionality')
                )
            ));
        }
    }

    /**
     * Render dashboard
     */
    public static function render() {
        $template = apply_filters('houzez_locations_template_path', HOUZEZ_TEMPLATES . 'locations/form.php');

        if (file_exists($template)) {
            include_once($template);
        }
    }
}