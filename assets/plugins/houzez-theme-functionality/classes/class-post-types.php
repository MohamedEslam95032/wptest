<?php
/**
 * Class Houzez_Post_Type_Agency
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 28/09/16
 * Time: 10:16 PM
 */
class Houzez_Post_Type {


	/**
	 * Sets up init
	 *
	 */
	public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'houzez_register_settings' ) );
    }


	public static function render() {
      
        // Flush the rewrite rules if the settings were updated.
        if ( isset( $_GET['settings-updated'] ) )
            flush_rewrite_rules();
        
        
        // Get current settings for statistics
        $settings = get_option('houzez_ptype_settings', self::get_default_settings());
        $enabled_count = 0;
        $total_count = count($settings);
        
        foreach ($settings as $setting) {
            if ($setting === 'enabled') {
                $enabled_count++;
            }
        }
        
        // Handle notifications
        $notification = '';
        $notification_type = '';
        
        if (isset($_GET['settings_saved'])) {
            $notification = __('Post type settings saved successfully!', 'houzez-theme-functionality');
            $notification_type = 'success';
        }
        
        ?>
        <div class="wrap houzez-template-library">
            <div class="houzez-header">
                <div class="houzez-header-content">
                    <div class="houzez-logo">
                        <h1><?php esc_html_e('Post Types Management', 'houzez-theme-functionality'); ?></h1>
                    </div>
                    <div class="houzez-header-actions">
                        <button type="submit" form="post-types-form" class="houzez-btn houzez-btn-primary">
                            <i class="dashicons dashicons-yes"></i>
                            <?php esc_html_e('Save Settings', 'houzez-theme-functionality'); ?>
                        </button>
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
                            <i class="dashicons dashicons-admin-post"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($total_count); ?></h3>
                            <p><?php esc_html_e('Total Post Types', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-yes-alt"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($enabled_count); ?></h3>
                            <p><?php esc_html_e('Enabled Post Types', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-dismiss"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($total_count - $enabled_count); ?></h3>
                            <p><?php esc_html_e('Disabled Post Types', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-admin-settings"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo round(($enabled_count / $total_count) * 100); ?>%</h3>
                            <p><?php esc_html_e('Activation Rate', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Main Configuration Card -->
                <div class="houzez-main-card">
                    <div class="houzez-card-header">
                        <h2>
                            <i class="dashicons dashicons-admin-post"></i>
                            <?php esc_html_e('Custom Post Types Configuration', 'houzez-theme-functionality'); ?>
                        </h2>
                        <div class="houzez-status-badge <?php echo $enabled_count > 0 ? 'houzez-status-success' : 'houzez-status-warning'; ?>">
                            <?php echo $enabled_count > 0 ? __('Active', 'houzez-theme-functionality') : __('Inactive', 'houzez-theme-functionality'); ?>
                        </div>
                    </div>
                    <div class="houzez-card-body">
                        <p class="houzez-description">
                            <?php esc_html_e('Enable or disable custom post types for your Houzez website. Disabled post types will not appear in the admin area or frontend.', 'houzez-theme-functionality'); ?>
                        </p>
                        
                        <form method="post" action="options.php" id="post-types-form">
                    <?php settings_fields( 'houzez_ptype_settings' ); ?>
                            
                            <div class="houzez-form-grid">
                                <div class="houzez-form-group">
                                    <label for="houzez_agents_post" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-users"></i>
                                        <?php esc_html_e('Agents', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="houzez_agents_post" name="houzez_ptype_settings[houzez_agents_post]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('houzez_agents_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('houzez_agents_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Manage real estate agents and their profiles', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="houzez_agencies_post" class="houzez-form-label">
                                        <i class="dashicons dashicons-building"></i>
                                        <?php esc_html_e('Agencies', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="houzez_agencies_post" name="houzez_ptype_settings[houzez_agencies_post]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('houzez_agencies_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('houzez_agencies_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Manage real estate agencies and their information', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="houzez_packages_post" class="houzez-form-label">
                                        <i class="dashicons dashicons-products"></i>
                                        <?php esc_html_e('Houzez Packages', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="houzez_packages_post" name="houzez_ptype_settings[houzez_packages_post]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('houzez_packages_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('houzez_packages_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Manage subscription packages for property listings', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="houzez_invoices_post" class="houzez-form-label">
                                        <i class="dashicons dashicons-media-spreadsheet"></i>
                                        <?php esc_html_e('Houzez Invoices', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="houzez_invoices_post" name="houzez_ptype_settings[houzez_invoices_post]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('houzez_invoices_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('houzez_invoices_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Manage billing and invoice records', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="houzez_partners_post" class="houzez-form-label">
                                        <i class="dashicons dashicons-groups"></i>
                                        <?php esc_html_e('Partners', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="houzez_partners_post" name="houzez_ptype_settings[houzez_partners_post]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('houzez_partners_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('houzez_partners_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Manage business partners and collaborations', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="houzez_testimonials_post" class="houzez-form-label">
                                        <i class="dashicons dashicons-format-quote"></i>
                                        <?php esc_html_e('Testimonials', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="houzez_testimonials_post" name="houzez_ptype_settings[houzez_testimonials_post]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('houzez_testimonials_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('houzez_testimonials_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Manage customer testimonials and reviews', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="houzez_packages_info_post" class="houzez-form-label">
                                        <i class="dashicons dashicons-info"></i>
                                        <?php esc_html_e('User Packages Info', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="houzez_packages_info_post" name="houzez_ptype_settings[houzez_packages_info_post]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('houzez_packages_info_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('houzez_packages_info_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Track user package information and usage', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>
                            </div>
                </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Auto-hide notification after 5 seconds
            setTimeout(function() {
                $('#houzez-notification').fadeOut();
            }, 5000);

            // Form submission with loading state
            $('#post-types-form').on('submit', function() {
                var $submitBtn = $('.houzez-btn-primary');
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

    public static function houzez_register_settings() {

        // Register the setting.
        register_setting( 'houzez_ptype_settings', 'houzez_ptype_settings', array( __CLASS__, 'houzez_validate_settings' ) );

        /* === Settings Sections === */
        add_settings_section( 'houzez_post_types_section', esc_html__( 'Custom Post Types', 'houzez-theme-functionality' ), array( __CLASS__, 'houzez_section_post_types' ), 'houzez_post_types' );

        /* === Settings Fields === */
        add_settings_field( 'houzez_agents_post',   esc_html__( 'Agents',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_agents_field'   ), 'houzez_post_types', 'houzez_post_types_section' );

        add_settings_field( 'houzez_agencies_post',   esc_html__( 'Agencies',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_agencies_field'   ), 'houzez_post_types', 'houzez_post_types_section' );

        add_settings_field( 'houzez_packages_post',   esc_html__( 'Houzez Packages',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_packages_field'   ), 'houzez_post_types', 'houzez_post_types_section' );

        add_settings_field( 'houzez_invoices_post',   esc_html__( 'Houzez Invoices',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_invoices_field'   ), 'houzez_post_types', 'houzez_post_types_section' );

        add_settings_field( 'houzez_partners_post',   esc_html__( 'Partners',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_partners_field'   ), 'houzez_post_types', 'houzez_post_types_section' );

        add_settings_field( 'houzez_testimonials_post',   esc_html__( 'Testimonials',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_testimonials_field'   ), 'houzez_post_types', 'houzez_post_types_section' );

        add_settings_field( 'houzez_packages_info_post',   esc_html__( 'User Packages Info',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_packages_info_field'   ), 'houzez_post_types', 'houzez_post_types_section' );

    }

    /**
     * Validates the plugin settings.
     *
     * @since  1.0.8
     * @access public
     * @param  array  $input
     * @return array
     */
    public static function houzez_validate_settings( $settings ) {

        // Text boxes.
        $settings['houzez_agents_post'] = $settings['houzez_agents_post'] ? trim( strip_tags( $settings['houzez_agents_post']   ), '/' ) : '';
        $settings['houzez_agencies_post'] = $settings['houzez_agencies_post'] ? trim( strip_tags( $settings['houzez_agencies_post']   ), '/' ) : '';
        $settings['houzez_packages_post'] = $settings['houzez_packages_post'] ? trim( strip_tags( $settings['houzez_packages_post']   ), '/' ) : '';
        $settings['houzez_partners_post'] = $settings['houzez_partners_post'] ? trim( strip_tags( $settings['houzez_partners_post']   ), '/' ) : '';
        $settings['houzez_testimonials_post'] = $settings['houzez_testimonials_post'] ? trim( strip_tags( $settings['houzez_testimonials_post']   ), '/' ) : '';
        $settings['houzez_packages_info_post'] = $settings['houzez_packages_info_post'] ? trim( strip_tags( $settings['houzez_packages_info_post']   ), '/' ) : '';
        $settings['houzez_invoices_post'] = $settings['houzez_invoices_post'] ? trim( strip_tags( $settings['houzez_invoices_post']   ), '/' ) : '';

        // Return the validated/sanitized settings.
        return $settings;
    }

    /**
     * Taxonomies section callback.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public static function houzez_section_post_types() { ?>

        <p class="description">
            <?php esc_html_e( 'Disable Custom Post Types which you do not want to show(if disabled then these will not show on back-end and front-end)', 'houzez-theme-functionality' ); ?>
        </p>
    <?php }


    /**
     * Agents field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_agents_field() { ?>

        <label>
            <select name="houzez_ptype_settings[houzez_agents_post]" class="regular-text">
                <option <?php selected(self::get_setting('houzez_agents_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('houzez_agents_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }


    /**
     * Agencies field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_agencies_field() { ?>

        <label>
            <select name="houzez_ptype_settings[houzez_agencies_post]" class="regular-text">
                <option <?php selected(self::get_setting('houzez_agencies_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('houzez_agencies_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    /**
     * Packages field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_packages_field() { ?>

        <label>
            <select name="houzez_ptype_settings[houzez_packages_post]" class="regular-text">
                <option <?php selected(self::get_setting('houzez_packages_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('houzez_packages_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    /**
     * Invoices field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_invoices_field() { ?>

        <label>
            <select name="houzez_ptype_settings[houzez_invoices_post]" class="regular-text">
                <option <?php selected(self::get_setting('houzez_invoices_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('houzez_invoices_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    /**
     * Partners field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_partners_field() { ?>

        <label>
            <select name="houzez_ptype_settings[houzez_partners_post]" class="regular-text">
                <option <?php selected(self::get_setting('houzez_partners_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('houzez_partners_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    /**
     * Testomonials field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_testimonials_field() { ?>

        <label>
            <select name="houzez_ptype_settings[houzez_testimonials_post]" class="regular-text">
                <option <?php selected(self::get_setting('houzez_testimonials_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('houzez_testimonials_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    /**
     * Packages Info field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_packages_info_field() { ?>

        <label>
            <select name="houzez_ptype_settings[houzez_packages_info_post]" class="regular-text">
                <option <?php selected(self::get_setting('houzez_packages_info_post'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('houzez_packages_info_post'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    


    /**
     * Returns taxonomy settings.
     *
     * @since  1.0.8
     * @access public
     * @param  string  $setting
     * @return mixed
     */
    public static function get_setting( $setting ) {

        $defaults = self::get_default_settings();
        $settings = wp_parse_args( get_option('houzez_ptype_settings', $defaults ), $defaults );

        return isset( $settings[ $setting ] ) ? $settings[ $setting ] : false;
    }

    /**
     * Returns the default settings for the plugin.
     *
     * @since  1.0.8
     * @access public
     * @return array
     */
    public static function get_default_settings() {

        $settings = array(
            'houzez_agents_post' => 'enabled',
            'houzez_agencies_post' => 'enabled',
            'houzez_packages_post' => 'enabled',
            'houzez_invoices_post' => 'enabled',
            'houzez_partners_post' => 'enabled',
            'houzez_testimonials_post' => 'enabled',
            'houzez_packages_info_post' => 'disabled',
        );

        return $settings;
    }
	
}