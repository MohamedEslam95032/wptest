<?php
/**
 * User Verification Class
 *
 * Manages the user verification process in Houzez Theme
 * 
 * @package Houzez
 * @since Houzez 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Houzez_User_Verification' ) ) {
    /**
     * Class Houzez_User_Verification
     */
    class Houzez_User_Verification {

        /**
         * Verification statuses
         */
        const STATUS_PENDING = 'pending';
        const STATUS_APPROVED = 'approved';
        const STATUS_REJECTED = 'rejected';
        const STATUS_ADDITIONAL_INFO = 'additional_info_required';
        const STATUS_NONE = '';

        /**
         * Is user approval enabled
         *
         * @var bool
         */
        private $is_enabled;
        /**
         * Secure uploads directory
         */
        private $secure_upload_dir;
        private $secure_upload_url;

        /**
         * Constructor
         */
        public function __construct() {

            // Check if user approval system is enabled
            $this->is_enabled = fave_option('enable_user_verification', 0);
            // Only set up hooks if user verification is enabled
            if ($this->is_enabled) {
                // Setup secure uploads directory
                $this->setup_secure_uploads();
                    
                // Initialize hooks
                $this->init_hooks();
                
                // Fix history entries that don't have document type info
                add_action('init', array($this, 'fix_history_document_types'), 20);
                
                // Migrate existing documents to secure storage
                add_action('init', array($this, 'migrate_documents_to_secure_storage'), 30);
                
                // Update document URLs to remove nonces
                add_action('init', array($this, 'update_document_urls'), 40);
            }
        }

        /**
         * Setup secure uploads directory for verification documents
         */
        private function setup_secure_uploads() {
            $upload_dir = wp_upload_dir();
            
            // Define the secure uploads directory (outside web root if possible)
            $secure_base_dir = WP_CONTENT_DIR . '/secure-uploads';
            $this->secure_upload_dir = $secure_base_dir . '/verification-docs';
            
            // Create the directory if it doesn't exist
            if (!file_exists($this->secure_upload_dir)) {
                wp_mkdir_p($this->secure_upload_dir);
                
                // Create an .htaccess file to deny direct access
                $htaccess_file = $secure_base_dir . '/.htaccess';
                if (!file_exists($htaccess_file)) {
                    $htaccess_content = "# Deny direct access to files\n";
                    $htaccess_content .= "<FilesMatch \".*\">\n";
                    $htaccess_content .= "    Order Allow,Deny\n";
                    $htaccess_content .= "    Deny from all\n";
                    $htaccess_content .= "</FilesMatch>\n";
                    $htaccess_content .= "# Deny directory listing\n";
                    $htaccess_content .= "Options -Indexes\n";
                    
                    file_put_contents($htaccess_file, $htaccess_content);
                }
                
                // Create an index.php file for extra security
                $index_file = $secure_base_dir . '/index.php';
                if (!file_exists($index_file)) {
                    file_put_contents($index_file, "<?php\n// Silence is golden.\n");
                }
            }
            
            // URL for secure file access (via a handler)
            $this->secure_upload_url = site_url('wp-admin/admin-ajax.php?action=houzez_secure_document');
        }
        
        /**
         * Handle secure file uploads
         * 
         * @param array $file The uploaded file data
         * @param string $user_id The user ID
         * @return array|WP_Error The file data or error
         */
        private function handle_secure_file_upload($file, $user_id) {
            // Check if file is valid
            if (!is_uploaded_file($file['tmp_name'])) {
                return new WP_Error('invalid_file', __('Invalid file upload', 'houzez'));
            }
            
            // Generate unique filename
            $filename = sanitize_file_name($file['name']);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = 'user_' . $user_id . '_' . uniqid() . '.' . $extension;
            
            // Create subdirectory for user
            $user_dir = $this->secure_upload_dir . '/user_' . $user_id;
            if (!file_exists($user_dir)) {
                wp_mkdir_p($user_dir);
            }
            
            $file_path = $user_dir . '/' . $new_filename;
            
            // Move the file
            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                return new WP_Error('move_error', __('Error moving uploaded file', 'houzez'));
            }
            
            // Return file data with simplified URL (no nonce)
            return array(
                'file' => $file_path,
                'url' => add_query_arg(array(
                    'user_id' => $user_id,
                    'file' => $new_filename
                ), $this->secure_upload_url),
                'type' => $file['type'],
                'name' => $filename,
                'secure' => true
            );
        }

        /**
         * Initialize hooks
         */
        public function init_hooks() {
            // Frontend actions
            add_action('wp_ajax_houzez_submit_verification', array($this, 'submit_verification_request'));
            add_action('wp_ajax_houzez_submit_additional_info', array($this, 'submit_additional_info'));
            
            // Admin actions
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('wp_ajax_houzez_process_verification', array($this, 'process_verification_request'));
            
            // Display verification badge
            //add_filter('houzez_display_agent_information', array($this, 'display_verification_badge'), 10, 1);
            
            // Enqueue scripts and styles
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            
            // Handle secure document delivery
            add_action('wp_ajax_houzez_secure_document', array($this, 'deliver_secure_document'));
            add_action('wp_ajax_nopriv_houzez_secure_document', array($this, 'deliver_secure_document'));
        }

        /**
         * Enqueue frontend scripts
         */
        public function enqueue_scripts() {
            if (is_page_template('template/user_dashboard_profile.php')) {
                wp_enqueue_script('houzez-verification', get_template_directory_uri() . '/js/verification.js', array('jquery'), '1.0', true);
                wp_localize_script('houzez-verification', 'houzez_verification', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'verify_nonce' => wp_create_nonce('houzez_verification_nonce'),
                    'processing' => __('Processing...', 'houzez'),
                    'submitting' => __('Submitting...', 'houzez'),
                    'submit' => __('Submit Verification Request', 'houzez'),
                    'submit_additional_info' => __('Submit Additional Information', 'houzez'),
                    'document_type_required' => __('Please select a document type', 'houzez'),
                    'document_required' => __('Please upload the front side of your document', 'houzez'),
                    'invalid_file_type' => __('Only JPG, PNG and PDF files are allowed', 'houzez'),
                    'file_too_large' => __('File size should not exceed 10MB', 'houzez'),
                    'error' => __('Something went wrong, please try again', 'houzez'),
                    'back_side_required' => __('Please upload the back side of your document', 'houzez'),
                    'choose_file' => __('No file chosen', 'houzez')
                ));
            }
        }

        /**
         * Enqueue admin scripts
         */
        public function admin_enqueue_scripts($hook) {
            if ('users_page_houzez-verification-requests' === $hook) {
                wp_enqueue_style('houzez-admin-verification', get_template_directory_uri() . '/css/admin/verification.css', array(), '1.0');
                wp_enqueue_script('houzez-admin-verification', get_template_directory_uri() . '/js/admin/verification.js', array('jquery'), '1.0', true);
                wp_localize_script('houzez-admin-verification', 'houzez_admin_verification', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'verify_nonce' => wp_create_nonce('houzez_admin_verification_nonce'),
                    'processing' => __('Processing...', 'houzez'),
                    'success_text' => __('Success!', 'houzez'),
                    'confirm_approve' => __('Are you sure you want to approve verification request for {user}?', 'houzez'),
                    'confirm_reject' => __('Are you sure you want to reject this verification request?', 'houzez'),
                    'confirm_reset' => __('Are you sure you want to reset the verification status for {user}?', 'houzez'),
                    'confirm_revoke' => __('Are you sure you want to revoke verification approval for {user}?', 'houzez'),
                    'confirm_request_info' => __('Please provide details about the additional information needed from {user}', 'houzez'),
                    'confirm_reject_no_reason' => __('You haven\'t provided a reason for rejection. Continue anyway?', 'houzez'),
                    'approve_text' => __('Approve', 'houzez'),
                    'reset_text' => __('Reset Status', 'houzez'),
                    'revoke_text' => __('Revoke Approval', 'houzez'),
                    'request_info_text' => __('Send Request', 'houzez'),
                    'reject_request_for' => __('Reject Verification for {user}', 'houzez'),
                    'request_info_for' => __('Request Information from {user}', 'houzez'),
                    'ajax_error' => __('Something went wrong, please try again.', 'houzez')
                ));
            }
        }

        /**
         * Add admin menu
         */
        public function add_admin_menu() {
            add_submenu_page(
                'users.php',
                __('Verification Requests', 'houzez'),
                __('Verification Requests', 'houzez'),
                'manage_options',
                'houzez-verification-requests',
                array($this, 'admin_page')
            );
        }

        /**
         * Admin page
         */
        public function admin_page() {
            include_once(get_template_directory() . '/framework/admin/verification-requests.php');
        }

        /**
         * Submit verification request
         */
        public function submit_verification_request() {
            // Check nonce
            if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'houzez_verification_nonce')) {
                wp_send_json_error(array('message' => __('Security check failed', 'houzez')));
            }

            // Check if user is logged in
            if (!is_user_logged_in()) {
                wp_send_json_error(array('message' => __('You must be logged in to submit a verification request', 'houzez')));
            }

            $user_id = get_current_user_id();
            
            /**
             * Action before verification request is processed
             * 
             * @param int $user_id The user ID submitting the verification request
             * @param array $_POST The submitted form data
             */
            do_action('houzez_before_verification_request', $user_id, $_POST);
            
            // Check if user already has a pending or approved verification
            $current_status = $this->get_verification_status($user_id);
            if ($current_status === self::STATUS_PENDING) {
                wp_send_json_error(array('message' => __('You already have a pending verification request', 'houzez')));
            } elseif ($current_status === self::STATUS_APPROVED) {
                wp_send_json_error(array('message' => __('Your account is already verified', 'houzez')));
            }

            // Get form data
            $full_name = isset($_POST['full_name']) ? sanitize_text_field($_POST['full_name']) : '';
            $document_type = isset($_POST['document_type']) ? sanitize_text_field($_POST['document_type']) : '';
            
            /**
             * Filter verification request form data
             * 
             * @param array $form_data The sanitized form data
             * @param int $user_id The user ID submitting the request
             * @return array Modified form data
             */
            $form_data = apply_filters('houzez_verification_request_data', array(
                'full_name' => $full_name,
                'document_type' => $document_type
            ), $user_id);
            
            // Update variables with filtered data
            $full_name = $form_data['full_name'];
            $document_type = $form_data['document_type'];
            
            // Validate form data
            if (empty($full_name) || empty($document_type)) {
                wp_send_json_error(array('message' => __('Please fill in all required fields', 'houzez')));
            }

            // Handle front side document upload
            if (empty($_FILES['verification_document'])) {
                wp_send_json_error(array('message' => __('Please upload a document', 'houzez')));
            }

            // Process front side file upload
            $uploaded_file = $_FILES['verification_document'];
            
            // Only allow certain file types
            $allowed_types = array('pdf', 'jpg', 'jpeg', 'png');
            
            /**
             * Filter allowed file types for verification documents
             * 
             * @param array $allowed_types Array of allowed file extensions
             * @return array Modified array of allowed extensions
             */
            $allowed_types = apply_filters('houzez_verification_allowed_file_types', $allowed_types);
            
            $file_extension = strtolower(pathinfo($uploaded_file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_extension, $allowed_types)) {
                wp_send_json_error(array('message' => __('Only PDF, JPG, and PNG files are allowed', 'houzez')));
            }

            // Move the front side file to the secure uploads directory
            $movefile = $this->handle_secure_file_upload($uploaded_file, $user_id);

            if (is_wp_error($movefile)) {
                // Error handling for front side
                wp_send_json_error(array(
                    'message' => __('Error uploading front side: ', 'houzez') . $movefile->get_error_message()
                ));
                return;
            }
            
            // Initialize verification data
            $verification_data = array(
                'full_name' => $full_name,
                'document_type' => $document_type,
                'document_path' => $movefile['file'],
                'document_url' => $movefile['url'],
                'document_type_mime' => $movefile['type'],
                'status' => self::STATUS_PENDING,
                'submitted_on' => current_time('mysql')
            );
            
            // Check if we need back side for this document type
            $requires_back = false;
            
            // Get document types data
            $document_types = $this->get_document_type('');
            
            if (isset($document_types[$document_type]) && $document_types[$document_type]['requires_back']) {
                $requires_back = true;
            }
            
            // If document requires back side, process that upload too
            if ($requires_back) {
                if (empty($_FILES['verification_document_back'])) {
                    wp_send_json_error(array('message' => __('Please upload the back side of your document', 'houzez')));
                    return;
                }
                
                // Process back side file upload
                $uploaded_back_file = $_FILES['verification_document_back'];
                
                // Check file extension
                $back_file_extension = strtolower(pathinfo($uploaded_back_file['name'], PATHINFO_EXTENSION));
                
                if (!in_array($back_file_extension, $allowed_types)) {
                    wp_send_json_error(array('message' => __('Only PDF, JPG, and PNG files are allowed for back side', 'houzez')));
                    return;
                }
                
                // Move the back side file to the secure uploads directory
                $movefile_back = $this->handle_secure_file_upload($uploaded_back_file, $user_id);
                
                if (is_wp_error($movefile_back)) {
                    // Error handling for back side
                    wp_send_json_error(array(
                        'message' => __('Error uploading back side: ', 'houzez') . $movefile_back->get_error_message()
                    ));
                    return;
                }
                
                // Add back side data to verification data
                $verification_data['document_back_path'] = $movefile_back['file'];
                $verification_data['document_back_url'] = $movefile_back['url'];
                $verification_data['document_back_type_mime'] = $movefile_back['type'];
            }
            
            /**
             * Filter verification data before saving
             * 
             * @param array $verification_data The verification data to be saved
             * @param int $user_id The user ID
             * @return array Modified verification data
             */
            $verification_data = apply_filters('houzez_verification_request_save_data', $verification_data, $user_id);
            
            // Save verification data
            update_user_meta($user_id, 'houzez_verification_data', $verification_data);
            update_user_meta($user_id, 'houzez_verification_status', self::STATUS_PENDING);
            
            // Add to verification history
            $this->add_to_verification_history($user_id, self::STATUS_PENDING, 'verification_submitted');
            
            // Update agent/agency post with pending verification status
            $this->update_agent_verification_status($user_id, 0);
            
            // Send email notification to admin
            $this->send_admin_notification($user_id, $verification_data);
            
            // Generate redirect link
            $profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');
            $redirect_link = add_query_arg( 'hpage', 'verification', $profile_link );
            
            /**
             * Action after verification request is successfully processed
             * 
             * @param int $user_id The user ID that submitted the verification request
             * @param array $verification_data The saved verification data
             */
            do_action('houzez_after_verification_request', $user_id, $verification_data);
            
            // Submit verification request success response
            $response = array(
                'message' => __('Your verification request has been submitted successfully. We will review your documents and get back to you soon.', 'houzez'),
                'redirect' => $redirect_link
            );
            
            /**
             * Filter verification request response
             * 
             * @param array $response The response data to be sent back
             * @param int $user_id The user ID
             * @param array $verification_data The saved verification data
             * @return array Modified response data
             */
            $response = apply_filters('houzez_verification_request_response', $response, $user_id, $verification_data);
            
            wp_send_json_success($response);
        }

        /**
         * Process verification request (approve/reject)
         */
        public function process_verification_request() {
            // Check nonce
            if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'houzez_admin_verification_nonce')) {
                wp_send_json_error(array('message' => __('Security check failed', 'houzez')));
            }

            // Check if user has permission
            if (!current_user_can('manage_options')) {
                wp_send_json_error(array('message' => __('You do not have permission to perform this action', 'houzez')));
            }

            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
            $rejection_reason = isset($_POST['rejection_reason']) ? sanitize_text_field($_POST['rejection_reason']) : '';

            if (empty($user_id) || empty($action)) {
                wp_send_json_error(array('message' => __('Invalid request', 'houzez')));
            }

            /**
             * Action before admin processes a verification request
             * 
             * @param int $user_id The user ID being processed
             * @param string $action The action being performed (approve, reject, reset, revoke, request_info)
             * @param array $_POST The submitted form data
             */
            do_action('houzez_before_process_verification', $user_id, $action, $_POST);

            $verification_data = get_user_meta($user_id, 'houzez_verification_data', true);
            if (empty($verification_data)) {
                wp_send_json_error(array('message' => __('Verification request not found', 'houzez')));
            }

            /**
             * Filter verification data before processing
             * 
             * @param array $verification_data The verification data
             * @param int $user_id The user ID being processed
             * @param string $action The action being performed
             * @return array Modified verification data
             */
            $verification_data = apply_filters('houzez_verification_process_data', $verification_data, $user_id, $action);

            // Update verification status
            if ($action === 'approve') {
                /**
                 * Action before approving a verification request
                 * 
                 * @param int $user_id The user ID being approved
                 * @param array $verification_data The verification data
                 */
                do_action('houzez_before_approve_verification', $user_id, $verification_data);
                
                $verification_data['status'] = self::STATUS_APPROVED;
                $verification_data['processed_on'] = current_time('mysql');
                
                update_user_meta($user_id, 'houzez_verification_data', $verification_data);
                update_user_meta($user_id, 'houzez_verification_status', self::STATUS_APPROVED);
                
                // Add to verification history
                $this->add_to_verification_history($user_id, self::STATUS_APPROVED, 'verification_approved');
                
                // Update agent/agency post with verified status
                $this->update_agent_verification_status($user_id, 1);

                // Send approval email
                $this->send_user_notification($user_id, self::STATUS_APPROVED);
                
                /**
                 * Action after approving a verification request
                 * 
                 * @param int $user_id The user ID that was approved
                 * @param array $verification_data The updated verification data
                 */
                do_action('houzez_after_approve_verification', $user_id, $verification_data);
                
                // Process verification request success messages
                $response = array(
                    'message' => __('Verification request approved successfully', 'houzez')
                );
                
                /**
                 * Filter approval response
                 * 
                 * @param array $response The response data
                 * @param int $user_id The user ID
                 * @param array $verification_data The verification data
                 * @return array Modified response data
                 */
                $response = apply_filters('houzez_verification_approve_response', $response, $user_id, $verification_data);
                
                wp_send_json_success($response);
            } elseif ($action === 'reject') {
                /**
                 * Action before rejecting a verification request
                 * 
                 * @param int $user_id The user ID being rejected
                 * @param string $rejection_reason The reason for rejection
                 * @param array $verification_data The verification data
                 */
                do_action('houzez_before_reject_verification', $user_id, $rejection_reason, $verification_data);
                
                /**
                 * Filter rejection reason
                 * 
                 * @param string $rejection_reason The reason for rejection
                 * @param int $user_id The user ID being rejected
                 * @return string Modified rejection reason
                 */
                $rejection_reason = apply_filters('houzez_verification_rejection_reason', $rejection_reason, $user_id);
                
                $verification_data['status'] = self::STATUS_REJECTED;
                $verification_data['processed_on'] = current_time('mysql');
                $verification_data['rejection_reason'] = $rejection_reason;
                
                update_user_meta($user_id, 'houzez_verification_data', $verification_data);
                update_user_meta($user_id, 'houzez_verification_status', self::STATUS_REJECTED);

                // Update agent/agency post with verified status
                $this->update_agent_verification_status($user_id, 0);
                
                // Add to verification history
                if (!empty($rejection_reason)) {
                    $this->add_to_verification_history($user_id, self::STATUS_REJECTED, 'custom_rejection', array($rejection_reason));
                } else {
                    $this->add_to_verification_history($user_id, self::STATUS_REJECTED, 'verification_rejected');
                }
                
                // Send rejection email
                $this->send_user_notification($user_id, self::STATUS_REJECTED, $rejection_reason);
                
                /**
                 * Action after rejecting a verification request
                 * 
                 * @param int $user_id The user ID that was rejected
                 * @param string $rejection_reason The reason for rejection
                 * @param array $verification_data The updated verification data
                 */
                do_action('houzez_after_reject_verification', $user_id, $rejection_reason, $verification_data);
                
                $response = array(
                    'message' => __('Verification request rejected successfully', 'houzez')
                );
                
                /**
                 * Filter rejection response
                 * 
                 * @param array $response The response data
                 * @param int $user_id The user ID
                 * @param array $verification_data The verification data
                 * @return array Modified response data
                 */
                $response = apply_filters('houzez_verification_reject_response', $response, $user_id, $verification_data);
                
                wp_send_json_success($response);
            } elseif ($action === 'reset') {
                /**
                 * Action before resetting a verification status
                 * 
                 * @param int $user_id The user ID being reset
                 * @param array $verification_data The verification data
                 */
                do_action('houzez_before_reset_verification', $user_id, $verification_data);
                
                // Reset verification status to none
                $verification_data['status'] = self::STATUS_NONE;
                $verification_data['processed_on'] = current_time('mysql');
                
                update_user_meta($user_id, 'houzez_verification_data', $verification_data);
                update_user_meta($user_id, 'houzez_verification_status', self::STATUS_NONE);
                
                // Add to verification history
                $this->add_to_verification_history($user_id, self::STATUS_NONE, 'verification_reset');
                
                /**
                 * Action after resetting a verification status
                 * 
                 * @param int $user_id The user ID that was reset
                 * @param array $verification_data The updated verification data
                 */
                do_action('houzez_after_reset_verification', $user_id, $verification_data);
                
                $response = array(
                    'message' => __('Verification status reset successfully', 'houzez')
                );
                
                /**
                 * Filter reset response
                 * 
                 * @param array $response The response data
                 * @param int $user_id The user ID
                 * @param array $verification_data The verification data
                 * @return array Modified response data
                 */
                $response = apply_filters('houzez_verification_reset_response', $response, $user_id, $verification_data);
                
                wp_send_json_success($response);
            } elseif ($action === 'revoke') {
                /**
                 * Action before revoking a verification approval
                 * 
                 * @param int $user_id The user ID being revoked
                 * @param array $verification_data The verification data
                 */
                do_action('houzez_before_revoke_verification', $user_id, $verification_data);
                
                // Revoke approval and set to rejected
                $verification_data['status'] = self::STATUS_REJECTED;
                $verification_data['processed_on'] = current_time('mysql');
                $verification_data['rejection_reason'] = __('Verification approval revoked by admin', 'houzez');
                
                update_user_meta($user_id, 'houzez_verification_data', $verification_data);
                update_user_meta($user_id, 'houzez_verification_status', self::STATUS_REJECTED);

                // Update agent/agency post with verified status
                $this->update_agent_verification_status($user_id, 0);
                
                // Add to verification history
                $this->add_to_verification_history($user_id, self::STATUS_REJECTED, 'verification_revoked');
                
                // Send notification to user
                $this->send_user_notification($user_id, self::STATUS_REJECTED, __('Your verification approval has been revoked.', 'houzez'));
                
                /**
                 * Action after revoking a verification approval
                 * 
                 * @param int $user_id The user ID that was revoked
                 * @param array $verification_data The updated verification data
                 */
                do_action('houzez_after_revoke_verification', $user_id, $verification_data);
                
                $response = array(
                    'message' => __('Verification approval revoked successfully', 'houzez')
                );
                
                /**
                 * Filter revoke response
                 * 
                 * @param array $response The response data
                 * @param int $user_id The user ID
                 * @param array $verification_data The verification data
                 * @return array Modified response data
                 */
                $response = apply_filters('houzez_verification_revoke_response', $response, $user_id, $verification_data);
                
                wp_send_json_success($response);
            } elseif ($action === 'request_info') {
                // Change status to additional info required
                $additional_info = isset($_POST['additional_info']) ? sanitize_textarea_field($_POST['additional_info']) : '';
                
                /**
                 * Action before requesting additional information
                 * 
                 * @param int $user_id The user ID
                 * @param string $additional_info The additional information requested
                 * @param array $verification_data The verification data
                 */
                do_action('houzez_before_request_info', $user_id, $additional_info, $verification_data);
                
                /**
                 * Filter additional information request
                 * 
                 * @param string $additional_info The additional information requested
                 * @param int $user_id The user ID
                 * @return string Modified additional information request
                 */
                $additional_info = apply_filters('houzez_verification_additional_info_request', $additional_info, $user_id);
                
                $verification_data['status'] = self::STATUS_ADDITIONAL_INFO;
                $verification_data['processed_on'] = current_time('mysql');
                $verification_data['additional_info_request'] = $additional_info;
                
                update_user_meta($user_id, 'houzez_verification_data', $verification_data);
                update_user_meta($user_id, 'houzez_verification_status', self::STATUS_ADDITIONAL_INFO);

                // Update agent/agency post with additional info required status
                $this->update_agent_verification_status($user_id, 0);
                
                // Add to verification history
                if (!empty($additional_info)) {
                    $this->add_to_verification_history($user_id, self::STATUS_ADDITIONAL_INFO, 'custom_additional_info', array($additional_info));
                } else {
                    $this->add_to_verification_history($user_id, self::STATUS_ADDITIONAL_INFO, 'additional_info_requested');
                }
                
                // Send notification to user
                $this->send_user_notification($user_id, self::STATUS_ADDITIONAL_INFO, $additional_info);
                
                /**
                 * Action after requesting additional information
                 * 
                 * @param int $user_id The user ID
                 * @param string $additional_info The additional information requested
                 * @param array $verification_data The updated verification data
                 */
                do_action('houzez_after_request_info', $user_id, $additional_info, $verification_data);
                
                $response = array(
                    'message' => __('Additional information request sent successfully', 'houzez')
                );
                
                /**
                 * Filter additional information request response
                 * 
                 * @param array $response The response data
                 * @param int $user_id The user ID
                 * @param array $verification_data The verification data
                 * @return array Modified response data
                 */
                $response = apply_filters('houzez_verification_request_info_response', $response, $user_id, $verification_data);
                
                wp_send_json_success($response);
            } else {
                // Error message
                wp_send_json_error(array('message' => __('Invalid action', 'houzez')));
            }
        }

        /**
         * Send admin notification
         */
        private function send_admin_notification($user_id, $verification_data) {
            $user = get_userdata($user_id);
            if (!$user) {
                return;
            }

            $admin_email = get_option('admin_email');
            
            /**
             * Filter the admin email address for verification notifications
             * 
             * @param string $admin_email The admin email address
             * @param int $user_id The user ID who submitted the verification
             * @param array $verification_data The verification data
             * @return string Modified admin email address
             */
            $admin_email = apply_filters('houzez_verification_admin_email', $admin_email, $user_id, $verification_data);
            
            $subject = sprintf(__('[%s] New Verification Request', 'houzez'), get_bloginfo('name'));
            
            /**
             * Filter the admin notification subject
             * 
             * @param string $subject The email subject
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @return string Modified email subject
             */
            $subject = apply_filters('houzez_verification_admin_subject', $subject, $user_id, $verification_data);
            
            // Get human-readable document type label
            $document_type = $verification_data['document_type'] ?? '';
            $document_type_label = $this->get_document_type_label($document_type);
            
            $message = sprintf(__('A new verification request has been submitted by %s (%s).', 'houzez'), $user->display_name, $user->user_email) . "\n\n";
            $message .= __('Details:', 'houzez') . "\n";
            $message .= sprintf(__('Full Name: %s', 'houzez'), $verification_data['full_name']) . "\n";
            $message .= sprintf(__('Document Type: %s', 'houzez'), $document_type_label) . "\n";
            $message .= sprintf(__('Submitted On: %s', 'houzez'), $verification_data['submitted_on']) . "\n\n";
            $message .= sprintf(__('Please review this request at: %s', 'houzez'), admin_url('users.php?page=houzez-verification-requests'));
            
            /**
             * Filter the admin notification message
             * 
             * @param string $message The email message
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @param WP_User $user The user object
             * @return string Modified email message
             */
            $message = apply_filters('houzez_verification_admin_message', $message, $user_id, $verification_data, $user);
            
            $headers = array('Content-Type: text/plain; charset=UTF-8');
            
            /**
             * Filter the admin notification headers
             * 
             * @param array $headers The email headers
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @return array Modified email headers
             */
            $headers = apply_filters('houzez_verification_admin_headers', $headers, $user_id, $verification_data);
            
            /**
             * Action before sending admin notification
             * 
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @param string $subject The email subject
             * @param string $message The email message
             * @param array $headers The email headers
             */
            do_action('houzez_before_admin_notification', $user_id, $verification_data, $subject, $message, $headers);
            
            $mail_sent = wp_mail($admin_email, $subject, $message, $headers);
            
            /**
             * Action after sending admin notification
             * 
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @param bool $mail_sent Whether the email was sent successfully
             */
            do_action('houzez_after_admin_notification', $user_id, $verification_data, $mail_sent);
        }

        /**
         * Send user notification
         */
        private function send_user_notification($user_id, $status, $additional_message = '') {
            $user = get_userdata($user_id);
            if (!$user) {
                return;
            }

            $site_name = get_bloginfo('name');
            $headers = array('Content-Type: text/plain; charset=UTF-8');
            
            /**
             * Filter the user notification headers
             * 
             * @param array $headers The email headers
             * @param int $user_id The user ID
             * @param string $status The verification status
             * @return array Modified email headers
             */
            $headers = apply_filters('houzez_verification_user_headers', $headers, $user_id, $status);
            
            if ($status === self::STATUS_APPROVED) {
                $subject = sprintf(__('[%s] Your verification has been APPROVED', 'houzez'), $site_name);
                $message = sprintf(__('Congratulations! Your %s account is now Verified. A badge will appear on your profile.', 'houzez'), $site_name) . "\n\n";
                $message .= __('Thank you for using our platform!', 'houzez');
            } elseif ($status === self::STATUS_ADDITIONAL_INFO) {
                $subject = sprintf(__('[%s] Additional Information Required for Verification', 'houzez'), $site_name);
                $message = sprintf(__('We\'ve reviewed your verification request for %s and need some additional information.', 'houzez'), $site_name) . "\n\n";
                
                if (!empty($additional_message)) {
                    $message .= sprintf(__('Information Needed: %s', 'houzez'), $additional_message) . "\n\n";
                }
                
                $message .= __('Please log in to your account and visit the Verification section to provide the requested information.', 'houzez') . "\n\n";
                $message .= __('Thank you for your cooperation.', 'houzez');
            } else {
                $subject = sprintf(__('[%s] Your verification has been REJECTED', 'houzez'), $site_name);
                $message = sprintf(__('We\'re sorry, your verification was declined.', 'houzez'), $site_name) . "\n\n";
                
                if (!empty($additional_message)) {
                    $message .= sprintf(__('Reason: %s', 'houzez'), $additional_message) . "\n\n";
                }
                
                $message .= __('You may re-apply once you have addressed the issues mentioned above.', 'houzez');
            }
            
            /**
             * Filter the user notification subject
             * 
             * @param string $subject The email subject
             * @param int $user_id The user ID
             * @param string $status The verification status
             * @param string $additional_message Additional message (e.g., rejection reason)
             * @return string Modified email subject
             */
            $subject = apply_filters('houzez_verification_user_subject', $subject, $user_id, $status, $additional_message);
            
            /**
             * Filter the user notification message
             * 
             * @param string $message The email message
             * @param int $user_id The user ID
             * @param string $status The verification status
             * @param string $additional_message Additional message (e.g., rejection reason)
             * @param WP_User $user The user object
             * @return string Modified email message
             */
            $message = apply_filters('houzez_verification_user_message', $message, $user_id, $status, $additional_message, $user);
            
            /**
             * Action before sending user notification
             * 
             * @param int $user_id The user ID
             * @param string $status The verification status
             * @param string $subject The email subject
             * @param string $message The email message
             * @param array $headers The email headers
             * @param string $additional_message Additional message (e.g., rejection reason)
             */
            do_action('houzez_before_user_notification', $user_id, $status, $subject, $message, $headers, $additional_message);
            
            $mail_sent = wp_mail($user->user_email, $subject, $message, $headers);
            
            /**
             * Action after sending user notification
             * 
             * @param int $user_id The user ID
             * @param string $status The verification status
             * @param bool $mail_sent Whether the email was sent successfully
             * @param string $additional_message Additional message (e.g., rejection reason)
             */
            do_action('houzez_after_user_notification', $user_id, $status, $mail_sent, $additional_message);
        }

        /**
         * Get document types with their properties
         * 
         * @param string $document_type Optional document type to get specific properties
         * @return array Document types array or specific document type properties
         */
        public function get_document_type($document_type = '') {
            $document_types = array(
                'id_card' => array(
                    'label' => esc_html__('ID Card', 'houzez'),
                    'requires_back' => true
                ),
                'passport' => array(
                    'label' => esc_html__('Passport', 'houzez'),
                    'requires_back' => false
                ),
                'drivers_license' => array(
                    'label' => esc_html__('Driver\'s License', 'houzez'),
                    'requires_back' => true
                ),
                'business_license' => array(
                    'label' => esc_html__('Business License', 'houzez'),
                    'requires_back' => false
                ),
                'other' => array(
                    'label' => esc_html__('Other Document', 'houzez'),
                    'requires_back' => false
                )
            );
            
            /**
             * Filter the document types array
             * 
             * @param array $document_types Array of document types and their properties
             * @param string $document_type The requested document type (if specified)
             * @return array Modified document types array
             */
            $document_types = apply_filters('houzez_verification_document_types', $document_types, $document_type);

            if (!empty($document_type) && isset($document_types[$document_type])) {
                /**
                 * Filter a specific document type's properties
                 * 
                 * @param array $properties The document type properties
                 * @param string $document_type The requested document type
                 * @return array Modified document type properties
                 */
                return apply_filters('houzez_verification_document_type_' . $document_type, $document_types[$document_type], $document_type);
            }

            return $document_types;
        }

        /**
         * Get document type label
         * 
         * @param string $document_type The document type key
         * @return string The human-readable label
         */
        public function get_document_type_label($document_type) {
            if (empty($document_type)) {
                return $document_type;
            }
            
            $document_types = $this->get_document_type($document_type);
            if (isset($document_types['label'])) {
                return $document_types['label'];
            }
            
            return $document_type;
        }

        /**
         * Get verification status for a user
         */
        public function get_verification_status($user_id) {
            return get_user_meta($user_id, 'houzez_verification_status', true);
        }

        /**
         * Get verification data for a user
         */
        public function get_verification_data($user_id) {
            return get_user_meta($user_id, 'houzez_verification_data', true);
        }

        /**
         * Check if user is verified
         */
        public function is_user_verified($user_id) {
            return $this->get_verification_status($user_id) === self::STATUS_APPROVED;
        }

        /**
         * Display verification badge
         */
        public function display_verification_badge($agent_info) {
            $user_id = $agent_info['agent_id'];
            
            if ($this->is_user_verified($user_id)) {
                $badge_html = '<span class="houzez-verified-badge" title="' . esc_attr__('Verified User', 'houzez') . '">';
                $badge_html .= '<i class="houzez-icon icon-check-circle-1 text-success"></i>';
                $badge_html .= '</span>';
                
                $agent_info['agent_name'] = $agent_info['agent_name'] . ' ' . $badge_html;
            }
            
            return $agent_info;
        }

        /**
         * Get verification requests
         */
        public function get_verification_requests($status = '') {
            global $wpdb;
            
            $meta_key = 'houzez_verification_status';
            $query = "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s";
            $params = array($meta_key);
            
            if (!empty($status) && $status !== 'with_additional_info') {
                $query .= " AND meta_value = %s";
                $params[] = $status;
            }
            
            $user_ids = $wpdb->get_col($wpdb->prepare($query, $params));
            
            $requests = array();
            foreach ($user_ids as $user_id) {
                $user = get_userdata($user_id);
                if ($user) {
                    $verification_data = $this->get_verification_data($user_id);
                    if (!empty($verification_data)) {
                        // Filter for users with additional information
                        if ($status === 'with_additional_info') {
                            if (isset($verification_data['additional_document_url']) && !empty($verification_data['additional_document_url'])) {
                                $requests[] = array(
                                    'user_id' => $user_id,
                                    'user_data' => $user,
                                    'verification_data' => $verification_data
                                );
                            }
                        } else {
                            $requests[] = array(
                                'user_id' => $user_id,
                                'user_data' => $user,
                                'verification_data' => $verification_data
                            );
                        }
                    }
                }
            }
            
            return $requests;
        }

        /**
         * Get history note text from token
         * 
         * @param string $token The token representing the history note type
         * @return string The translated history note text
         */
        public function get_history_note_text($token) {
            $notes = array(
                'verification_submitted' => __('Verification request submitted', 'houzez'),
                'verification_approved' => __('Verification request approved', 'houzez'),
                'verification_rejected' => __('Verification request rejected', 'houzez'),
                'verification_reset' => __('Verification status reset by admin', 'houzez'),
                'verification_revoked' => __('Verification approval revoked by admin', 'houzez'),
                'additional_info_requested' => __('Additional information requested', 'houzez'),
                'additional_info_submitted' => __('Additional information submitted', 'houzez'),
                'custom_rejection' => __('Verification request rejected. Reason: %s', 'houzez'),
                'custom_additional_info' => __('Additional information requested: %s', 'houzez'),
            );
            
            /**
             * Filter the verification history note texts
             *
             * @param array $notes Array of note tokens and their corresponding texts
             * @param string $token The current token being processed
             * @return array Modified array of notes
             */
            $notes = apply_filters('houzez_verification_history_notes', $notes, $token);
            
            if (isset($notes[$token])) {
                return $notes[$token];
            }
            
            // If token not found, return the token itself as fallback
            return $token;
        }

        /**
         * Add entry to verification history
         * 
         * @param int $user_id The user ID
         * @param string $status The verification status
         * @param string $note_token The token for the note or custom note text
         * @param array $args Additional arguments for dynamic text
         */
        private function add_to_verification_history($user_id, $status, $note_token = '', $args = array()) {
            /**
             * Action before adding an entry to verification history
             * 
             * @param int $user_id The user ID
             * @param string $status The verification status
             * @param string $note_token The note token
             * @param array $args Additional arguments for dynamic text
             */
            do_action('houzez_before_add_verification_history', $user_id, $status, $note_token, $args);
            
            $history = get_user_meta($user_id, 'houzez_verification_history', true);
            
            if (!is_array($history)) {
                $history = array();
            }
            
            // Get user data for more context
            $user = get_userdata($user_id);
            $admin_user = wp_get_current_user();
            
            // Add contextual information about who performed the action
            $context = '';
            if ($status === 'pending') {
                $context = sprintf(
                    __('Submitted by %s', 'houzez'),
                    $user->display_name
                );
            } else {
                // For approved/rejected states, include admin info if available
                if (current_user_can('manage_options') && $admin_user->ID !== $user_id) {
                    $context = sprintf(
                        __('Processed by %s', 'houzez'),
                        $admin_user->display_name
                    );
                }
            }
            
            // Get verification data to include document type
            $verification_data = $this->get_verification_data($user_id);
            $document_type = '';
            
            if (!empty($verification_data)) {
                // If this is an additional info submission, use that document type
                if ($status === 'pending' && isset($verification_data['additional_document_type']) && 
                    !empty($verification_data['additional_document_type'])) {
                    $document_type = $verification_data['additional_document_type'];
                } else {
                    // Otherwise use the original document type
                    $document_type = isset($verification_data['document_type']) ? $verification_data['document_type'] : '';
                }
            }
            
            // Create history entry with note token instead of full text
            $entry = array(
                'status' => $status,
                'date' => current_time('mysql'),
                'note_token' => $note_token,
                'args' => $args,
                'context' => $context,
                'document_type' => $document_type
            );
            
            /**
             * Filter the verification history entry before adding it to the history array
             * 
             * @param array $entry The history entry data
             * @param int $user_id The user ID
             * @param string $status The verification status
             * @param array $verification_data The user's verification data
             * @return array Modified history entry
             */
            $entry = apply_filters('houzez_verification_history_entry', $entry, $user_id, $status, $verification_data);
            
            // Add the entry to history array
            $history[] = $entry;
            
            /**
             * Filter the complete history array before saving to user meta
             * 
             * @param array $history The complete history array including the new entry
             * @param int $user_id The user ID
             * @param array $entry The newly added entry
             * @return array Modified history array
             */
            $history = apply_filters('houzez_verification_history_array', $history, $user_id, $entry);
            
            update_user_meta($user_id, 'houzez_verification_history', $history);
            
            /**
             * Action after adding an entry to verification history
             * 
             * @param int $user_id The user ID
             * @param string $status The verification status
             * @param array $entry The history entry that was added
             * @param array $history The complete history array
             */
            do_action('houzez_after_add_verification_history', $user_id, $status, $entry, $history);
        }
        
        /**
         * Get verification history for a user
         * 
         * @param int $user_id User ID
         * @return array Array of history entries with readable notes
         */
        public function get_verification_history($user_id) {
            $history = get_user_meta($user_id, 'houzez_verification_history', true);
            
            if (!is_array($history)) {
                return array();
            }
            
            // Process each history entry to add readable note
            foreach ($history as &$entry) {
                // For backward compatibility
                if (!isset($entry['note_token']) && isset($entry['notes'])) {
                    $entry['readable_note'] = $entry['notes'];
                } else {
                    $note_token = isset($entry['note_token']) ? $entry['note_token'] : '';
                    $args = isset($entry['args']) ? $entry['args'] : array();
                    
                    if (!empty($note_token)) {
                        $note_text = $this->get_history_note_text($note_token);
                        
                        // Handle dynamic text
                        if (!empty($args) && strpos($note_text, '%s') !== false) {
                            $entry['readable_note'] = vsprintf($note_text, $args);
                        } else {
                            $entry['readable_note'] = $note_text;
                        }
                    } else {
                        $entry['readable_note'] = '';
                    }
                }
            }
            
            return $history;
        }

        /**
         * Submit additional information
         */
        public function submit_additional_info() {
            // Check nonce
            if (!isset($_POST['additional_info_security']) || !wp_verify_nonce($_POST['additional_info_security'], 'houzez_additional_info_nonce')) {
                wp_send_json_error(array('message' => __('Security check failed', 'houzez')));
            }

            // Check if user is logged in
            if (!is_user_logged_in()) {
                wp_send_json_error(array('message' => __('You must be logged in to submit additional information', 'houzez')));
            }

            $user_id = get_current_user_id();
            
            /**
             * Action before additional information is processed
             * 
             * @param int $user_id The user ID submitting additional information
             * @param array $_POST The submitted form data
             */
            do_action('houzez_before_additional_info_submission', $user_id, $_POST);
            
            // Check if user has a verification in additional_info_required status
            $current_status = $this->get_verification_status($user_id);
            if ($current_status !== self::STATUS_ADDITIONAL_INFO) {
                wp_send_json_error(array('message' => __('You do not have an active request for additional information', 'houzez')));
            }

            // Get existing verification data
            $verification_data = $this->get_verification_data($user_id);
            if (empty($verification_data)) {
                wp_send_json_error(array('message' => __('Verification data not found', 'houzez')));
            }

            // Get form data
            $document_type = isset($_POST['document_type']) ? sanitize_text_field($_POST['document_type']) : '';
            $additional_notes = isset($_POST['additional_notes']) ? sanitize_textarea_field($_POST['additional_notes']) : '';
            
            /**
             * Filter additional information form data
             * 
             * @param array $form_data The sanitized form data
             * @param int $user_id The user ID submitting the request
             * @param array $verification_data The existing verification data
             * @return array Modified form data
             */
            $form_data = apply_filters('houzez_additional_info_form_data', array(
                'document_type' => $document_type,
                'additional_notes' => $additional_notes
            ), $user_id, $verification_data);
            
            // Update variables with filtered data
            $document_type = $form_data['document_type'];
            $additional_notes = $form_data['additional_notes'];
            
            // Validate form data
            if (empty($document_type)) {
                wp_send_json_error(array('message' => __('Please select a document type', 'houzez')));
            }

            // Handle front side document upload
            if (empty($_FILES['additional_document'])) {
                wp_send_json_error(array('message' => __('Please upload a document', 'houzez')));
            }

            // Process front side file upload
            $uploaded_file = $_FILES['additional_document'];
            
            // Only allow certain file types
            $allowed_types = array('pdf', 'jpg', 'jpeg', 'png');
            
            /**
             * Filter allowed file types for additional information documents
             * 
             * @param array $allowed_types Array of allowed file extensions
             * @return array Modified array of allowed extensions
             */
            $allowed_types = apply_filters('houzez_additional_info_allowed_file_types', $allowed_types);
            
            $file_extension = strtolower(pathinfo($uploaded_file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_extension, $allowed_types)) {
                wp_send_json_error(array('message' => __('Only PDF, JPG, and PNG files are allowed', 'houzez')));
            }

            // Move the front side file to the secure uploads directory
            $movefile = $this->handle_secure_file_upload($uploaded_file, $user_id);

            if (is_wp_error($movefile)) {
                // Error handling for front side
                wp_send_json_error(array(
                    'message' => __('Error uploading front side: ', 'houzez') . $movefile->get_error_message()
                ));
                return;
            }
            
            // Update verification data with front side document
            $verification_data['additional_document_type'] = $document_type;
            $verification_data['additional_document_path'] = $movefile['file'];
            $verification_data['additional_document_url'] = $movefile['url'];
            $verification_data['additional_document_type_mime'] = $movefile['type'];
            $verification_data['additional_notes'] = $additional_notes;
            
            // Check if we need back side for this document type
            $requires_back = false;
            
            // Get document types data
            $document_types = $this->get_document_type('');
            
            if (isset($document_types[$document_type]) && $document_types[$document_type]['requires_back']) {
                $requires_back = true;
            }
            
            // If document requires back side, process that upload too
            if ($requires_back) {
                if (empty($_FILES['additional_document_back'])) {
                    wp_send_json_error(array('message' => __('Please upload the back side of your document', 'houzez')));
                    return;
                }
                
                // Process back side file upload
                $uploaded_back_file = $_FILES['additional_document_back'];
                
                // Check file extension
                $back_file_extension = strtolower(pathinfo($uploaded_back_file['name'], PATHINFO_EXTENSION));
                
                if (!in_array($back_file_extension, $allowed_types)) {
                    wp_send_json_error(array('message' => __('Only PDF, JPG, and PNG files are allowed for back side', 'houzez')));
                    return;
                }
                
                // Move the back side file to the secure uploads directory
                $movefile_back = $this->handle_secure_file_upload($uploaded_back_file, $user_id);
                
                if (is_wp_error($movefile_back)) {
                    // Error handling for back side
                    wp_send_json_error(array(
                        'message' => __('Error uploading back side: ', 'houzez') . $movefile_back->get_error_message()
                    ));
                    return;
                }
                
                // Add back side data to verification data
                $verification_data['additional_document_back_path'] = $movefile_back['file'];
                $verification_data['additional_document_back_url'] = $movefile_back['url'];
                $verification_data['additional_document_back_type_mime'] = $movefile_back['type'];
            }
            
            /**
             * Filter additional information data before saving
             * 
             * @param array $verification_data The updated verification data to be saved
             * @param int $user_id The user ID
             * @return array Modified verification data
             */
            $verification_data = apply_filters('houzez_additional_info_save_data', $verification_data, $user_id);
            
            // Change status back to pending
            $verification_data['status'] = self::STATUS_PENDING;
            $verification_data['additional_info_submitted_on'] = current_time('mysql');
            
            update_user_meta($user_id, 'houzez_verification_data', $verification_data);
            update_user_meta($user_id, 'houzez_verification_status', self::STATUS_PENDING);
            
            // Add to verification history
            $this->add_to_verification_history(
                $user_id, 
                self::STATUS_PENDING, 
                'additional_info_submitted'
            );
            
            // Update agent/agency post with pending verification status
            $this->update_agent_verification_status($user_id, 0);
            
            // Send email notification to admin
            $this->send_admin_additional_info_notification($user_id, $verification_data);
            
            /**
             * Action after additional information is successfully processed
             * 
             * @param int $user_id The user ID that submitted the additional information
             * @param array $verification_data The updated verification data
             */
            do_action('houzez_after_additional_info_submission', $user_id, $verification_data);
            
            // Generate redirect link
            $profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');
            $redirect_link = add_query_arg( 'hpage', 'verification', $profile_link );
            
            // Submit additional info success
            $response = array(
                'message' => __('Your additional information has been submitted successfully. We will review your documents and get back to you soon.', 'houzez'),
                'redirect' => $redirect_link
            );
            
            /**
             * Filter additional information submission response
             * 
             * @param array $response The response data to be sent back
             * @param int $user_id The user ID
             * @param array $verification_data The updated verification data
             * @return array Modified response data
             */
            $response = apply_filters('houzez_additional_info_response', $response, $user_id, $verification_data);
            
            wp_send_json_success($response);
        }

        /**
         * Send admin notification for additional info
         */
        private function send_admin_additional_info_notification($user_id, $verification_data) {
            $user = get_userdata($user_id);
            if (!$user) {
                return;
            }

            $admin_email = get_option('admin_email');
            
            /**
             * Filter the admin email address for additional info notifications
             * 
             * @param string $admin_email The admin email address
             * @param int $user_id The user ID who submitted the additional info
             * @param array $verification_data The verification data
             * @return string Modified admin email address
             */
            $admin_email = apply_filters('houzez_verification_additional_info_admin_email', $admin_email, $user_id, $verification_data);
            
            $subject = sprintf(__('[%s] Additional Information Submitted for Verification', 'houzez'), get_bloginfo('name'));
            
            /**
             * Filter the admin notification subject for additional info
             * 
             * @param string $subject The email subject
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @return string Modified email subject
             */
            $subject = apply_filters('houzez_verification_additional_info_admin_subject', $subject, $user_id, $verification_data);
            
            // Get human-readable document type label for additional document
            $additional_document_type = $verification_data['additional_document_type'] ?? '';
            $additional_document_type_label = $this->get_document_type_label($additional_document_type);
            
            $message = sprintf(__('Additional information has been submitted by %s (%s) for their verification request.', 'houzez'), $user->display_name, $user->user_email) . "\n\n";
            $message .= __('Details:', 'houzez') . "\n";
            $message .= sprintf(__('Document Type: %s', 'houzez'), $additional_document_type_label) . "\n";
            
            if (!empty($verification_data['additional_notes'])) {
                $message .= __('Additional Notes:', 'houzez') . "\n";
                $message .= $verification_data['additional_notes'] . "\n\n";
            }
            
            $message .= sprintf(__('Submitted On: %s', 'houzez'), $verification_data['additional_info_submitted_on']) . "\n\n";
            $message .= sprintf(__('Please review this updated request at: %s', 'houzez'), admin_url('users.php?page=houzez-verification-requests'));
            
            /**
             * Filter the admin notification message for additional info
             * 
             * @param string $message The email message
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @param WP_User $user The user object
             * @return string Modified email message
             */
            $message = apply_filters('houzez_verification_additional_info_admin_message', $message, $user_id, $verification_data, $user);
            
            $headers = array('Content-Type: text/plain; charset=UTF-8');
            
            /**
             * Filter the admin notification headers for additional info
             * 
             * @param array $headers The email headers
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @return array Modified email headers
             */
            $headers = apply_filters('houzez_verification_additional_info_admin_headers', $headers, $user_id, $verification_data);
            
            /**
             * Action before sending admin notification for additional info
             * 
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @param string $subject The email subject
             * @param string $message The email message
             * @param array $headers The email headers
             */
            do_action('houzez_before_additional_info_admin_notification', $user_id, $verification_data, $subject, $message, $headers);
            
            $mail_sent = wp_mail($admin_email, $subject, $message, $headers);
            
            /**
             * Action after sending admin notification for additional info
             * 
             * @param int $user_id The user ID
             * @param array $verification_data The verification data
             * @param bool $mail_sent Whether the email was sent successfully
             */
            do_action('houzez_after_additional_info_admin_notification', $user_id, $verification_data, $mail_sent);
        }

        /**
         * Fix existing history entries that don't have document type information
         */
        public function fix_history_document_types() {
            // Only run this for admin users to avoid performance issues on frontend
            if (!is_admin() || !current_user_can('manage_options')) {
                return;
            }
            
            // Check if we already ran this fix
            $fix_run = get_option('houzez_verification_history_fix', false);
            if ($fix_run) {
                return;
            }
            
            global $wpdb;
            
            // Get all users with verification history
            $meta_key = 'houzez_verification_history';
            $users_with_history = $wpdb->get_col($wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s",
                $meta_key
            ));
            
            if (empty($users_with_history)) {
                update_option('houzez_verification_history_fix', true);
                return;
            }
            
            foreach ($users_with_history as $user_id) {
                $history = get_user_meta($user_id, $meta_key, true);
                $verification_data = $this->get_verification_data($user_id);
                
                if (!is_array($history) || empty($verification_data)) {
                    continue;
                }
                
                $updated = false;
                foreach ($history as $key => $entry) {
                    // Only update entries that don't have document_type
                    if (!isset($entry['document_type']) || empty($entry['document_type'])) {
                        $document_type = isset($verification_data['document_type']) ? $verification_data['document_type'] : '';
                        
                        if ($entry['status'] === 'pending' && isset($verification_data['additional_document_type']) && 
                            !empty($verification_data['additional_document_type'])) {
                            // Check if this was the additional info submission
                            // This is approximate since we don't have exact timestamp matches
                            $document_type = $verification_data['additional_document_type'];
                        }
                        
                        $history[$key]['document_type'] = $document_type;
                        $updated = true;
                    }
                }
                
                if ($updated) {
                    update_user_meta($user_id, $meta_key, $history);
                }
            }
            
            // Mark that we've run this fix
            update_option('houzez_verification_history_fix', true);
        }

        /**
         * Migrate existing documents to secure storage
         */
        public function migrate_documents_to_secure_storage() {
            // Only run this for admin users to avoid performance issues on frontend
            if (!is_admin() || !current_user_can('manage_options')) {
                return;
            }
            
            // Check if we already ran this migration
            $migration_run = get_option('houzez_secure_docs_migration_v1', false);
            if ($migration_run) {
                return;
            }
            
            global $wpdb;
            
            // Get all users with verification data
            $meta_key = 'houzez_verification_data';
            $users_with_verification = $wpdb->get_col($wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s",
                $meta_key
            ));
            
            if (empty($users_with_verification)) {
                update_option('houzez_secure_docs_migration_v1', true);
                return;
            }
            
            foreach ($users_with_verification as $user_id) {
                $verification_data = $this->get_verification_data($user_id);
                
                if (empty($verification_data)) {
                    continue;
                }
                
                $updated = false;
                
                // Migrate main document if it exists as an attachment
                if (isset($verification_data['document_id']) && !empty($verification_data['document_id']) && 
                    (!isset($verification_data['document_path']) || empty($verification_data['document_path']))) {
                    
                    $attachment_id = $verification_data['document_id'];
                    $attachment_path = get_attached_file($attachment_id);
                    
                    if ($attachment_path && file_exists($attachment_path)) {
                        // Copy to secure storage
                        $filename = basename($attachment_path);
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        $new_filename = 'user_' . $user_id . '_' . uniqid() . '.' . $extension;
                        
                        // Create user directory
                        $user_dir = $this->secure_upload_dir . '/user_' . $user_id;
                        if (!file_exists($user_dir)) {
                            wp_mkdir_p($user_dir);
                        }
                        
                        $new_path = $user_dir . '/' . $new_filename;
                        
                        if (copy($attachment_path, $new_path)) {
                            // Update verification data
                            $verification_data['document_path'] = $new_path;
                            $verification_data['document_url'] = add_query_arg(array(
                                'user_id' => $user_id,
                                'file' => $new_filename
                            ), $this->secure_upload_url);
                            $verification_data['document_type_mime'] = get_post_mime_type($attachment_id);
                            
                            $updated = true;
                        }
                    }
                }
                
                // Migrate additional document if it exists
                if (isset($verification_data['additional_document_id']) && !empty($verification_data['additional_document_id']) && 
                    (!isset($verification_data['additional_document_path']) || empty($verification_data['additional_document_path']))) {
                    
                    $attachment_id = $verification_data['additional_document_id'];
                    $attachment_path = get_attached_file($attachment_id);
                    
                    if ($attachment_path && file_exists($attachment_path)) {
                        // Copy to secure storage
                        $filename = basename($attachment_path);
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        $new_filename = 'user_' . $user_id . '_additional_' . uniqid() . '.' . $extension;
                        
                        // Create user directory
                        $user_dir = $this->secure_upload_dir . '/user_' . $user_id;
                        if (!file_exists($user_dir)) {
                            wp_mkdir_p($user_dir);
                        }
                        
                        $new_path = $user_dir . '/' . $new_filename;
                        
                        if (copy($attachment_path, $new_path)) {
                            // Update verification data
                            $verification_data['additional_document_path'] = $new_path;
                            $verification_data['additional_document_url'] = add_query_arg(array(
                                'user_id' => $user_id,
                                'file' => $new_filename
                            ), $this->secure_upload_url);
                            $verification_data['additional_document_type_mime'] = get_post_mime_type($attachment_id);
                            
                            $updated = true;
                        }
                    }
                }
                
                if ($updated) {
                    update_user_meta($user_id, $meta_key, $verification_data);
                }
            }
            
            // Mark migration as completed
            update_option('houzez_secure_docs_migration_v1', true);
        }

        /**
         * Deliver a secure document
         */
        public function deliver_secure_document() {
            // Get the parameters
            $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
            $filename = isset($_GET['file']) ? sanitize_file_name($_GET['file']) : '';
            
            // Load the verification data to determine document type
            $verification_data = $this->get_verification_data($user_id);
            
            // Check if this is a back side document
            $is_back_side = false;
            $is_additional_document = false;
            
            // Define allowed document paths and check which one this is
            $allowed_paths = array();
            
            if (!empty($verification_data)) {
                // Main document paths
                if (isset($verification_data['document_path'])) {
                    $doc_path = $verification_data['document_path'];
                    $allowed_paths[basename($doc_path)] = $doc_path;
                }
                
                // Main document back side
                if (isset($verification_data['document_back_path'])) {
                    $doc_back_path = $verification_data['document_back_path'];
                    $allowed_paths[basename($doc_back_path)] = $doc_back_path;
                }
                
                // Additional document paths
                if (isset($verification_data['additional_document_path'])) {
                    $additional_doc_path = $verification_data['additional_document_path'];
                    $allowed_paths[basename($additional_doc_path)] = $additional_doc_path;
                }
                
                // Additional document back side
                if (isset($verification_data['additional_document_back_path'])) {
                    $additional_doc_back_path = $verification_data['additional_document_back_path'];
                    $allowed_paths[basename($additional_doc_back_path)] = $additional_doc_back_path;
                }
            }
            
            // Check if the requested file is in our allowed paths
            if (!array_key_exists($filename, $allowed_paths)) {
                wp_die(__('Invalid file request', 'houzez'));
            }
            
            // Get the full file path
            $file_path = $allowed_paths[$filename];
            
            // Basic security check - verify user permissions
            // Admins and the file owner can access
            if (!current_user_can('manage_options') && get_current_user_id() != $user_id) {
                wp_die(__('You do not have permission to access this file', 'houzez'));
            }
            
            // Check if file exists
            if (!file_exists($file_path)) {
                wp_die(__('File not found', 'houzez'));
            }
            
            // Get file info
            $file_info = wp_check_filetype($file_path);
            $content_type = $file_info['type'];
            
            // Set headers for file download
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            
            // Disable cache
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Output the file
            readfile($file_path);
            exit;
        }

        /**
         * Update document URLs to remove nonces
         */
        public function update_document_urls() {
            // Only run this for logged-in users
            if (!is_user_logged_in()) {
                return;
            }
            
            // Check if we already ran this migration
            $url_update_run = get_option('houzez_doc_urls_updated_v1', false);
            if ($url_update_run) {
                return;
            }
            
            global $wpdb;
            
            // Get all users with verification data
            $meta_key = 'houzez_verification_data';
            $users_with_verification = $wpdb->get_col($wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s",
                $meta_key
            ));
            
            if (empty($users_with_verification)) {
                update_option('houzez_doc_urls_updated_v1', true);
                return;
            }
            
            foreach ($users_with_verification as $user_id) {
                $verification_data = $this->get_verification_data($user_id);
                
                if (empty($verification_data)) {
                    continue;
                }
                
                $updated = false;
                
                // Update main document URL if it exists and contains nonce
                if (isset($verification_data['document_path']) && !empty($verification_data['document_path'])) {
                    $document_path = $verification_data['document_path'];
                    $filename = basename($document_path);
                    
                    // Check if the current URL has a nonce parameter
                    if (isset($verification_data['document_url']) && strpos($verification_data['document_url'], 'nonce=') !== false) {
                        // Generate new URL without nonce
                        $verification_data['document_url'] = add_query_arg(array(
                            'user_id' => $user_id,
                            'file' => $filename
                        ), $this->secure_upload_url);
                        $updated = true;
                    }
                }
                
                // Update main document back side URL if it exists and contains nonce
                if (isset($verification_data['document_back_path']) && !empty($verification_data['document_back_path'])) {
                    $document_back_path = $verification_data['document_back_path'];
                    $back_filename = basename($document_back_path);
                    
                    // Check if the current URL has a nonce parameter
                    if (isset($verification_data['document_back_url']) && strpos($verification_data['document_back_url'], 'nonce=') !== false) {
                        // Generate new URL without nonce
                        $verification_data['document_back_url'] = add_query_arg(array(
                            'user_id' => $user_id,
                            'file' => $back_filename
                        ), $this->secure_upload_url);
                        $updated = true;
                    }
                }
                
                // Update additional document URL if it exists and contains nonce
                if (isset($verification_data['additional_document_path']) && !empty($verification_data['additional_document_path'])) {
                    $additional_doc_path = $verification_data['additional_document_path'];
                    $additional_filename = basename($additional_doc_path);
                    
                    // Check if the current URL has a nonce parameter
                    if (isset($verification_data['additional_document_url']) && strpos($verification_data['additional_document_url'], 'nonce=') !== false) {
                        // Generate new URL without nonce
                        $verification_data['additional_document_url'] = add_query_arg(array(
                            'user_id' => $user_id,
                            'file' => $additional_filename
                        ), $this->secure_upload_url);
                        $updated = true;
                    }
                }
                
                // Update additional document back side URL if it exists and contains nonce
                if (isset($verification_data['additional_document_back_path']) && !empty($verification_data['additional_document_back_path'])) {
                    $additional_doc_back_path = $verification_data['additional_document_back_path'];
                    $additional_back_filename = basename($additional_doc_back_path);
                    
                    // Check if the current URL has a nonce parameter
                    if (isset($verification_data['additional_document_back_url']) && strpos($verification_data['additional_document_back_url'], 'nonce=') !== false) {
                        // Generate new URL without nonce
                        $verification_data['additional_document_back_url'] = add_query_arg(array(
                            'user_id' => $user_id,
                            'file' => $additional_back_filename
                        ), $this->secure_upload_url);
                        $updated = true;
                    }
                }
                
                if ($updated) {
                    update_user_meta($user_id, $meta_key, $verification_data);
                }
            }
            
            // Mark URL update as completed
            update_option('houzez_doc_urls_updated_v1', true);
        }

        /**
         * Update agent/agency verification status
         * 
         * @param int $user_id User ID
         * @param string $status Verification status (pending, verified, or empty to remove)
         */
        private function update_agent_verification_status($user_id, $status = 0) {
            $user = get_userdata($user_id);
            
            if (!$user) {
                return;
            }
            
            // Get post ID based on user role
            $post_id = false;
            
            if (in_array('houzez_agent', $user->roles) || in_array('author', $user->roles)) {
                $post_id = get_user_meta($user_id, 'fave_author_agent_id', true);
                $prefix = 'fave_agent_';
            } elseif (in_array('houzez_agency', $user->roles)) {
                $post_id = get_user_meta($user_id, 'fave_author_agency_id', true);
                $prefix = 'fave_agency_';
            }
            
            // If we found a post ID, update the verification status
            if ($post_id) {
                $post = get_post($post_id);
                
                if ($post) {
                    update_post_meta($post_id, $prefix . 'verified', $status);
                }
            }
        }
    }
}

// Initialize class
$GLOBALS['houzez_user_verification'] = new Houzez_User_Verification(); 