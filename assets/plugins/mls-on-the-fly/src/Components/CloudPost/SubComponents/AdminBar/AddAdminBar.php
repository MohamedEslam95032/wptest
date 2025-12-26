<?php
namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\AdminBar;
use Realtyna\Core\Abstracts\ComponentAbstract;

class AddAdminBar extends ComponentAbstract
{
    
    public function register(): void
    {
        add_action("wp_before_admin_bar_render", [
            $this,
            "addCustomMenuItemToAdminBar",
        ]);
        

        add_action('wp_footer', [$this,'mls_on_the_fly_admin_tab_content'], PHP_INT_MAX);

        // Set handler early
       add_action('init', function() {
            set_error_handler('Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\AdminBar\capture_live_errors');
            error_reporting(E_ALL);
            ini_set('display_errors', 0);
        });

        // Global variable to store errors
        global $live_php_errors;
        $live_php_errors = [];

        // Custom error handler
        function capture_live_errors($errno, $errstr, $errfile, $errline) {
            global $live_php_errors;
            
            $error_types = [
                E_ERROR => 'Fatal Error',
                E_WARNING => 'Warning',
                E_NOTICE => 'Notice',
                E_USER_ERROR => 'User Error',
                E_USER_WARNING => 'User Warning',
                E_USER_NOTICE => 'User Notice',
                E_DEPRECATED => 'Deprecated',
            ];
            
            $errtype = isset($error_types[$errno]) ? $error_types[$errno] : 'Unknown Error';
            $live_php_errors[] = "[$errtype] $errstr in $errfile on line $errline";
            
            return false;
        }
 
    }
    public function addCustomMenuItemToAdminBar()
    {
        global $wp_admin_bar, $wpdb;

        // Add the MLS-on the fly menu
        $wp_admin_bar->add_menu([
            "id" => "MLS-on-the-fly-admin-tab",
            "title" => "MLS On The Fly",
            "href" => admin_url("admin.php?page=mls-on-the-fly"),
            "meta" => [
                "title" => __("MLS On The Fly"),
            ],
        ]);
        // Only add these submenu items on the front-end, not in admin dashboard
        if (!is_admin()) {
            // Add the requests item
            $wp_admin_bar->add_menu([
                "id" => "monitor-mls-requests",
                "parent" => "MLS-on-the-fly-admin-tab",
                "title" => "Monitor MLS Requests",
                "href" => admin_url("admin.php?page=mls-on-the-fly"),
                "meta" => [
                    "title" => __("Monitor Requests"),
                ],
            ]);

            // Add the raw data item
            $wp_admin_bar->add_menu([
                "id" => "show-raw-data",
                "parent" => "MLS-on-the-fly-admin-tab",
                "title" => "Show Raw Data",
                "href" => admin_url("admin.php?page=mls-on-the-fly"),
                "meta" => [
                    "title" => __("Show Raw Data"),
                ],
            ]);

            // Add the errors item
            $wp_admin_bar->add_menu([
                "id" => "php-errors",
                "parent" => "MLS-on-the-fly-admin-tab",
                "title" => "PHP Errors",
                "href" => admin_url("admin.php?page=mls-on-the-fly"),
                "meta" => [
                    "title" => __("PHP Errors"),
                ],
            ]);
        }
        wp_register_script(
            "mls-on-the-fly-admin-tab",
            REALTYNA_MLS_ON_THE_FLY_URL .
                "assets/js/mls-on-the-fly-admin-tab.js",
            ["jquery"],
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );
        wp_enqueue_script("mls-on-the-fly-admin-tab");
    }

    function mls_on_the_fly_admin_tab_content() {
        $admin_bar_mls_requests = null;
        $admin_bar_mls_requests = apply_filters("mls_on_the_fly_requests", $admin_bar_mls_requests , 10, 3);

        $admin_bar_is_single = false;
        $admin_bar_is_single = apply_filters("mls_on_the_fly_is_single", $admin_bar_is_single , 10, 3);

        $admin_bar_single_raw_data = null; 
        $admin_bar_single_raw_data = apply_filters("mls_on_the_fly_raw_data", $admin_bar_single_raw_data , 10, 3);

        

        // Register the shortcode for raw data
        add_shortcode('admin_bar_raw_data', function () use ($admin_bar_single_raw_data) {  
            ob_start();
            if(!$admin_bar_single_raw_data){
                return '<h4>No single property request sent on this page.</h4>';
            }
            d($admin_bar_single_raw_data);
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        });
        // Register the shortcode for showing mls requests
        add_shortcode('admin_bar_mls_request', function () use ($admin_bar_mls_requests) {  
            ob_start();
            if(!$admin_bar_mls_requests){
                return '<h4>No RF requests sent on this page.</h4>';
            }
            d($admin_bar_mls_requests);
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        });

        // Register the shortcode to start output buffering to capture errors
        

        

        add_shortcode('admin_bar_mls_errors', function () {
            global $live_php_errors;
            
            if (!empty($live_php_errors)) {
                $output = '';
                $output .= '<div style="background: #2d2d2d; padding: 15px; border-radius: 6px; font-family: monospace; color: #f8f8f8; overflow-y: auto;">';
                
                foreach ($live_php_errors as $error) {
                    // Extract error type from the error message
                    preg_match('/\[(.*?)\]/', $error, $matches);
                    $error_type = isset($matches[1]) ? $matches[1] : 'Unknown Error';
                    
                    // Determine color based on error type
                    $color = '#f8f8f8'; // Default color
                    if (strpos($error_type, 'Fatal') !== false) {
                        $color = '#ff5252'; // Red for fatal errors
                    } elseif (strpos($error_type, 'Warning') !== false) {
                        $color = '#ffb86c'; // Orange for warnings
                    } elseif (strpos($error_type, 'Notice') !== false) {
                        $color = '#8be9fd'; // Light blue for notices
                    } elseif (strpos($error_type, 'Deprecated') !== false) {
                        $color = '#f1fa8c'; // Yellow for deprecated
                    } elseif (strpos($error_type, 'User') !== false) {
                        $color = '#bd93f9'; // Purple for user errors
                    }
                    
                    // Format the error message with color
                    $formatted_error = preg_replace('/\[(.*?)\]/', '<span style="color: ' . $color . '; font-weight: bold;">[$1]</span>', esc_html($error));
                    
                    // Add line highlighting
                    $output .= '<div style="padding: 8px; margin-bottom: 6px; border-left: 3px solid ' . $color . '; background: rgba(255,255,255,0.05);">' . $formatted_error . '</div>';
                }
                
                $output .= '</div>';
                return $output;
            } else {
                return '<div style="background: #f8f8f8; padding: 15px; border-radius: 6px; text-align: center; color: #555; border: 1px solid #ddd;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
                                <path fill="#555" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                            </svg>
                            <span style="font-size: 16px; font-weight: 500;">No errors found</span>
                        </div>';
            }
        });

        echo '<style>
        .mls-on-the-fly-admin-bar-tab-content {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background-color: #ece7f4;
            border: 2px solid #6a32c2;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            z-index: 1000;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            display: none;
        }
        
        /* Header Styles - Made narrower */
        .mls-on-the-fly-admin-bar-tab-header {
            position: relative;
            width: 100%;
            background-color: #6a32c2;
            color: #ffffff;
            border-bottom: 2px solid #ece7f4;
            padding: 8px 10px; /* Reduced padding from 15px to 8px */
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .mls-on-the-fly-admin-bar-tab-header:hover {
            cursor: ns-resize;
        }
        
        /* Smaller header title */
        .mls-on-the-fly-admin-bar-tab-header h3 {
            margin: 0;
            font-size: 16px; /* Reduced font size */
            line-height: 1.2; /* Tighter line height */
        }
        
        /* Main content container with sidebar and content area */
        .mls-on-the-fly-admin-bar-content-container {
            display: flex;
            flex-direction: row;
            height: calc(100% - 40px); /* Adjusted to account for smaller header */
            width: 100%;
        }
        
        /* Sidebar for tabs */
        .mls-on-the-fly-admin-bar-sidebar {
            width: 180px;
            background-color: #f0f0f0;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            padding: 15px 0;
        }
        
        /* Button Styles as vertical tabs */
        .mls-on-the-fly-admin-bar-tab-button {
            background-color: #ffffff;
            color: #6a32c2;
            border: none;
            border-left: 5px solid #6a32c2;
            padding: 12px 15px;
            margin: 5px 0;
            font-size: 14px;
            cursor: pointer;
            text-align: left;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }
        
        .mls-on-the-fly-admin-bar-tab-button-deactive {
            background-color: transparent;
            color: #6a32c2;
            border: none;
            border-left: 5px solid transparent;
            padding: 12px 15px;
            margin: 5px 0;
            font-size: 14px;
            cursor: pointer;
            text-align: left;
            font-weight: normal;
            transition: background-color 0.2s ease;
        }
        
        .mls-on-the-fly-admin-bar-tab-button-deactive:hover {
            background-color: #e8e8e8;
            border-left: 5px solid #b39ddb;
        }
        
        /* Content area */
        .mls-on-the-fly-admin-bar-tab-content-body {
            flex: 1;
            padding: 15px;
            overflow: auto;
            box-sizing: border-box;
            color: #333333;
        }
        
        /* Close Button Styles - Adjusted to fit inside violet bar and made wider */
        .mls-on-the-fly-admin-bar-close-button {
            position: absolute;
            top: 4px; /* Moved up slightly to center in the header */
            right: 10px;
            background-color: rgba(255, 255, 255, 0.2); /* Semi-transparent background */
            color: #ffffff; /* White text to match header */
            border: 1px solid rgba(255, 255, 255, 0.4); /* Subtle border */
            padding: 2px 12px; /* Wider padding (increased from 8px to 12px) */
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
            transition: all 0.2s ease;
            height: 22px; /* Fixed height to ensure it fits in the header */
            line-height: 18px; /* Center the X vertically */
        }
        
        .mls-on-the-fly-admin-bar-close-button:hover {
            background-color: rgba(255, 255, 255, 0.3); /* Slightly more visible on hover */
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.6);
        }
        </style>';
        
        echo '<div id="mls-on-the-fly-admin-tab-content-raw-data" class="mls-on-the-fly-admin-bar-tab-content" style="display:none;">
            <div class="mls-on-the-fly-admin-bar-tab-header" id="resize-handle-raw">
                <h3>MLS On The Fly Debug Panel</h3>
                <button id="mls-on-the-fly-admin-tab-content-raw-data-close" class="mls-on-the-fly-admin-bar-close-button">X</button>
            </div>
            <div class="mls-on-the-fly-admin-bar-content-container">
                <div class="mls-on-the-fly-admin-bar-sidebar">
                    <button id="raw-data-button-1" class="mls-on-the-fly-admin-bar-tab-button tab-raw-data">Raw Data</button>
                    <button id="rf-requests-button-1" class="mls-on-the-fly-admin-bar-tab-button-deactive tab-requests">RF Requests</button>
                    <button id="mls-errors-button-1" class="mls-on-the-fly-admin-bar-tab-button-deactive tab-errors">PHP Errors</button>
                </div>
                <div class="mls-on-the-fly-admin-bar-tab-content-body">
                    '. do_shortcode('[admin_bar_raw_data]').'
                </div>
            </div>
        </div>
        
        <div id="mls-on-the-fly-admin-tab-content-requests" class="mls-on-the-fly-admin-bar-tab-content" style="display:none;">
            <div class="mls-on-the-fly-admin-bar-tab-header" id="resize-handle-requests">
                <h3>MLS On The Fly Debug Panel</h3>
                <button id="mls-on-the-fly-admin-tab-content-requests-close" class="mls-on-the-fly-admin-bar-close-button">X</button>
            </div>
            <div class="mls-on-the-fly-admin-bar-content-container">
                <div class="mls-on-the-fly-admin-bar-sidebar">
                    <button id="raw-data-button-2" class="mls-on-the-fly-admin-bar-tab-button-deactive tab-raw-data">Raw Data</button>
                    <button id="rf-requests-button-2" class="mls-on-the-fly-admin-bar-tab-button tab-requests">RF Requests</button>
                    <button id="mls-errors-button-2" class="mls-on-the-fly-admin-bar-tab-button-deactive tab-errors">PHP Errors</button>
                </div>
                <div class="mls-on-the-fly-admin-bar-tab-content-body">
                    '. do_shortcode('[admin_bar_mls_request]') . '
                </div>
            </div>
        </div>
        
        <div id="mls-on-the-fly-admin-tab-content-errors" class="mls-on-the-fly-admin-bar-tab-content" style="display:none;">
            <div class="mls-on-the-fly-admin-bar-tab-header" id="resize-handle-errors">
                <h3>MLS On The Fly Debug Panel</h3>
                <button id="mls-on-the-fly-admin-tab-content-errors-close" class="mls-on-the-fly-admin-bar-close-button">X</button>
            </div>
            <div class="mls-on-the-fly-admin-bar-content-container">
                <div class="mls-on-the-fly-admin-bar-sidebar">
                    <button id="raw-data-button-3" class="mls-on-the-fly-admin-bar-tab-button-deactive tab-raw-data">Raw Data</button>
                    <button id="rf-requests-button-3" class="mls-on-the-fly-admin-bar-tab-button-deactive tab-requests">RF Requests</button>
                    <button id="mls-errors-button-3" class="mls-on-the-fly-admin-bar-tab-button tab-errors">PHP Errors</button>
                </div>
                <div class="mls-on-the-fly-admin-bar-tab-content-body">
                    '.  do_shortcode('[admin_bar_mls_errors]').'
                </div>
            </div>
        </div>';
    
}  

}
