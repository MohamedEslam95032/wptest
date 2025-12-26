<?php
/**
 * Houzez Image Sizes Class
 * Adds image size configuration capability to Houzez
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Image_Sizes {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Default image size assignments
     */
    private $default_assignments = array(
        'listing_grid_v1' => 'houzez-item-image-6',
        'listing_grid_v2' => 'houzez-item-image-6',
        'listing_grid_v3' => 'houzez-item-image-6',
        'listing_grid_v4' => 'full',
        'listing_grid_v5' => 'houzez-item-image-6',
        'listing_grid_v6' => 'houzez-item-image-6',
        'listing_grid_v7' => 'houzez-item-image-6',
        'listing_list_v1' => 'houzez-item-image-6',
        'listing_list_v2' => 'houzez-item-image-6',
        'listing_list_v4' => 'houzez-item-image-6',
        'listing_list_v7' => 'houzez-item-image-6',
        
        'property_detail_v1' => 'full',
        'property_detail_v2' => 'full',
        'property_detail_v3-4' => 'houzez-gallery',
        'property_detail_v5' => 'full',
        'property_detail_v6' => 'houzez-gallery',
        'property_detail_v7' => 'houzez-top-v7',
        'property_detail_block_gallery' => 'houzez-item-image-6',

        'agent_profile' => 'houzez-top-v7',
        'agency_profile' => 'houzez-top-v7',
        'blog_post' => 'full',
        'blog_grid' => 'houzez-item-image-6'
    );

    /**
     * Returns the instance of this class
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'houzez_admin_sub_menus', array( $this, 'add_submenu_page' ), 20 );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'after_setup_theme', array( $this, 'setup_dynamic_image_sizes' ), 11 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        
        // Initialize default settings if not already set
        $this->maybe_initialize_defaults();
        
        // Filter WordPress image sizes to remove disabled ones
        add_filter( 'intermediate_image_sizes_advanced', array( $this, 'filter_image_sizes' ), 10, 1 );
        
        // AJAX handlers for custom image sizes
        add_action( 'wp_ajax_houzez_add_custom_image_size', array( $this, 'ajax_add_custom_image_size' ) );
        add_action( 'wp_ajax_houzez_get_custom_image_size', array( $this, 'ajax_get_custom_image_size' ) );
        add_action( 'wp_ajax_houzez_update_custom_image_size', array( $this, 'ajax_update_custom_image_size' ) );
        add_action( 'wp_ajax_houzez_delete_custom_image_size', array( $this, 'ajax_delete_custom_image_size' ) );
    }
    
    /**
     * Initialize default settings if they're not already set
     */
    private function maybe_initialize_defaults() {
        // Check if we've already initialized the settings
        if (get_option('houzez_image_sizes_initialized')) {
            return;
        }
        
        // Set WordPress core sizes to disabled by default
        add_option('houzez_enable_thumbnail_size', false);
        add_option('houzez_enable_medium_size', false);
        add_option('houzez_enable_medium_large_size', false);
        add_option('houzez_enable_large_size', false);
        
        // Set Houzez sizes to enabled by default
        add_option('houzez_enable_gallery_size', true);
        add_option('houzez_enable_top_v7_size', true);
        add_option('houzez_enable_item_image_6_size', true);
        
        // Set layout image assignments
        if (!get_option('houzez_layout_image_assignments')) {
            update_option('houzez_layout_image_assignments', $this->default_assignments);
        }
        
        // Mark as initialized
        add_option('houzez_image_sizes_initialized', true);
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        
        if ( isset( $_GET['page'] ) && $_GET['page'] != 'houzez_image_sizes' ) {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('wp-jquery-ui-dialog');
        
        // Get layout assignments for JavaScript validation
        $image_assignments = get_option('houzez_layout_image_assignments', array());
        $layout_labels = array(
            'listing_grid_v1' => __('Listing Grid v1', 'houzez'),
            'listing_grid_v2' => __('Listing Grid v2', 'houzez'),
            'listing_grid_v3' => __('Listing Grid v3', 'houzez'),
            'listing_grid_v4' => __('Listing Grid v4', 'houzez'),
            'listing_grid_v5' => __('Listing Grid v5', 'houzez'),
            'listing_grid_v6' => __('Listing Grid v6', 'houzez'),
            'listing_grid_v7' => __('Listing Grid v7', 'houzez'),
            'listing_list_v1' => __('Listing List v1', 'houzez'),
            'listing_list_v2' => __('Listing List v2', 'houzez'),
            'listing_list_v4' => __('Listing List v4', 'houzez'),
            'listing_list_v7' => __('Listing List v7', 'houzez'),
            
            'property_detail_v1' => __('Property Detail v1', 'houzez'),
            'property_detail_v2' => __('Property Detail v2', 'houzez'),
            'property_detail_v3-4' => __('Property Detail v3-4', 'houzez'),
            'property_detail_v5' => __('Property Detail v5', 'houzez'),
            'property_detail_v6' => __('Property Detail v6', 'houzez'),
            'property_detail_v7' => __('Property Detail v7', 'houzez'),
            'property_detail_block_gallery' => __('Property Detail Block Gallery', 'houzez'),

            'agent_profile' => __('Agent', 'houzez'),
            'agency_profile' => __('Agency', 'houzez'),
            'blog_post' => __('Blog Post', 'houzez'),
            'blog_grid' => __('Blog Grid', 'houzez'),
        );
        
        // Add inline styles for the settings page
        $custom_css = '
            /* Main wrapper styling */
            .houzez-image-sizes-wrap {
                max-width: 100%;
                margin: 20px 0;
                color: #333;
            }
            
            /* Form table adjustments */
            .houzez-image-sizes-wrap .form-table th {
                padding: 18px 12px 18px 0;
                width: 200px;
                font-weight: 600;
            }
            
            /* Page header styling */
            .houzez-image-sizes-wrap h1 {
                margin-bottom: 10px;
                color: #23282d;
                font-size: 26px;
            }
            
            /* Page description styling */
            .page-description {
                margin-top: 0;
                margin-bottom: 25px;
                font-size: 14px;
                color: #646970;
                max-width: 800px;
                line-height: 1.5;
            }
            
            /* Tabs styling */
            .nav-tab-wrapper {
                margin-bottom: 25px;
                border-bottom: 2px solid #2271b1;
            }
            
            .nav-tab {
                border-radius: 4px 4px 0 0;
                margin-right: 5px;
                padding: 12px 18px;
                font-size: 14px;
                font-weight: 500;
                border: 1px solid #c3c4c7;
                border-bottom: none;
                background-color: #f0f0f1;
                transition: all 0.2s ease;
            }
            
            .nav-tab:hover {
                background-color: #fff;
                color: #2271b1;
            }
            
            .nav-tab-active, 
            .nav-tab-active:hover {
                background-color: #2271b1;
                color: #fff;
                border-color: #2271b1;
            }
            
            /* Size group card styling */
            .houzez-size-group {
                background: #fff;
                border: 1px solid #dcdcde;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                padding: 20px 25px;
                margin-bottom: 25px;
                transition: box-shadow 0.3s ease;
            }
            
            .houzez-size-group:hover {
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            }
            
            .houzez-size-group h3 {
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
                margin-top: 0;
                margin-bottom: 15px;
                font-size: 18px;
                display: flex;
                align-items: center;
                color: #2271b1;
            }
            
            .houzez-size-group h3 .dashicons {
                margin-right: 10px;
                color: #2271b1;
                font-size: 20px;
                width: 20px;
                height: 20px;
            }
            
            .houzez-size-group .description {
                color: #646970;
                font-size: 13px;
                margin-bottom: 20px;
            }
            
            /* Information and warning boxes */
            .image-size-note {
                background-color: #f0f6fc;
                padding: 18px 20px;
                border-left: 4px solid #2271b1;
                margin: 20px 0;
                border-radius: 0 4px 4px 0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            
            .image-size-warning {
                background-color: #fcf9e8;
                padding: 18px 20px;
                border-left: 4px solid #d63638;
                margin: 20px 0;
                border-radius: 0 4px 4px 0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            
            .image-size-warning p,
            .image-size-note p {
                margin: 0 0 10px 0;
                line-height: 1.6;
            }
            
            .image-size-warning p:last-child,
            .image-size-note p:last-child {
                margin-bottom: 0;
            }
            
            .image-size-warning strong,
            .image-size-note strong {
                color: #23282d;
            }
            
            /* Toggle & form elements styling */
            .enable-settings-toggle {
                margin-bottom: 18px;
            }
            
            .default-value {
                color: #646970;
                font-style: italic;
                font-size: 0.9em;
                display: block;
                margin-top: 6px;
            }
            
            input[type="number"].small-text {
                width: 80px !important;
                height: 36px;
                padding: 0 8px;
                border-radius: 4px;
            }
            
            /* New size form styling */
            #add-new-size-form {
                background: #fff;
                padding: 15px 22px;
                border: 1px solid #dcdcde;
                margin: 15px 0;
                border-radius: 8px;
                box-shadow: 0 1px 5px rgba(0,0,0,0.05);
                display: none;
                position: relative;
                overflow: hidden;
                width: 100%;
                left: 0;
                transition: all 0.3s ease;
                transform: translateY(0);
            }
            
            #add-new-size-form .houzez-size-group {
                margin-bottom: 0;
            }
            
            #add-new-size-form h3 {
                margin-top: 0;
                padding-bottom: 10px;
                margin-bottom: 10px;
                font-size: 16px;
            }
            
            #add-new-size-form .form-table {
                margin-top: 0;
            }
            
            #add-new-size-form .form-table th {
                padding: 8px 10px 8px 0;
                width: 120px;
            }
            
            #add-new-size-form .form-table td {
                padding: 8px 0;
            }
            
            #add-new-size-form .description {
                margin: 4px 0 0;
                font-size: 12px;
            }
            
            #add-new-size-form input[type="text"] {
                width: 100%;
                max-width: 300px;
                height: 32px;
            }
            
            #add-new-size-form input[type="number"] {
                width: 80px !important;
                height: 32px;
            }
            
            #add-new-size-form input[type="checkbox"] {
                margin-top: 2px;
                vertical-align: middle;
            }
            
            #add-new-size-form input[type="checkbox"] + .description {
                display: inline-block;
                margin-left: 10px;
                vertical-align: middle;
            }
            
            #add-new-size-form .submit-wrapper {
                padding: 10px 0 0 !important;
                margin-top: 10px !important;
                border-top: 1px solid #f0f0f1;
            }
            
            /* Tables styling */
            .image-sizes-table {
                table-layout: fixed;
                border-collapse: separate;
                border-spacing: 0;
                margin-top: 15px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                border-radius: 8px;
                overflow: hidden;
            }
            
            .image-sizes-table th {
                background-color: #f9f9f9;
                border-bottom: 1px solid #dcdcde;
                padding: 12px 15px;
                text-align: left;
                font-weight: 600;
                color: #23282d;
            }
            
            .image-sizes-table td {
                padding: 15px;
                vertical-align: middle;
                border-bottom: 1px solid #f0f0f1;
            }
            
            .image-sizes-table tr:last-child td {
                border-bottom: none;
            }
            
            .image-sizes-table tr:hover td {
                background-color: #f6f7f7;
            }
            
            .image-sizes-table .column-name {
                width: 20%;
                font-weight: 500;
            }
            
            .image-sizes-table .column-slug {
                display: none; /* Hide slug column */
            }
            
            .image-sizes-table .column-width,
            .image-sizes-table .column-height {
                width: 15%;
            }
            
            .image-sizes-table .column-crop,
            .image-sizes-table .column-enabled {
                width: 10%;
                text-align: center;
            }
            
            .image-sizes-table .column-actions {
                width: 20%;
            }
            
            .table-section {
                background-color: #f0f6fc;
            }
            
            .table-section td {
                padding: 14px 18px;
                font-weight: 600;
                color: #2271b1;
                border-top: 1px solid #dcdcde;
            }
            
            .table-section td .description {
                font-weight: normal;
                font-size: 12px;
                margin-top: 5px;
                color: #646970;
            }
            
            /* Layout Image Assignments styling */
            .layout-assignments-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                grid-gap: 20px;
                margin-top: 20px;
            }
            
            .layout-assignment-item {
                border: 1px solid #dcdcde;
                border-radius: 8px;
                padding: 18px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,0.04);
                transition: all 0.25s ease;
            }
            
            .layout-assignment-item:hover {
                border-color: #2271b1;
                box-shadow: 0 3px 10px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }
            
            .layout-assignment-item .layout-label {
                margin-bottom: 12px;
                font-weight: 600;
                color: #2c3338;
                font-size: 14px;
            }
            
            .layout-assignment-item .layout-control select {
                width: 100%;
                height: 36px;
                padding: 0 10px;
                border-radius: 4px;
            }
            
            /* Toggle Switch styling - improved */
            .switch {
                position: relative;
                display: inline-block;
                width: 46px;
                height: 22px;
                vertical-align: middle;
            }
            
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .3s;
                border-radius: 22px;
            }
            
            .slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .3s;
                border-radius: 50%;
                box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            }
            
            input:checked + .slider {
                background-color: #2271b1;
            }
            
            input:focus + .slider {
                box-shadow: 0 0 2px #2271b1;
            }
            
            input:checked + .slider:before {
                transform: translateX(24px);
            }
            
            /* Buttons styling */
            .button {
                height: 36px;
                padding: 0 16px;
                line-height: 34px;
                font-size: 13px;
                font-weight: 500;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            
            .button-primary {
                background: #2271b1;
                border-color: #2271b1;
                color: #fff;
            }
            
            .button-primary:hover {
                background: #135e96;
                border-color: #135e96;
            }
            
            .tablenav .button {
                margin-right: 5px;
            }
            
            .button-small {
                height: 30px;
                line-height: 28px;
                padding: 0 10px;
                font-size: 12px;
            }
            
            .button + .button {
                margin-left: 8px;
            }
            
            /* Add New Image Size button styling */
            #toggle-add-new-size {
                display: flex !important;
                align-items: center;
                justify-content: center;
                padding: 0 15px;
                height: 40px;
                font-weight: 500;
            }
            
            #toggle-add-new-size .dashicons {
                margin-right: 8px;
                font-size: 16px;
                width: 16px;
                height: 16px;
                margin-top: 0;
            }
            
            /* Submit button area */
            .submit-wrapper {
                margin-top: 25px;
                display: flex;
                align-items: center;
                padding: 15px 0;
                border-top: 1px solid #f0f0f1;
            }
            
            .assignment-help {
                margin-left: 20px;
                font-style: italic;
                color: #646970;
            }
            
            /* Tab content wrapper */
            .tab-content {
                margin-top: 25px;
                animation: fadeIn 0.3s ease-in-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(5px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            /* Dialog styling improvements */
            .ui-dialog {
                border-radius: 8px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
                border: none !important;
                padding: 0 !important;
                min-width: 500px !important;
                max-width: 90% !important;
                position: fixed;
            }
            
            .ui-dialog .ui-dialog-titlebar {
                background: #2271b1 !important;
                color: #fff !important;
                border: none !important;
                border-radius: 8px 8px 0 0 !important;
                padding: 15px 20px !important;
                font-size: 16px !important;
                font-weight: 500 !important;
                position: relative;
            }

            .ui-dialog .ui-dialog-titlebar-close {
                position: absolute !important;
                right: 10px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                width: 30px !important;
                height: 30px !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
                cursor: pointer !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                z-index: 1 !important;
                display: none !important;
            }

            .ui-dialog .ui-dialog-titlebar-close:before {
                content: "×" !important;
                font-size: 22px !important;
                color: #fff !important;
                opacity: 0.8;
                transition: opacity 0.2s;
                line-height: 1 !important;
                font-weight: 400 !important;
            }

            .ui-dialog .ui-dialog-titlebar-close:hover:before {
                opacity: 1;
            }

            .ui-dialog .ui-dialog-titlebar-close .ui-icon,
            .ui-dialog .ui-dialog-titlebar-close .ui-button-icon {
                display: none !important;
            }
            
            .ui-dialog .ui-dialog-content {
                padding: 25px !important;
                position: relative !important;
            }

            .ui-dialog .ui-dialog-content .form-table {
                margin-top: 0;
            }

            .ui-dialog .ui-dialog-content .form-table th {
                padding: 15px 10px 15px 0;
                width: 140px;
                font-weight: 500;
            }

            .ui-dialog .ui-dialog-content .form-table td {
                padding: 15px 0;
            }

            .ui-dialog .ui-dialog-content input[type="text"],
            .ui-dialog .ui-dialog-content input[type="number"] {
                width: 100%;
                max-width: 250px;
                padding: 6px 8px;
                height: 35px;
                border-radius: 4px;
            }

            .ui-dialog .ui-dialog-content input[type="checkbox"] {
                margin-top: 2px;
            }
            
            .ui-dialog .ui-dialog-buttonpane {
                border-top: 1px solid #dcdcde !important;
                background: #f6f7f7 !important;
                padding: 15px 20px !important;
                margin-top: 0 !important;
                border-radius: 0 0 8px 8px !important;
            }

            .ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
                float: none !important;
                text-align: right !important;
            }
            
            .ui-dialog .ui-button {
                background: #fff !important;
                border: 1px solid #c3c4c7 !important;
                color: #2c3338 !important;
                border-radius: 4px !important;
                padding: 8px 20px !important;
                font-size: 13px !important;
                font-weight: 500 !important;
                transition: all 0.2s !important;
                height: auto !important;
                line-height: normal !important;
                min-height: 36px !important;
            }
            
            .ui-dialog .ui-button:first-of-type {
                background: #2271b1 !important;
                color: #fff !important;
                border-color: #2271b1 !important;
            }
            
            .ui-dialog .ui-button:first-of-type:hover {
                background: #135e96 !important;
                border-color: #135e96 !important;
            }
            
            .ui-dialog .ui-button:hover {
                background: #f0f0f1 !important;
            }

            /* Edit form in dialog */
            #edit-size-form {
                margin: 0;
                padding: 0;
            }
            
            #edit-size-form .form-table {
                margin: 0;
            }

            #edit-size-form .form-table th {
                width: 140px;
                padding: 15px 10px 15px 0;
                font-weight: 500;
            }
            
            #edit-size-form .form-table td {
                padding: 15px 0;
            }

            #edit-size-form label {
                display: inline-block;
                margin-bottom: 5px;
                color: #1d2327;
            }

            #edit-size-form .description {
                color: #646970;
                font-size: 12px;
                margin-top: 4px;
                display: block;
            }

            /* Checkbox styling */
            .ui-dialog .checkbox-wrap {
                position: relative;
                display: inline-block;
            }

            .ui-dialog input[type="checkbox"] {
                margin: 0 8px 0 0;
                vertical-align: middle;
            }

            .ui-dialog .checkbox-label {
                vertical-align: middle;
                color: #2c3338;
                font-size: 13px;
            }
            
            /* Action buttons styling */
            .reset-houzez-size,
            .edit-custom-size,
            .delete-custom-size,
            a[href*="options-media.php"],
            .button-small {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                min-width: 80px;
                text-align: center;
                height: 32px;
                line-height: 1 !important;
                padding: 0 12px !important;
                text-decoration: none;
                vertical-align: middle;
                box-sizing: border-box;
            }

            /* Fix for action buttons in table cells */
            .image-sizes-table td .button {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                line-height: 1 !important;
                height: 32px;
                padding: 0 12px !important;
                font-size: 13px;
                min-width: 70px;
                margin: 2px;
                text-align: center;
            }

            /* Fix table cells with action buttons */
            .image-sizes-table td {
                vertical-align: middle;
            }

            /* Icon-only button */
            .image-sizes-table .view-assignments {
                min-width: 32px !important;
                padding: 0 !important;
                width: 32px;
            }
        ';
        wp_add_inline_style('wp-admin', $custom_css);

        // Add JavaScript to handle the enable/disable toggle and custom image sizes
        $custom_js = '
            jQuery(document).ready(function($) {
                // Variable to store layout assignments
                var imageAssignments = ' . json_encode($image_assignments) . ';
                var layoutLabels = ' . json_encode($layout_labels) . ';
                
                // Function to check if image size is assigned to any layout
                function checkImageSizeAssignments(sizeId) {
                    var assignedLayouts = [];
                    
                    $.each(imageAssignments, function(layout, size) {
                        if (size === sizeId) {
                            assignedLayouts.push(layoutLabels[layout] || layout);
                        }
                    });
                    
                    return assignedLayouts;
                }
                
                // View Assignments button handler
                $(".view-assignments").on("click", function(e) {
                    e.preventDefault();
                    var sizeId = $(this).data("size");
                    var assignedLayouts = checkImageSizeAssignments(sizeId);
                    
                    var dialogTitle = "' . esc_js(__('Image Size Assignments', 'houzez')) . '";
                    var dialogContent = "";
                    
                    if(assignedLayouts.length > 0) {
                        var layoutsList = "<ul style=\"margin-left: 20px; list-style-type: disc;\">";
                        $.each(assignedLayouts, function(i, layout) {
                            layoutsList += "<li>" + layout + "</li>";
                        });
                        layoutsList += "</ul>";
                        
                        dialogContent = "<p>' . esc_js(__('This image size is currently assigned to the following layouts:', 'houzez')) . '</p>" + layoutsList;
                    } else {
                        dialogContent = "<p>' . esc_js(__('This image size is not currently assigned to any layouts.', 'houzez')) . '</p>";
                    }
                    
                    // Create and show dialog
                    $("<div title=\"" + dialogTitle + "\"></div>").html(dialogContent).dialog({
                        resizable: false,
                        modal: true,
                        width: 400,
                        buttons: {
                            "' . esc_js(__('Go to Layout Assignments', 'houzez')) . '": function() {
                                window.location.href = "?page=houzez_image_sizes&tab=layout_assignments";
                            },
                            "' . esc_js(__('Close', 'houzez')) . '": function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
                
                // Toggle add new size form
                $("#toggle-add-new-size").on("click", function(e) {
                    e.preventDefault();
                    $("#add-new-size-form").slideToggle(300);
                });
                
                $("#cancel_add_new_size").on("click", function(e) {
                    e.preventDefault();
                    $("#add-new-size-form").slideUp(300);
                    // Clear form
                    $("#new_size_name").val("");
                    $("#new_size_width").val("");
                    $("#new_size_height").val("");
                    $("#new_size_crop").prop("checked", false);
                });
                
                // Handle the Houzez size toggles with assignment check
                $(".houzez-size-toggle").on("change", function() {
                    var prefix = $(this).data("prefix");
                    var sizeId = getCorrectSizeId(prefix);
                    var inputs = $("input[name^=\'" + prefix + "\']").not($(this)).not("[type=hidden]");
                    var hiddenFields = $("input[name^=\'" + prefix + "_hidden\']");
                    
                    if(!$(this).is(":checked")) {
                        // Check if this size is assigned to any layouts
                        var assignedLayouts = checkImageSizeAssignments(sizeId);
                        
                        if(assignedLayouts.length > 0) {
                            // Prevent toggle and show warning
                            $(this).prop("checked", true);
                            
                            var layoutsList = "<ul style=\"margin-left: 20px; list-style-type: disc;\">";
                            $.each(assignedLayouts, function(i, layout) {
                                layoutsList += "<li>" + layout + "</li>";
                            });
                            layoutsList += "</ul>";
                            
                            // Create and show dialog
                            $("<div title=\"' . esc_js(__('Cannot Disable Image Size', 'houzez')) . '\"></div>").html(
                                "<p>' . esc_js(__('This image size is currently assigned to the following layouts:', 'houzez')) . '</p>" + 
                                layoutsList + 
                                "<p>' . esc_js(__('Please unassign this image size from these layouts in the Layout Image Assignments tab before disabling it.', 'houzez')) . '</p>"
                            ).dialog({
                                resizable: false,
                                modal: true,
                                width: 400,
                                buttons: {
                                    "' . esc_js(__('Go to Layout Assignments', 'houzez')) . '": function() {
                                        window.location.href = "?page=houzez_image_sizes&tab=layout_assignments";
                                    },
                                    "' . esc_js(__('Cancel', 'houzez')) . '": function() {
                                        $(this).dialog("close");
                                    }
                                }
                            });
                            
                            return false;
                        }
                        
                        // Disable the fields but preserve their values with hidden fields
                        inputs.each(function() {
                            var name = $(this).attr("name");
                            var value = $(this).val();
                            
                            // For checkboxes, we need to handle differently
                            if($(this).attr("type") === "checkbox") {
                                value = $(this).is(":checked") ? "1" : "0";
                            }
                            
                            // Create hidden field with same name to preserve value
                            var hiddenField = $("<input>")
                                .attr("type", "hidden")
                                .attr("name", name + "_hidden")
                                .attr("data-original", name)
                                .val(value);
                                
                            $(this).after(hiddenField);
                        });
                        
                        // Now disable visible fields
                        inputs.prop("disabled", true);
                    } else {
                        // Enable the fields and use their values
                        inputs.prop("disabled", false);
                        
                        // Clear any hidden fields
                        hiddenFields.remove();
                    }
                });
                
                // Handle WordPress core size toggles with assignment check
                $(".wp-core-size-toggle").on("change", function() {
                    var sizeName = $(this).data("size");
                    
                    if(!$(this).is(":checked")) {
                        // Check if this size is assigned to any layouts
                        var assignedLayouts = checkImageSizeAssignments(sizeName);
                        
                        if(assignedLayouts.length > 0) {
                            // Prevent toggle and show warning
                            $(this).prop("checked", true);
                            
                            var layoutsList = "<ul style=\"margin-left: 20px; list-style-type: disc;\">";
                            $.each(assignedLayouts, function(i, layout) {
                                layoutsList += "<li>" + layout + "</li>";
                            });
                            layoutsList += "</ul>";
                            
                            // Create and show dialog
                            $("<div title=\"' . esc_js(__('Cannot Disable Image Size', 'houzez')) . '\"></div>").html(
                                "<p>' . esc_js(__('This image size is currently assigned to the following layouts:', 'houzez')) . '</p>" + 
                                layoutsList + 
                                "<p>' . esc_js(__('Please unassign this image size from these layouts in the Layout Image Assignments tab before disabling it.', 'houzez')) . '</p>"
                            ).dialog({
                                resizable: false,
                                modal: true,
                                width: 400,
                                buttons: {
                                    "' . esc_js(__('Go to Layout Assignments', 'houzez')) . '": function() {
                                        window.location.href = "?page=houzez_image_sizes&tab=layout_assignments";
                                    },
                                    "' . esc_js(__('Cancel', 'houzez')) . '": function() {
                                        $(this).dialog("close");
                                    }
                                }
                            });
                            
                            return false;
                        }
                    }
                });
                
                // Form submission handling - properly handle disabled fields
                $("form").on("submit", function(e) {
                    // Before submit, process hidden fields for disabled sections
                    $("input[name$=_hidden]").each(function() {
                        var originalName = $(this).data("original");
                        $(this).attr("name", originalName);
                    });
                });
                
                // Reset to default values
                $(".reset-houzez-size").on("click", function() {
                    var prefix = $(this).data("prefix");
                    var width = $(this).data("width");
                    var height = $(this).data("height");
                    var crop = $(this).data("crop");
                    
                    $("input[name=\'" + prefix + "_w\']").val(width);
                    $("input[name=\'" + prefix + "_h\']").val(height);
                    
                    // Force reset the crop checkbox (directly set to true since all default values use crop=1)
                    $("input[name=\'" + prefix + "_crop\']").prop("checked", true);
                    
                    // Log the action for debugging
                    console.log("Reset " + prefix + " to defaults - Width: " + width + ", Height: " + height + ", Crop set to: true");
                });
                
                // Custom Image Sizes Handling
                $("#add_new_size").on("click", function(e) {
                    e.preventDefault();
                    
                    var name = $("#new_size_name").val();
                    var width = $("#new_size_width").val();
                    var height = $("#new_size_height").val();
                    var crop = $("#new_size_crop").is(":checked") ? 1 : 0;
                    
                    if(!name || !width || !height) {
                        alert("' . esc_js(__('Please fill in all required fields.', 'houzez')) . '");
                        return;
                    }
                    
                    // Send AJAX request to add the new size
                    $.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {
                            action: "houzez_add_custom_image_size",
                            name: name,
                            width: width,
                            height: height,
                            crop: crop,
                            nonce: "' . wp_create_nonce('houzez_image_sizes_nonce') . '"
                        },
                        success: function(response) {
                            if(response.success) {
                                window.location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        }
                    });
                });
                
                // Edit Custom Size
                $(".edit-custom-size").on("click", function(e) {
                    e.preventDefault();
                    
                    var name = $(this).data("name");
                    
                    // Get current size data via AJAX
                    $.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {
                            action: "houzez_get_custom_image_size",
                            name: name,
                            nonce: "' . wp_create_nonce('houzez_image_sizes_nonce') . '"
                        },
                        success: function(response) {
                            if(response.success) {
                                var data = response.data;
                                
                                // Populate edit form
                                $("#edit_size_original_name").val(name);
                                $("#edit_size_name").val(name);
                                $("#edit_size_width").val(data.width);
                                $("#edit_size_height").val(data.height);
                                
                                if(data.crop) {
                                    $("#edit_size_crop").prop("checked", true);
                                } else {
                                    $("#edit_size_crop").prop("checked", false);
                                }
                                
                                if(data.enabled) {
                                    $("#edit_size_enabled").prop("checked", true);
                                } else {
                                    $("#edit_size_enabled").prop("checked", false);
                                }
                                
                                // Open dialog
                                $("#edit-size-dialog").dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 400,
                                    modal: true,
                                    buttons: {
                                        "' . esc_js(__('Save Changes', 'houzez')) . '": function() {
                                            var originalName = $("#edit_size_original_name").val();
                                            var newName = $("#edit_size_name").val();
                                            var width = $("#edit_size_width").val();
                                            var height = $("#edit_size_height").val();
                                            var crop = $("#edit_size_crop").is(":checked") ? 1 : 0;
                                            var enabled = $("#edit_size_enabled").is(":checked") ? 1 : 0;
                                            
                                            if(!newName || !width || !height) {
                                                alert("' . esc_js(__('Please fill in all required fields.', 'houzez')) . '");
                                                return;
                                            }
                                            
                                            // Check if size is assigned to layouts
                                            if(!enabled) {
                                                var sizeId = sanitizeTitle(originalName);
                                                var assignedLayouts = checkImageSizeAssignments(sizeId);
                                                
                                                if(assignedLayouts.length > 0) {
                                                    alert("' . esc_js(__('This image size is assigned to layouts and cannot be disabled. Please unassign it first.', 'houzez')) . '");
                                                    return;
                                                }
                                            }
                                            
                                            // Send AJAX request to update the size
                                            $.ajax({
                                                url: ajaxurl,
                                                type: "POST",
                                                data: {
                                                    action: "houzez_update_custom_image_size",
                                                    original_name: originalName,
                                                    name: newName,
                                                    width: width,
                                                    height: height,
                                                    crop: crop,
                                                    enabled: enabled,
                                                    nonce: "' . wp_create_nonce('houzez_image_sizes_nonce') . '"
                                                },
                                                success: function(response) {
                                                    if(response.success) {
                                                        window.location.reload();
                                                    } else {
                                                        alert(response.data.message);
                                                    }
                                                }
                                            });
                                            
                                            $(this).dialog("close");
                                        },
                                        "' . esc_js(__('Cancel', 'houzez')) . '": function() {
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                            } else {
                                alert(response.data.message);
                            }
                        }
                    });
                });
                
                // Delete Custom Size
                $(".delete-custom-size").on("click", function(e) {
                    e.preventDefault();
                    
                    var name = $(this).data("name");
                    var sizeId = sanitizeTitle(name);
                    
                    // Check if size is assigned to layouts
                    var assignedLayouts = checkImageSizeAssignments(sizeId);
                    
                    if(assignedLayouts.length > 0) {
                        var layoutsList = "<ul style=\"margin-left: 20px; list-style-type: disc;\">";
                        $.each(assignedLayouts, function(i, layout) {
                            layoutsList += "<li>" + layout + "</li>";
                        });
                        layoutsList += "</ul>";
                        
                        // Create and show dialog
                        $("<div title=\"' . esc_js(__('Cannot Delete Image Size', 'houzez')) . '\"></div>").html(
                            "<p>' . esc_js(__('This image size is currently assigned to the following layouts:', 'houzez')) . '</p>" + 
                            layoutsList + 
                            "<p>' . esc_js(__('Please unassign this image size from these layouts in the Layout Image Assignments tab before deleting it.', 'houzez')) . '</p>"
                        ).dialog({
                            resizable: false,
                            modal: true,
                            width: 400,
                            buttons: {
                                "' . esc_js(__('Go to Layout Assignments', 'houzez')) . '": function() {
                                    window.location.href = "?page=houzez_image_sizes&tab=layout_assignments";
                                },
                                "' . esc_js(__('Cancel', 'houzez')) . '": function() {
                                    $(this).dialog("close");
                                }
                            }
                        });
                        
                        return false;
                    }
                    
                    if(!confirm("' . esc_js(__('Are you sure you want to delete this image size? This cannot be undone.', 'houzez')) . '")) {
                        return;
                    }
                    
                    // Send AJAX request to delete the size
                    $.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {
                            action: "houzez_delete_custom_image_size",
                            name: name,
                            nonce: "' . wp_create_nonce('houzez_image_sizes_nonce') . '"
                        },
                        success: function(response) {
                            if(response.success) {
                                window.location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        }
                    });
                });
                
                // Initialize toggle states on page load
                $(".houzez-size-toggle").each(function() {
                    var prefix = $(this).data("prefix");
                    var inputs = $("input[name^=\'" + prefix + "\']").not(this);
                    
                    if(!$(this).is(":checked")) {
                        // Create hidden fields to preserve values even when disabled
                        inputs.each(function() {
                            var name = $(this).attr("name");
                            var value = $(this).val();
                            
                            // For checkboxes, we need to handle differently
                            if($(this).attr("type") === "checkbox") {
                                value = $(this).is(":checked") ? "1" : "0";
                            }
                            
                            // Create hidden field with same name to preserve value
                            var hiddenField = $("<input>")
                                .attr("type", "hidden")
                                .attr("name", name + "_hidden")
                                .attr("data-original", name)
                                .val(value);
                                
                            $(this).after(hiddenField);
                        });
                        
                        inputs.prop("disabled", true);
                    }
                });

                // Handle Custom Size Toggle with assignment check
                $(".custom-size-toggle").on("change", function() {
                    var name = $(this).data("name");
                    var sizeId = sanitizeTitle(name);
                    var enabled = $(this).is(":checked") ? 1 : 0;
                    
                    if(!enabled) {
                        // Check if this size is assigned to any layouts
                        var assignedLayouts = checkImageSizeAssignments(sizeId);
                        
                        if(assignedLayouts.length > 0) {
                            // Prevent toggle and show warning
                            $(this).prop("checked", true);
                            
                            var layoutsList = "<ul style=\"margin-left: 20px; list-style-type: disc;\">";
                            $.each(assignedLayouts, function(i, layout) {
                                layoutsList += "<li>" + layout + "</li>";
                            });
                            layoutsList += "</ul>";
                            
                            // Create and show dialog
                            $("<div title=\"' . esc_js(__('Cannot Disable Image Size', 'houzez')) . '\"></div>").html(
                                "<p>' . esc_js(__('This image size is currently assigned to the following layouts:', 'houzez')) . '</p>" + 
                                layoutsList + 
                                "<p>' . esc_js(__('Please unassign this image size from these layouts in the Layout Image Assignments tab before disabling it.', 'houzez')) . '</p>"
                            ).dialog({
                                resizable: false,
                                modal: true,
                                width: 400,
                                buttons: {
                                    "' . esc_js(__('Go to Layout Assignments', 'houzez')) . '": function() {
                                        window.location.href = "?page=houzez_image_sizes&tab=layout_assignments";
                                    },
                                    "' . esc_js(__('Cancel', 'houzez')) . '": function() {
                                        $(this).dialog("close");
                                    }
                                }
                            });
                            
                            return false;
                        }
                        
                        // Continue with AJAX to update the size
                        $.ajax({
                            url: ajaxurl,
                            type: "POST",
                            data: {
                                action: "houzez_update_custom_image_size",
                                original_name: name,
                                name: name,
                                enabled: enabled,
                                update_enabled_only: true,
                                nonce: "' . wp_create_nonce('houzez_image_sizes_nonce') . '"
                            },
                            success: function(response) {
                                if(!response.success) {
                                    alert(response.data.message);
                                    // Revert toggle if failed
                                    $(this).prop("checked", !enabled);
                                }
                            }.bind(this)
                        });
                    } else {
                        // If enabling, proceed with AJAX
                        $.ajax({
                            url: ajaxurl,
                            type: "POST",
                            data: {
                                action: "houzez_update_custom_image_size",
                                original_name: name,
                                name: name,
                                enabled: enabled,
                                update_enabled_only: true,
                                nonce: "' . wp_create_nonce('houzez_image_sizes_nonce') . '"
                            },
                            success: function(response) {
                                if(!response.success) {
                                    alert(response.data.message);
                                    // Revert toggle if failed
                                    $(this).prop("checked", !enabled);
                                }
                            }.bind(this)
                        });
                    }
                });
                
                // Helper function to sanitize title (similar to WordPress sanitize_title)
                function sanitizeTitle(text) {
                    return text.toLowerCase()
                        .replace(/[^a-z0-9_\-]/g, "-")
                        .replace(/-+/g, "-")
                        .replace(/^-|-$/g, "");
                }
                
                // Function to get the correct size ID based on prefix
                function getCorrectSizeId(prefix) {
                    var sizeMap = {
                        "houzez_gallery": "houzez-gallery",
                        "houzez_top_v7": "houzez-top-v7",
                        "houzez_item_image_6": "houzez-item-image-6"
                    };
                    
                    return sizeMap[prefix] || "houzez-" + prefix.replace("houzez_", "");
                }
            });
        ';
        wp_add_inline_script('jquery', $custom_js);
    }

    /**
     * Add the submenu page to Houzez menu
     */
    public function add_submenu_page( $sub_menus ) {
        $sub_menus['houzez_image_sizes'] = array(
            'houzez_dashboard',
            esc_html__( 'Media Manager', 'houzez' ),
            esc_html__( 'Media Manager', 'houzez' ),
            'manage_options',
            'houzez_image_sizes',
            array( $this, 'render_page' )
        );
        
        return $sub_menus;
    }

    /**
     * Register all settings for the image sizes
     */
    public function register_settings() {
        // Register image assignments setting
        register_setting(
            'houzez_layout_image_assignments_group',
            'houzez_layout_image_assignments',
            array(
                'type'              => 'array',
                'sanitize_callback' => array($this, 'sanitize_image_assignments'),
                'default'           => array(),
            )
        );

        // Register WordPress core size disable options
        register_setting( 'houzez_image_sizes_group', 'houzez_enable_thumbnail_size',  [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => false,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_enable_medium_size',  [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => false,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_enable_medium_large_size',  [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => false,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_enable_large_size',  [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => false,
        ] );
        
        // Register enable settings
        register_setting( 'houzez_image_sizes_group', 'houzez_enable_gallery_size',  [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => true,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_enable_top_v7_size',  [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => true,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_enable_item_image_6_size',  [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => true,
        ] );

        // Register individual size settings
        register_setting( 'houzez_image_sizes_group', 'houzez_gallery_w',  [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 1170,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_gallery_h',  [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 785,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_gallery_crop', [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => true,
        ] );

        register_setting( 'houzez_image_sizes_group', 'houzez_top_v7_w',  [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 780,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_top_v7_h',  [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 780,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_top_v7_crop', [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => true,
        ] );

        register_setting( 'houzez_image_sizes_group', 'houzez_item_image_6_w',  [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 584,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_item_image_6_h',  [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 438,
        ] );
        register_setting( 'houzez_image_sizes_group', 'houzez_item_image_6_crop', [
            'type'              => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default'           => true,
        ] );

        // Add a single section to hold all fields
        add_settings_section(
            'houzez_image_sizes_section',
            '',
            array( $this, 'section_description' ),
            'houzez_image_sizes'  // Page slug
        );
    }

    /**
     * Settings section description
     */
    public function section_description() {
        ?>
        <div class="image-size-warning">
            <p>
                <strong><?php esc_html_e('Important:', 'houzez'); ?></strong> 
                <?php esc_html_e('Changes to these settings will only apply to newly uploaded images. For existing images, you will need to regenerate thumbnails.', 'houzez'); ?>
                <a href="<?php echo esc_url(admin_url('plugin-install.php?s=force-regenerate-thumbnails&tab=search&type=term')); ?>" target="_blank">
                    <?php esc_html_e('Install Force Regenerate Thumbnails plugin', 'houzez'); ?>
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * Render the settings page
     */
    public function render_page() {
        
        // Get default sizes for reference
        $defaults = array(
            'gallery' => array(
                'width' => 1170,
                'height' => 785,
                'crop' => 1
            ),
            'top_v7' => array(
                'width' => 780,
                'height' => 780,
                'crop' => 1
            ),
            'item_image_6' => array(
                'width' => 584,
                'height' => 438,
                'crop' => 1
            )
        );

        // Get custom sizes
        $custom_sizes = get_option('houzez_custom_image_sizes', array());
        
        // Get WordPress core sizes
        $wp_core_sizes = array(
            'thumbnail' => array(
                'width' => get_option('thumbnail_size_w', 150),
                'height' => get_option('thumbnail_size_h', 150),
                'crop' => (bool) get_option('thumbnail_crop', 1),
                'enabled' => get_option('houzez_enable_thumbnail_size', false),
                'core' => true
            ),
            'medium' => array(
                'width' => get_option('medium_size_w', 300),
                'height' => get_option('medium_size_h', 300),
                'crop' => false,
                'enabled' => get_option('houzez_enable_medium_size', false),
                'core' => true
            ),
            'medium_large' => array(
                'width' => get_option('medium_large_size_w', 768),
                'height' => get_option('medium_large_size_h', 0),
                'crop' => false,
                'enabled' => get_option('houzez_enable_medium_large_size', false),
                'core' => true
            ),
            'large' => array(
                'width' => get_option('large_size_w', 1024),
                'height' => get_option('large_size_h', 1024),
                'crop' => false,
                'enabled' => get_option('houzez_enable_large_size', false),
                'core' => true
            )
        );
        
        // Get Houzez built-in sizes
        $houzez_sizes = array(
            'Image Variation v1' => array(
                'width' => get_option('houzez_item_image_6_w', $defaults['item_image_6']['width']),
                'height' => get_option('houzez_item_image_6_h', $defaults['item_image_6']['height']),
                'crop' => (bool) get_option('houzez_item_image_6_crop', $defaults['item_image_6']['crop']),
                'enabled' => (bool) get_option('houzez_enable_item_image_6_size', true),
                'option_prefix' => 'houzez_item_image_6',
                'default_width' => $defaults['item_image_6']['width'],
                'default_height' => $defaults['item_image_6']['height'],
                'default_crop' => $defaults['item_image_6']['crop']
            ),
            'Image Variation v2' => array(
                'width' => get_option('houzez_top_v7_w', $defaults['top_v7']['width']),
                'height' => get_option('houzez_top_v7_h', $defaults['top_v7']['height']),
                'crop' => (bool) get_option('houzez_top_v7_crop', $defaults['top_v7']['crop']),
                'enabled' => (bool) get_option('houzez_enable_top_v7_size', true),
                'option_prefix' => 'houzez_top_v7',
                'default_width' => $defaults['top_v7']['width'],
                'default_height' => $defaults['top_v7']['height'],
                'default_crop' => $defaults['top_v7']['crop']
            ),
            'Image Variation v3' => array(
                'width' => get_option('houzez_gallery_w', $defaults['gallery']['width']),
                'height' => get_option('houzez_gallery_h', $defaults['gallery']['height']),
                'crop' => (bool) get_option('houzez_gallery_crop', $defaults['gallery']['crop']),
                'enabled' => (bool) get_option('houzez_enable_gallery_size', true),
                'option_prefix' => 'houzez_gallery',
                'default_width' => $defaults['gallery']['width'],
                'default_height' => $defaults['gallery']['height'],
                'default_crop' => $defaults['gallery']['crop']
            )
        );

        // Prepare available image sizes for dropdowns
        $available_image_sizes = array();
        
        // Add "full" size (original uploaded image)
        $available_image_sizes['full'] = esc_html__('Full Size (Original Image)', 'houzez');
        
        // Add WordPress core sizes
        foreach ($wp_core_sizes as $slug => $size) {
            if (get_option('houzez_enable_' . $slug . '_size', true)) {
                $width = $size['width'];
                $height = $size['height'] ?: '(auto)';
                $available_image_sizes[$slug] = sprintf('%s (%dx%s) [WordPress]', ucfirst($slug), $width, $height);
            }
        }
        
        // Add Houzez sizes
        foreach ($houzez_sizes as $name => $size) {
            // Use explicit slugs for built-in Houzez sizes to ensure they match the registered sizes
            $slug_map = array(
                'Image Variation v1' => 'houzez-item-image-6',
                'Image Variation v2' => 'houzez-top-v7',
                'Image Variation v3' => 'houzez-gallery'
            );
            
            // Use the explicit slug from the map if available, otherwise fall back to sanitize_title
            $slug = isset($slug_map[$name]) ? $slug_map[$name] : sanitize_title('houzez-' . $name);
            
            if ($size['enabled']) {
                $available_image_sizes[$slug] = sprintf('%s (%dx%d) [Houzez]', $name, $size['width'], $size['height']);
            }
        }
        
        // Add custom sizes
        if (!empty($custom_sizes) && is_array($custom_sizes)) {
            foreach ($custom_sizes as $name => $size) {
                if (!empty($size['enabled'])) {
                    $slug = sanitize_title($name);
                    $available_image_sizes[$slug] = sprintf('%s (%dx%d) [Custom]', $name, $size['width'], $size['height']);
                }
            }
        }
        
        // Get layout image assignments
        $image_assignments = get_option('houzez_layout_image_assignments', $this->default_assignments);

        // Determine active tab
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'layout_assignments';
        
        // Calculate stats for the header
        $total_sizes = count($houzez_sizes) + count($wp_core_sizes) + count($custom_sizes);
        $enabled_sizes = 0;
        foreach ($houzez_sizes as $size) {
            if ($size['enabled']) $enabled_sizes++;
        }
        foreach ($wp_core_sizes as $size) {
            if ($size['enabled']) $enabled_sizes++;
        }
        foreach ($custom_sizes as $size) {
            if (!empty($size['enabled'])) $enabled_sizes++;
        }
        $custom_count = count($custom_sizes);
        $assignments_count = count($image_assignments);
        ?>
        <div class="wrap houzez-template-library">
            <div class="houzez-header">
                <div class="houzez-header-content">
                    <div class="houzez-logo">
                        <h1><?php esc_html_e('Media Manager', 'houzez'); ?></h1>
                    </div>
                    <div class="houzez-header-actions">
                        
                    </div>
                </div>
            </div>

            <div class="houzez-dashboard">
                <!-- Quick Stats -->
                <div class="houzez-stats-grid">
                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-format-image"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo esc_html($total_sizes); ?></h3>
                            <p><?php esc_html_e('Total Image Sizes', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-yes-alt"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo esc_html($enabled_sizes); ?></h3>
                            <p><?php esc_html_e('Enabled Sizes', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-admin-customizer"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo esc_html($custom_count); ?></h3>
                            <p><?php esc_html_e('Custom Sizes', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-admin-links"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo esc_html($assignments_count); ?></h3>
                            <p><?php esc_html_e('Layout Assignments', 'houzez'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="houzez-main-card">
                    <div class="houzez-card-header">
                        <h2>
                            <i class="dashicons dashicons-admin-media"></i>
                            <?php esc_html_e('Image Size Management', 'houzez'); ?>
                        </h2>
                        <p class="houzez-description">
                            <?php esc_html_e('Control how images are displayed throughout your website by managing dimensions, cropping, and layout assignments.', 'houzez'); ?>
                        </p>
                    </div>
                    <div class="houzez-card-body">
                        <h2 class="nav-tab-wrapper">
                            <a href="?page=houzez_image_sizes&tab=layout_assignments" class="nav-tab <?php echo $active_tab == 'layout_assignments' ? 'nav-tab-active' : ''; ?>">
                                <?php esc_html_e('Layout Image Assignments', 'houzez'); ?>
                            </a>
                            <a href="?page=houzez_image_sizes&tab=manage_sizes" class="nav-tab <?php echo $active_tab == 'manage_sizes' ? 'nav-tab-active' : ''; ?>">
                                <?php esc_html_e('Manage Image Sizes', 'houzez'); ?>
                            </a>
                        </h2>

                        <?php if ($active_tab == 'layout_assignments') : ?>
                            <!-- Layout Image Assignments Tab -->
                            <div id="layout-assignments-tab" class="tab-content">
                                <p class="description">
                                    <?php esc_html_e('Assign specific image sizes to different areas of your website. This controls which image dimensions are used in each layout element.', 'houzez'); ?>
                                </p>
                                
                                <form method="post" action="options.php">
                                    <?php settings_fields('houzez_layout_image_assignments_group'); ?>
                                    
                                    <!-- Group 1: Property Listings -->
                                    <div class="houzez-size-group">
                                        <h3>
                                            <span class="dashicons dashicons-grid-view"></span> 
                                            <?php esc_html_e('Property Listings', 'houzez'); ?>
                                        </h3>
                                        <p class="description"><?php esc_html_e('Control image sizes for property listings across different views.', 'houzez'); ?></p>
                                        
                                        <div class="layout-assignments-grid">
                                            <?php 
                                            $listing_elements = [
                                                'listing_grid_v1' => esc_html__('Listing Grid v1', 'houzez'),
                                                'listing_grid_v2' => esc_html__('Listing Grid v2', 'houzez'),
                                                'listing_grid_v3' => esc_html__('Listing Grid v3', 'houzez'),
                                                'listing_grid_v4' => esc_html__('Listing Grid v4', 'houzez'),
                                                'listing_grid_v5' => esc_html__('Listing Grid v5', 'houzez'),
                                                'listing_grid_v6' => esc_html__('Listing Grid v6', 'houzez'),
                                                'listing_grid_v7' => esc_html__('Listing Grid v7', 'houzez'),
                                                'listing_list_v1' => esc_html__('Listing List v1', 'houzez'),
                                                'listing_list_v2' => esc_html__('Listing List v2', 'houzez'),
                                                'listing_list_v4' => esc_html__('Listing List v4', 'houzez'),
                                                'listing_list_v7' => esc_html__('Listing List v7', 'houzez'),
                                                
                                            ];
                                            
                                            foreach ($listing_elements as $key => $label) : 
                                            ?>
                                                <div class="layout-assignment-item">
                                                    <div class="layout-label">
                                                        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                                                    </div>
                                                    <div class="layout-control">
                                                        <select name="houzez_layout_image_assignments[<?php echo esc_attr($key); ?>]" id="<?php echo esc_attr($key); ?>" class="regular-text">
                                                            <?php foreach ($available_image_sizes as $size_key => $size_label) : ?>
                                                                <option value="<?php echo esc_attr($size_key); ?>" <?php selected(isset($image_assignments[$key]) ? $image_assignments[$key] : 'houzez-item-image-6', $size_key); ?>>
                                                                    <?php echo esc_html($size_label); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Group 2: Property Detail Pages -->
                                    <div class="houzez-size-group">
                                        <h3>
                                            <span class="dashicons dashicons-admin-home"></span> 
                                            <?php esc_html_e('Property Detail Pages', 'houzez'); ?>
                                        </h3>
                                        <p class="description"><?php esc_html_e('Control image sizes for property detail pages and sliders.', 'houzez'); ?></p>
                                        
                                        <div class="layout-assignments-grid">
                                            <?php 
                                            $detail_elements = [
                                                'property_detail_v1' => esc_html__('Property Detail v1', 'houzez'),
                                                'property_detail_v2' => esc_html__('Property Detail v2', 'houzez'),
                                                'property_detail_v3-4' => esc_html__('Property Detail v3-4', 'houzez'),
                                                'property_detail_v5' => esc_html__('Property Detail v5', 'houzez'),
                                                'property_detail_v6' => esc_html__('Property Detail v6', 'houzez'),
                                                'property_detail_v7' => esc_html__('Property Detail v7', 'houzez'),
                                                'property_detail_block_gallery' => esc_html__('Property Detail Block Gallery', 'houzez'),
                                            ];
                                            
                                            foreach ($detail_elements as $key => $label) : 
                                            ?>
                                                <div class="layout-assignment-item">
                                                    <div class="layout-label">
                                                        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                                                    </div>
                                                    <div class="layout-control">
                                                        <select name="houzez_layout_image_assignments[<?php echo esc_attr($key); ?>]" id="<?php echo esc_attr($key); ?>" class="regular-text">
                                                            <?php foreach ($available_image_sizes as $size_key => $size_label) : ?>
                                                                <option value="<?php echo esc_attr($size_key); ?>" <?php selected(isset($image_assignments[$key]) ? $image_assignments[$key] : 'houzez-item-image-6', $size_key); ?>>
                                                                    <?php echo esc_html($size_label); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Group 3: Profiles and Blog -->
                                    <div class="houzez-size-group">
                                        <h3>
                                            <span class="dashicons dashicons-admin-users"></span> 
                                            <?php esc_html_e('Others', 'houzez'); ?>
                                        </h3>
                                        <p class="description"><?php esc_html_e('Control image sizes for agent/agency and blog posts.', 'houzez'); ?></p>
                                        
                                        <div class="layout-assignments-grid">
                                            <?php 
                                            $profile_blog_elements = [
                                                'agent_profile' => esc_html__('Agent', 'houzez'),
                                                'agency_profile' => esc_html__('Agency', 'houzez'),
                                                'blog_post' => esc_html__('Blog Post', 'houzez'),
                                                'blog_grid' => esc_html__('Blog Grid', 'houzez'),
                                            ];
                                            
                                            foreach ($profile_blog_elements as $key => $label) : 
                                            ?>
                                                <div class="layout-assignment-item">
                                                    <div class="layout-label">
                                                        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                                                    </div>
                                                    <div class="layout-control">
                                                        <select name="houzez_layout_image_assignments[<?php echo esc_attr($key); ?>]" id="<?php echo esc_attr($key); ?>" class="regular-text">
                                                            <?php foreach ($available_image_sizes as $size_key => $size_label) : ?>
                                                                <option value="<?php echo esc_attr($size_key); ?>" <?php selected(isset($image_assignments[$key]) ? $image_assignments[$key] : 'houzez-item-image-6', $size_key); ?>>
                                                                    <?php echo esc_html($size_label); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="submit-wrapper">
                                        <?php submit_button(__('Save Assignments', 'houzez'), 'primary', 'submit', false); ?> &nbsp
                                        <p class="description assignment-help">
                                            <?php esc_html_e('Tip: You can create custom image sizes in the "Manage Image Sizes" tab if you need specific dimensions.', 'houzez'); ?>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        <?php else : ?>
                            <!-- Manage Image Sizes Tab -->
                            <div id="manage-sizes-tab" class="tab-content">
                                <form method="post" action="options.php">
                                    <?php settings_fields('houzez_image_sizes_group'); ?>
                                    <?php do_settings_sections('houzez_image_sizes'); ?>
                                    
                                    <div class="tablenav top">
                                        <div class="alignleft actions">
                                            <a href="#" id="toggle-add-new-size" class="button button-primary">
                                                <span class="dashicons dashicons-plus"></span>
                                                <?php esc_html_e('Add New Image Size', 'houzez'); ?>
                                            </a>
                                        </div>
                                        <br class="clear">
                                    </div>
                                    
                                    <!-- Add New Size Form (initially hidden) -->
                                    <div id="add-new-size-form">
                                        <div class="houzez-size-group">
                                            <h3><?php esc_html_e('Create New Custom Image Size', 'houzez'); ?></h3>
                                            <table class="form-table">
                                                <tr>
                                                    <th scope="row"><?php esc_html_e('Name', 'houzez'); ?></th>
                                                    <td>
                                                        <input type="text" id="new_size_name" name="new_size_name" class="regular-text" />
                                                        <p class="description"><?php esc_html_e('A descriptive name for this image size', 'houzez'); ?></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php esc_html_e('Width', 'houzez'); ?></th>
                                                    <td>
                                                        <input type="number" id="new_size_width" name="new_size_width" class="small-text" />
                                                        <p class="description"><?php esc_html_e('Width in pixels', 'houzez'); ?></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php esc_html_e('Height', 'houzez'); ?></th>
                                                    <td>
                                                        <input type="number" id="new_size_height" name="new_size_height" class="small-text" />
                                                        <p class="description"><?php esc_html_e('Height in pixels', 'houzez'); ?></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php esc_html_e('Hard Crop?', 'houzez'); ?></th>
                                                    <td>
                                                        <label for="new_size_crop">
                                                            <input type="checkbox" id="new_size_crop" name="new_size_crop" value="1" />
                                                            <span class="description"><?php esc_html_e('Whether to crop the image to exact dimensions', 'houzez'); ?></span>
                                                        </label>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div class="submit-wrapper" style="padding: 10px 0;">
                                                <button type="button" id="add_new_size" class="button button-primary">
                                                    <?php esc_html_e('Add Custom Image Size', 'houzez'); ?>
                                                </button>
                                                <button type="button" id="cancel_add_new_size" class="button button-secondary">
                                                    <?php esc_html_e('Cancel', 'houzez'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Image sizes unified table -->
                                    <table class="wp-list-table widefat fixed striped image-sizes-table">
                                        <thead>
                                            <tr>
                                                <th class="column-name"><?php esc_html_e('Name', 'houzez'); ?></th>
                                                <th class="column-slug"><?php esc_html_e('Slug', 'houzez'); ?></th>
                                                <th class="column-width"><?php esc_html_e('Width', 'houzez'); ?></th>
                                                <th class="column-height"><?php esc_html_e('Height', 'houzez'); ?></th>
                                                <th class="column-crop"><?php esc_html_e('Crop', 'houzez'); ?></th>
                                                <th class="column-enabled"><?php esc_html_e('Enabled', 'houzez'); ?></th>
                                                <th class="column-actions"><?php esc_html_e('Actions', 'houzez'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Houzez Built-in Sizes -->
                                            <tr class="table-section">
                                                <td colspan="6">
                                                    <strong><?php esc_html_e('Houzez Built-in Sizes', 'houzez'); ?></strong>
                                                    <span class="description"><?php esc_html_e('These optimized image sizes are specifically designed for Houzez theme property listings, galleries, and various layout components to ensure optimal performance and visual quality.', 'houzez'); ?></span>
                                                </td>
                                            </tr>
                                            <!-- Loop through sizes in the desired order (v1, v2, v3) -->
                                            <?php 
                                            $ordered_sizes = array(
                                                'Image Variation v1' => $houzez_sizes['Image Variation v1'],
                                                'Image Variation v2' => $houzez_sizes['Image Variation v2'],
                                                'Image Variation v3' => $houzez_sizes['Image Variation v3']
                                            );
                                            foreach ($ordered_sizes as $name => $size) : ?>
                                                <tr class="houzez-size-row">
                                                    <td><?php echo esc_html($name); ?></td>
                                                    <td class="column-slug">
                                                        <?php 
                                                        $slug_map = array(
                                                            'Image Variation v1' => 'houzez-item-image-6',
                                                            'Image Variation v2' => 'houzez-top-v7',
                                                            'Image Variation v3' => 'houzez-gallery'
                                                        );
                                                        echo isset($slug_map[$name]) ? esc_html($slug_map[$name]) : 'houzez-' . esc_html(sanitize_title($name)); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="<?php echo esc_attr($size['option_prefix']); ?>_w" 
                                                            value="<?php echo esc_attr($size['width']); ?>" 
                                                            class="small-text" <?php disabled(!$size['enabled']); ?> />
                                                        <span class="default-value">
                                                            (<?php esc_html_e('Default', 'houzez'); ?>: <?php echo esc_html($size['default_width']); ?>px)
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="<?php echo esc_attr($size['option_prefix']); ?>_h" 
                                                            value="<?php echo esc_attr($size['height']); ?>" 
                                                            class="small-text" <?php disabled(!$size['enabled']); ?> />
                                                        <span class="default-value">
                                                            (<?php esc_html_e('Default', 'houzez'); ?>: <?php echo esc_html($size['default_height']); ?>px)
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" name="<?php echo esc_attr($size['option_prefix']); ?>_crop" 
                                                            value="1" <?php checked($size['crop'], true); ?> <?php disabled(!$size['enabled']); ?> />
                                                    </td>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" name="houzez_enable_<?php echo esc_attr(str_replace('houzez_', '', $size['option_prefix'])); ?>_size" 
                                                                value="1" <?php checked($size['enabled'], true); ?> 
                                                                class="houzez-size-toggle" data-prefix="<?php echo esc_attr($size['option_prefix']); ?>" />
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="button button-small reset-houzez-size" 
                                                            data-prefix="<?php echo esc_attr($size['option_prefix']); ?>"
                                                            data-width="<?php echo esc_attr($size['default_width']); ?>"
                                                            data-height="<?php echo esc_attr($size['default_height']); ?>"
                                                            data-crop="<?php echo $size['default_crop'] ? '1' : '0'; ?>">
                                                            <?php esc_html_e('Reset to Default', 'houzez'); ?>
                                                        </button>
                                                        <a href="#" class="button button-small view-assignments" data-size="<?php echo isset($slug_map[$name]) ? esc_attr($slug_map[$name]) : esc_attr('houzez-' . sanitize_title($name)); ?>">
                                                            <span class="dashicons dashicons-visibility" style="margin:0;font-size:14px;width:14px;height:14px;"></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            
                                            <!-- WordPress Core Sizes -->
                                            <tr class="table-section">
                                                <td colspan="6">
                                                    <strong><?php esc_html_e('WordPress Core Sizes', 'houzez'); ?></strong>
                                                    <span class="description"><?php esc_html_e('These sizes are defined by WordPress core. You can disable generation but dimensions must be changed in Media Settings.', 'houzez'); ?></span>
                                                </td>
                                            </tr>
                                            <?php foreach ($wp_core_sizes as $name => $size) : ?>
                                                <tr class="core-size-row">
                                                    <td><?php echo esc_html(ucfirst($name)); ?></td>
                                                    <td class="column-slug"><?php echo esc_html($name); ?></td>
                                                    <td><?php echo esc_html($size['width']); ?></td>
                                                    <td><?php echo esc_html($size['height']); ?></td>
                                                    <td><?php echo $size['crop'] ? esc_html__('Yes', 'houzez') : esc_html__('No', 'houzez'); ?></td>
                                                    <td>
                                                        <!-- Hidden field to ensure unchecked toggles are tracked -->
                                                        <input type="hidden" name="houzez_core_sizes_submitted[]" value="<?php echo esc_attr($name); ?>">
                                                        
                                                        <label class="switch">
                                                            <input type="checkbox" name="houzez_enable_<?php echo esc_attr($name); ?>_size" 
                                                                   value="1" <?php checked(get_option('houzez_enable_' . $name . '_size', true), true); ?> 
                                                                   class="wp-core-size-toggle"
                                                                   data-size="<?php echo esc_attr($name); ?>" />
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo esc_url(admin_url('options-media.php')); ?>" class="button button-small">
                                                            <?php esc_html_e('Edit in Media Settings', 'houzez'); ?>
                                                        </a>
                                                        <a href="#" class="button button-small view-assignments" data-size="<?php echo esc_attr($name); ?>">
                                                            <span class="dashicons dashicons-visibility" style="margin:0;font-size:14px;width:14px;height:14px;"></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            
                                            <!-- Custom Image Sizes -->
                                            <tr class="table-section">
                                                <td colspan="6">
                                                    <strong><?php esc_html_e('Custom Image Sizes', 'houzez'); ?></strong>
                                                    <span class="description"><?php esc_html_e('These are custom image sizes that you have created.', 'houzez'); ?></span>
                                                </td>
                                            </tr>
                                            <?php if (!empty($custom_sizes) && is_array($custom_sizes)) : ?>
                                                <?php foreach ($custom_sizes as $name => $size) : ?>
                                                    <tr class="custom-size-row">
                                                        <td><?php echo esc_html($name); ?></td>
                                                        <td class="column-slug"><?php echo esc_html(sanitize_title($name)); ?></td>
                                                        <td><?php echo esc_html($size['width']); ?></td>
                                                        <td><?php echo esc_html($size['height']); ?></td>
                                                        <td><?php echo $size['crop'] ? esc_html__('Yes', 'houzez') : esc_html__('No', 'houzez'); ?></td>
                                                        <td>
                                                            <label class="switch">
                                                                <input type="checkbox" name="custom_size_enabled_<?php echo esc_attr(sanitize_title($name)); ?>"
                                                                       value="1" <?php checked(!empty($size['enabled']), true); ?> 
                                                                       class="custom-size-toggle"
                                                                       data-name="<?php echo esc_attr($name); ?>" />
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <a href="#" class="button button-small edit-custom-size" data-name="<?php echo esc_attr($name); ?>">
                                                                <?php esc_html_e('Edit', 'houzez'); ?>
                                                            </a>
                                                            <a href="#" class="button button-small delete-custom-size" data-name="<?php echo esc_attr($name); ?>">
                                                                <?php esc_html_e('Delete', 'houzez'); ?>
                                                            </a>
                                                            <a href="#" class="button button-small view-assignments" data-size="<?php echo esc_attr(sanitize_title($name)); ?>">
                                                                <span class="dashicons dashicons-visibility" style="margin:0;font-size:14px;width:14px;height:14px;"></span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr class="no-custom-sizes">
                                                    <td colspan="6"><?php esc_html_e('No custom image sizes defined.', 'houzez'); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    
                                    <?php submit_button(__('Save All Changes', 'houzez')); ?>
                                </form>
                                
                            </div>
                        <?php endif; ?>

                        <!-- Edit Modal Dialog -->
                        <div id="edit-size-dialog" style="display:none;" title="<?php esc_attr_e('Edit Custom Image Size', 'houzez'); ?>">
                            <form id="edit-size-form">
                                <input type="hidden" id="edit_size_original_name" name="edit_size_original_name" value="" />
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Name', 'houzez'); ?></th>
                                        <td>
                                            <input type="text" id="edit_size_name" name="edit_size_name" class="regular-text" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Width', 'houzez'); ?></th>
                                        <td>
                                            <input type="number" id="edit_size_width" name="edit_size_width" class="small-text" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Height', 'houzez'); ?></th>
                                        <td>
                                            <input type="number" id="edit_size_height" name="edit_size_height" class="small-text" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Hard Crop?', 'houzez'); ?></th>
                                        <td>
                                            <input type="checkbox" id="edit_size_crop" name="edit_size_crop" value="1" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Enabled?', 'houzez'); ?></th>
                                        <td>
                                            <input type="checkbox" id="edit_size_enabled" name="edit_size_enabled" value="1" />
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Apply the settings when registering image sizes
     */
    public function setup_dynamic_image_sizes() {
        // Define default sizes as constants so they're always available
        $defaults = array(
            'gallery' => array(
                'width' => 1170,
                'height' => 785,
                'crop' => 1
            ),
            'top_v7' => array(
                'width' => 780,
                'height' => 780,
                'crop' => 1
            ),
            'item_image_6' => array(
                'width' => 584,
                'height' => 438,
                'crop' => 1
            )
        );

        // Store defaults for reference in admin UI
        update_option('houzez_image_sizes_defaults', $defaults);

        // Set default image assignments if not already set
        $current_assignments = get_option('houzez_layout_image_assignments', array());
        if (empty($current_assignments)) {
            update_option('houzez_layout_image_assignments', $this->default_assignments);
        }

        // Image Variation v3 (houzez-gallery)
        if (get_option('houzez_enable_gallery_size', true)) {
            $gw = get_option('houzez_gallery_w', $defaults['gallery']['width']);
            $gh = get_option('houzez_gallery_h', $defaults['gallery']['height']);
            $gc = get_option('houzez_gallery_crop', $defaults['gallery']['crop']);
            // Ensure we don't register sizes with 0 dimensions
            if ($gw > 0 && $gh > 0) {
                add_image_size('houzez-gallery', absint($gw), absint($gh), (bool)$gc);
            }
        }

        // Image Variation v2 (houzez-top-v7)
        if (get_option('houzez_enable_top_v7_size', true)) {
            $tw = get_option('houzez_top_v7_w', $defaults['top_v7']['width']);
            $th = get_option('houzez_top_v7_h', $defaults['top_v7']['height']);
            $tc = get_option('houzez_top_v7_crop', $defaults['top_v7']['crop']);
            // Ensure we don't register sizes with 0 dimensions
            if ($tw > 0 && $th > 0) {
                add_image_size('houzez-top-v7', absint($tw), absint($th), (bool)$tc);
            }
        }

        // Property Card (formerly Item-image-6)
        if (get_option('houzez_enable_item_image_6_size', true)) {
            $iw = get_option('houzez_item_image_6_w', $defaults['item_image_6']['width']);
            $ih = get_option('houzez_item_image_6_h', $defaults['item_image_6']['height']);
            $ic = get_option('houzez_item_image_6_crop', $defaults['item_image_6']['crop']);
            // Ensure we don't register sizes with 0 dimensions
            if ($iw > 0 && $ih > 0) {
                add_image_size('houzez-item-image-6', absint($iw), absint($ih), (bool)$ic);
            }
        }

        // Handle custom image sizes
        $custom_sizes = get_option('houzez_custom_image_sizes', array());
        
        if (!empty($custom_sizes) && is_array($custom_sizes)) {
            foreach ($custom_sizes as $size_key => $size_data) {
                if (!empty($size_data['enabled']) && $size_data['enabled'] && 
                    $size_data['width'] > 0 && $size_data['height'] > 0) {
                    add_image_size(
                        sanitize_title($size_key),  // Removed 'houzez-' prefix
                        absint($size_data['width']),
                        absint($size_data['height']), 
                        (bool)$size_data['crop']
                    );
                }
            }
        }
    }

    /**
     * Filter WordPress image sizes to remove disabled ones
     */
    public function filter_image_sizes( $sizes ) {
        // Check if WordPress core sizes are disabled
        if ( !get_option( 'houzez_enable_thumbnail_size', true ) ) {
            unset( $sizes['thumbnail'] );
        }
        
        if ( !get_option( 'houzez_enable_medium_size', true ) ) {
            unset( $sizes['medium'] );
        }
        
        if ( !get_option( 'houzez_enable_medium_large_size', true ) ) {
            unset( $sizes['medium_large'] );
        }
        
        if ( !get_option( 'houzez_enable_large_size', true ) ) {
            unset( $sizes['large'] );
        }
        
        return $sizes;
    }

    /**
     * AJAX handler for adding a custom image size
     */
    public function ajax_add_custom_image_size() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'houzez_image_sizes_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'houzez')));
        }

        // Get and validate data
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $width = isset($_POST['width']) ? absint($_POST['width']) : 0;
        $height = isset($_POST['height']) ? absint($_POST['height']) : 0;
        $crop = isset($_POST['crop']) ? (bool)($_POST['crop']) : false;

        // Validate inputs
        if (empty($name) || empty($width) || empty($height)) {
            wp_send_json_error(array('message' => __('Please provide all required fields.', 'houzez')));
        }

        // Get existing custom sizes
        $custom_sizes = get_option('houzez_custom_image_sizes', array());

        // Check if name already exists
        if (isset($custom_sizes[$name])) {
            wp_send_json_error(array('message' => __('An image size with this name already exists.', 'houzez')));
        }

        // Add new size
        $custom_sizes[$name] = array(
            'width' => $width,
            'height' => $height,
            'crop' => $crop,
            'enabled' => true
        );

        // Save updated sizes
        update_option('houzez_custom_image_sizes', $custom_sizes);

        wp_send_json_success();
    }

    /**
     * AJAX handler for getting a custom image size
     */
    public function ajax_get_custom_image_size() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'houzez_image_sizes_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'houzez')));
        }

        // Get and validate data
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';

        // Validate inputs
        if (empty($name)) {
            wp_send_json_error(array('message' => __('Invalid image size name.', 'houzez')));
        }

        // Get existing custom sizes
        $custom_sizes = get_option('houzez_custom_image_sizes', array());

        // Check if name exists
        if (!isset($custom_sizes[$name])) {
            wp_send_json_error(array('message' => __('Image size not found.', 'houzez')));
        }

        // Return the size data
        wp_send_json_success($custom_sizes[$name]);
    }

    /**
     * AJAX handler for updating a custom image size
     */
    public function ajax_update_custom_image_size() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'houzez_image_sizes_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'houzez')));
        }

        // Get and validate data
        $original_name = isset($_POST['original_name']) ? sanitize_text_field($_POST['original_name']) : '';
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $update_enabled_only = isset($_POST['update_enabled_only']) && $_POST['update_enabled_only'];
        $enabled = isset($_POST['enabled']) ? (bool)($_POST['enabled']) : false;

        // For enabled-only toggle from the data table
        if ($update_enabled_only) {
            if (empty($original_name)) {
                wp_send_json_error(array('message' => __('Invalid image size name.', 'houzez')));
            }

            // Get existing custom sizes
            $custom_sizes = get_option('houzez_custom_image_sizes', array());

            // Check if original name exists
            if (!isset($custom_sizes[$original_name])) {
                wp_send_json_error(array('message' => __('Image size not found.', 'houzez')));
            }

            // Update only the enabled status
            $custom_sizes[$original_name]['enabled'] = $enabled;

            // Save updated sizes
            update_option('houzez_custom_image_sizes', $custom_sizes);

            wp_send_json_success();
            return;
        }

        // For full edit from the edit dialog
        $width = isset($_POST['width']) ? absint($_POST['width']) : 0;
        $height = isset($_POST['height']) ? absint($_POST['height']) : 0;
        $crop = isset($_POST['crop']) ? (bool)($_POST['crop']) : false;

        // Validate inputs
        if (empty($original_name) || empty($name) || empty($width) || empty($height)) {
            wp_send_json_error(array('message' => __('Please provide all required fields.', 'houzez')));
        }

        // Get existing custom sizes
        $custom_sizes = get_option('houzez_custom_image_sizes', array());

        // Check if original name exists
        if (!isset($custom_sizes[$original_name])) {
            wp_send_json_error(array('message' => __('Original image size not found.', 'houzez')));
        }

        // Check if new name already exists (if different from original)
        if ($name !== $original_name && isset($custom_sizes[$name])) {
            wp_send_json_error(array('message' => __('An image size with this name already exists.', 'houzez')));
        }

        // If name changed, remove old entry
        if ($name !== $original_name) {
            unset($custom_sizes[$original_name]);
        }

        // Add/update the size
        $custom_sizes[$name] = array(
            'width' => $width,
            'height' => $height,
            'crop' => $crop,
            'enabled' => $enabled
        );

        // Save updated sizes
        update_option('houzez_custom_image_sizes', $custom_sizes);

        wp_send_json_success();
    }

    /**
     * AJAX handler for deleting a custom image size
     */
    public function ajax_delete_custom_image_size() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'houzez_image_sizes_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'houzez')));
        }

        // Get and validate data
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';

        // Validate inputs
        if (empty($name)) {
            wp_send_json_error(array('message' => __('Invalid image size name.', 'houzez')));
        }

        // Get existing custom sizes
        $custom_sizes = get_option('houzez_custom_image_sizes', array());

        // Check if name exists
        if (!isset($custom_sizes[$name])) {
            wp_send_json_error(array('message' => __('Image size not found.', 'houzez')));
        }

        // Remove the size
        unset($custom_sizes[$name]);

        // Save updated sizes
        update_option('houzez_custom_image_sizes', $custom_sizes);

        wp_send_json_success();
    }

    /**
     * Sanitize the image assignments array
     */
    public function sanitize_image_assignments($input) {
        $sanitized_input = array();
        
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $sanitized_input[sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        return $sanitized_input;
    }

    /**
     * Check if an image size is assigned to any layouts
     * 
     * @param string $size_name The image size name to check
     * @return array Array of layouts using this size or empty array if none
     */
    public function check_image_size_assignments($size_name) {
        $image_assignments = get_option('houzez_layout_image_assignments', array());
        $assigned_layouts = array();
        
        if (!empty($image_assignments)) {
            foreach ($image_assignments as $layout => $assigned_size) {
                if ($assigned_size === $size_name) {
                    // Get a human-readable layout name
                    $layout_name = str_replace('_', ' ', $layout);
                    $layout_name = ucwords($layout_name);
                    $assigned_layouts[$layout] = $layout_name;
                }
            }
        }
        
        return $assigned_layouts;
    }

    /**
     * Get enabled image sizes with formatted labels for Elementor dropdowns
     * 
     * This method retrieves all enabled image sizes (WordPress core, Houzez built-in, and custom)
     * and formats them with dimensions for display in Elementor dropdowns.
     * 
     * @since 4.0
     * @return array Associative array of enabled image sizes with formatted labels
     *               Format: [size_slug => 'Size Name (WidthxHeight)']
     */
    public static function get_enabled_image_sizes() {
        
        // Initialize array to store all available sizes
        $available_sizes = array();

        
        $available_sizes['global'] = __('Global (Use Media Settings)', 'houzez');
        
        // Add "full" (original) size option
        $available_sizes['full'] = __('Full Size (Original)', 'houzez');
        
        // Add WordPress core sizes if they are enabled in settings
        $wp_core_sizes = array('thumbnail', 'medium', 'medium_large', 'large');
        foreach ($wp_core_sizes as $size) {
            if (get_option('houzez_enable_' . $size . '_size', false)) {
                $width = get_option($size . '_size_w', 0);
                $height = get_option($size . '_size_h', 0);
                $height_label = $height ? $height : 'auto';
                $available_sizes[$size] = sprintf('%s (%dx%s)', ucfirst($size), $width, $height_label);
            }
        }
        
        // Get default sizes for reference in case the option values are empty
        $defaults = array(
            'gallery' => array(
                'width' => 1170,
                'height' => 785
            ),
            'top_v7' => array(
                'width' => 780,
                'height' => 780
            ),
            'item_image_6' => array(
                'width' => 584,
                'height' => 438
            )
        );
        
        // Add Houzez built-in sizes if they are enabled in settings
        $houzez_sizes = array(
            'houzez-gallery' => 'Image Variation v3',
            'houzez-top-v7' => 'Image Variation v2',
            'houzez-item-image-6' => 'Image Variation v1'
        );
        
        foreach ($houzez_sizes as $slug => $label) {
            // Convert slug format to option name format (e.g., 'houzez-gallery' to 'houzez_gallery')
            $option_prefix = str_replace('-', '_', str_replace('houzez-', 'houzez_', $slug));
            
            // Map option prefix to default key
            $default_key = str_replace('houzez_', '', $option_prefix);
            
            // Check if this size is enabled (defaults to true for built-in sizes)
            if (get_option('houzez_enable_' . str_replace('houzez_', '', $option_prefix) . '_size', true)) {
                // Get dimensions to include in the label, fallback to defaults if empty
                $width = get_option($option_prefix . '_w', isset($defaults[$default_key]['width']) ? $defaults[$default_key]['width'] : 0);
                $height = get_option($option_prefix . '_h', isset($defaults[$default_key]['height']) ? $defaults[$default_key]['height'] : 0);
                
                $available_sizes[$slug] = sprintf('%s (%dx%d)', $label, $width, $height);
            }
        }
        
        // Add custom user-defined sizes if they are enabled
        $custom_sizes = get_option('houzez_custom_image_sizes', array());
        if (!empty($custom_sizes) && is_array($custom_sizes)) {
            foreach ($custom_sizes as $name => $size_data) {
                if (!empty($size_data['enabled'])) {
                    $slug = sanitize_title($name);
                    $available_sizes[$slug] = sprintf('%s (%dx%d)', $name, $size_data['width'], $size_data['height']);
                }
            }
        }
        
        return $available_sizes;
    }

    /**
     * Get all disabled image sizes
     * 
     * @return array Array of disabled image size names
     */
    public function get_disabled_image_sizes() {
        $disabled_sizes = array();
        
        // Check WordPress core sizes
        $wp_core_sizes = array('thumbnail', 'medium', 'medium_large', 'large');
        foreach ($wp_core_sizes as $size) {
            if (!get_option('houzez_enable_' . $size . '_size', false)) {
                $disabled_sizes[] = $size;
            }
        }
        
        // Check Houzez built-in sizes
        $houzez_sizes = array(
            'gallery' => 'houzez-gallery',
            'top_v7' => 'houzez-top-v7',
            'item_image_6' => 'houzez-item-image-6'
        );
        
        foreach ($houzez_sizes as $option_key => $size_name) {
            if (!get_option('houzez_enable_' . $option_key . '_size', true)) {
                $disabled_sizes[] = $size_name;
            }
        }
        
        // Check custom sizes
        $custom_sizes = get_option('houzez_custom_image_sizes', array());
        if (!empty($custom_sizes)) {
            foreach ($custom_sizes as $name => $size_data) {
                if (empty($size_data['enabled'])) {
                    $disabled_sizes[] = sanitize_title($name);
                }
            }
        }
        
        return $disabled_sizes;
    }

    /**
     * Get disabled image sizes for Elementor
     * 
     * @return array Array formatted for Elementor's 'exclude' parameter
     */
    public static function get_disabled_sizes_for_elementor() {
        $instance = self::instance();
        return $instance->get_disabled_image_sizes();
    }
    
    

    /**
     * Static wrapper for get_enabled_image_sizes method
     * 
     * Provides a convenient static access point for Elementor integration.
     * 
     * @since 4.0
     * @return array Array of enabled image sizes formatted for Elementor
     */
    public static function get_enabled_image_sizes_for_elementor() {
        $instance = self::instance();
        return $instance->get_enabled_image_sizes();
    }
    
    /**
     * Function to get the correct size ID based on option prefix
     * 
     * @param string $prefix The option prefix
     * @return string The correct size slug
     */
    private function get_correct_size_id($prefix) {
        $size_map = array(
            'houzez_gallery' => 'houzez-gallery',
            'houzez_top_v7' => 'houzez-top-v7',
            'houzez_item_image_6' => 'houzez-item-image-6'
        );
        
        return isset($size_map[$prefix]) ? $size_map[$prefix] : 'houzez-' . str_replace('houzez_', '', $prefix);
    }
}

// Initialize the class
Houzez_Image_Sizes::instance(); 