<?php
/**
 * Class fcc_Post_Type_Agency
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 28/09/16
 * Time: 10:16 PM
 */
class FCC_API_Settings {


	/**
	 * Sets up init
	 *
	 */
	public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'settings_init' ) );
        
        // Add AJAX handlers
        add_action( 'wp_ajax_fcc_remove_api_key', array( __CLASS__, 'ajax_remove_api_key' ) );
        add_action( 'wp_ajax_fcc_update_rates', array( __CLASS__, 'ajax_update_rates' ) );
        
        // Hook into form submission to handle redirects
        add_action( 'admin_init', array( __CLASS__, 'handle_form_submission' ) );

        // Update cron job when API settings updated
        add_action( 'update_option_fcc_api_settings', array( __CLASS__, 'updated_option' ), 10, 2 );
    }

    /**
     * Handle form submission and redirect with notifications
     */
    public static function handle_form_submission() {
        // This method is kept for future use but the redirect is now handled in sanitize_settings
    }
    
    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'houzez_dashboard',
            __('Currency Switcher Settings', 'houzez-theme-functionality'),
            __('Currency Switcher', 'houzez-theme-functionality'),
            'manage_options',
            'fcc_api_settings',
            array(__CLASS__, 'render')
        );
    }
    
    /**
     * AJAX handler for removing API key
     */
    public static function ajax_remove_api_key() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['fcc_remove_api_key_nonce'], 'fcc_remove_api_key')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'houzez-theme-functionality')));
        }
        
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'houzez-theme-functionality')));
        }
        
        // Remove the API key
        $options = get_option('fcc_api_settings', array());
        $options['api_key'] = '';
        update_option('fcc_api_settings', $options);
        
        // Clear any scheduled cron jobs
        wp_clear_scheduled_hook('fcc_update_rates');
        
        wp_send_json_success(array('message' => __('API key removed successfully.', 'houzez-theme-functionality')));
    }
    
    /**
     * AJAX handler for updating rates
     */
    public static function ajax_update_rates() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['fcc_update_rates_nonce'], 'fcc_update_rates')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'houzez-theme-functionality')));
        }
        
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'houzez-theme-functionality')));
        }
        
        // Check if API key is set
        $api_key = self::get_setting('api_key');
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API key is required to update rates.', 'houzez-theme-functionality')));
        }
        
        // Update rates
        $result = FCC_Rates::update();
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        } elseif ($result === null) {
            wp_send_json_error(array('message' => __('Failed to update rates. Please check your API key and try again.', 'houzez-theme-functionality')));
        } else {
            wp_send_json_success(array('message' => __('Exchange rates updated successfully.', 'houzez-theme-functionality')));
        }
    }

    public static function render() {

        // Flush the rewrite rules if the settings were updated.
        if ( isset( $_GET['settings-updated'] ) ) {
            flush_rewrite_rules();
            FCC_Rates::update();
        }
        
        
        
        // Get current settings for statistics
        $api_key = self::get_setting('api_key');
        $update_interval = self::get_setting('update_interval');
        $has_api_key = !empty($api_key);
        
        // Check if rates are available
        global $wpdb;
        $rates_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}favethemes_currency_converter");
        $last_update = get_option('fcc_last_update_time');
        
        // Handle notifications
        $notification = '';
        $notification_type = '';

        if (isset($_GET['settings_saved'])) {
            $notification = __('Settings saved successfully!', 'houzez-theme-functionality');
            $notification_type = 'success';
        } elseif (isset($_GET['api_key_removed'])) {
            $notification = __('API key removed successfully!', 'houzez-theme-functionality');
            $notification_type = 'success';
        } elseif (isset($_GET['rates_updated'])) {
            $notification = __('Exchange rates updated successfully!', 'houzez-theme-functionality');
            $notification_type = 'success';
        } elseif (isset($_GET['error'])) {
            $error_code = sanitize_text_field($_GET['error']);
            switch ($error_code) {
                case 'invalid_api_key':
                    $notification = __('Invalid API key. Please check your Open Exchange Rates API key.', 'houzez-theme-functionality');
                    break;
                case 'api_error':
                    $notification = __('Error connecting to the exchange rates API. Please try again later.', 'houzez-theme-functionality');
                    break;
                default:
                    $notification = __('An error occurred while processing your request.', 'houzez-theme-functionality');
            }
            $notification_type = 'error';
        }
        
            ?>
        <div class="wrap houzez-template-library">
            <div class="houzez-header">
                <div class="houzez-header-content">
                    <div class="houzez-logo">
                        <h1><?php esc_html_e('Currency Switcher Settings', 'houzez-theme-functionality'); ?></h1>
                    </div>
                    <div class="houzez-header-actions">
                        <?php if ($has_api_key): ?>
                            <button type="button" id="update-rates-btn" class="houzez-btn houzez-btn-primary">
                                <i class="dashicons dashicons-update"></i>
                                <?php esc_html_e('Update Rates', 'houzez-theme-functionality'); ?>
                            </button>
                            <button type="button" id="remove-api-key-btn" class="houzez-btn houzez-btn-secondary">
                                <i class="dashicons dashicons-trash"></i>
                                <?php esc_html_e('Remove API Key', 'houzez-theme-functionality'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="houzez-dashboard">
                <!-- Notifications -->
                <?php if ($notification): ?>
                <div id="houzez-notification" class="houzez-notification <?php echo esc_attr($notification_type); ?>">
                    <span class="dashicons dashicons-<?php echo $notification_type === 'success' ? 'yes-alt' : 'warning'; ?>"></span>
                    <?php echo esc_html($notification); ?>
                </div>
                <?php endif; ?>

                <!-- Quick Stats -->
                <div class="houzez-stats-grid">
                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-admin-network"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo $has_api_key ? __('Connected', 'houzez-theme-functionality') : __('Not Connected', 'houzez-theme-functionality'); ?></h3>
                            <p><?php esc_html_e('API Status', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-money-alt"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($rates_count); ?></h3>
                            <p><?php esc_html_e('Exchange Rates', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-clock"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3>
                                <?php if ($last_update): ?>
                                    <?php echo human_time_diff($last_update, current_time('timestamp')); ?> ago
                                <?php else: ?>
                                    <?php esc_html_e('Never', 'houzez-theme-functionality'); ?>
                                <?php endif; ?>
                            </h3>
                            <p><?php esc_html_e('Last Update', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-update"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo $update_interval ? esc_html($update_interval) : __('Manual', 'houzez-theme-functionality'); ?></h3>
                            <p><?php esc_html_e('Update Interval', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- API Status Card -->
                <div class="houzez-main-card">
                    <div class="houzez-card-header">
                        <h2>
                            <i class="dashicons dashicons-admin-settings"></i>
                            <?php esc_html_e('API Configuration', 'houzez-theme-functionality'); ?>
                        </h2>
                        <div class="api-status-info">
                            <?php if ($has_api_key): ?>
                                <div class="status-indicator status-success">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <?php esc_html_e('Connected', 'houzez-theme-functionality'); ?>
                                </div>
                            <?php else: ?>
                                <div class="status-indicator status-warning">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php esc_html_e('Not Connected', 'houzez-theme-functionality'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="houzez-card-body">
                        <?php if (!$has_api_key): ?>
                            <p class="houzez-description">
                                <?php esc_html_e('Connect to Open Exchange Rates API to enable automatic currency conversion. Get your free API key from openexchangerates.org', 'houzez-theme-functionality'); ?>
                            </p>
                        <?php else: ?>
                            <p class="houzez-description">
                                <?php esc_html_e('Your API is connected and ready to fetch exchange rates. You can update rates manually or configure automatic updates.', 'houzez-theme-functionality'); ?>
                                </p>
                        <?php endif; ?>
                        
                        <form method="post" action="options.php" id="currency-settings-form">
                                    <?php settings_fields( 'fcc_api_settings' ); ?>
                                    <?php do_settings_sections( 'fcc_api_settings' ); ?>
                            
                            <div class="houzez-form-grid">
                                <div class="houzez-form-group">
                                    <label for="api_key" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-network"></i>
                                        <?php esc_html_e('API Key', 'houzez-theme-functionality'); ?>
                                        <span class="required">*</span>
                                    </label>
                                    <div class="api-key-field-container">
                                        <input type="password" 
                                               id="api_key" 
                                               name="fcc_api_settings[api_key]" 
                                               value="<?php echo esc_attr($api_key); ?>" 
                                               class="houzez-form-input"
                                               placeholder="<?php esc_attr_e('Enter your Open Exchange Rates API key', 'houzez-theme-functionality'); ?>" />
                                        <button type="button" class="toggle-password-btn" onclick="toggleApiKeyVisibility()">
                                            <span class="dashicons dashicons-visibility" id="toggle-icon"></span>
                                        </button>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Get your free API key from openexchangerates.org', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="update_interval" class="houzez-form-label">
                                        <i class="dashicons dashicons-clock"></i>
                                        <?php esc_html_e('Update Interval', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="update_interval" 
                                            name="fcc_api_settings[update_interval]" 
                                            class="houzez-form-select">
                                        <option value=""><?php esc_html_e('Manual Updates Only', 'houzez-theme-functionality'); ?></option>
                                        <option value="hourly" <?php selected($update_interval, 'hourly'); ?>><?php esc_html_e('Every Hour', 'houzez-theme-functionality'); ?></option>
                                        <option value="daily" <?php selected($update_interval, 'daily'); ?>><?php esc_html_e('Daily', 'houzez-theme-functionality'); ?></option>
                                        <option value="weekly" <?php selected($update_interval, 'weekly'); ?>><?php esc_html_e('Weekly', 'houzez-theme-functionality'); ?></option>
                                        <option value="biweekly" <?php selected($update_interval, 'biweekly'); ?>><?php esc_html_e('Biweekly', 'houzez-theme-functionality'); ?></option>
                                        <option value="monthly" <?php selected($update_interval, 'monthly'); ?>><?php esc_html_e('Monthly', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('How often to automatically update exchange rates', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="houzez-form-actions">
                                <div class="houzez-form-actions-left">
                                    <a href="https://openexchangerates.org/" target="_blank" class="houzez-btn houzez-btn-outline">
                                        <i class="dashicons dashicons-external"></i>
                                        <?php esc_html_e('Get API Key', 'houzez-theme-functionality'); ?>
                                    </a>
                                </div>
                                <div class="houzez-form-actions-right">
                                    <button type="submit" class="houzez-btn houzez-btn-primary">
                                        <i class="dashicons dashicons-yes"></i>
                                        <?php esc_html_e('Save Settings', 'houzez-theme-functionality'); ?>
                                    </button>
                                </div>
                            </div>
                                </form>
                    </div>
                </div>

                <!-- How It Works Information Card -->
                <div class="houzez-main-card">
                    <div class="houzez-card-header">
                        <h2>
                            <i class="dashicons dashicons-info"></i>
                            <?php esc_html_e('How It Works', 'houzez-theme-functionality'); ?>
                        </h2>
                    </div>
                    
                    <div class="houzez-card-body">
                        <div class="houzez-actions">
                            <div class="houzez-action">
                                <div class="houzez-action-icon">
                                    <i class="dashicons dashicons-admin-network"></i>
                                </div>
                                <div class="houzez-action-content">
                                    <h4><?php esc_html_e('API Integration', 'houzez-theme-functionality'); ?></h4>
                                    <p><?php esc_html_e('Connects to Open Exchange Rates API to fetch real-time currency exchange rates from a trusted financial data provider.', 'houzez-theme-functionality'); ?></p>
                                </div>
                            </div>

                            <div class="houzez-action">
                                <div class="houzez-action-icon">
                                    <i class="dashicons dashicons-update"></i>
                                </div>
                                <div class="houzez-action-content">
                                    <h4><?php esc_html_e('Automatic Updates', 'houzez-theme-functionality'); ?></h4>
                                    <p><?php esc_html_e('Exchange rates are automatically updated based on your selected frequency using WordPress cron jobs for reliable scheduling.', 'houzez-theme-functionality'); ?></p>
                                </div>
                            </div>

                            <div class="houzez-action">
                                <div class="houzez-action-icon">
                                    <i class="dashicons dashicons-database"></i>
                                </div>
                                <div class="houzez-action-content">
                                    <h4><?php esc_html_e('Local Storage', 'houzez-theme-functionality'); ?></h4>
                                    <p><?php esc_html_e('Rates are stored locally in your WordPress database for fast access, reduced API calls, and improved performance.', 'houzez-theme-functionality'); ?></p>
                                </div>
                            </div>

                            <div class="houzez-action">
                                <div class="houzez-action-icon">
                                    <i class="dashicons dashicons-money-alt"></i>
                                </div>
                                <div class="houzez-action-content">
                                    <h4><?php esc_html_e('Currency Switching', 'houzez-theme-functionality'); ?></h4>
                                    <p><?php esc_html_e('Enables visitors to view property prices in their preferred currency with accurate, up-to-date conversion rates.', 'houzez-theme-functionality'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden forms for AJAX actions -->
        <form id="remove-api-key-form" method="post" style="display: none;">
            <input type="hidden" name="action" value="remove_api_key">
            <?php wp_nonce_field('fcc_remove_api_key', 'fcc_remove_api_key_nonce'); ?>
        </form>

        <form id="update-rates-form" method="post" style="display: none;">
            <input type="hidden" name="action" value="update_rates">
            <?php wp_nonce_field('fcc_update_rates', 'fcc_update_rates_nonce'); ?>
        </form>

        <script>
        function toggleApiKeyVisibility() {
            const apiKeyInput = document.getElementById('api_key');
            const toggleIcon = document.getElementById('toggle-icon');
            
            if (apiKeyInput.type === 'password') {
                apiKeyInput.type = 'text';
                toggleIcon.className = 'dashicons dashicons-hidden';
            } else {
                apiKeyInput.type = 'password';
                toggleIcon.className = 'dashicons dashicons-visibility';
            }
        }

        jQuery(document).ready(function($) {
            // Auto-hide notification after 5 seconds
            setTimeout(function() {
                $('#houzez-notification').fadeOut();
            }, 5000);

            // Remove API Key
            $('#remove-api-key-btn').on('click', function() {
                if (confirm('<?php echo esc_js(__('Are you sure you want to remove the API key? This will disable automatic currency updates.', 'houzez-theme-functionality')); ?>')) {
                    var formData = $('#remove-api-key-form').serialize();
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: formData + '&action=fcc_remove_api_key',
                        success: function(response) {
                            if (response.success) {
                                window.location.href = '<?php echo admin_url('admin.php?page=fcc_api_settings&api_key_removed=1'); ?>';
                            } else {
                                alert('<?php echo esc_js(__('Error removing API key. Please try again.', 'houzez-theme-functionality')); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php echo esc_js(__('Error removing API key. Please try again.', 'houzez-theme-functionality')); ?>');
                        }
                    });
                }
            });

            // Update Rates
            $('#update-rates-btn').on('click', function() {
                var $btn = $(this);
                var originalText = $btn.html();
                
                $btn.prop('disabled', true).html('<i class="dashicons dashicons-update"></i> <?php echo esc_js(__('Updating...', 'houzez-theme-functionality')); ?>');
                
                var formData = $('#update-rates-form').serialize();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: formData + '&action=fcc_update_rates',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '<?php echo admin_url('admin.php?page=fcc_api_settings&rates_updated=1'); ?>';
                        } else {
                            alert(response.data.message || '<?php echo esc_js(__('Error updating rates. Please try again.', 'houzez-theme-functionality')); ?>');
                            $btn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function() {
                        alert('<?php echo esc_js(__('Error updating rates. Please try again.', 'houzez-theme-functionality')); ?>');
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Form submission with loading state
            $('#currency-settings-form').on('submit', function() {
                var $submitBtn = $(this).find('button[type="submit"]');
                var originalText = $submitBtn.html();
                
                $submitBtn.prop('disabled', true).html('<i class="dashicons dashicons-update"></i> <?php echo esc_js(__('Saving...', 'houzez-theme-functionality')); ?>');
                
                // Let the form submit normally, but show loading state
                setTimeout(function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                }, 3000);
            });
        });
        </script>
        <?php
    }

    /**
     * Register settings
     */
    public static function settings_init() {
        register_setting( 'fcc_api_settings', 'fcc_api_settings', array( __CLASS__, 'sanitize_settings' ) );
    }
    
    /**
     * Sanitize settings
     */
    public static function sanitize_settings( $input ) {
        $sanitized = array();
        
        if ( isset( $input['api_key'] ) ) {
            $sanitized['api_key'] = sanitize_text_field( $input['api_key'] );
        }
        
        if ( isset( $input['update_interval'] ) ) {
            $sanitized['update_interval'] = sanitize_text_field( $input['update_interval'] );
    }

        // Redirect with success message after saving
        add_action( 'admin_notices', function() {
            echo '<script>window.location.href = "' . admin_url('admin.php?page=fcc_api_settings&settings_saved=1') . '";</script>';
        });

        return $sanitized;
    }

    /**
     * Section callback.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public static function fcc_section_callback() {}


    /**
     * API Key field callback.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public static function fcc_api_callback() {

        $api_key = self::get_setting('api_key');
        ?>
        <label for="api_key" class="form-field">
            <input type="password" id="api_key" name="fcc_api_settings[api_key]" value="<?php echo $api_key; ?>" class="regular-text" placeholder="<?php esc_html_e( 'Enter the Open Exchange Rates API key', 'favethemes-currency-converter' ); ?>">
            <p>
            <?php printf(
                _x( 'Get yours at: %1s', 'URL where to get the API key', 'favethemes-currency-converter' ),
                '<a href="//openexchangerates.org/" target="_blank">openexchangerates.org</a>' ); ?>
            </p>
        </label>
        
            <?php 
        }


    /**
     * Interval field callback.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public static function fcc_interval_field_callback() { 

        $update_frequency = self::get_setting('update_interval');
        ?>
        <label for="update_interval">
            <?php // esc_html_e( 'Rates update frequency:', 'favethemes-currency-converter' ); ?>
            <select name="fcc_api_settings[update_interval]" id="update_interval">
                <option value="hourly"   <?php selected( $update_frequency, 'hourly',   true ); ?>><?php esc_html_e( 'Hourly',  'favethemes-currency-converter' ); ?></option>
                <option value="daily"    <?php selected( $update_frequency, 'daily',    true ); ?>><?php esc_html_e( 'Daily',   'favethemes-currency-converter' ); ?></option>
                <option value="weekly"   <?php selected( $update_frequency, 'weekly',   true ); ?>><?php esc_html_e( 'Weekly',  'favethemes-currency-converter' ); ?></option>
                <option value="biweekly" <?php selected( $update_frequency, 'biweekly', true ); ?>><?php esc_html_e( 'Biweekly','favethemes-currency-converter' ); ?></option>
                <option value="monthly"  <?php selected( $update_frequency, 'monthly',  true ); ?>><?php esc_html_e( 'Monthly', 'favethemes-currency-converter' ); ?></option>
            </select>
        </label>
        
        <p>
            <?php esc_html_e( 'Specify the frequency when to update currencies exchange rates', 'favethemes-currency-converter' ); ?>
        </p>
        <?php
    }


    /**
     * Updated option callback.
     *
     * @since   1.0.0
     *
     * @param string $old_value
     * @param string $new_value
     */
    public static function updated_option( $old_value, $new_value ) {

        if ( $old_value != $new_value ) {

            wp_clear_scheduled_hook( 'favethemes_currencies_update' );

            $api_key = isset( $new_value['api_key'] ) ? $new_value['api_key'] : ( isset( $old_value['api_key'] ) ? $old_value['api_key'] : '' );

            if ( ! empty( $api_key ) ) {

                $interval = isset( $new_value['update_interval'] ) ? $new_value['update_interval'] : ( isset( $old_value['update_interval'] ) ? $old_value['update_interval'] : 'weekly' );

                HOUZEZ_Cron::FCC_schedule_updates($api_key, $interval);

            }

        }

    }



    /**
     * Returns settings.
     *
     * @since  1.0.0
     * @access public
     * @param  string  $setting
     * @return mixed
     */
    public static function get_setting( $setting ) {

        $defaults = self::get_default_settings();
        $settings = wp_parse_args( get_option('fcc_api_settings', $defaults ), $defaults );

        return isset( $settings[ $setting ] ) ? $settings[ $setting ] : false;
    }

    /**
     * Returns the default settings for the plugin.
     *
     * @since  1.0.0
     * @access public
     * @return array
     */
    public static function get_default_settings() {

        $settings = array(
            'api_key' => '',
            'update_interval' => 'weekly',
        );

        return $settings;
    }

}