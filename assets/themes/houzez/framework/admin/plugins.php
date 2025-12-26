<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Security: Verify nonce for plugin actions
if ( isset( $_POST['houzez_plugin_action'] ) && ! wp_verify_nonce( $_POST['houzez_plugin_nonce'], 'houzez_plugin_action' ) ) {
	wp_die( esc_html__( 'Security check failed. Please try again.', 'houzez' ) );
}

$current_user = wp_get_current_user();

include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( ABSPATH . 'wp-admin/includes/file.php' );

// Get plugin updates
if ( ! function_exists( 'get_plugin_updates' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/update.php' );
}

// Only perform expensive operations when on the plugins page
if ( isset( $_GET['page'] ) && $_GET['page'] === 'houzez_plugins' ) {
	// Enqueue admin styles for the header
	wp_enqueue_style('houzez-admin-styles', get_template_directory_uri() . '/css/admin/admin.min.css', array(), '1.0.0');
	
	// If refresh parameter is set, clear all caches and force update check
	if ( isset( $_GET['refresh'] ) && $_GET['refresh'] === '1' ) {
		// Clear WordPress.org version cache
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_houzez_wporg_version_%'" );
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_houzez_wporg_version_%'" );
		
		// Clear the plugin updates cache
		delete_transient('houzez_plugin_updates_cache');
		
		// Force a fresh check for plugin updates by deleting the transient
		delete_site_transient( 'update_plugins' );
		
		// Clear plugin cache to get fresh data
		if ( ! function_exists( 'wp_clean_plugins_cache' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		wp_clean_plugins_cache();
		
		// Trigger a new update check
		wp_update_plugins();
	} else {
		// Only trigger update check if the update transient is old (older than 12 hours)
		$update_plugins = get_site_transient( 'update_plugins' );
		if ( ! $update_plugins || ( isset( $update_plugins->last_checked ) && ( time() - $update_plugins->last_checked ) > 12 * HOUR_IN_SECONDS ) ) {
			wp_update_plugins();
		}
	}
}

// Helper function to get cached plugin data to avoid multiple get_plugin_data() calls
function houzez_get_cached_plugin_data($plugin_path) {
	static $plugin_data_cache = array();
	
	if (!isset($plugin_data_cache[$plugin_path])) {
		if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_path)) {
			$plugin_data_cache[$plugin_path] = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
		} else {
			$plugin_data_cache[$plugin_path] = false;
		}
	}
	
	return $plugin_data_cache[$plugin_path];
}

// Helper function to manually check WordPress.org plugin version
function houzez_check_wporg_plugin_version($plugin_slug, $installed_version) {
	if (empty($plugin_slug) || empty($installed_version)) {
		return false;
	}
	
	// Check cache first (cache for 6 hours for better performance)
	$cache_key = 'houzez_wporg_version_' . $plugin_slug;
	$cached_data = get_transient($cache_key);
	
	if ($cached_data !== false) {
		// Return cached comparison result
		if (isset($cached_data['version'])) {
			return version_compare($installed_version, $cached_data['version'], '<');
		}
		// If cached as 'no_update', return false
		if ($cached_data === 'no_update') {
			return false;
		}
	}
	
	// Only make API call if we're on the plugins admin page to avoid slowing down other pages
	if (!isset($_GET['page']) || $_GET['page'] !== 'houzez_plugins') {
		return false;
	}
	
	// Use WordPress.org API to get latest version with shorter timeout
	$api_url = "https://api.wordpress.org/plugins/info/1.0/{$plugin_slug}.json";
	$response = wp_remote_get($api_url, array(
		'timeout' => 3, // Reduced from 10 to 3 seconds
		'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
	));


	if (is_wp_error($response)) {
		// Cache the failure for 30 minutes to avoid repeated failed requests
		set_transient($cache_key, 'no_update', 30 * MINUTE_IN_SECONDS);
		return false;
	}
	
	$response_code = wp_remote_retrieve_response_code($response);
	if ($response_code !== 200) {
		// Cache the failure for 30 minutes
		set_transient($cache_key, 'no_update', 30 * MINUTE_IN_SECONDS);
		return false;
	}
	
	$body = wp_remote_retrieve_body($response);
	$plugin_info = json_decode($body, true);
	
	if (isset($plugin_info['version'])) {
		// Cache the version for 6 hours
		$cache_data = array('version' => $plugin_info['version']);
		set_transient($cache_key, $cache_data, 6 * HOUR_IN_SECONDS);
		return version_compare($installed_version, $plugin_info['version'], '<');
	}
	
	// Cache as no update available for 6 hours
	set_transient($cache_key, 'no_update', 6 * HOUR_IN_SECONDS);
	return false;
}

$plugins_array = array(
	array(
		'name'     		=> 'Houzez Theme Functionality',
		'slug'     		=> 'houzez-theme-functionality',
		'source'   		=> 'https://default.houzez.co/plugins/houzez-theme-functionality.zip',
		'path'   		=> 'houzez-theme-functionality/houzez-theme-functionality.php',
		'required' 		=> true,
		'version' 		=> '4.2.5', 
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
		'version' 		=> '4.0.4', 
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
		'version' 		=> '1.3.2', 
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
		'version' 		=> '1.5.0', 
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
		'version' 		=> '1.3.1', 
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
		'version' 		=> '6.7.38', 
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

// Get plugin update information
$plugin_updates = get_plugin_updates();

// Also get updates directly from WordPress transients for more reliable detection
$update_plugins = get_site_transient( 'update_plugins' );
if ( ! $update_plugins ) {
	$update_plugins = new stdClass();
	$update_plugins->response = array();
}
?>

<div class="wrap houzez-template-library">
	<div class="houzez-header">
		<div class="houzez-header-content">
			<div class="houzez-logo">
				<h1><?php esc_html_e('Plugin Management', 'houzez'); ?></h1>
			</div>
			<div class="houzez-header-actions">
				<?php if( houzez_theme_verified() ) { ?>
					<button type="button" class="houzez-btn houzez-btn-primary houzez-bulk-install" data-action="install-required">
						<i class="dashicons dashicons-download"></i>
						<?php esc_html_e('Install Required Plugins', 'houzez'); ?>
					</button>
					<button type="button" class="houzez-btn houzez-btn-secondary houzez-bulk-update" data-action="update-all">
						<i class="dashicons dashicons-update"></i>
						<?php esc_html_e('Update All', 'houzez'); ?>
					</button>
					<button type="button" class="houzez-btn houzez-btn-secondary houzez-refresh-status" data-action="refresh">
						<i class="dashicons dashicons-update-alt"></i>
						<?php esc_html_e('Refresh Status', 'houzez'); ?>
					</button>
				<?php } ?>
			</div>
			</div>
		</div>

	<div class="houzez-dashboard">
		<!-- Plugin Statistics -->
		<div class="houzez-stats-grid">
			<?php
			$stats = houzez_get_plugin_stats($plugins_array);
			?>
			<div class="houzez-stat-card">
				<div class="houzez-stat-icon">
					<i class="dashicons dashicons-admin-plugins"></i>
			</div>
				<div class="houzez-stat-content">
					<h3><?php echo esc_html($stats['total']); ?></h3>
					<p><?php esc_html_e('Total Plugins', 'houzez'); ?></p>
			</div>
			</div>
			<div class="houzez-stat-card">
				<div class="houzez-stat-icon">
					<i class="dashicons dashicons-yes-alt"></i>
				</div>
				<div class="houzez-stat-content">
					<h3 id="active-plugins-count"><?php echo esc_html($stats['active']); ?></h3>
					<p><?php esc_html_e('Active Plugins', 'houzez'); ?></p>
				</div>
			</div>
			<div class="houzez-stat-card">
				<div class="houzez-stat-icon">
					<i class="dashicons dashicons-update"></i>
				</div>
				<div class="houzez-stat-content">
					<h3><?php echo esc_html($stats['updates']); ?></h3>
					<p><?php esc_html_e('Updates Available', 'houzez'); ?></p>
				</div>
			</div>
			<div class="houzez-stat-card">
				<div class="houzez-stat-icon">
					<i class="dashicons dashicons-star-filled"></i>
				</div>
				<div class="houzez-stat-content">
					<h3><?php echo esc_html($stats['required']); ?></h3>
					<p><?php esc_html_e('Required Plugins', 'houzez'); ?></p>
				</div>
			</div>
		</div>

		<!-- Required Plugins Status -->
		<div class="houzez-required-status">
			<?php
			$required_status = houzez_get_required_plugins_status($plugins_array);
			$status_class = '';
			$status_icon = '';
			$status_text = '';
			
			if ($required_status['active'] === $required_status['total']) {
				$status_class = 'status-complete';
				$status_icon = 'dashicons-yes-alt';
				$status_text = __('All required plugins are active', 'houzez');
			} elseif ($required_status['installed'] === $required_status['total']) {
				$status_class = 'status-partial';
				$status_icon = 'dashicons-warning';
				$status_text = __('All required plugins are installed but some are inactive', 'houzez');
			} else {
				$status_class = 'status-incomplete';
				$status_icon = 'dashicons-dismiss';
				$status_text = __('Some required plugins are missing', 'houzez');
			}
			?>
			<div class="required-status-container <?php echo esc_attr($status_class); ?>">
				<div class="status-icon">
					<span class="dashicons <?php echo esc_attr($status_icon); ?>"></span>
				</div>
				<div class="status-content">
					<div class="status-title">
						<?php esc_html_e('Required Plugins Status', 'houzez'); ?>
					</div>
					<div class="status-description">
						<?php echo esc_html($status_text); ?>
					</div>
					<div class="status-progress">
						<div class="progress-bar">
							<div class="progress-fill" style="width: <?php echo esc_attr(($required_status['active'] / $required_status['total']) * 100); ?>%"></div>
						</div>
						<div class="progress-text">
							<span class="progress-active"><?php echo esc_html($required_status['active']); ?></span>
							<span class="progress-separator">/</span>
							<span class="progress-total"><?php echo esc_html($required_status['total']); ?></span>
							<span class="progress-label"><?php esc_html_e('active', 'houzez'); ?></span>
						</div>
					</div>
				</div>
				<?php if ($required_status['active'] < $required_status['total'] && houzez_theme_verified()) { ?>
				<div class="status-action">
					<button type="button" class="button button-primary houzez-fix-required" data-action="fix-required">
						<span class="dashicons dashicons-admin-tools"></span>
						<?php esc_html_e('Fix Now', 'houzez'); ?>
					</button>
				</div>
				<?php } ?>
			</div>
		</div>

		<!-- Plugin Filter Section -->
		<div class="houzez-plugins-filter-section">
			<div class="filter-container">
				<div class="filter-label">
					<span class="dashicons dashicons-filter"></span>
					<span><?php esc_html_e('Filter Plugins:', 'houzez'); ?></span>
				</div>
				<div class="filter-buttons">
					<button type="button" class="filter-btn active" data-filter="all">
						<span class="dashicons dashicons-admin-plugins"></span>
						<?php esc_html_e('All', 'houzez'); ?>
						<span class="filter-count" id="count-all">0</span>
					</button>
					<button type="button" class="filter-btn" data-filter="required">
						<span class="dashicons dashicons-star-filled"></span>
						<?php esc_html_e('Required', 'houzez'); ?>
						<span class="filter-count" id="count-required">0</span>
					</button>
					<button type="button" class="filter-btn" data-filter="recommended">
						<span class="dashicons dashicons-star-empty"></span>
						<?php esc_html_e('Recommended', 'houzez'); ?>
						<span class="filter-count" id="count-recommended">0</span>
					</button>
					<button type="button" class="filter-btn" data-filter="active">
						<span class="dashicons dashicons-yes-alt"></span>
						<?php esc_html_e('Active', 'houzez'); ?>
						<span class="filter-count" id="count-active">0</span>
					</button>
					<button type="button" class="filter-btn" data-filter="inactive">
						<span class="dashicons dashicons-minus"></span>
						<?php esc_html_e('Inactive', 'houzez'); ?>
						<span class="filter-count" id="count-inactive">0</span>
					</button>
					<button type="button" class="filter-btn" data-filter="updates">
						<span class="dashicons dashicons-update"></span>
						<?php esc_html_e('Updates', 'houzez'); ?>
						<span class="filter-count" id="count-updates">0</span>
					</button>
				</div>
			</div>
		</div>

		<div class="admin-houzez-row">
			
			<div class="admin-houzez-box-wrap admin-houzez-box-wrap-plugins">
				
				<!-- No plugins found message (hidden by default) -->
				<div id="houzez-no-plugins-found" class="houzez-no-plugins-message" style="display: none;">
					<div class="no-plugins-content">
						<span class="dashicons dashicons-search"></span>
						<h3><?php esc_html_e('No plugins found', 'houzez'); ?></h3>
						<p><?php esc_html_e('No plugins match the selected filter criteria.', 'houzez'); ?></p>
					</div>
				</div>
				
				<?php
				foreach ( $plugins_array as $plugin ) { 
					$plugin_info = houzez_get_plugin_info($plugin, $plugin_updates);
					?>

					<div class="admin-houzez-box admin-houzez-box-plugins <?php echo esc_attr($plugin_info['status_class']); ?>" 
						 data-plugin-slug="<?php echo esc_attr($plugin['slug']); ?>"
						 data-plugin-required="<?php echo $plugin['required'] ? 'true' : 'false'; ?>"
						 data-plugin-status="<?php echo esc_attr($plugin_info['status']); ?>"
						 data-plugin-has-update="<?php echo $plugin_info['has_update'] ? 'true' : 'false'; ?>"
						 data-plugin-source="<?php echo isset($plugin['source']) ? esc_attr($plugin['source']) : ''; ?>">
						<!-- Plugin Icon -->
						<div class="admin-houzez-box-image">
							<img src="<?php echo esc_url( $plugin['thumbnail'] ); ?>" alt="<?php echo esc_attr( $plugin['name'] ); ?>">
							<div class="plugin-status-badge <?php echo esc_attr($plugin_info['status']); ?>">
								<?php echo esc_html($plugin_info['status_text']); ?>
							</div>
						</div>

						<!-- Main Content Area -->
						<div class="admin-houzez-box-content">
							<div class="plugin-main-content">
								<!-- Plugin Header -->
								<div class="plugin-header-section">
									<h3>
										<?php echo esc_html( $plugin['name'] ); ?>
										<?php if ($plugin_info['has_update']) { ?>
											<span class="update-badge" title="<?php esc_attr_e('Update Available', 'houzez'); ?>">
												<span class="dashicons dashicons-update"></span>
											</span>
										<?php } ?>
									</h3>
									
									<!-- Author and Version Info -->
									<div class="plugin-meta-info">
										<div class="author-info">
											<strong><?php esc_html_e('Author:', 'houzez'); ?></strong>
											<a target="_blank" href="<?php echo esc_url($plugin['author_url']); ?>" rel="noopener">
												<?php echo esc_html($plugin['author']); ?>
											</a>
										</div>
										
										<?php if( !empty($plugin['version']) || !empty($plugin_info['installed_version']) ) { ?>
											<div class="version-info">
												<?php if (!empty($plugin_info['installed_version'])) { ?>
													<strong><?php esc_html_e('Installed:', 'houzez'); ?></strong>
													<?php echo esc_html($plugin_info['installed_version']); ?>
													<?php if (!empty($plugin['version']) && $plugin_info['has_update']) { ?>
														| <strong><?php esc_html_e('Available:', 'houzez'); ?></strong>
														<?php echo esc_html($plugin['version']); ?>
													<?php } ?>
												<?php } elseif (!empty($plugin['version'])) { ?>
													<strong><?php esc_html_e('Version:', 'houzez'); ?></strong>
													<?php echo esc_html($plugin['version']); ?>
												<?php } ?>
											</div>
										<?php } ?>
									</div>
									
									<!-- Plugin Labels -->
									<div class="plugin-labels">
										<?php if( $plugin['required'] ) { ?>
										<span class="admin-houzez-required-label"><?php esc_html_e('Required', 'houzez'); ?></span>
										<?php } else { ?>
										<span class="admin-houzez-recommended-label"><?php esc_html_e('Recommended', 'houzez'); ?></span>
										<?php } ?>

										<?php if( isset($plugin['wp_org']) && $plugin['wp_org'] ) { ?>
										<span class="admin-houzez-wporg-label"><?php esc_html_e('WordPress.org', 'houzez'); ?></span>
										<?php } ?>
									</div>
								</div>

								<!-- Plugin Info -->
								<div class="plugin-info-section">
									<div class="plugin-description">
										<?php echo esc_html( $plugin['description'] ); ?>
									</div>

									<?php if ($plugin_info['compatibility_warning']) { ?>
									<div class="compatibility-warning">
										<span class="dashicons dashicons-warning"></span>
										<?php echo esc_html($plugin_info['compatibility_warning']); ?>
									</div>
									<?php } ?>
								</div>

							</div>
						</div>

						<!-- Plugin Actions -->
						<?php if( houzez_theme_verified() ) { ?>
						<div class="plugin-actions-section">
							<?php
							$action_links = houzez_get_action_links( $plugin, $plugin_info );
							if ( $action_links ) {
								echo $action_links;
							}
							?>
						</div>
						<?php } else { ?>
							<div class="plugin-actions-section">
								<div class="theme-verification-notice">
									<p><?php esc_html_e('Theme verification required to manage plugins.', 'houzez'); ?></p>
								</div>
							</div>
						<?php } ?>


					</div>
					<?php
				}
				?>

			</div>

		</div>
	</div>
</div>

<!-- Loading Overlay -->
<div id="houzez-plugin-loading" class="houzez-loading-overlay" style="display: none;">
	<div class="loading-content">
		<div class="loading-header">
			<div class="header-icon">
				<span class="dashicons dashicons-admin-plugins"></span>
			</div>
			<h3 class="loading-title"><?php esc_html_e('Processing Plugins...', 'houzez'); ?></h3>
			<div class="houzez-spinner"></div>
		</div>
		
		<div class="loading-progress">
			<div class="progress-info">
				<span class="progress-count">0/0</span>
				<span class="progress-percentage">0%</span>
			</div>
			<div class="houzez-progress-bar small">
				<div class="houzez-progress-fill default animated" style="width: 0%"></div>
			</div>
		</div>
		
		<div class="loading-details">
			<div class="current-action">
				<span class="action-icon">⏳</span>
				<span class="action-text"><?php esc_html_e('Preparing...', 'houzez'); ?></span>
			</div>
			<div class="current-plugin">
				<span class="plugin-name"></span>
			</div>
		</div>
		
		<div class="loading-log">
			<div class="log-header">
				<span class="dashicons dashicons-list-view"></span>
				<?php esc_html_e('Progress Log', 'houzez'); ?>
			</div>
			<div class="log-content">
				<!-- Progress messages will be added here -->
			</div>
		</div>
	</div>
</div>

<!-- Nonce for AJAX requests -->
<input type="hidden" id="houzez-plugin-nonce" value="<?php echo wp_create_nonce('houzez-admin-nonce'); ?>">

<script>
// Update active plugins count when plugins are activated/deactivated
jQuery(document).ready(function($) {
	// Function to update the active plugins count
	function updateActivePluginsCount() {
		var activeCount = 0;
		$('.admin-houzez-box-plugins').each(function() {
			var status = $(this).attr('data-plugin-status');
			if (status === 'active') {
				activeCount++;
			}
		});
		$('#active-plugins-count').text(activeCount);
	}
	
	// Listen for plugin status changes from existing functionality
	$(document).on('plugin-activated plugin-deactivated', function() {
		setTimeout(updateActivePluginsCount, 100);
	});
	
	// Also update count when plugin boxes are updated (for compatibility with existing JS)
	var observer = new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
			if (mutation.type === 'attributes' && mutation.attributeName === 'data-plugin-status') {
				updateActivePluginsCount();
			}
		});
	});
	
	// Observe all plugin boxes for status changes
	$('.admin-houzez-box-plugins').each(function() {
		observer.observe(this, {
			attributes: true,
			attributeFilter: ['data-plugin-status']
		});
	});
});
</script>





<?php
// Helper function to get plugin statistics
function houzez_get_plugin_stats($plugins_array) {
	$stats = array(
		'total' => count($plugins_array),
		'active' => 0,
		'updates' => 0,
		'required' => 0
	);
	
	// Get cached update information to avoid multiple API calls
	$cached_updates = get_transient('houzez_plugin_updates_cache');
	if ($cached_updates === false) {
		$cached_updates = array();
	}
	
	// Only get plugin updates if we're on the plugins page for better performance
	if (isset($_GET['page']) && $_GET['page'] === 'houzez_plugins') {
		$plugin_updates = get_plugin_updates();
		$update_plugins = get_site_transient( 'update_plugins' );
	} else {
		$plugin_updates = array();
		$update_plugins = (object) array('response' => array());
	}
	
	foreach ($plugins_array as $plugin) {
		if ($plugin['required']) {
			$stats['required']++;
		}
		
		if (is_plugin_active($plugin['path'])) {
			$stats['active']++;
		}
		
		// Check for WordPress.org updates (multiple sources)
		$has_update = isset($plugin_updates[$plugin['path']]) || 
		              (isset($update_plugins->response[$plugin['path']]));
		
		// Use cached update info to avoid API calls
		if (!$has_update && file_exists(WP_PLUGIN_DIR . '/' . $plugin['path'])) {
			$cache_key = $plugin['slug'];
			
			// Check cached update status first
			if (isset($cached_updates[$cache_key])) {
				$has_update = $cached_updates[$cache_key];
			} else {
				// Only check for updates if we're on the plugins page
				if (isset($_GET['page']) && $_GET['page'] === 'houzez_plugins') {
					// Check for WordPress.org plugins using API (with improved caching)
					if (isset($plugin['wp_org']) && $plugin['wp_org']) {
						$plugin_data = houzez_get_cached_plugin_data($plugin['path']);
						if ($plugin_data && !empty($plugin_data['Version'])) {
							$has_update = houzez_check_wporg_plugin_version($plugin['slug'], $plugin_data['Version']);
						}
					}
					// Check for custom plugin updates
					elseif (!empty($plugin['version'])) {
						$plugin_data = houzez_get_cached_plugin_data($plugin['path']);
						if ($plugin_data && !empty($plugin_data['Version'])) {
							$has_update = version_compare($plugin_data['Version'], $plugin['version'], '<');
						}
					}
					
					// Cache the result for 30 minutes
					$cached_updates[$cache_key] = $has_update;
				}
			}
		}
		
		if ($has_update) {
			$stats['updates']++;
		}
	}
	
	// Update the cache
	if (isset($_GET['page']) && $_GET['page'] === 'houzez_plugins') {
		set_transient('houzez_plugin_updates_cache', $cached_updates, 30 * MINUTE_IN_SECONDS);
	}
	
	return $stats;
}

// Helper function to get required plugins status
function houzez_get_required_plugins_status($plugins_array) {
	$status = array(
		'total' => 0,
		'installed' => 0,
		'active' => 0
	);
	
	foreach ($plugins_array as $plugin) {
		if ($plugin['required']) {
			$status['total']++;
			
			// Check if plugin is installed
			if (file_exists(WP_PLUGIN_DIR . '/' . $plugin['path'])) {
				$status['installed']++;
				
				// Check if plugin is active
				if (is_plugin_active($plugin['path'])) {
					$status['active']++;
				}
			}
		}
	}
	
	return $status;
}

// Helper function to get detailed plugin information
function houzez_get_plugin_info($plugin, $plugin_updates) {
	$info = array(
		'status' => 'not-installed',
		'status_text' => __('Not Installed', 'houzez'),
		'status_class' => 'status-not-installed',
		'has_update' => false,
		'installed_version' => '',
		'compatibility_warning' => ''
	);
	
	$plugin_file = $plugin['path'];
	
	// Only get update plugins data if we're on the plugins page
	if (isset($_GET['page']) && $_GET['page'] === 'houzez_plugins') {
		$update_plugins = get_site_transient( 'update_plugins' );
	} else {
		$update_plugins = (object) array('response' => array());
	}
	
	// Check if plugin is installed
	if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
		if (is_plugin_active($plugin_file)) {
			$info['status'] = 'active';
			$info['status_text'] = __('Active', 'houzez');
			$info['status_class'] = 'status-active';
		} else {
			$info['status'] = 'inactive';
			$info['status_text'] = __('Inactive', 'houzez');
			$info['status_class'] = 'status-inactive';
		}
		
		// Get installed version using cached data
		$plugin_data = houzez_get_cached_plugin_data($plugin_file);
		$info['installed_version'] = $plugin_data ? $plugin_data['Version'] : '';
		
		// Check for updates - WordPress.org plugins (multiple sources)
		if (isset($plugin_updates[$plugin_file]) || (isset($update_plugins->response[$plugin_file]))) {
			$info['has_update'] = true;
			$info['status'] = 'update-available';
			$info['status_text'] = __('Update Available', 'houzez');
			$info['status_class'] = 'status-update-available';
		}
		// Fallback check for WordPress.org plugins using API (only on plugins page)
		elseif (isset($plugin['wp_org']) && $plugin['wp_org'] && !empty($info['installed_version'])) {
			// Use cached data if available, otherwise only check on plugins page
			$cached_updates = get_transient('houzez_plugin_updates_cache');
			$cache_key = $plugin['slug'];
			
			if ($cached_updates && isset($cached_updates[$cache_key])) {
				$has_api_update = $cached_updates[$cache_key];
			} elseif (isset($_GET['page']) && $_GET['page'] === 'houzez_plugins') {
				$has_api_update = houzez_check_wporg_plugin_version($plugin['slug'], $info['installed_version']);
			} else {
				$has_api_update = false;
			}
			
			if ($has_api_update) {
				$info['has_update'] = true;
				$info['status'] = 'update-available';
				$info['status_text'] = __('Update Available', 'houzez');
				$info['status_class'] = 'status-update-available';
			}
		}
		// Check for updates - Custom plugins (compare with our plugins array version)
		elseif (!empty($plugin['version']) && !empty($info['installed_version'])) {
			if (version_compare($info['installed_version'], $plugin['version'], '<')) {
				$info['has_update'] = true;
				$info['status'] = 'update-available';
				$info['status_text'] = __('Update Available', 'houzez');
				$info['status_class'] = 'status-update-available';
			}
		}
		
		// Check compatibility
		if ($plugin_data && !empty($plugin_data['RequiresWP'])) {
			global $wp_version;
			if (version_compare($wp_version, $plugin_data['RequiresWP'], '<')) {
				$info['compatibility_warning'] = sprintf(
					__('This plugin requires WordPress %s or higher.', 'houzez'),
					$plugin_data['RequiresWP']
				);
			}
		}
	}
	
	return $info;
}

function houzez_get_action_links( $plugin, $plugin_info = null ) {
	if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'update_plugins' ) && ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'delete_plugins' ) ) {
		return '';
	}

	if ( ! $plugin_info ) {
		$plugin_updates = get_plugin_updates();
		$plugin_info = houzez_get_plugin_info( $plugin, $plugin_updates );
	}

	$button = '';
	$plugin_name = $plugin['name']; 
	$plugin_file = $plugin['path'];
	$plugin_slug = $plugin['slug'];
	$plugin_source = isset($plugin['source']) ? $plugin['source'] : '';
	$is_required = $plugin['required'];

	// Check if plugin is installed
	$is_installed = file_exists( WP_PLUGIN_DIR . '/' . $plugin_file );
	$is_active = is_plugin_active( $plugin_file );
	
	// Check for updates (both WordPress.org and custom plugins)
	// Only get update data if we're on the plugins page
	if (isset($_GET['page']) && $_GET['page'] === 'houzez_plugins') {
		$plugin_updates_list = get_plugin_updates();
		$update_plugins = get_site_transient( 'update_plugins' );
	} else {
		$plugin_updates_list = array();
		$update_plugins = (object) array('response' => array());
	}
	
	$has_update = isset( $plugin_updates_list[ $plugin_file ] ) || 
	              (isset($update_plugins->response[$plugin_file]));
	
	// Fallback check for WordPress.org plugins using API (use cached data when possible)
	if ( ! $has_update && $is_installed && isset($plugin['wp_org']) && $plugin['wp_org'] ) {
		$cached_updates = get_transient('houzez_plugin_updates_cache');
		$cache_key = $plugin['slug'];
		
		if ($cached_updates && isset($cached_updates[$cache_key])) {
			$has_update = $cached_updates[$cache_key];
		} elseif (isset($_GET['page']) && $_GET['page'] === 'houzez_plugins') {
			$plugin_data = houzez_get_cached_plugin_data( $plugin_file );
			if ( $plugin_data && ! empty( $plugin_data['Version'] ) ) {
				$has_update = houzez_check_wporg_plugin_version($plugin['slug'], $plugin_data['Version']);
			}
		}
	}
	
	// For custom plugins, compare installed version with required version
	if ( ! $has_update && $is_installed && ! empty( $plugin['version'] ) ) {
		$plugin_data = houzez_get_cached_plugin_data( $plugin_file );
		if ( $plugin_data && ! empty( $plugin_data['Version'] ) && version_compare( $plugin_data['Version'], $plugin['version'], '<' ) ) {
			$has_update = true;
		}
	}

	if ( ! $is_installed ) {
		// 1. Not installed - Show only Install button
		$install_text = esc_attr__( 'Install Now', 'houzez' );
		if ( ! empty( $plugin_source ) ) {
			$button = sprintf(
				'<a class="houzez-plugin-js houzez-install-btn button button-primary" data-name="%s" data-slug="%s" data-source="%s" data-file="%s" href="#">%s</a>',
				esc_attr( $plugin_name ),
				esc_attr( $plugin_slug ),
				esc_url( $plugin_source ),
				esc_attr( $plugin_file ),
				$install_text
			);
		} else {
			$button = sprintf(
				'<a class="houzez-plugin-js houzez-install-btn button button-primary" href="#" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
				esc_attr( $plugin_name ),
				esc_attr( $plugin_slug ),
				esc_attr( $plugin_file ),
				$install_text
			);
		}
	} elseif ( ! $is_active ) {
		// 2. Installed but not active - Show Activate, Update (if available), and Uninstall (if not required)
		
		// Activate button
		if ( current_user_can( 'activate_plugin', $plugin_file ) ) {
			$button = sprintf( '<a href="#" class="houzez-plugin-js houzez-activate-btn button button-primary" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
				esc_attr( $plugin_name ),
				esc_attr( $plugin_slug ),
				esc_attr( $plugin_file ),
				esc_attr__( 'Activate', 'houzez' )
			);
		}

		// Update button (if update available)
		if ( $has_update && current_user_can( 'update_plugins' ) ) {
			$button .= sprintf( ' <a href="#" class="houzez-plugin-js houzez-update-btn button" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
				esc_attr( $plugin_name ),
				esc_attr( $plugin_slug ),
				esc_attr( $plugin_file ),
				esc_attr__( 'Update Now', 'houzez' )
			);
		}

		// Uninstall button (only for non-required plugins)
		if ( ! $is_required && current_user_can( 'delete_plugins' ) ) {
			$button .= sprintf( ' <a href="#" class="houzez-plugin-js houzez-uninstall-btn button button-link-delete" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
				esc_attr( $plugin_name ),
				esc_attr( $plugin_slug ),
				esc_attr( $plugin_file ),
				esc_attr__( 'Uninstall', 'houzez' )
			);
		}
	} else {
		// 3. Active - Show Deactivate and Update (if available). NO Uninstall for active plugins
		
		// Active status indicator
		$button = sprintf('<span class="button button-disabled">%s</span>',
			esc_attr__( 'Active', 'houzez' )
		);

		// Deactivate button
		if ( current_user_can( 'deactivate_plugin', $plugin_file ) ) {
			$button .= sprintf( ' <a href="#" class="houzez-plugin-js houzez-deactivate-btn button" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
				esc_attr( $plugin_name ),
				esc_attr( $plugin_slug ),
				esc_attr( $plugin_file ),
				esc_attr__( 'Deactivate', 'houzez' )
			);
		}

		// Update button (if update available)
		if ( $has_update && current_user_can( 'update_plugins' ) ) {
			$button .= sprintf( ' <a href="#" class="houzez-plugin-js houzez-update-btn button" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
				esc_attr( $plugin_name ),
				esc_attr( $plugin_slug ),
				esc_attr( $plugin_file ),
				esc_attr__( 'Update Now', 'houzez' )
			);
		}
	}

	return $button;
}
?>
