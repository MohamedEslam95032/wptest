<?php
/**
 * User Approval Class
 *
 * Handles user approval functionality for Houzez
 *
 * @package    Houzez_Login_Register
 * @subpackage Classes
 * @author     Waqas Riaz
 * @copyright  Copyright (c) 2023, Favethemes
 * @link       https://themeforest.net/user/favethemes
 * @since      1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Houzez_User_Approval class
 */
class Houzez_User_Approval {

    /**
     * Is user approval enabled
     *
     * @var bool
     */
    private $is_enabled;

    /**
     * Auto-approved roles
     *
     * @var array
     */
    private $auto_approved_roles;

    /**
     * Constructor
     */
    public function __construct() {
        // Check if user approval system is enabled
        $this->is_enabled = houzez_login_option('enable_user_approval', 0);
        
        // Get auto-approved roles
        $this->auto_approved_roles = houzez_login_option('auto_approved_roles', array());

        // Only set up hooks if user approval is enabled
        if ($this->is_enabled) {
            // One-time upgrade: mark all existing users as approved.
            add_action('admin_init', array($this, 'approve_existing_users'));

            // Set user account as pending on registration
            add_action( 'user_register', array( $this, 'set_account_pending' ), 10 );
            add_action( 'houzez_after_register', array( $this, 'set_account_pending' ), 20 );
            add_action( 'houzez_api_after_register', array( $this, 'set_account_pending' ), 10 );
            
            // Block non-approved users from logging in
            add_filter( 'authenticate', array( $this, 'block_nonapproved_logins' ), 30, 3 );

            // Admin-side hooks - always active so admins can manage existing statuses
            // Add approve/decline actions to user list
            add_filter( 'user_row_actions', array( $this, 'add_approve_decline_actions' ), 10, 2 );
            
            // Handle approve user action
            add_action( 'admin_post_houzez_approve_user', array( $this, 'handle_approve_user' ) );
            
            // Handle decline user action
            add_action( 'admin_post_houzez_decline_user', array( $this, 'handle_decline_user' ) );
            
            // Handle suspend user action
            add_action( 'admin_post_houzez_suspend_user', array( $this, 'handle_suspend_user' ) );
            
            // Add approval status column to users table
            add_filter( 'manage_users_columns', array( $this, 'add_user_approval_column' ) );
            
            // Display approval status in column
            add_filter( 'manage_users_custom_column', array( $this, 'display_user_approval_column' ), 10, 3 );
            
            // Add CSS for styling the status column
            add_action( 'admin_head', array( $this, 'approval_status_column_style' ) );
            
            // Add bulk actions
            add_filter( 'bulk_actions-users', array( $this, 'add_bulk_actions' ) );
            
            // Handle bulk actions
            add_filter( 'handle_bulk_actions-users', array( $this, 'handle_bulk_actions' ), 10, 3 );
            
            // Add admin notices for bulk actions
            add_action( 'admin_notices', array( $this, 'bulk_action_admin_notice' ) );
            
            // Add filter for approval status in users list - higher priority (20) makes it appear after other filters
            add_action( 'restrict_manage_users', array( $this, 'add_approval_status_filter' ), 20 );
            
            // Modify user query to filter by approval status
            add_action( 'pre_get_users', array( $this, 'filter_users_by_approval_status' ) );
        }
    }

    /**
     * One-time upgrade to approve all existing users
     */
    public function approve_existing_users() {
        if (!get_option('houzez_bulk_approved_done') && current_user_can('manage_options')) {
            $all_users = get_users(['fields' => 'ID']);
            foreach ($all_users as $uid) {
                update_user_meta($uid, 'houzez_account_approved', 1);
            }
            update_option('houzez_bulk_approved_done', 1);
        }
    }

    /**
     * Check if a user should be auto-approved based on their role
     */
    private function should_auto_approve_user($user_id) {
        // Get user data
        $user = get_userdata($user_id);
        if (!$user || empty($user->roles)) {
            return false;
        }
        
        // Check if any of the user's roles are in the auto-approved list
        if (!empty($this->auto_approved_roles)) {
            foreach ($user->roles as $role) {
                if (in_array($role, $this->auto_approved_roles)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Notify admin about auto-approved user
     */
    private function notify_admin_of_auto_approval($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            return;
        }
        
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        
        $subject = sprintf(__('[%s] User Auto-Approved: %s', 'houzez-login-register'), $site_name, $user->user_login);
        
        // Get the user's roles as readable text
        $roles = array();
        if (!empty($user->roles)) {
            foreach ($user->roles as $role) {
                $roles[] = ucfirst(str_replace('houzez_', '', $role));
            }
        }
        $roles_text = !empty($roles) ? implode(', ', $roles) : __('None', 'houzez-login-register');
        
        $message = sprintf(
            __("A new user has been automatically approved on your site %s.\n\nUsername: %s\nEmail: %s\nRoles: %s\n\nThis user was auto-approved because their role was in your auto-approval list.", 'houzez-login-register'),
            $site_name,
            $user->user_login,
            $user->user_email,
            $roles_text
        );
        
        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Notify user about auto-approval
     */
    private function notify_user_of_auto_approval($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            return;
        }
        
        $site_name = get_bloginfo('name');
        
        $subject = sprintf(__('Your account on %s has been approved', 'houzez-login-register'), $site_name);
        
        $message = sprintf(
            __("Hello %s,\n\nGood news! Your account on %s has been automatically approved. You can now log in here:\n%s\n\nThank you!", 'houzez-login-register'),
            $user->first_name ? $user->first_name : $user->user_login,
            $site_name,
            home_url('/')
        );
        
        wp_mail($user->user_email, $subject, $message);
    }

    /**
     * Set account as pending on registration
     */
    public function set_account_pending( $user_id ) {
        $hook = current_filter();

        // Check if this is an API registration (REST API context)
        $is_api_registration = defined('REST_REQUEST') && REST_REQUEST;
        
        // Check if this is AJAX registration
        $is_ajax_registration = defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] === 'houzez_register';

        // Check if this is admin-created user (from wp-admin/user-new.php)
        $is_admin_created = is_admin() && current_user_can('create_users') && !$is_api_registration;

        // 1) If we're on user_register but this is the AJAX houzez_register, skip (let houzez_after_register handle it)
        if (
            $hook === 'user_register'
            && $is_ajax_registration
        ) {
            return;
        }

        // 2) If we're on houzez_after_register but NOT the AJAX houzez_register or API registration, skip
        if (
            $hook === 'houzez_after_register'
            && !$is_ajax_registration
            && !$is_api_registration
        ) {
            return;
        }

        // 3) Admin-created users are auto-approved
        if ( $is_admin_created ) {
            update_user_meta( $user_id, 'houzez_account_approved', 1 );
            update_user_meta( $user_id, 'houzez_approval_method', 'admin_created' );
            return;
        }

        // 4) If user approval system is disabled, auto-approve everyone
        if ( ! $this->is_enabled ) {
            update_user_meta( $user_id, 'houzez_account_approved', 1 );
            update_user_meta( $user_id, 'houzez_approval_method', 'system_disabled' );
            return;
        }

        // 5) User approval is enabled - check for auto-approved roles
        if ( $this->should_auto_approve_user( $user_id ) ) {
            update_user_meta( $user_id, 'houzez_account_approved', 1 );
            $approval_method = $is_api_registration ? 'api_auto_approved_role' : 'auto_approved_role';
            update_user_meta( $user_id, 'houzez_approval_method', $approval_method );
            $this->publish_user_associated_posts( $user_id );
            //$this->notify_admin_of_auto_approval( $user_id );
            //$this->notify_user_of_auto_approval( $user_id );
        } else {
            // 6) Everyone else stays pending for approval
            update_user_meta( $user_id, 'houzez_account_approved', 0 );
            $approval_method = $is_api_registration ? 'api_pending' : 'pending';
            update_user_meta( $user_id, 'houzez_approval_method', $approval_method );
        }
    }


    /**
     * Block non-approved users from logging in
     */
    public function block_nonapproved_logins( $user, $username, $password ) {
        // Only check approval status if feature is enabled
        if (!$this->is_enabled) {
            return $user;
        }

        if ( is_a( $user, 'WP_User' ) ) {
            // fetch raw meta; if missing, leave $meta as empty string
            $meta = get_user_meta( $user->ID, 'houzez_account_approved', true );
            if ( $meta !== '' ) {
                $status = intval( $meta );
                if ( $status === -1 ) {
                    return new WP_Error( 'account_not_approved', __( '<strong>ERROR</strong>: Your registration has been declined.', 'houzez-login-register' ) );
                } elseif ( $status === 0 ) {
                    return new WP_Error( 'account_not_approved', __( '<strong>ERROR</strong>: Your account is pending approval. Please wait for an administrator to activate it.', 'houzez-login-register' ) );
                } elseif ( $status === 2 ) {
                    return new WP_Error( 'account_suspended', __( '<strong>ERROR</strong>: Your account has been suspended. Please contact an administrator for assistance.', 'houzez-login-register' ) );
                }
            }
        }
        return $user;
    }

    /**
     * Add approve/decline actions to user list
     */
    public function add_approve_decline_actions( $actions, $user ) {
        if ( ! current_user_can( 'edit_users' ) ) {
            return $actions;
        }
        
        // Check if user is an administrator - don't show actions for admin users
        if (in_array('administrator', $user->roles)) {
            return $actions;
        }
        
        $status = get_user_meta( $user->ID, 'houzez_account_approved', true );
        if( $status != "" ) {
            $status = intval( $status );
        }

        // build base admin-post URL
        $base = admin_url( 'admin-post.php?user_id=' . $user->ID );
        
        // Prepare action links based on current status
        $action_links = array();
        
        // Approve link - show for pending, declined, or suspended users
        if ($status === 0 || $status === -1 || $status === 2) {
            $action_links[] = sprintf(
                '<a href="%s" class="houzez-approve-user">%s</a>',
                wp_nonce_url( $base . '&action=houzez_approve_user', 'houzez-approve-user_' . $user->ID ),
                __( 'Approve', 'houzez-login-register' )
            );
        }
        
        // Decline link - show only for pending users
        if ($status === 0) {
            $action_links[] = sprintf(
                '<a href="%s" class="houzez-decline-user">%s</a>',
                wp_nonce_url( $base . '&action=houzez_decline_user', 'houzez-decline-user_' . $user->ID ),
                __( 'Decline', 'houzez-login-register' )
            );
        }
        
        // Suspend link - show only for approved users
        if ($status === 1) {
            $action_links[] = sprintf(
                '<a href="%s" class="houzez-suspend-user">%s</a>',
                wp_nonce_url( $base . '&action=houzez_suspend_user', 'houzez-suspend-user_' . $user->ID ),
                __( 'Suspend', 'houzez-login-register' )
            );
        }
        
        if (!empty($action_links)) {
            $actions['user_actions'] = implode(' | ', $action_links);
        }
        
        return $actions;
    }

    /**
     * Handle approve user action
     */
    public function handle_approve_user() {
        if ( ! current_user_can( 'edit_users' ) ) {
            wp_die( __( 'Unauthorized', 'houzez-login-register' ) );
        }
        $user_id = intval( $_GET['user_id'] );
        if ( ! $user_id || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'houzez-approve-user_' . $user_id ) ) {
            wp_die( __( 'Invalid request', 'houzez-login-register' ) );
        }
        update_user_meta( $user_id, 'houzez_account_approved', 1 );
        update_user_meta( $user_id, 'houzez_approval_method', 'admin_approved' );
        
        // Publish agent/agency post if exists
        $this->publish_user_associated_posts($user_id);
        
        // Get user data
        $u = get_userdata( $user_id );
        
        // send approval email
        $subject = __( 'Your account has been approved', 'houzez-login-register' );
        $body    = sprintf(
            __( "Hello %s,\n\nGood news! Your account on %s has just been approved. You can now log in here:\n%s\n\nThank you!", 'houzez-login-register' ),
            $u->first_name ?: $u->user_login,
            get_bloginfo( 'name' ),
            home_url('/')
        );
        
        // Trigger notification hook
        $notificationArgs = array(
            'title'   => $subject,
            'message' => $body,
            'type'    => 'user_approved',
            'to'      => $u->user_email,
            'user_id' => $user_id,
            'user_login' => $u->user_login,
            'user_email' => $u->user_email,
            'admin_user' => wp_get_current_user()->user_login,
        );
        do_action('houzez_send_notification', $notificationArgs);
        
        wp_mail( $u->user_email, $subject, $body );
        
        // Clean URL and add our parameter
        $redirect_url = remove_query_arg(array('houzez_user_approved', 'houzez_user_declined'), wp_get_referer());
        wp_redirect(add_query_arg('houzez_user_approved', '1', $redirect_url));
        exit;
    }

    /**
     * Handle decline user action
     */
    public function handle_decline_user() {
        if ( ! current_user_can( 'edit_users' ) ) {
            wp_die( __( 'Unauthorized', 'houzez-login-register' ) );
        }
        $user_id = intval( $_GET['user_id'] );
        if ( ! $user_id || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'houzez-decline-user_' . $user_id ) ) {
            wp_die( __( 'Invalid request', 'houzez-login-register' ) );
        }
        
        // Check if user is an administrator - prevent declining admins
        $user = get_userdata($user_id);
        if ($user && in_array('administrator', $user->roles)) {
            wp_die(__('Error: You cannot decline an administrator account.', 'houzez-login-register'));
        }
        
        update_user_meta( $user_id, 'houzez_account_approved', -1 );
        update_user_meta( $user_id, 'houzez_approval_method', 'admin_declined' );
        
        // Trash agent/agency post if exists
        $this->trash_user_associated_posts($user_id);
        
        // send declined email
        $u = get_userdata( $user_id );
        $subject = __( 'Your account registration has been declined', 'houzez-login-register' );
        $body    = sprintf(
            __( "Hello %s,\n\nWe're sorry to let you know that your account registration on %s has been declined. If you believe this is an error, please contact us.\n\nRegards,", 'houzez-login-register' ),
            $u->first_name ?: $u->user_login,
            get_bloginfo( 'name' )
        );
        
        // Trigger notification hook
        $notificationArgs = array(
            'title'   => $subject,
            'message' => $body,
            'type'    => 'user_declined',
            'to'      => $u->user_email,
            'user_id' => $user_id,
            'user_login' => $u->user_login,
            'user_email' => $u->user_email,
            'admin_user' => wp_get_current_user()->user_login,
        );
        do_action('houzez_send_notification', $notificationArgs);
        
        wp_mail( $u->user_email, $subject, $body );
        
        // Clean URL and add our parameter
        $redirect_url = remove_query_arg(array('houzez_user_approved', 'houzez_user_declined'), wp_get_referer());
        wp_redirect(add_query_arg('houzez_user_declined', '1', $redirect_url));
        exit;
    }

    /**
     * Handle suspend user action
     */
    public function handle_suspend_user() {
        if ( ! current_user_can( 'edit_users' ) ) {
            wp_die( __( 'Unauthorized', 'houzez-login-register' ) );
        }
        $user_id = intval( $_GET['user_id'] );
        if ( ! $user_id || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'houzez-suspend-user_' . $user_id ) ) {
            wp_die( __( 'Invalid request', 'houzez-login-register' ) );
        }
        
        // Check if user is an administrator - prevent suspending admins
        $user = get_userdata($user_id);
        if ($user && in_array('administrator', $user->roles)) {
            wp_die(__('Error: You cannot suspend an administrator account.', 'houzez-login-register'));
        }
        
        update_user_meta( $user_id, 'houzez_account_approved', 2 );
        update_user_meta( $user_id, 'houzez_approval_method', 'admin_suspended' );
        
        // Trash agent/agency post if exists
        $this->trash_user_associated_posts($user_id);
        
        // send suspended email
        $u = get_userdata( $user_id );
        $subject = __( 'Your account has been suspended', 'houzez-login-register' );
        $body    = sprintf(
            __( "Hello %s,\n\nWe're sorry to let you know that your account on %s has been suspended. If you believe this is an error, please contact us.\n\nRegards,", 'houzez-login-register' ),
            $u->first_name ?: $u->user_login,
            get_bloginfo( 'name' )
        );
        
        // Trigger notification hook
        $notificationArgs = array(
            'title'   => $subject,
            'message' => $body,
            'type'    => 'user_suspended',
            'to'      => $u->user_email,
            'user_id' => $user_id,
            'user_login' => $u->user_login,
            'user_email' => $u->user_email,
            'admin_user' => wp_get_current_user()->user_login,
        );
        do_action('houzez_send_notification', $notificationArgs);
        
        wp_mail( $u->user_email, $subject, $body );
        
        // Clean URL and add our parameter
        $redirect_url = remove_query_arg(array('houzez_user_approved', 'houzez_user_declined'), wp_get_referer());
        wp_redirect(add_query_arg('houzez_user_suspended', '1', $redirect_url));
        exit;
    }

    /**
     * Add approval status column to users table
     */
    public function add_user_approval_column( $columns ) {
        $columns['approval_status'] = __( 'Status', 'houzez-login-register' );
        return $columns;
    }

    /**
     * Display approval status in column
     */
    public function display_user_approval_column( $value, $column_name, $user_id ) {
        if ( 'approval_status' !== $column_name ) {
            return $value;
        }
        
        // Check if user is an administrator - show dash for admin users
        $user = get_userdata($user_id);
        if ($user && in_array('administrator', $user->roles)) {
            return '<span class="houzez-status">-</span>';
        }
    
        $status = get_user_meta( $user_id, 'houzez_account_approved', true );
        if( $status != "" ) {
            $status = intval( $status );
        }
        
        // Display only status without action buttons
        if ( $status === 0 ) {
            return sprintf(
                '<span class="houzez-status houzez-status-pending">%s</span>',
                __( 'Pending', 'houzez-login-register' )
            );
        } elseif ( $status == 1 || $status == '') {
            return sprintf(
                '<span class="houzez-status houzez-status-approved">%s</span>',
                __( 'Approved', 'houzez-login-register' )
            );
        } elseif ( $status == -1 ) {
            return sprintf(
                '<span class="houzez-status houzez-status-declined">%s</span>',
                __( 'Declined', 'houzez-login-register' )
            );
        } elseif ( $status == 2 ) {
            return sprintf(
                '<span class="houzez-status houzez-status-suspended">%s</span>',
                __( 'Suspended', 'houzez-login-register' )
            );
        }
        
        return $value;
    }

    /**
     * Add CSS for styling the status column
     */
    public function approval_status_column_style() {
        echo '<style>
            .houzez-status { 
                display: inline-block;
                font-weight: 500;
            }
            .houzez-status-pending { 
                color: #FFA500;
            }
            .houzez-status-approved { 
                color: #198754;
            }
            .houzez-status-declined { 
                color: #8b0000;
            }
            .houzez-status-suspended { 
                color: #dc3545;
            }
            .column-approval_status {
                width: 8%;
            }
            .houzez-approve-user {
                color: #198754 !important;
            }
            .houzez-decline-user {
                color: #8b0000 !important;
            }
            .houzez-suspend-user {
                color: #dc3545 !important;
            }
        </style>';
    }
    
    /**
     * Add bulk actions for approving and declining users
     */
    public function add_bulk_actions( $bulk_actions ) {
        $bulk_actions['houzez_approve_users'] = __( 'Approve Users', 'houzez-login-register' );
        $bulk_actions['houzez_decline_users'] = __( 'Decline Users', 'houzez-login-register' );
        $bulk_actions['houzez_suspend_users'] = __( 'Suspend Users', 'houzez-login-register' );
        return $bulk_actions;
    }
    
    /**
     * Handle bulk actions for approving and declining users
     */
    public function handle_bulk_actions( $redirect_to, $action, $user_ids ) {
        // Clean up redirect URL to remove conflicting parameters
        $redirect_to = remove_query_arg(array('action', 'action2', 'new_role', 'new_role2', 'bulk_action'), $redirect_to);
        
        if ( 'houzez_approve_users' === $action ) {
            $approved = 0;
            foreach ( $user_ids as $user_id ) {
                $user = get_userdata( $user_id );
                
                // Skip administrators
                if ( $user && in_array( 'administrator', $user->roles ) ) {
                    continue;
                }
                
                update_user_meta( $user_id, 'houzez_account_approved', 1 );
                update_user_meta( $user_id, 'houzez_approval_method', 'bulk_approved' );
                
                // Publish agent/agency post if exists
                $this->publish_user_associated_posts($user_id);
                
                // Send approval email
                $subject = __( 'Your account has been approved', 'houzez-login-register' );
                $body    = sprintf(
                    __( "Hello %s,\n\nGood news! Your account on %s has just been approved. You can now log in here:\n%s\n\nThank you!", 'houzez-login-register' ),
                    $user->first_name ?: $user->user_login,
                    get_bloginfo( 'name' ),
                    home_url('/')
                );
                
                // Trigger notification hook
                $notificationArgs = array(
                    'title'   => $subject,
                    'message' => $body,
                    'type'    => 'user_approved',
                    'to'      => $user->user_email,
                    'user_id' => $user_id,
                    'user_login' => $user->user_login,
                    'user_email' => $user->user_email,
                    'admin_user' => wp_get_current_user()->user_login,
                    'bulk_action' => true,
                );
                do_action('houzez_send_notification', $notificationArgs);
                
                wp_mail( $user->user_email, $subject, $body );
                
                $approved++;
            }
            
            $redirect_to = add_query_arg( 'houzez_bulk_approved', $approved, $redirect_to );
            return $redirect_to;
        }
        
        if ( 'houzez_decline_users' === $action ) {
            $declined = 0;
            foreach ( $user_ids as $user_id ) {
                $user = get_userdata( $user_id );
                
                // Skip administrators
                if ( $user && in_array( 'administrator', $user->roles ) ) {
                    continue;
                }
                
                update_user_meta( $user_id, 'houzez_account_approved', -1 );
                update_user_meta( $user_id, 'houzez_approval_method', 'bulk_declined' );
                
                // Trash agent/agency post if exists
                $this->trash_user_associated_posts($user_id);
                
                // Send declined email
                $subject = __( 'Your account registration has been declined', 'houzez-login-register' );
                $body    = sprintf(
                    __( "Hello %s,\n\nWe're sorry to let you know that your account registration on %s has been declined. If you believe this is an error, please contact us.\n\nRegards,", 'houzez-login-register' ),
                    $user->first_name ?: $user->user_login,
                    get_bloginfo( 'name' )
                );
                
                // Trigger notification hook
                $notificationArgs = array(
                    'title'   => $subject,
                    'message' => $body,
                    'type'    => 'user_declined',
                    'to'      => $user->user_email,
                    'user_id' => $user_id,
                    'user_login' => $user->user_login,
                    'user_email' => $user->user_email,
                    'admin_user' => wp_get_current_user()->user_login,
                );
                do_action('houzez_send_notification', $notificationArgs);
                
                wp_mail( $user->user_email, $subject, $body );
                
                $declined++;
            }
            
            $redirect_to = add_query_arg( 'houzez_bulk_declined', $declined, $redirect_to );
            return $redirect_to;
        }
        
        if ( 'houzez_suspend_users' === $action ) {
            $suspended = 0;
            foreach ( $user_ids as $user_id ) {
                $user = get_userdata( $user_id );
                
                // Skip administrators
                if ( $user && in_array( 'administrator', $user->roles ) ) {
                    continue;
                }
                
                update_user_meta( $user_id, 'houzez_account_approved', 2 );
                update_user_meta( $user_id, 'houzez_approval_method', 'bulk_suspended' );
                
                // Trash agent/agency post if exists
                $this->trash_user_associated_posts($user_id);
                
                // Send suspended email
                $subject = __( 'Your account has been suspended', 'houzez-login-register' );
                $body    = sprintf(
                    __( "Hello %s,\n\nWe're sorry to let you know that your account on %s has been suspended. If you believe this is an error, please contact us.\n\nRegards,", 'houzez-login-register' ),
                    $user->first_name ?: $user->user_login,
                    get_bloginfo( 'name' )
                );
                
                // Trigger notification hook
                $notificationArgs = array(
                    'title'   => $subject,
                    'message' => $body,
                    'type'    => 'user_suspended',
                    'to'      => $user->user_email,
                    'user_id' => $user_id,
                    'user_login' => $user->user_login,
                    'user_email' => $user->user_email,
                    'admin_user' => wp_get_current_user()->user_login,
                );
                do_action('houzez_send_notification', $notificationArgs);
                
                wp_mail( $user->user_email, $subject, $body );
                
                $suspended++;
            }
            
            $redirect_to = add_query_arg( 'houzez_bulk_suspended', $suspended, $redirect_to );
            return $redirect_to;
        }
        
        return $redirect_to;
    }
    
    /**
     * Show admin notices for bulk actions
     */
    public function bulk_action_admin_notice() {
        if ( ! empty( $_REQUEST['houzez_bulk_approved'] ) ) {
            $approved_count = intval( $_REQUEST['houzez_bulk_approved'] );
            printf(
                '<div class="updated notice is-dismissible"><p>' . 
                _n( 
                    '%s user has been approved.',
                    '%s users have been approved.',
                    $approved_count,
                    'houzez-login-register'
                ) . '</p></div>',
                $approved_count
            );
        }
        
        if ( ! empty( $_REQUEST['houzez_bulk_declined'] ) ) {
            $declined_count = intval( $_REQUEST['houzez_bulk_declined'] );
            printf(
                '<div class="updated notice is-dismissible"><p>' . 
                _n( 
                    '%s user has been declined.',
                    '%s users have been declined.',
                    $declined_count,
                    'houzez-login-register'
                ) . '</p></div>',
                $declined_count
            );
        }
        
        if ( ! empty( $_REQUEST['houzez_bulk_suspended'] ) ) {
            $suspended_count = intval( $_REQUEST['houzez_bulk_suspended'] );
            printf(
                '<div class="updated notice is-dismissible"><p>' . 
                _n( 
                    '%s user has been suspended.',
                    '%s users have been suspended.',
                    $suspended_count,
                    'houzez-login-register'
                ) . '</p></div>',
                $suspended_count
            );
        }
    }
    
    /**
     * Publish agent or agency posts associated with a user
     */
    private function publish_user_associated_posts($user_id) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return;
        }
        
        // Get post ID based on user role
        $post_id = false;
        
        if (in_array('houzez_agent', $user->roles) || in_array('author', $user->roles)) {
            $post_id = get_user_meta($user_id, 'fave_author_agent_id', true);
            $post_type = 'houzez_agent';
        } elseif (in_array('houzez_agency', $user->roles)) {
            $post_id = get_user_meta($user_id, 'fave_author_agency_id', true);
            $post_type = 'houzez_agency';
        }
        
        // If we found a post ID, publish it
        if ($post_id) {
            $post = get_post($post_id);
            
            if ($post && $post->post_status != 'publish') {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                ));
            }
        }
    }

    /**
     * Trash agent or agency posts associated with a user
     */
    private function trash_user_associated_posts($user_id) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return;
        }
        
        // Get post ID based on user role
        $post_id = false;
        
        if (in_array('houzez_agent', $user->roles) || in_array('author', $user->roles)) {
            $post_id = get_user_meta($user_id, 'fave_author_agent_id', true);
        } elseif (in_array('houzez_agency', $user->roles)) {
            $post_id = get_user_meta($user_id, 'fave_author_agency_id', true);
        }
        
        // If we found a post ID, trash it
        if ($post_id) {
            $post = get_post($post_id);
            
            if ($post) {
                wp_trash_post($post_id);
            }
        }
    }

    /**
     * Add filter dropdown for approval status
     */
    public function add_approval_status_filter() {
        // Only show on the users.php page
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'users') {
            return;
        }
        
        // Get current selected value, default to 'all' if not set
        $selected = isset($_GET['approval_status']) ? $_GET['approval_status'] : 'all';
        
        // Statuses
        $statuses = array(
            'all' => __('All Statuses', 'houzez-login-register'),
            'pending' => __('Pending Approval', 'houzez-login-register'),
            'approved' => __('Approved', 'houzez-login-register'),
            'declined' => __('Declined', 'houzez-login-register'),
            'suspended' => __('Suspended', 'houzez-login-register')
        );
        
        // No form wrapper - integrate with WordPress's existing form
        echo '<div class="alignleft actions">';
        
        // Only preserve the search parameter if it exists
        if (isset($_GET['s']) && !empty($_GET['s'])) {
            echo '<input type="hidden" name="s" value="' . esc_attr($_GET['s']) . '" />';
        }
        
        // Output dropdown with JavaScript to handle form submission
        echo '<select id="approval_status_filter">';
        foreach ($statuses as $value => $label) {
            echo '<option value="' . esc_attr($value) . '"' . selected($selected, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        
        // Add the filter button with JavaScript to handle the submission
        echo '<input type="button" id="approval-status-submit" class="button action" value="' . esc_attr__('Filter', 'houzez-login-register') . '" />';
        
        echo '</div>';
        
        // Add JavaScript to handle the filter submission
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#approval-status-submit').on('click', function() {
                var statusValue = $('#approval_status_filter').val();
                console.log('Selected status:', statusValue); // Debug log
                
                var currentUrl = window.location.href.split('?')[0];
                var params = new URLSearchParams(window.location.search);
                
                // Remove existing approval_status parameter
                params.delete('approval_status');
                // Also remove pagination when filtering
                params.delete('paged');
                
                // Only add approval_status if it's not 'all'
                if (statusValue && statusValue !== 'all') {
                    params.set('approval_status', statusValue);
                }
                
                // Build the new URL
                var newUrl = currentUrl;
                if (params.toString()) {
                    newUrl += '?' + params.toString();
                }
                
                console.log('Redirecting to:', newUrl); // Debug log
                
                // Redirect to the new URL
                window.location.href = newUrl;
            });
            
            // Also handle Enter key press on the dropdown
            $('#approval_status_filter').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#approval-status-submit').click();
                    return false;
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Filter users by approval status
     */
    public function filter_users_by_approval_status($query) {
        global $pagenow;
        
        // Only run on the users.php admin page
        if ('users.php' !== $pagenow || !is_admin()) {
            return;
        }
        
        // Check if filter is set
        if (!isset($_GET['approval_status']) || empty($_GET['approval_status'])) {
            return;
        }
        
        // Get status code based on selected filter
        $status_code = '';
        $status_filter = $_GET['approval_status'];
        
        switch ($status_filter) {
            case 'pending':
                $status_code = '0';
                break;
            case 'approved':
                $status_code = '1';
                break;
            case 'declined':
                $status_code = '-1';
                break;
            case 'suspended':
                $status_code = '2';
                break;
            default:
                return; // No valid filter selected
        }
        
        // Set up meta query to filter users
        $meta_query = array(
            array(
                'key' => 'houzez_account_approved',
                'value' => $status_code,
                'compare' => '='
            )
        );
        
        // Add meta query to the user query
        $query->set('meta_query', $meta_query);
    }
}

// Initialize the class
new Houzez_User_Approval(); 