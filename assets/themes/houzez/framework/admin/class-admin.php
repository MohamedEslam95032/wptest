<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Admin {

    public static $instance;
    private $template_path = HOUZEZ_FRAMEWORK . 'admin/';

    public function __construct() {

        add_action( 'admin_menu', array( $this, 'houzez_register_admin_pages' ) );
        add_action( 'admin_menu', array( $this, 'remove_parent_menu' ) );
        add_action('wp_ajax_houzez_plugin_installation', array( __CLASS__, 'houzez_plugin_installation'));
        add_action('wp_ajax_houzez_plugin_activate', array( __CLASS__, 'houzez_plugin_activate'));
        add_action('wp_ajax_houzez_plugin_update', array( __CLASS__, 'houzez_plugin_update'));
        add_action('wp_ajax_houzez_plugin_deactivate', array( __CLASS__, 'houzez_plugin_deactivate'));
        add_action('wp_ajax_houzez_plugin_uninstall', array( __CLASS__, 'houzez_plugin_uninstall'));
        add_action('wp_ajax_houzez_bulk_plugin_action', array( __CLASS__, 'houzez_bulk_plugin_action'));
        add_action('wp_ajax_houzez_feedback', array( $this, 'houzez_feedback'));
        add_action('wp_ajax_houzez_verify_purchase', array( $this, 'verify_purchase'));
        add_action('wp_ajax_houzez_deactivate_purchase', array( $this, 'deactivate_purchase'));

        // https://github.com/elementor/elementor/issues/6022
		add_action( 'admin_init', function() {
			if ( did_action( 'elementor/loaded' ) ) {
				remove_action( 'admin_init', [ \Elementor\Plugin::$instance->admin, 'maybe_redirect_to_getting_started' ] );
			}
		}, 1 );

        // Add modern header to Theme Builder page
        add_action( 'admin_head', array( $this, 'add_theme_builder_header' ) );
    }

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	public function houzez_register_admin_pages() {
    	$sub_menus = array();

    	$houzez = houzez_theme_branding();

        add_menu_page(
            $houzez,
            $houzez,
            'manage_options',
            'houzez_dashboard',
            '',
            HOUZEZ_IMAGE.'houzez-icon.svg',
            '5'
        );

        $sub_menus['plugins'] = array(
            'houzez_dashboard',
            esc_html__( 'Plugins', 'houzez' ),
            esc_html__( 'Plugins', 'houzez' ),
            'manage_options',
            'houzez_plugins',
            array( $this, 'plugins' ),
        );

        if( class_exists('\HouzezStudio\Houzez_Studio') ) {
        	$sub_menus['houzez_studio'] = array( 
	            'houzez_dashboard', 
	            esc_html__( 'Theme Builder', 'houzez' ),
	            esc_html__( 'Theme Builder', 'houzez' ),
	            'edit_pages', 
	            'edit.php?post_type=fts_builder',
	        );
        }

        if( class_exists('Houzez') ) {
	        $sub_menus['houzez_fbuilder'] = array( 
	            'houzez_dashboard', 
	            esc_html__( 'Fields builder', 'houzez' ),
	            esc_html__( 'Fields builder', 'houzez' ),
	            'manage_options', 
	            'houzez_fbuilder', 
	            array( 'Houzez_Fields_Builder', 'render' )
	        );

			// $sub_menus['houzez-template-library'] = array(
			// 	'houzez_dashboard',
			// 	esc_html__( 'Template Library', 'houzez' ),
			// 	esc_html__( 'Template Library', 'houzez' ),
			// 	'manage_options',
			// 	'houzez-template-library',
			// 	array( 'Houzez_Library', 'admin_page' ),
			// );

			$sub_menus['houzez_image_sizes'] = array(
				'houzez_dashboard',
				esc_html__( 'Media Manager', 'houzez' ),
				esc_html__( 'Media Manager', 'houzez' ),
				'manage_options',
				'houzez_image_sizes',
				array( 'Houzez_Image_Sizes', 'render_page' ),
	        );

	        $sub_menus['houzez_currencies'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Currencies', 'houzez' ),
	            esc_html__( 'Currencies', 'houzez' ),
	            'manage_options',
	            'houzez_currencies',
	            array( 'Houzez_Currencies', 'render' )
	        );

	        $sub_menus['fcc_api_settings'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Currency Switcher', 'houzez' ),
	            esc_html__( 'Currency Switcher', 'houzez' ),
	            'manage_options',
	            'fcc_api_settings',
	            array( 'FCC_API_Settings', 'render' )
	        );

	        $sub_menus['houzez_post_types'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Post Types', 'houzez' ),
	            esc_html__( 'Post Types', 'houzez' ),
	            'manage_options',
	            'houzez_post_types',
	            array( 'Houzez_Post_Type', 'render' )
	        );

	        $sub_menus['houzez_taxonomies'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Taxonomies', 'houzez' ),
	            esc_html__( 'Taxonomies', 'houzez' ),
	            'manage_options',
	            'houzez_taxonomies',
	            array( 'Houzez_Taxonomies', 'render' )
	        );

	        $sub_menus['houzez_permalinks'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Permalinks', 'houzez' ),
	            esc_html__( 'Permalinks', 'houzez' ),
	            'manage_options',
	            'houzez_permalinks',
	            array( 'Houzez_Permalinks', 'render' )
	        );

	        $sub_menus['houzez_import_locations'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Import Locations', 'houzez' ),
	            esc_html__( 'Import Locations', 'houzez' ),
	            'manage_options',
	            'import_locations',
	            array( 'Houzez_Import_Locations', 'render' )
	        );
	    }

        // $sub_menus['mobile_app'] = array(
        //     'houzez_dashboard',
        //     esc_html__( 'Mobile App', 'houzez' ),
        //     esc_html__( 'Mobile App', 'houzez' ),
        //     'manage_options',
        //     'houzez_mobile_app',
        //     array( $this, 'mobile_app' ),
        // );

	    // Add filter for third party uses
        $sub_menus = apply_filters( 'houzez_admin_sub_menus', $sub_menus, 20 );

        $sub_menus['documentation'] = array(
            'houzez_dashboard',
            esc_html__( 'Documentation', 'houzez' ),
            esc_html__( 'Documentation', 'houzez' ),
            'manage_options',
            'houzez_help',
            array( $this, 'documentation' ),
        );

        $sub_menus['feedback'] = array(
            'houzez_dashboard',
            esc_html__( 'Feedback', 'houzez' ),
            esc_html__( 'Feedback', 'houzez' ),
            'manage_options',
            'houzez_feedback',
            array( $this, 'feedback' ),
        );

        $sub_menus['purchase_code'] = array(
            'houzez_dashboard',
            esc_html__( 'Purchase Code', 'houzez' ),
            esc_html__( 'Purchase Code', 'houzez' ),
            'manage_options',
            'houzez_purchase',
            array( $this, 'purchase_code' ),
        );

		if ( class_exists( 'OCDI_Plugin' ) && class_exists('Houzez') && houzez_theme_verified() ) {
			$sub_menus['demo_import'] = array(
				'houzez_dashboard',
				esc_html__( 'Demo Import', 'houzez' ),
				esc_html__( 'Demo Import', 'houzez' ),
				'manage_options',
				'admin.php?page=houzez-one-click-demo-import',
			);
		}

		/*$sub_menus['houzez_new_html'] = array(
	            'houzez_dashboard',
	            esc_html__( 'New HTML', 'houzez' ),
	            esc_html__( 'New HTML', 'houzez' ),
	            'manage_options',
	            'houzez_new_html',
	            array( 'Houzez_HTML', 'render' )
	        );*/

        if ( $sub_menus ) {
            foreach ( $sub_menus as $sub_menu ) {
                call_user_func_array( 'add_submenu_page', $sub_menu );
            }
        }
	}

	public static function houzez_plugin_installation() {
		check_ajax_referer( 'houzez-admin-nonce' );

		$status = array();
		$download_link = null;
		$plugin_source = isset( $_POST['plugin_source'] ) ? $_POST['plugin_source'] : '';
		$plugin_slug = isset( $_POST['plugin_slug'] ) ? $_POST['plugin_slug'] : '';

		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		// Check if current user have permission to install plugin or not
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error();
		}

		if( empty($plugin_slug) ) {
			wp_send_json_error();
		}

		// Retrieves plugin installer pages from the WordPress.org Plugins API.
		$plugin_api = plugins_api(
			'plugin_information',
			array(
				'slug' => sanitize_key( wp_unslash( $plugin_slug ) ),
			)
		);
		
		if ( ! empty( $plugin_source ) ) {

			$download_link = esc_url( $plugin_source );

		} else {
			if ( is_wp_error( $plugin_api ) ) {
				wp_send_json_error();
			}
			$download_link        = $plugin_api->download_link;
		}

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$response = $upgrader->install( $download_link );

		if ( is_wp_error( $response ) ) {
			$status['errorCode']    = $response->get_error_code();
			$status['errorMessage'] = $response->get_error_message();
			wp_send_json_error( $status );
		} else {
			wp_send_json_success();
		}
		
		
	}

	public static function houzez_plugin_activate() {
	    check_ajax_referer( 'houzez-admin-nonce' );

	    $error = array();
	    $plugin_file = isset( $_POST['plugin_file'] ) ? $_POST['plugin_file'] : '';

	    if( empty($plugin_file) ) {
	    	wp_send_json_error();
	    }

	    // Check if current user has permission to activate plugins
	    if ( ! current_user_can( 'activate_plugins' ) ) {
	    	$error['errorMessage'] = __( 'You do not have permission to activate plugins.', 'houzez' );
	    	wp_send_json_error( $error );
	    }

		$response  = activate_plugin( $plugin_file );
		if ( is_wp_error( $response ) ) {
			$error['errorMessage'] = $response->get_error_message();
			wp_send_json_error( $error );
		} else {
			wp_send_json_success();
		}
	}

	public static function houzez_plugin_update() {
		check_ajax_referer( 'houzez-admin-nonce' );

		$error = array();
		$plugin_file = isset( $_POST['plugin_file'] ) ? sanitize_text_field( $_POST['plugin_file'] ) : '';
		$plugin_name = isset( $_POST['plugin_name'] ) ? sanitize_text_field( $_POST['plugin_name'] ) : '';

		if( empty($plugin_file) ) {
			wp_send_json_error();
		}

		// Check if current user have permission to update plugin or not
		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_send_json_error();
		}

		// Get plugin data from our plugins array to check if it's a custom plugin
		$plugins_array = self::get_plugins_array();
		$plugin_data = null;
		
		foreach ( $plugins_array as $plugin ) {
			if ( $plugin['path'] === $plugin_file ) {
				$plugin_data = $plugin;
				break;
			}
		}

		// If plugin data found and it has a custom source, use custom update process
		if ( $plugin_data !== null && is_array( $plugin_data ) && isset( $plugin_data['source'] ) && ! empty( $plugin_data['source'] ) ) {
			$result = self::update_custom_plugin( $plugin_file, $plugin_data['source'], $plugin_data['name'] );
			if ( $result['success'] ) {
				wp_send_json_success();
			} else {
				$error['errorMessage'] = $result['message'];
				wp_send_json_error( $error );
			}
		} else {
			// Use WordPress built-in update for WordPress.org plugins
			include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

			$skin = new WP_Ajax_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$response = $upgrader->upgrade( $plugin_file );

			if ( is_wp_error( $response ) ) {
				$error['errorCode'] = $response->get_error_code();
				$error['errorMessage'] = $response->get_error_message();
				wp_send_json_error( $error );
			} else {
				wp_send_json_success();
			}
		}
	}

	public static function houzez_plugin_deactivate() {
		check_ajax_referer( 'houzez-admin-nonce' );

		$error = array();
		$plugin_file = isset( $_POST['plugin_file'] ) ? sanitize_text_field( $_POST['plugin_file'] ) : '';
		$plugin_name = isset( $_POST['plugin_name'] ) ? sanitize_text_field( $_POST['plugin_name'] ) : '';

		if( empty($plugin_file) ) {
			wp_send_json_error();
		}

		// Check if current user have permission to deactivate plugin or not
		if ( ! current_user_can( 'deactivate_plugin', $plugin_file ) ) {
			wp_send_json_error();
		}

		$response = deactivate_plugins( $plugin_file );
		// deactivate_plugins() doesn't return WP_Error, it returns null on success
		// We'll assume success if no fatal error occurred
		wp_send_json_success();
	}

	public static function houzez_plugin_uninstall() {
		check_ajax_referer( 'houzez-admin-nonce' );

		$error = array();
		$plugin_file = isset( $_POST['plugin_file'] ) ? sanitize_text_field( $_POST['plugin_file'] ) : '';
		$plugin_name = isset( $_POST['plugin_name'] ) ? sanitize_text_field( $_POST['plugin_name'] ) : '';

		if( empty($plugin_file) ) {
			wp_send_json_error();
		}

		// Check if current user have permission to delete plugins or not
		if ( ! current_user_can( 'delete_plugins' ) ) {
			wp_send_json_error();
		}

		// Check if plugin is required - don't allow uninstalling required plugins
		$plugins_array = self::get_plugins_array();
		$is_required = false;
		foreach ( $plugins_array as $plugin ) {
			if ( $plugin['path'] === $plugin_file && $plugin['required'] ) {
				$is_required = true;
				break;
			}
		}

		if ( $is_required ) {
			$error['errorMessage'] = __( 'Cannot uninstall required plugin.', 'houzez' );
			wp_send_json_error( $error );
		}

		// First deactivate the plugin if it's active
		if ( is_plugin_active( $plugin_file ) ) {
			deactivate_plugins( $plugin_file );
		}

		// Include necessary files for plugin deletion
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Delete the plugin
		$response = delete_plugins( array( $plugin_file ) );
		
		if ( is_wp_error( $response ) ) {
			$error['errorCode'] = $response->get_error_code();
			$error['errorMessage'] = $response->get_error_message();
			wp_send_json_error( $error );
		} else {
			wp_send_json_success();
		}
	}

	public static function houzez_bulk_plugin_action() {
		check_ajax_referer( 'houzez-admin-nonce' );

		$bulk_action = isset( $_POST['bulk_action'] ) ? sanitize_text_field( $_POST['bulk_action'] ) : '';
		$plugins = isset( $_POST['plugins'] ) ? array_map( 'sanitize_text_field', $_POST['plugins'] ) : array();

		if ( empty( $plugins ) || empty( $bulk_action ) ) {
			wp_send_json_error( __( 'No plugins selected or invalid action.', 'houzez' ) );
		}

		// Check user capabilities
		if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'update_plugins' ) ) {
			wp_send_json_error( __( 'You do not have permission to manage plugins.', 'houzez' ) );
		}

		// Clear plugin cache to get fresh status
		if ( ! function_exists( 'wp_clean_plugins_cache' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		wp_clean_plugins_cache();

		$results = array();
		$errors = array();

		// Get plugin array for reference
		$plugins_array = self::get_plugins_array();

		foreach ( $plugins as $plugin_slug ) {
			$plugin_data = null;
			foreach ( $plugins_array as $plugin ) {
				if ( isset( $plugin['slug'] ) && $plugin['slug'] === $plugin_slug ) {
					$plugin_data = $plugin;
					break;
				}
			}

			if ( ! $plugin_data ) {
				$errors[] = sprintf( __( 'Plugin %s not found in configuration.', 'houzez' ), $plugin_slug );
				continue;
			}

			// Ensure we have required plugin data
			if ( ! isset( $plugin_data['path'] ) || ! isset( $plugin_data['name'] ) ) {
				$errors[] = sprintf( __( 'Invalid plugin configuration for %s.', 'houzez' ), $plugin_slug );
				continue;
			}

			switch ( $bulk_action ) {
				case 'install-required':
					$plugin_path_full = WP_PLUGIN_DIR . '/' . $plugin_data['path'];
					$file_exists = file_exists( $plugin_path_full );
					
					// More reliable active check: if file doesn't exist, it can't be active
					$is_active = $file_exists && is_plugin_active( $plugin_data['path'] );
					
					if ( $plugin_data['required'] ) {
						if ( $is_active ) {
							// Plugin is already active - skip it
							$results[] = sprintf( __( '%s is already active - skipped.', 'houzez' ), $plugin_data['name'] );
						} elseif ( ! $file_exists ) {
							// Plugin not installed - install it
							$plugin_source = isset( $plugin_data['source'] ) ? $plugin_data['source'] : '';
							
							$result = self::install_single_plugin( $plugin_data['slug'], $plugin_source, $plugin_data['name'] );
							if ( ! $result['success'] ) {
								$errors[] = $result['message'];
							} else {
								$results[] = $result['message'];
								
								// Auto-activate after successful installation
								if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_data['path'] ) ) {
									$activate_result = activate_plugin( $plugin_data['path'] );
									if ( is_wp_error( $activate_result ) ) {
										$errors[] = sprintf( __( 'Installed %s but failed to activate: %s', 'houzez' ), $plugin_data['name'], $activate_result->get_error_message() );
									} else {
										$results[] = sprintf( __( '%s activated successfully.', 'houzez' ), $plugin_data['name'] );
									}
								} else {
									$errors[] = sprintf( __( 'Plugin %s was installed but file still not found at: %s', 'houzez' ), $plugin_data['name'], WP_PLUGIN_DIR . '/' . $plugin_data['path'] );
								}
							}
						} else {
							// Plugin is installed but not active - activate it
							$activate_result = activate_plugin( $plugin_data['path'] );
							if ( is_wp_error( $activate_result ) ) {
								$errors[] = sprintf( __( 'Failed to activate %s: %s', 'houzez' ), $plugin_data['name'], $activate_result->get_error_message() );
							} else {
								$results[] = sprintf( __( '%s activated successfully.', 'houzez' ), $plugin_data['name'] );
							}
						}
					}
					break;
				case 'activate-required':
					if ( $plugin_data['required'] && file_exists( WP_PLUGIN_DIR . '/' . $plugin_data['path'] ) ) {
						if ( is_plugin_active( $plugin_data['path'] ) ) {
							// Plugin is already active - skip it
							$results[] = sprintf( __( '%s is already active - skipped.', 'houzez' ), $plugin_data['name'] );
						} else {
							// Plugin is installed but not active - activate it
							$activate_result = activate_plugin( $plugin_data['path'] );
							if ( is_wp_error( $activate_result ) ) {
								$errors[] = sprintf( __( 'Failed to activate %s: %s', 'houzez' ), $plugin_data['name'], $activate_result->get_error_message() );
							} else {
								$results[] = sprintf( __( '%s activated successfully.', 'houzez' ), $plugin_data['name'] );
							}
						}
					}
					break;
				case 'update-all':
					if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_data['path'] ) ) {
						$needs_update = false;
						
						// Check if it's a custom plugin with source URL
						if ( isset( $plugin_data['source'] ) && ! empty( $plugin_data['source'] ) ) {
							// For custom plugins, check version comparison
							$plugin_file_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_data['path'] );
							$installed_version = $plugin_file_data['Version'];
							
							if ( ! empty( $plugin_data['version'] ) && ! empty( $installed_version ) && 
								 version_compare( $installed_version, $plugin_data['version'], '<' ) ) {
								$needs_update = true;
								$result = self::update_custom_plugin( $plugin_data['path'], $plugin_data['source'], $plugin_data['name'] );
								if ( ! $result['success'] ) {
									$errors[] = $result['message'];
								} else {
									$results[] = $result['message'];
								}
							}
						} else {
							// For WordPress.org plugins, use built-in update mechanism
							$plugin_updates = get_plugin_updates();
							if ( isset( $plugin_updates[ $plugin_data['path'] ] ) ) {
								$needs_update = true;
								$result = self::update_single_plugin( $plugin_data['path'], $plugin_data['name'] );
								if ( ! $result['success'] ) {
									$errors[] = $result['message'];
								} else {
									$results[] = $result['message'];
								}
							}
						}
						
						// If no update was needed, log it
						if ( ! $needs_update ) {
							$results[] = sprintf( __( '%s is already up to date - skipped.', 'houzez' ), $plugin_data['name'] );
						}
					}
					break;
			}
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( implode( '<br>', $errors ) );
		} elseif ( ! empty( $results ) ) {
			wp_send_json_success( implode( '<br>', $results ) );
		} else {
			// No actions were performed - this might indicate an issue
			wp_send_json_error( __( 'No actions were performed. Please check if the plugins are already active or if there are permission issues.', 'houzez' ) );
		}
	}

	private static function install_single_plugin( $plugin_slug, $plugin_source = '', $plugin_name = '' ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		// Determine download URL
		if ( ! empty( $plugin_source ) ) {
			$download_url = $plugin_source;
		} else {
			// Get from WordPress.org
			$api = plugins_api( 'plugin_information', array(
				'slug' => $plugin_slug,
				'fields' => array( 'download_link' => true )
			) );

			if ( is_wp_error( $api ) ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Failed to get plugin information for %s: %s', 'houzez' ), $plugin_name, $api->get_error_message() )
				);
			}

			$download_url = $api->download_link;
		}

		// Install the plugin
		$result = $upgrader->install( $download_url );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to install %s: %s', 'houzez' ), $plugin_name, $result->get_error_message() )
			);
		}

		if ( $result === false ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to install %s. Please try again.', 'houzez' ), $plugin_name )
			);
		}

		return array(
			'success' => true,
			'message' => sprintf( __( '%s installed successfully.', 'houzez' ), $plugin_name )
		);
	}

	private static function update_single_plugin( $plugin_file, $plugin_name = '' ) {
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$result = $upgrader->upgrade( $plugin_file );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to update %s: %s', 'houzez' ), $plugin_name, $result->get_error_message() )
			);
		}

		if ( $result === false ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to update %s. Please try again.', 'houzez' ), $plugin_name )
			);
		}

		return array(
			'success' => true,
			'message' => sprintf( __( '%s updated successfully.', 'houzez' ), $plugin_name )
		);
	}

	private static function update_custom_plugin( $plugin_file, $plugin_source, $plugin_name = '' ) {
		// Check if plugin is currently active
		$was_active = is_plugin_active( $plugin_file );

		// Include necessary files
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Create upgrader instance
		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		// Download the plugin zip file
		$download_result = download_url( $plugin_source );
		
		if ( is_wp_error( $download_result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to download %s: %s', 'houzez' ), $plugin_name, $download_result->get_error_message() )
			);
		}

		// Get plugin directory name from plugin file path
		$plugin_dir = dirname( $plugin_file );
		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_dir;

		// Deactivate plugin before updating
		if ( $was_active ) {
			deactivate_plugins( $plugin_file );
		}

		// Remove old plugin directory
		if ( file_exists( $plugin_path ) ) {
			global $wp_filesystem;
			
			// Initialize WP_Filesystem
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			
			$filesystem_initialized = WP_Filesystem();
			
			if ( $filesystem_initialized && $wp_filesystem ) {
				$wp_filesystem->delete( $plugin_path, true );
			} else {
				// Fallback to PHP functions if WP_Filesystem fails
				self::recursive_delete( $plugin_path );
			}
		}

		// Extract the new plugin
		$unzip_result = unzip_file( $download_result, WP_PLUGIN_DIR );
		
		// Clean up downloaded file
		unlink( $download_result );

		if ( is_wp_error( $unzip_result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to extract %s: %s', 'houzez' ), $plugin_name, $unzip_result->get_error_message() )
			);
		}

		// Reactivate plugin if it was active before
		if ( $was_active && file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
			$activate_result = activate_plugin( $plugin_file );
			if ( is_wp_error( $activate_result ) ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Updated %s but failed to reactivate: %s', 'houzez' ), $plugin_name, $activate_result->get_error_message() )
				);
			}
		}

		return array(
			'success' => true,
			'message' => sprintf( __( '%s updated successfully.', 'houzez' ), $plugin_name )
		);
	}

	private static function recursive_delete( $dir ) {
		if ( ! is_dir( $dir ) ) {
			return;
		}

		$files = array_diff( scandir( $dir ), array( '.', '..' ) );
		foreach ( $files as $file ) {
			$path = $dir . '/' . $file;
			if ( is_dir( $path ) ) {
				self::recursive_delete( $path );
			} else {
				unlink( $path );
			}
		}
		rmdir( $dir );
	}

	private static function get_plugins_array() {
		return array(
			array(
				'name'     		=> 'Houzez Theme Functionality',
				'slug'     		=> 'houzez-theme-functionality',
				'source'   		=> 'https://default.houzez.co/plugins/houzez-theme-functionality.zip',
				'path'   		=> 'houzez-theme-functionality/houzez-theme-functionality.php',
				'required' 		=> true,
				'version' 		=> '4.0.0', 
				'author' 		=> 'FaveThemes',
				'author_url' 	=> 'https://themeforest.net/user/favethemes/portfolio',
				'description' 	=> 'Theme core plugin to add all the functionality for Houzez theme', 
				'thumbnail' 	=> HOUZEZ_IMAGE . 'houzez-icon.jpg',
				'wp_org'		=> false,
			),
			array(
				'name'     		=> 'Houzez Login Register',
				'slug'     		=> 'houzez-login-register',
				'source'   		=> 'https://default.houzez.co/plugins/houzez-login-register.zip',
				'path'   		=> 'houzez-login-register/houzez-login-register.php',
				'required' 		=> true,
				'version' 		=> '4.0.0', 
				'author' 		=> 'FaveThemes',
				'author_url' 	=> 'https://themeforest.net/user/favethemes/portfolio',
				'description' 	=> 'Theme core plugin to login & register functionality', 
				'thumbnail' 	=> HOUZEZ_IMAGE . 'houzez-icon.jpg',
				'wp_org'		=> false,
			),
			array(
				'name'     		=> 'Houzez Studio',
				'slug'     		=> 'houzez-studio',
				'source'   		=> 'https://default.houzez.co/plugins/houzez-studio.zip',
				'path'   		=> 'houzez-studio/houzez-studio.php',
				'required' 		=> true,
				'version' 		=> '1.3.0', 
				'author' 		=> 'FaveThemes',
				'author_url' 	=> 'https://themeforest.net/user/favethemes/portfolio',
				'description' 	=> 'Theme core plugin for advanced functionality', 
				'thumbnail' 	=> HOUZEZ_IMAGE . 'houzez-icon.jpg',
				'wp_org'		=> false,
			),
			array(
				'name'     		=> 'Houzez CRM',
				'slug'     		=> 'houzez-crm',
				'source'   		=> 'https://default.houzez.co/plugins/houzez-crm.zip',
				'path'   		=> 'houzez-crm/houzez-crm.php',
				'required' 		=> false,
				'version' 		=> '1.4.5', 
				'author' 		=> 'FaveThemes',
				'author_url' 	=> 'https://themeforest.net/user/favethemes/portfolio',
				'description' 	=> 'Theme core plugin to add the CRM functionality', 
				'thumbnail' 	=> HOUZEZ_IMAGE . 'houzez-icon.jpg',
				'wp_org'		=> false,
			),
			array(
				'name'     		=> 'Favethemes Insights',
				'slug'     		=> 'favethemes-insights',
				'source'   		=> 'https://default.houzez.co/plugins/favethemes-insights.zip',
				'path'   		=> 'favethemes-insights/favethemes-insights.php',
				'required' 		=> false,
				'version' 		=> '1.3.0', 
				'author' 		=> 'FaveThemes',
				'author_url' 	=> 'https://themeforest.net/user/favethemes/portfolio',
				'description' 	=> 'Theme core plugin to add the insight data chart', 
				'thumbnail' 	=> HOUZEZ_IMAGE . 'houzez-icon.jpg',
				'wp_org'		=> false,
			),
			array(
				'name'     		=> 'Redux Framework',
				'slug'     		=> 'redux-framework',
				'path'   		=> 'redux-framework/redux-framework.php',
				'required' 		=> true,
				'version' 		=> '', 
				'author' 		=> 'Team Redux',
				'author_url' 	=> 'https://wordpress.org/plugins/redux-framework/',
				'description' 	=> 'Theme Options Framework', 
				'thumbnail' 	=> HOUZEZ_IMAGE . 'redux-icon.jpg',
				'wp_org'		=> true,
			),
			array(
				'name'     		=> 'One Click Demo Import',
				'slug'     		=> 'one-click-demo-import',
				'path'   		=> 'one-click-demo-import/one-click-demo-import.php',
				'required' 		=> false,
				'version' 		=> '', 
				'author' 		=> 'ProteusThemes',
				'author_url' 	=> 'https://wordpress.org/plugins/one-click-demo-import/',
				'description' 	=> 'Import demo content with one click', 
				'thumbnail'    => HOUZEZ_IMAGE . 'demo-import-icon.jpg',
				'wp_org'		=> true,
			),
			array(
				'name'         => 'Elementor Page Builder',
				'slug'         => 'elementor',
				'path'         => 'elementor/elementor.php',
				'required'     => true,
				'version'      => '',
				'author'       => 'Elementor.com',
				'author_url'   => 'https://elementor.com/',
				'description'  => "The World's Leading WordPress Drag & Drop Page Builder",
				'thumbnail'    => HOUZEZ_IMAGE . 'elementor-icon.jpg',
				'wp_org'		=> true,
			),
			array(
				'name'     		=> 'MLS On The Fly ®',
				'slug'     		=> 'mls-on-the-fly',
				'source'   		=> 'https://default.houzez.co/plugins/mls-on-the-fly.zip',
				'path'   		=> 'mls-on-the-fly/mls-on-the-fly.php',
				'required' 		=> false,
				'version' 		=> '1.6.0.5', 
				'author' 		=> 'Realtyna',
				'author_url' 	=> 'https://realtyna.com/mls-on-the-fly/',
				'description' 	=> 'MLS/IDX data feed integration', 
				'thumbnail'    => HOUZEZ_IMAGE . 'houzez-icon.jpg',
				'wp_org'		=> false,
			),
			array(
				'name'     		=> 'Slider Revolution',
				'slug'     		=> 'revslider',
				'source'   		=> 'https://default.houzez.co/plugins/revslider.zip',
				'path'   		=> 'revslider/revslider.php',
				'required' 		=> false,
				'version' 		=> '', 
				'author' 		=> 'themepunch',
				'author_url' 	=> 'https://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380',
				'description' 	=> 'Create stunning sliders, carousels, and hero headers', 
				'thumbnail' 	=> HOUZEZ_IMAGE . 'slider-revolution-icon.jpg',
				'wp_org'		=> false,
			),
			array(
				'name'     		=> 'MailChimp For WP',
				'slug'     		=> 'mailchimp-for-wp',
				'path'   		=> 'mailchimp-for-wp/mailchimp-for-wp.php',
				'required' 		=> false,
				'version' 		=> '', 
				'author' 		=> 'ibericode',
				'author_url' 	=> 'https://wordpress.org/plugins/mailchimp-for-wp/',
				'description' 	=> 'Grow your Mailchimp lists with beautiful forms', 
				'thumbnail'    => HOUZEZ_IMAGE . 'mailchimp-icon.jpg',
				'wp_org'		=> true,
			),
			array(
				'name'     		=> 'HubSpot',
				'slug'     		=> 'leadin',
				'path'   		=> 'leadin/leadin.php',
				'required' 		=> false,
				'version' 		=> '', 
				'author' 		=> 'HubSpot',
				'author_url' 	=> 'https://wordpress.org/plugins/leadin/',
				'description' 	=> 'HubSpot – CRM, Email Marketing, Live Chat, Forms & Analytics', 
				'thumbnail'    => HOUZEZ_IMAGE . 'icon-256x256.png',
				'wp_org'		=> true,
			),
		);
	}

	public function houzez_feedback() {

		$headers   = array();
		$current_user = wp_get_current_user();

		$target_email   = is_email("houzez@favethemes.com");
		$website        = get_bloginfo( 'name' );
		$site_url       = network_site_url( '/' );
		$sender_name    = $current_user->display_name;
		$sender_email   = sanitize_email( $_POST['email'] );
		$sender_email   = is_email( $sender_email ); 
		$sender_subject = sanitize_text_field( $_POST['subject'] );
		$message        = stripslashes( $_POST['message'] );

		$nonce = $_POST['feedback_nonce'];
        if (!wp_verify_nonce( $nonce, 'houzez_feedback_security') ) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Invalid Nonce!', 'houzez')
            ));
            wp_die();
        }

		if (!$sender_email) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Email address is Invalid!', 'houzez')
            ));
            wp_die();
        }

        if ( empty($message) ) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Your message is empty!', 'houzez')
            ));
            wp_die();
        }

        $subject = sprintf( esc_html__('New Feedback by %s from %s', 'houzez'), $sender_name, $website );

        $body = esc_html__("You have received new message from: ", 'houzez') . $sender_name . " <br/>";

        if ( ! empty( $website ) ) {
            $body .= esc_html__( "Website : ", 'houzez' ) . '<a href="' . esc_url( $site_url ) . '" target="_blank">' . $website . "</a><br/><br/>";
        }

        if ( ! empty( $sender_subject ) ) {
            $body .= esc_html__( "Subject : ", 'houzez' ) .$sender_subject. "<br/>";
        }

        $body .= "<br/>" . esc_html__("Message:", 'houzez') . " <br/>";
        $body .= wpautop( $message ) . " <br/>";
        $body .= sprintf( esc_html__( 'You can contact %s via email %s', 'houzez'), $sender_name, $sender_email );

		$headers[] = "Reply-To: $sender_name <$sender_email>";
		$headers[] = "Content-Type: text/html; charset=UTF-8";
		$headers   = apply_filters( "houzez_feedback_mail_header", $headers ); 

		if ( wp_mail( $target_email, $subject, $body, $headers ) ) {
            echo json_encode( array(
                'success' => true,
                'msg' => esc_html__("Thank you for your feedback!", 'houzez')
            ));
        } else {
            echo json_encode(array(
                    'success' => false,
                    'msg' => esc_html__("Server Error: Make sure Email function working on your server!", 'houzez')
                )
            );
        }
        wp_die();
	}

	public function verify_purchase() {

		$item_purchase_code = sanitize_text_field( $_POST['item_purchase_code'] );

		$nonce = $_POST['nonce'];
        if (!wp_verify_nonce( $nonce, 'envato_api_nonce') ) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Invalid Nonce!', 'houzez')
            ));
            wp_die();
        }

		if ( ! $item_purchase_code ) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Please enter an item purchase code.', 'houzez')
            ));
            wp_die();
        }

        $houzez_item_id = 15752549;
        $error = new WP_Error();

        $envato_token = 'Jr6vzZumpYac1wfox71aovkT0aNyDXMD';

        // Use the v3 API endpoint for better reliability
        $apiurl = "https://api.envato.com/v3/market/author/sale?code=" . urlencode( $item_purchase_code );
        
        $header = array(
            'headers' => array( 
                "Authorization" => "Bearer " . $envato_token,
                "User-Agent" => "Houzez Theme Verification System/4.1.0"
            ),
            'timeout' => 30
        );
        
        $request = wp_safe_remote_request( $apiurl, $header );

        // Log API request details for debugging
        //error_log( "Houzez Verification: API URL - " . $apiurl );
        //error_log( "Houzez Verification: Purchase Code - " . $item_purchase_code );

        if ( is_wp_error( $request ) ) {
            // API connection error
            error_log( "Houzez Verification: WP Error - " . $request->get_error_message() );
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('API connection error: ', 'houzez') . $request->get_error_message()
            ));
            wp_die();
        }

        if ( ! isset( $request['body'] ) || empty( $request['body'] ) ) {
            error_log( "Houzez Verification: Empty response body" );
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Empty response from API server', 'houzez')
            ));
            wp_die();
        }

        $response_body = json_decode( $request['body'], true );
        
        // Log the full API response for debugging
        //error_log( "Houzez Verification: API Response - " . print_r( $response_body, true ) );
        //error_log( "Houzez Verification: HTTP Status - " . wp_remote_retrieve_response_code( $request ) );

        $http_status = wp_remote_retrieve_response_code( $request );
        
        if ( $http_status === 200 && isset( $response_body['item'] ) ) {
            // v3 API returns data in 'item' key
            $purchase_data = $response_body['item'];
            
            if ( isset( $purchase_data['id'] ) && $houzez_item_id == $purchase_data['id'] ) {
                update_option( 'houzez_activation', 'activated' );
                update_option( 'houzez_purchase_code', sanitize_text_field( $item_purchase_code ) );
                
                error_log( "Houzez Verification: Purchase verified successfully" );
                
                echo json_encode(array(
                    'success' => true,
                    'msg' => esc_html__('Thanks for verifying houzez purchase!', 'houzez')
                ));
                wp_die();
                
            } else {
                $expected_id = $houzez_item_id;
                $actual_id = isset( $purchase_data['id'] ) ? $purchase_data['id'] : 'not found';
                
                error_log( "Houzez Verification: Item ID mismatch - Expected: {$expected_id}, Got: {$actual_id}" );
                
                echo json_encode(array(
                    'success' => false,
                    'msg' => esc_html__('Purchase code is valid but not for this theme. Expected item ID: ', 'houzez') . $houzez_item_id
                ));
                wp_die();
            }
            
        } else {
            // Handle various error responses
            $error_message = esc_html__('Invalid purchase code or API error', 'houzez');
            
            if ( $http_status === 404 ) {
                $error_message = esc_html__('Purchase code not found', 'houzez');
            } elseif ( $http_status === 403 ) {
                $error_message = esc_html__('API access forbidden - token may be invalid', 'houzez');
            } elseif ( $http_status === 401 ) {
                $error_message = esc_html__('API authentication failed - invalid token', 'houzez');
            } elseif ( isset( $response_body['error'] ) ) {
                $error_message = esc_html__('API Error: ', 'houzez') . $response_body['error'];
            }
            
            error_log( "Houzez Verification: Failed - HTTP {$http_status}, Error: {$error_message}" );
            
            echo json_encode(array(
                'success' => false,
                'msg' => $error_message
            ));
            wp_die();
        }

	}

	public function deactivate_purchase() {
		$nonce = $_POST['nonce'];
        if (!wp_verify_nonce( $nonce, 'envato_api_nonce') ) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Invalid Nonce!', 'houzez')
            ));
            wp_die();
        }

        update_option( 'houzez_activation', 'none' );
        update_option( 'houzez_purchase_code', '' );

        echo json_encode(array(
            'success' => true,
            'msg' => esc_html__('Deactivated', 'houzez')
        ));
        wp_die();
	}


	public function documentation() {
		require_once $this->template_path . 'documentation.php';
	}

	public function plugins() {
		require_once $this->template_path . 'plugins.php';
	}

	public function feedback() {
		require_once $this->template_path . 'feedback.php';
	}

	public function purchase_code() {
		require_once $this->template_path . 'purchase.php';
	}

	public function mobile_app() {
		require_once $this->template_path . 'mobile-app.php';
	}

	public function remove_parent_menu() {
		global $submenu;
		unset( $submenu['houzez_dashboard'][0] );
	}

    public function add_theme_builder_header() {
        // Only add header on the fts_builder post type edit page
        $screen = get_current_screen();
        if ( ! $screen || $screen->post_type !== 'fts_builder' || $screen->base !== 'edit' ) {
            return;
        }
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Find the wrap div and add our header
			var $wrap = $('.wrap');
			if ($wrap.length) {
				// Remove the default h1 title
				$wrap.find('h1.wp-heading-inline').hide();
				
				// Add our modern header
				var headerHtml = `
					<div class="houzez-header" style="margin: -10px -20px 15px -22px;">
						<div class="houzez-header-content">
							<div class="houzez-logo">
								<h1><?php esc_html_e('Theme Builder', 'houzez'); ?></h1>
							</div>
							<div class="houzez-header-actions">
								<a href="<?php echo esc_url(admin_url('post-new.php?post_type=fts_builder')); ?>" class="houzez-btn houzez-btn-primary">
									<i class="dashicons dashicons-plus"></i>
									<?php esc_html_e('Add New Layout', 'houzez'); ?>
								</a>
							</div>
						</div>
					</div>
				`;
				
				// Insert the header at the beginning of the wrap
				$wrap.prepend(headerHtml);
				
				
			}
        });
        </script>
        
        <style>
        .post-type-fts_builder .page-title-action, #screen-options-link-wrap {
            display: none !important;
        }
        </style>
        <?php
	}

}

return Houzez_Admin::instance();