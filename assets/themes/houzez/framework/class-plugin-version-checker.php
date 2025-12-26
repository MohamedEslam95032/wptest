<?php
/**
 * Plugin Version Checker
 *
 * This class checks versions of required Houzez plugins and displays admin notices when updates are needed.
 *
 * @package Houzez
 * @since Houzez 3.4.9
 * @author Waqas Riaz
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Houzez_Plugin_Version_Checker {
    
    /**
     * The singleton instance
     */
    private static $instance = null;
    
    /**
     * Theme version from the theme
     */
    private $theme_version;

    private $current_screen;

    private $is_tgmpa_page;

    private $is_houzez_plugins_page;
    
    /**
     * Plugin configuration - all in one place
     * Add or update plugins here when needed
     * 
     * Key format: 'plugin-slug' => [
     *    'file' => 'plugin-folder/main-plugin-file.php',
     *    'name' => 'Plugin Display Name',
     *    'version' => 'required.version.number'
     * ]
     */
    const PLUGIN_CONFIG = [
        'houzez-theme-functionality' => [
            'file' => 'houzez-theme-functionality/houzez-theme-functionality.php',
            'name' => 'Houzez Theme - Functionality',
            'version' => '4.2.5'
        ],
        'houzez-login-register' => [
            'file' => 'houzez-login-register/houzez-login-register.php',
            'name' => 'Houzez Login Register',
            'version' => '4.0.4'
        ],
        'houzez-studio' => [
            'file' => 'houzez-studio/houzez-studio.php',
            'name' => 'Houzez Studio',
            'version' => '1.3.2'
        ],
        'houzez-crm' => [
            'file' => 'houzez-crm/houzez-crm.php',
            'name' => 'Houzez CRM',
            'version' => '1.5.0'
        ],
        'favethemes-insights' => [
            'file' => 'favethemes-insights/favethemes-insights.php',
            'name' => 'Favethemes Insights',
            'version' => '1.3.1'
        ]
        // Add new plugins here as needed
    ];

    /**
     * Required plugins configuration - plugins that MUST be installed and activated
     * These are essential for the theme to work properly
     */
    const REQUIRED_PLUGINS = [
        'houzez-theme-functionality' => [
            'file' => 'houzez-theme-functionality/houzez-theme-functionality.php',
            'name' => 'Houzez Theme - Functionality',
            'required' => true
        ],
        'houzez-login-register' => [
            'file' => 'houzez-login-register/houzez-login-register.php',
            'name' => 'Houzez Login Register',
            'required' => true
        ],
        'houzez-studio' => [
            'file' => 'houzez-studio/houzez-studio.php',
            'name' => 'Houzez Studio',
            'required' => true
        ],
        'elementor' => [
            'file' => 'elementor/elementor.php',
            'name' => 'Elementor',
            'required' => true
        ],
        'redux-framework' => [
            'file' => 'redux-framework/redux-framework.php',
            'name' => 'Redux Framework',
            'required' => true
        ]
    ];
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->theme_version = HOUZEZ_THEME_VERSION;
        
        // Check for missing required plugins first (higher priority)
        $missing_required_plugins = $this->get_missing_required_plugins();
        
        // Check for plugins needing updates
        $plugins_need_update = $this->get_plugins_needing_updates();
        
        $this->is_tgmpa_page = (isset($_GET['page']) && $_GET['page'] === 'tgmpa-install-plugins');
        $this->is_houzez_plugins_page = $this->is_notice_required();
        
        // Show required plugins notice first (higher priority)
        if (!empty($missing_required_plugins) && !$this->is_houzez_plugins_page) {
            add_action('admin_footer', array($this, 'inject_required_plugins_notice'));
        }
        // Show update notice only if no missing required plugins
        elseif (!empty($plugins_need_update && !$this->is_houzez_plugins_page)) {
            add_action('admin_footer', array($this, 'inject_critical_notice'));
        }
        
        // Add plugin action link filters for the WP native plugins page
        add_action('load-plugins.php', array($this, 'add_plugin_action_link_filters'), 1);
        
        // Handle theme activation redirect
        add_action('admin_init', array($this, 'handle_theme_activation_redirect'));
        
        // Suppress admin notices on houzez_plugins page
        add_action('admin_head', array($this, 'suppress_admin_notices_on_houzez_plugins_page'));
        
        // Early suppression of admin notices
        if ($this->is_houzez_plugins_page) {
            add_action('admin_init', array($this, 'early_suppress_admin_notices'), 1);
        }
    }
    
    /**
     * Get the singleton instance
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Check if plugin is active
     */
    public function is_plugin_active($plugin_path) {
        // First check if the function exists
        if (!function_exists('is_plugin_active')) {
            // Include plugin.php if not already included
            if (!function_exists('get_plugins')) {
                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
        }
        
        // Check if file exists before checking if active
        $plugin_abs_path = WP_PLUGIN_DIR . '/' . $plugin_path;
        if (!file_exists($plugin_abs_path)) {
            return false;
        }
        
        return is_plugin_active($plugin_path);
    }
    
    /**
     * Get plugin data including version
     */
    public function get_plugin_data($plugin_path) {
        if (!function_exists('get_plugin_data')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        
        $plugin_abs_path = WP_PLUGIN_DIR . '/' . $plugin_path;
        
        if (file_exists($plugin_abs_path)) {
            try {
                $data = get_plugin_data($plugin_abs_path);
                return $data;
            } catch (Exception $e) {
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Check if version needs update
     */
    public function needs_update($current_version, $expected_version) {
        return version_compare($current_version, $expected_version, '<');
    }
    
    /**
     * Get plugins needing updates
     */
    public function get_plugins_needing_updates() {
        $plugins_need_update = array();
        
        foreach (self::PLUGIN_CONFIG as $plugin_slug => $plugin_info) {
            try {
                $plugin_file = $plugin_info['file'];
                $plugin_name = $plugin_info['name'];
                $required_version = $plugin_info['version'];
                
                // Get plugin data from file header (check if plugin is installed)
                $plugin_data = $this->get_plugin_data($plugin_file);
                
                // Skip if plugin is not installed
                if (!$plugin_data) {
                    continue;
                }
                
                // Get current plugin version from plugin file header
                if (isset($plugin_data['Version'])) {
                    $current_version = $plugin_data['Version'];
                    
                    if ($this->needs_update($current_version, $required_version)) {
                        $is_active = $this->is_plugin_active($plugin_file);
                        $plugins_need_update[] = array(
                            'name' => $plugin_name,
                            'current_version' => $current_version,
                            'expected_version' => $required_version,
                            'is_active' => $is_active
                        );
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        return $plugins_need_update;
    }
    
    /**
     * Get missing required plugins (not installed or not activated)
     */
    public function get_missing_required_plugins() {
        $missing_plugins = array();
        
        foreach (self::REQUIRED_PLUGINS as $plugin_slug => $plugin_info) {
            try {
                $plugin_file = $plugin_info['file'];
                $plugin_name = $plugin_info['name'];
                
                // Check if plugin is installed
                $plugin_data = $this->get_plugin_data($plugin_file);
                $is_installed = ($plugin_data !== false);
                
                // Check if plugin is active
                $is_active = $this->is_plugin_active($plugin_file);
                
                // Add to missing list if not installed or not active
                if (!$is_installed || !$is_active) {
                    $missing_plugins[] = array(
                        'name' => $plugin_name,
                        'file' => $plugin_file,
                        'is_installed' => $is_installed,
                        'is_active' => $is_active,
                        'status' => !$is_installed ? 'not_installed' : 'not_active'
                    );
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        return $missing_plugins;
    }

    /**
     * Hook in plugin action link filters for the WP native plugins page.
     *
     * - Add update notice if update available.
     * - Prevent deactivation of required plugins.
     *
     * @since 1.0.0
     */
    public function add_plugin_action_link_filters() {
        // Check all plugins in PLUGIN_CONFIG for updates
        foreach (self::PLUGIN_CONFIG as $plugin_slug => $plugin_info) {
            if ($this->does_plugin_require_update($plugin_slug)) {
                add_filter('plugin_action_links_' . $plugin_info['file'], array($this, 'filter_plugin_action_links_update'), 20);
            }
        }
        
        // Check all required plugins for deactivation prevention
        foreach (self::REQUIRED_PLUGINS as $plugin_slug => $plugin_info) {
            if ($this->is_plugin_active($plugin_info['file'])) {
                add_filter('plugin_action_links_' . $plugin_info['file'], array($this, 'filter_plugin_action_links_deactivate'), 20);
            }
        }
    }

    /**
     * Check if a plugin requires an update
     *
     * @param string $plugin_slug The plugin slug
     * @return bool True if plugin requires update, false otherwise
     */
    public function does_plugin_require_update($plugin_slug) {
        if (!isset(self::PLUGIN_CONFIG[$plugin_slug])) {
            return false;
        }
        
        $plugin_info = self::PLUGIN_CONFIG[$plugin_slug];
        $plugin_file = $plugin_info['file'];
        $required_version = $plugin_info['version'];
        
        // Get plugin data
        $plugin_data = $this->get_plugin_data($plugin_file);
        
        if (!$plugin_data || !isset($plugin_data['Version'])) {
            return false;
        }
        
        $current_version = $plugin_data['Version'];
        
        return $this->needs_update($current_version, $required_version);
    }

    /**
     * Remove the 'Deactivate' link on the WP native plugins page for required plugins.
     *
     * @param array $actions Action links.
     * @return array
     */
    public function filter_plugin_action_links_deactivate($actions) {
        //unset($actions['deactivate']);
        
        // Add a notice that this plugin is required
        //$actions['required'] = '<span style="color: #d63638; font-weight: bold;">' . __('Required by Houzez', 'houzez') . '</span>';
        
        return $actions;
    }

    /**
     * Add a 'Update Required' link on the WP native plugins page if the plugin does not meet the
     * minimum version requirements.
     *
     * @param array $actions Action links.
     * @return array
     */
    public function filter_plugin_action_links_update($actions) {
        $houzez_plugins_url = admin_url('admin.php?page=houzez_plugins');
        
        $actions['houzez_update'] = sprintf(
            '<a href="%1$s" title="%2$s" class="edit">%3$s</a>',
            esc_url($houzez_plugins_url),
            esc_attr__('This plugin needs to be updated to be compatible with your theme.', 'houzez'),
            esc_html__('Update Required', 'houzez')
        );

        return $actions;
    }

    /**
     * Handle theme activation redirect to houzez_plugins page
     * 
     * This method checks if the theme was just activated and redirects
     * the user to the plugins page to install required plugins
     */
    public function handle_theme_activation_redirect() {
        // Check if we should redirect (only on first activation)
        if (get_transient('houzez_theme_activated')) {
            // Delete the transient so this only happens once
            delete_transient('houzez_theme_activated');
            
            // Don't redirect if we're already on the plugins page or doing AJAX
            if (isset($_GET['page']) && $_GET['page'] === 'houzez_plugins') {
                return;
            }
            
            if (wp_doing_ajax()) {
                return;
            }
            
            // Don't redirect if user is not an admin
            if (!current_user_can('manage_options')) {
                return;
            }
            
            // Check if any required plugins are missing
            $missing_plugins = $this->get_missing_required_plugins();
            
            // Only redirect if there are missing required plugins
            if (!empty($missing_plugins)) {
                // Redirect to houzez plugins page
                wp_safe_redirect(admin_url('admin.php?page=houzez_plugins&houzez_welcome=1'));
                exit;
            }
        }
    }

    /**
     * Inject required plugins notice
     */
    public function inject_required_plugins_notice() {
        $missing_plugins = $this->get_missing_required_plugins();
        
        if (empty($missing_plugins)) {
            return;
        }
        
        // Add the CSS for our notice
        echo '<style>
            .houzez-required-plugins-notice {
                background-color: #fff3cd;
                border-left: 4px solid #ffc107;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
                margin: 5px 15px 15px 0;
                padding: 15px 20px;
                position: relative;
            }
            .houzez-required-plugins-notice h3 {
                color: #856404;
                margin: 10px 0;
                font-size: 18px;
            }
            .houzez-required-plugins-notice .plugin-status {
                font-size: 12px;
                font-weight: bold;
                padding: 2px 6px;
                border-radius: 3px;
                margin-left: 8px;
            }
            .houzez-required-plugins-notice .status-not-installed {
                background-color: #dc3545;
                color: white;
            }
            .houzez-required-plugins-notice .status-not-active {
                background-color: #fd7e14;
                color: white;
            }
            .houzez-install-button {
                background-color: #28a745;
                border: none;
                border-radius: 3px;
                color: white;
                cursor: pointer;
                display: inline-block;
                font-size: 14px;
                font-weight: bold;
                margin-top: 10px;
                padding: 10px 20px;
                text-decoration: none;
                text-shadow: none;
            }
            .houzez-install-button:hover {
                background-color: #218838;
                color: white;
            }
        </style>';
        
        // Define the plugins page URL 
        $plugins_page_url = admin_url('admin.php?page=houzez_plugins');
        
        // Add JavaScript to insert the notice
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Create the notice HTML
                var noticeHtml = '<div class="houzez-required-plugins-notice">' +
                    '<h3><?php echo esc_js(__('Required Plugins Missing', 'houzez')); ?></h3>' +
                    '<p><?php echo esc_js(__('The following plugins are required for Houzez theme to work properly:', 'houzez')); ?></p>' +
                    '<ul style="list-style-type: disc; padding-left: 20px; margin: 10px 0;">';
                
                <?php foreach ($missing_plugins as $plugin): ?>
                    noticeHtml += '<li><strong><?php echo esc_js($plugin['name']); ?></strong>';
                    <?php if ($plugin['status'] === 'not_installed'): ?>
                        noticeHtml += '<span class="plugin-status status-not-installed"><?php echo esc_js(__('Not Installed', 'houzez')); ?></span>';
                    <?php else: ?>
                        noticeHtml += '<span class="plugin-status status-not-active"><?php echo esc_js(__('Not Active', 'houzez')); ?></span>';
                    <?php endif; ?>
                    noticeHtml += '</li>';
                <?php endforeach; ?>
                
                noticeHtml += '</ul>' +
                    '<p><?php echo esc_js(__('Please install and activate these plugins to ensure all theme features work correctly.', 'houzez')); ?></p>' +
                    '<p><a href="<?php echo esc_js($plugins_page_url); ?>" class="houzez-install-button"><?php echo esc_js(__('Install Required Plugins', 'houzez')); ?></a></p>' +
                    '</div>';
                
                // Insert the notice at the top of the admin content
                $('#wpbody-content').prepend(noticeHtml);
                
                // Remove any duplicate notices
                $('.houzez-required-plugins-notice:not(:first)').remove();
            });
        </script>
        <?php
    }
    
    /**
     * Inject a critical notice directly into the admin head
     */
    public function inject_critical_notice() {
        
        $plugins_need_update = $this->get_plugins_needing_updates();
        
        if (empty($plugins_need_update)) {
            return;
        }
        
        // Add the CSS for our notice, but position it better
        echo '<style>
            .houzez-critical-update-notice {
                background-color: #fcf8e3;
                border-left: 4px solid #dc3232;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
                margin: 5px 15px 15px 0;
                padding: 10px 15px;
                position: relative;
            }
            .houzez-critical-update-notice h3 {
                color: #dc3232;
                margin: 10px 0;
            }
            .houzez-update-button {
                background-color: #0073aa;
                border: none;
                border-radius: 3px;
                color: white;
                cursor: pointer;
                display: inline-block;
                font-size: 13px;
                margin-top: 5px;
                padding: 7px 15px;
                text-decoration: none;
                text-shadow: none;
            }
            .houzez-update-button:hover {
                background-color: #0085ba;
                color: white;
            }
        </style>';
        
        // Define the update page URL 
        $update_page_url = admin_url('admin.php?page=houzez_plugins');
        
        // Add JavaScript to insert the notice at the right position
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Create the notice HTML
                var noticeHtml = '<div class="houzez-critical-update-notice">' +
                    '<h3><?php echo esc_js(__('Plugin Updates Required', 'houzez')); ?></h3>' +
                    '<p><?php echo esc_js(__('The following installed plugins need to be updated to ensure compatibility with the current theme version:', 'houzez')); ?></p>' +
                    '<ul style="list-style-type: disc; padding-left: 20px;">';
                
                <?php foreach ($plugins_need_update as $plugin): ?>
                    noticeHtml += '<li><strong><?php echo esc_js($plugin['name']); ?></strong> - ' +
                        '<?php echo esc_js(sprintf(__('Current version: %s, Required version: %s', 'houzez'), 
                            $plugin['current_version'], 
                            $plugin['expected_version'])); ?>' +
                        <?php if (!$plugin['is_active']): ?>
                        ' <span style="color: #d63638; font-weight: bold;"><?php echo esc_js(__('(Inactive)', 'houzez')); ?></span>' +
                        <?php endif; ?>
                        '</li>';
                <?php endforeach; ?>
                
                noticeHtml += '</ul>' +
                    '<p><?php echo esc_js(__('Please update these plugins to ensure all features work correctly. Inactive plugins are also shown as they may be needed for theme functionality.', 'houzez')); ?></p>';
                
                <?php if (!$this->is_tgmpa_page): ?>
                    noticeHtml += '<p><a href="<?php echo esc_js($update_page_url); ?>" class="houzez-update-button"><?php echo esc_js(__('Update Plugins', 'houzez')); ?></a></p>';
                <?php endif; ?>
                
                noticeHtml += '</div>';
                
                // Find the correct position to insert the notice (after header, before content)
                // This targets the main content wrapper but inserts before any other elements
                $('#wpbody-content').prepend(noticeHtml);
                
                // Remove any incorrectly positioned notice
                $('.houzez-critical-update-notice:not(:first)').remove();
            });
        </script>
        <?php
    }
    
    /**
     * Suppress admin notices on houzez_plugins page
     */
    public function suppress_admin_notices_on_houzez_plugins_page() {
        if ($this->is_houzez_plugins_page) {
            // Remove all admin notices on houzez_plugins page
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
            remove_all_actions('network_admin_notices');
            
            // Also remove user admin notices
            remove_all_actions('user_admin_notices');
            
        }
    }

    /**
     * Early suppression of admin notices
     */
    public function early_suppress_admin_notices() {
        // Remove all admin notices on houzez_plugins page
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
        remove_all_actions('network_admin_notices');
        remove_all_actions('user_admin_notices');
    }

    /**
     * Check if the current page is a Houzez-related page.
     *
     * @return bool True if the current page is a Houzez-related page, false otherwise.
     */
    public function is_notice_required() {
        $houzez_pages = array('houzez_plugins', 'houzez-template-library', 'houzez_fbuilder', 'houzez_image_sizes', 'houzez_currencies', 'houzez_feedback', 'fcc_api_settings', 'houzez_post_types', 'houzez_purchase', 'houzez_taxonomies', 'houzez_permalinks', 'import_locations', 'fave_white_label', 'hcrm_settings', 'houzez_help', 'houzez-verification-requests');
        return (isset($_GET['page']) && in_array($_GET['page'], $houzez_pages)) || 
               (isset($_GET['post_type']) && $_GET['post_type'] === 'fts_builder');
    }
}

// Initialize the class
add_action('admin_init', function() {
    Houzez_Plugin_Version_Checker::get_instance();
}); 

/**
 * Set transient when theme is activated
 * This triggers the redirect to houzez_plugins page on first activation
 */
add_action('after_switch_theme', function() {
    // Set a transient that expires in 1 hour (in case something goes wrong)
    set_transient('houzez_theme_activated', true, HOUR_IN_SECONDS);
}); 