<?php
/**
 * Class Houzez_Post_Type_Agency
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 28/09/16
 * Time: 10:16 PM
 */
class Houzez_Permalinks {


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
        $settings = get_option('houzez_settings', array());
        $configured_count = 0;
        $total_fields = 11; // Total number of permalink fields
        
        // Count configured permalinks (non-empty values)
        $permalink_fields = [
            'property_rewrite_base', 'property_type_rewrite_base', 'property_feature_rewrite_base',
            'property_status_rewrite_base', 'property_country_rewrite_base', 'property_state_rewrite_base',
            'property_city_rewrite_base', 'property_area_rewrite_base', 'property_label_rewrite_base',
            'agent_rewrite_base', 'agency_rewrite_base'
        ];
        
        foreach ($permalink_fields as $field) {
            if (!empty($settings[$field])) {
                $configured_count++;
            }
        }
        
        // Handle notifications
        $notification = '';
        $notification_type = '';
        
        if (isset($_GET['settings_saved'])) {
            $notification = __('Permalink settings saved successfully!', 'houzez-theme-functionality');
            $notification_type = 'success';
        }
        
        ?>
        <div class="wrap houzez-template-library">
            <div class="houzez-header">
                <div class="houzez-header-content">
                    <div class="houzez-logo">
                        <h1><?php esc_html_e('Permalinks Management', 'houzez-theme-functionality'); ?></h1>
                    </div>
                    <div class="houzez-header-actions">
                        <button type="submit" form="permalinks-form" class="houzez-btn houzez-btn-primary">
                            <i class="dashicons dashicons-yes"></i>
                            <?php esc_html_e('Save Permalinks', 'houzez-theme-functionality'); ?>
                        </button>
                        <button type="button" id="reset-permalinks" class="houzez-btn houzez-btn-secondary">
                            <i class="dashicons dashicons-undo"></i>
                            <?php esc_html_e('Reset to Defaults', 'houzez-theme-functionality'); ?>
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
                            <i class="dashicons dashicons-admin-links"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($total_fields); ?></h3>
                            <p><?php esc_html_e('Total Permalinks', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-yes-alt"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo intval($configured_count); ?></h3>
                            <p><?php esc_html_e('Configured URLs', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-admin-home"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo parse_url(home_url(), PHP_URL_HOST); ?></h3>
                            <p><?php esc_html_e('Base Domain', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-stat-card">
                        <div class="houzez-stat-icon">
                            <i class="dashicons dashicons-performance"></i>
                        </div>
                        <div class="houzez-stat-content">
                            <h3><?php echo round(($configured_count / $total_fields) * 100); ?>%</h3>
                            <p><?php esc_html_e('Configuration Rate', 'houzez-theme-functionality'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Main Configuration Card -->
                <div class="houzez-main-card">
                    <div class="houzez-card-header">
                        <h2>
                            <i class="dashicons dashicons-admin-links"></i>
                            <?php esc_html_e('Custom Permalink Configuration', 'houzez-theme-functionality'); ?>
                        </h2>
                        <div class="houzez-status-badge <?php echo $configured_count > 0 ? 'houzez-status-success' : 'houzez-status-warning'; ?>">
                            <?php echo $configured_count > 0 ? __('Configured', 'houzez-theme-functionality') : __('Default', 'houzez-theme-functionality'); ?>
                        </div>
                    </div>
                    <div class="houzez-card-body">
                        <p class="houzez-description">
                            <?php esc_html_e('Customize URL structures for properties, taxonomies, and other content types. Changes will take effect after saving and may require permalink refresh.', 'houzez-theme-functionality'); ?>
                        </p>
                        
                        <form method="post" action="options.php" id="permalinks-form">
                    <?php settings_fields( 'houzez_settings' ); ?>
                            
                            <div class="houzez-form-grid">
                                <!-- Property URLs -->
                                <div class="houzez-form-group houzez-form-group-full">
                                    <h3 style="margin: 0 0 20px 0; color: #1d2327; font-size: 16px; font-weight: 600;">
                                        <i class="dashicons dashicons-admin-home" style="margin-right: 8px; color: #0088cc;"></i>
                                        <?php esc_html_e('Property URLs', 'houzez-theme-functionality'); ?>
                                    </h3>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-home"></i>
                                        <?php esc_html_e('Property Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_rewrite_base" name="houzez_settings[property_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_rewrite_base() ); ?>" class="houzez-form-input" placeholder="property" />
                                        <span class="url-suffix">/property-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('Base URL structure for individual property pages', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="agent_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-users"></i>
                                        <?php esc_html_e('Agent Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="agent_rewrite_base" name="houzez_settings[agent_rewrite_base]" value="<?php echo esc_attr( houzez_get_agent_rewrite_base() ); ?>" class="houzez-form-input" placeholder="agent" />
                                        <span class="url-suffix">/agent-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for agent profile pages', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="agency_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-building"></i>
                                        <?php esc_html_e('Agency Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="agency_rewrite_base" name="houzez_settings[agency_rewrite_base]" value="<?php echo esc_attr( houzez_get_agency_rewrite_base() ); ?>" class="houzez-form-input" placeholder="agency" />
                                        <span class="url-suffix">/agency-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for agency profile pages', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <!-- Taxonomy URLs -->
                                <div class="houzez-form-group houzez-form-group-full">
                                    <h3 style="margin: 30px 0 20px 0; color: #1d2327; font-size: 16px; font-weight: 600;">
                                        <i class="dashicons dashicons-category" style="margin-right: 8px; color: #0088cc;"></i>
                                        <?php esc_html_e('Property Taxonomy URLs', 'houzez-theme-functionality'); ?>
                                    </h3>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_type_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-multisite"></i>
                                        <?php esc_html_e('Property Type Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_type_rewrite_base" name="houzez_settings[property_type_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_type_rewrite_base() ); ?>" class="houzez-form-input" placeholder="property-type" />
                                        <span class="url-suffix">/type-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for property type archives (e.g., apartments, houses)', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_status_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-flag"></i>
                                        <?php esc_html_e('Property Status Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_status_rewrite_base" name="houzez_settings[property_status_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_status_rewrite_base() ); ?>" class="houzez-form-input" placeholder="property-status" />
                                        <span class="url-suffix">/status-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for property status archives (e.g., for-sale, for-rent)', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_feature_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-star-filled"></i>
                                        <?php esc_html_e('Property Feature Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_feature_rewrite_base" name="houzez_settings[property_feature_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_feature_rewrite_base() ); ?>" class="houzez-form-input" placeholder="property-feature" />
                                        <span class="url-suffix">/feature-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for property feature archives (e.g., swimming-pool, garage)', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_label_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-tag"></i>
                                        <?php esc_html_e('Property Label Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_label_rewrite_base" name="houzez_settings[property_label_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_label_rewrite_base() ); ?>" class="houzez-form-input" placeholder="property-label" />
                                        <span class="url-suffix">/label-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for property label archives (e.g., featured, hot-offer)', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <!-- Location URLs -->
                                <div class="houzez-form-group houzez-form-group-full">
                                    <h3 style="margin: 30px 0 20px 0; color: #1d2327; font-size: 16px; font-weight: 600;">
                                        <i class="dashicons dashicons-location" style="margin-right: 8px; color: #0088cc;"></i>
                                        <?php esc_html_e('Location URLs', 'houzez-theme-functionality'); ?>
                                    </h3>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_country_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-site"></i>
                                        <?php esc_html_e('Property Country Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_country_rewrite_base" name="houzez_settings[property_country_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_country_rewrite_base() ); ?>" class="houzez-form-input" placeholder="country" />
                                        <span class="url-suffix">/country-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for country-based property listings', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_state_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-admin-site-alt2"></i>
                                        <?php esc_html_e('Property State Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_state_rewrite_base" name="houzez_settings[property_state_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_state_rewrite_base() ); ?>" class="houzez-form-input" placeholder="state" />
                                        <span class="url-suffix">/state-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for state/province-based property listings', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_city_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-building"></i>
                                        <?php esc_html_e('Property City Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_city_rewrite_base" name="houzez_settings[property_city_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_city_rewrite_base() ); ?>" class="houzez-form-input" placeholder="city" />
                                        <span class="url-suffix">/city-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for city-based property listings', 'houzez-theme-functionality'); ?>
                                    </div>
                                </div>

                                <div class="houzez-form-group">
                                    <label for="property_area_rewrite_base" class="houzez-form-label">
                                        <i class="dashicons dashicons-location"></i>
                                        <?php esc_html_e('Property Area Slug', 'houzez-theme-functionality'); ?>
                                    </label>
                                    <div class="houzez-url-field">
                                        <span class="url-prefix"><?php echo esc_url( home_url( '/' ) ); ?></span>
                                        <input type="text" id="property_area_rewrite_base" name="houzez_settings[property_area_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_area_rewrite_base() ); ?>" class="houzez-form-input" placeholder="area" />
                                        <span class="url-suffix">/area-name/</span>
                                    </div>
                                    <div class="houzez-form-help">
                                        <?php esc_html_e('URL structure for area/neighborhood-based property listings', 'houzez-theme-functionality'); ?>
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
            $('#permalinks-form').on('submit', function() {
                var $submitBtn = $('.houzez-btn-primary');
                var originalText = $submitBtn.html();
                
                $submitBtn.prop('disabled', true).html('<i class="dashicons dashicons-update"></i> <?php echo esc_js(__('Saving...', 'houzez-theme-functionality')); ?>');
                
                // Let the form submit normally, but show loading state
                setTimeout(function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                }, 3000);
            });

            // Reset to defaults functionality
            $('#reset-permalinks').on('click', function() {
                if (confirm('<?php echo esc_js(__('Are you sure you want to reset all permalinks to their default values?', 'houzez-theme-functionality')); ?>')) {
                    // Reset all input fields to their default values
                    $('#property_rewrite_base').val('property');
                    $('#agent_rewrite_base').val('agent');
                    $('#agency_rewrite_base').val('agency');
                    $('#property_type_rewrite_base').val('property-type');
                    $('#property_status_rewrite_base').val('property-status');
                    $('#property_feature_rewrite_base').val('property-feature');
                    $('#property_label_rewrite_base').val('property-label');
                    $('#property_country_rewrite_base').val('country');
                    $('#property_state_rewrite_base').val('state');
                    $('#property_city_rewrite_base').val('city');
                    $('#property_area_rewrite_base').val('area');
                }
            });
        });
        </script>
    <?php
    }

    public static function houzez_register_settings() {

        // Register the setting.
        register_setting( 'houzez_settings', 'houzez_settings', array( __CLASS__, 'houzez_validate_settings' ) );

        /* === Settings Sections === */
        add_settings_section( 'permalinks', esc_html__( 'Permalinks', 'houzez-theme-functionality' ), array( __CLASS__, 'houzez_section_permalinks' ), 'houzez_permalinks' );

        /* === Settings Fields === */
        add_settings_field( 'property_rewrite_base',   esc_html__( 'Property Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_slug_field'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'property_type_rewrite_base',   esc_html__( 'Property Type Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_type_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'property_feature_rewrite_base',   esc_html__( 'Property Feature Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_feature_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'property_status_rewrite_base',   esc_html__( 'Property Status Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_status_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'property_country_rewrite_base',   esc_html__( 'Property Country Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_country_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'property_state_rewrite_base',   esc_html__( 'Property State Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_state_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'property_city_rewrite_base',   esc_html__( 'Property City Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_city_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'property_area_rewrite_base',   esc_html__( 'Property Area Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_area_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'property_label_rewrite_base',   esc_html__( 'Property Label Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_property_label_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'agent_rewrite_base',   esc_html__( 'Agent Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_agent_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );

        add_settings_field( 'agency_rewrite_base',   esc_html__( 'Agency Slug',   'houzez-theme-functionality' ), array( __CLASS__, 'houzez_agency_rewrite_base'   ), 'houzez_permalinks', 'permalinks' );
        
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
        $settings['property_rewrite_base'] = $settings['property_rewrite_base'] ? trim( strip_tags( $settings['property_rewrite_base']   ), '/' ) : '';
        $settings['property_type_rewrite_base'] = $settings['property_type_rewrite_base'] ? trim( strip_tags( $settings['property_type_rewrite_base']   ), '/' ) : '';
        $settings['property_feature_rewrite_base'] = $settings['property_feature_rewrite_base'] ? trim( strip_tags( $settings['property_feature_rewrite_base']   ), '/' ) : '';
        $settings['property_status_rewrite_base'] = $settings['property_status_rewrite_base'] ? trim( strip_tags( $settings['property_status_rewrite_base']   ), '/' ) : '';
        $settings['property_area_rewrite_base'] = $settings['property_area_rewrite_base'] ? trim( strip_tags( $settings['property_area_rewrite_base']   ), '/' ) : '';
        $settings['property_label_rewrite_base'] = $settings['property_label_rewrite_base'] ? trim( strip_tags( $settings['property_label_rewrite_base']   ), '/' ) : '';

        $settings['property_country_rewrite_base'] = $settings['property_country_rewrite_base'] ? trim( strip_tags( $settings['property_country_rewrite_base']   ), '/' ) : '';

        $settings['agent_rewrite_base'] = $settings['agent_rewrite_base'] ? trim( strip_tags( $settings['agent_rewrite_base']   ), '/' ) : '';

        $settings['agency_rewrite_base'] = $settings['agency_rewrite_base'] ? trim( strip_tags( $settings['agency_rewrite_base']   ), '/' ) : '';

        // Return the validated/sanitized settings.
        return $settings;
    }

    /**
     * Permalinks section callback.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public static function houzez_section_permalinks() { ?>

        <p class="description">
            <?php esc_html_e( 'Set up custom permalinks for the property section on your site.', 'houzez-theme-functionality' ); ?>
        </p>
    <?php }

    /**
     * Property rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_property_slug_field() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Agent rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_agent_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[agent_rewrite_base]" value="<?php echo esc_attr( houzez_get_agent_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Agency rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_agency_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[agency_rewrite_base]" value="<?php echo esc_attr( houzez_get_agency_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Property type rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_property_type_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_type_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_type_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Property status rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_property_status_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_status_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_status_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Property feature rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_property_feature_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_feature_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_feature_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Property label rewrite base field callback.
     *
     * @since  2.0.6
     * @access public
     * @return void
     */
    public static function houzez_property_label_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_label_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_label_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Property area rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_property_area_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_area_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_area_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Property city rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_property_city_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_city_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_city_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Property state rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_property_state_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_state_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_state_rewrite_base() ); ?>" />
        </label>

    <?php }

    /**
     * Property country rewrite base field callback.
     *
     * @since  1.0.8
     * @access public
     * @return void
     */
    public static function houzez_property_country_rewrite_base() { ?>

        <label>
            <code><?php echo esc_url( home_url( '/' ) ); ?></code>
            <input type="text" class="regular-text code" name="houzez_settings[property_country_rewrite_base]" value="<?php echo esc_attr( houzez_get_property_country_rewrite_base() ); ?>" />
        </label>

    <?php }

	
}