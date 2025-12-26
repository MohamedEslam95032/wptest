<?php
/**
 * Class Houzez_Post_Type_Agency
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 28/09/16
 * Time: 10:16 PM
 */
class Houzez_Taxonomies {


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
        $settings = get_option('houzez_tax_settings', self::get_default_settings());
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
            $notification = __('Taxonomy settings saved successfully!', 'houzez-theme-functionality');
            $notification_type = 'success';
        }
        
        ?>
        <div class="wrap houzez-template-library">
            <div class="houzez-header">
                <div class="houzez-header-content">
                    <div class="houzez-logo">
                        <h1><?php esc_html_e('Taxonomies Management', 'houzez-theme-functionality'); ?></h1>
                    </div>
                    <div class="houzez-header-actions">
                        <button type="submit" form="taxonomies-form" class="houzez-btn houzez-btn-primary">
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
                            <i class="dashicons dashicons-category"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($total_count); ?></h3>
                            <p><?php esc_html_e('Total Taxonomies', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-yes-alt"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($enabled_count); ?></h3>
                            <p><?php esc_html_e('Enabled Taxonomies', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-dismiss"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($total_count - $enabled_count); ?></h3>
                            <p><?php esc_html_e('Disabled Taxonomies', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-location"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo round(($enabled_count / $total_count) * 100); ?>%</h3>
                            <p><?php esc_html_e('Location Coverage', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Main Configuration Card -->
                <div class="houzez-main-card">
                    <div class="houzez-card-header">
                        <h2>
                            <i class="dashicons dashicons-category"></i>
                            <?php esc_html_e('Location Taxonomies Configuration', 'houzez-theme-functionality'); ?>
                        </h2>
                        <div class="houzez-status-badge <?php echo $enabled_count > 0 ? 'houzez-status-success' : 'houzez-status-warning'; ?>">
                            <?php echo $enabled_count > 0 ? __('Active', 'houzez-theme-functionality') : __('Inactive', 'houzez-theme-functionality'); ?>
                        </div>
                    </div>
                    <div class="houzez-card-body">
                        <p class="houzez-description">
                            <?php esc_html_e('Enable or disable location taxonomies for property categorization. Disabled taxonomies will not appear in the admin area or frontend search filters.', 'houzez-theme-functionality'); ?>
                        </p>
                        
                        <form method="post" action="options.php" id="taxonomies-form">
                    <?php settings_fields( 'houzez_tax_settings' ); ?>
                            
                            <div class="houzez-form-grid">
                                <div class="houzez-form-group">
                                    <label for="property_country" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-site"></i>
                                        <?php esc_html_e('Country', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="property_country" name="houzez_tax_settings[property_country]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('property_country'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('property_country'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Organize properties by country for international listings', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_state" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-site-alt2"></i>
                                        <?php esc_html_e('County / State', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="property_state" name="houzez_tax_settings[property_state]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('property_state'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('property_state'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Categorize properties by state, province, or county', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_city" class="houzez-form-label">
                                        <i class="dashicons dashicons-building"></i>
                                        <?php esc_html_e('City', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="property_city" name="houzez_tax_settings[property_city]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('property_city'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('property_city'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Group properties by city for local market organization', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_area" class="houzez-form-label">
                                        <i class="dashicons dashicons-location"></i>
                                        <?php esc_html_e('Area', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <select id="property_area" name="houzez_tax_settings[property_area]" class="houzez-form-select">
                                        <option <?php selected(self::get_setting('property_area'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez-theme-functionality'); ?></option>
                                        <option <?php selected(self::get_setting('property_area'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez-theme-functionality'); ?></option>
                                    </select>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Define specific neighborhoods or areas within cities', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>
                            </div>
                </form>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="houzez-main-card">
                    <div class="houzez-card-header">
                        <h2>
                            <i class="dashicons dashicons-info"></i>
                            <?php esc_html_e('Location Hierarchy', 'houzez-theme-functionality'); ?>
                        </h2>
                    </div>
                    
                    <div class="houzez-card-body">
                        <div class="houzez-actions">
                            <div class="houzez-action">
                                <div class="houzez-action-icon">
                                    <i class="dashicons dashicons-admin-site"></i>
                                </div>
                                <div class="houzez-action-content">
                                    <h4><?php esc_html_e('Country Level', 'houzez-theme-functionality'); ?></h4>
                                    <p><?php esc_html_e('Top-level geographic classification for international property listings and global market coverage.', 'houzez-theme-functionality'); ?></p>
                                </div>
                            </div>

                            <div class="houzez-action">
                                <div class="houzez-action-icon">
                                    <i class="dashicons dashicons-admin-site-alt2"></i>
                                </div>
                                <div class="houzez-action-content">
                                    <h4><?php esc_html_e('State/Province Level', 'houzez-theme-functionality'); ?></h4>
                                    <p><?php esc_html_e('Regional classification within countries for better property organization and regional market analysis.', 'houzez-theme-functionality'); ?></p>
                                </div>
                            </div>

                            <div class="houzez-action">
                                <div class="houzez-action-icon">
                                    <i class="dashicons dashicons-building"></i>
                                </div>
                                <div class="houzez-action-content">
                                    <h4><?php esc_html_e('City Level', 'houzez-theme-functionality'); ?></h4>
                                    <p><?php esc_html_e('Urban area classification for local market focus and city-specific property searches.', 'houzez-theme-functionality'); ?></p>
                                </div>
                            </div>

                            <div class="houzez-action">
                                <div class="houzez-action-icon">
                                    <i class="dashicons dashicons-location"></i>
                                </div>
                                <div class="houzez-action-content">
                                    <h4><?php esc_html_e('Area/Neighborhood Level', 'houzez-theme-functionality'); ?></h4>
                                    <p><?php esc_html_e('Granular location classification for specific neighborhoods, districts, or local areas within cities.', 'houzez-theme-functionality'); ?></p>
                                </div>
                            </div>
                        </div>
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
            $('#taxonomies-form').on('submit', function() {
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
        register_setting( 'houzez_tax_settings', 'houzez_tax_settings', array( __CLASS__, 'houzez_validate_settings' ) );

        /* === Settings Sections === */
        add_settings_section( 'houzez_taxonomies_section', esc_html__( 'Taxonomies', 'houzez-theme-functionality' ), array( __CLASS__, 'houzez_section_taxonomies' ), 'houzez_taxonomies' );

        /* === Settings Fields === */
        
        add_settings_field( 'property_country',   esc_html__( 'Country',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_tax_country_field'   ), 'houzez_taxonomies', 'houzez_taxonomies_section' );

        add_settings_field( 'property_city',   esc_html__( 'City',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_tax_city_field'   ), 'houzez_taxonomies', 'houzez_taxonomies_section' );

        add_settings_field( 'property_area',   esc_html__( 'Area',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_tax_neighborhood_field'   ), 'houzez_taxonomies', 'houzez_taxonomies_section' );

        add_settings_field( 'property_state',   esc_html__( 'County / State',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_tax_state_field'   ), 'houzez_taxonomies', 'houzez_taxonomies_section' );

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
        $settings['property_country'] = $settings['property_country'] ? trim( strip_tags( $settings['property_country']   ), '/' ) : '';
        $settings['property_city'] = $settings['property_city'] ? trim( strip_tags( $settings['property_city']   ), '/' ) : '';
        $settings['property_area'] = $settings['property_area'] ? trim( strip_tags( $settings['property_area']   ), '/' ) : '';
        $settings['property_state'] = $settings['property_state'] ? trim( strip_tags( $settings['property_state']   ), '/' ) : '';

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
    public static function houzez_section_taxonomies() { ?>

        <p class="description">
            <?php esc_html_e( 'Disable Taxonomies which you do not want to show(if disabled then these will not show on back-end and front-end)', 'houzez-theme-functionality' ); ?>
        </p>
    <?php }
    
    /**
     * Country field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_tax_country_field() { ?>

        <label>
            <select name="houzez_tax_settings[property_country]" class="regular-text">
                <option <?php selected(self::get_setting('property_country'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('property_country'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    /**
     * City field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_tax_city_field() { ?>

        <label>
            <select name="houzez_tax_settings[property_city]" class="regular-text">
                <option <?php selected(self::get_setting('property_city'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('property_city'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    /**
     * Neighbourhood field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_tax_neighborhood_field() { ?>

        <label>
            <select name="houzez_tax_settings[property_area]" class="regular-text">
                <option <?php selected(self::get_setting('property_area'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('property_area'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
            </select>
        </label>

    <?php }

    /**
     * State field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_tax_state_field() { ?>

        <label>
            <select name="houzez_tax_settings[property_state]" class="regular-text">
                <option <?php selected(self::get_setting('property_state'), 'enabled'); ?> value="enabled"><?php esc_html_e('Enabled', 'houzez'); ?></option>
                <option <?php selected(self::get_setting('property_state'), 'disabled'); ?> value="disabled"><?php esc_html_e('Disabled', 'houzez'); ?></option>
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
        $settings = wp_parse_args( get_option('houzez_tax_settings', $defaults ), $defaults );

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
            'property_country' => 'enabled',
            'property_city' => 'enabled',
            'property_area' => 'enabled',
            'property_state' => 'enabled',
        );

        return $settings;
    }
	
}