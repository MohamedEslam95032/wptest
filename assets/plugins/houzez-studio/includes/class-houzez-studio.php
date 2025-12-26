<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://themeforest.net/user/favethemes
 * @since      1.0.0
 *
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/includes
 * @author     Waqas Riaz <waqas@favethemes.com>
 */

namespace HouzezStudio;
use Elementor;


class Houzez_Studio {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Houzez_Studio_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Instance of Elemenntor Frontend class.
	 *
	 * @var \Elementor\Frontend()
	 */
	private static $elementor_instance;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The minimum Elementor version number required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $minimum_elementor_version = '3.13.0';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'FTS_VERSION' ) ) {
			$this->version = FTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'houzez-studio';

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), -1 );

		add_action( 'init', array( $this, 'initialize_plugin' ) );


		$this->load_dependencies();

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_cache_hooks();
		$this->define_http_timeout_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Houzez_Studio_Loader. Orchestrates the hooks of the plugin.
	 * - Houzez_Studio_i18n. Defines internationalization functionality.
	 * - Houzez_Studio_Admin. Defines all hooks for the admin area.
	 * - Houzez_Studio_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// Load WPML & Polylang Compatibility if WPML is installed and activated.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'POLYLANG_BASENAME' ) ) {
			require_once FTS_DIR_PATH . 'includes/compatibility/class-houzez-studio-wpml-compatibility.php';
		}

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once FTS_DIR_PATH . 'includes/class-houzez-studio-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once FTS_DIR_PATH . 'includes/class-houzez-studio-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once FTS_DIR_PATH . 'admin/class-houzez-studio-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once FTS_DIR_PATH . 'public/class-houzez-studio-public.php';

		// Only load Elementor-dependent classes if Elementor is active
		if ( did_action( 'elementor/loaded' ) ) {
			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once FTS_DIR_PATH . 'includes/class-houzez-render-template.php';

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once FTS_DIR_PATH . 'elementor/class-houzez-elementor.php';
		}

		/**
		 * The class responsible change query
		 */
		//require_once FTS_DIR_PATH . 'includes/class-houzez-query-switcher.php';

		/**
		 * Helper functions
		 * core plugin.
		 */
		require_once FTS_DIR_PATH . 'includes/helpers.php';

		$this->loader = new Houzez_Studio_Loader();

	}

	public function initialize_plugin() {

		$is_elementor_installed = ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) ? true : false;

		$is_elementor_outdated = ( $is_elementor_installed && ( ! version_compare( ELEMENTOR_VERSION, self::$minimum_elementor_version, '>=' ) ) ) ? true : false;

		if ( ( ! $is_elementor_installed ) || $is_elementor_outdated ) {
			$this->check_elementor_version( $is_elementor_installed, $is_elementor_outdated );
		}
		
		if( $is_elementor_installed ) {
			self::$elementor_instance = Elementor\Plugin::instance();
		}

	}

	/**
	 * Prints the admin notics when Elementor is not installed or activated or version outdated.
	 *
	 * @since 1.0.0
	 */
	public function check_elementor_version( $is_elementor_installed, $is_elementor_outdated ) {


		if ( ( ! did_action( 'elementor/loaded' ) ) || ( ! $is_elementor_installed ) ) {
			add_action( 'admin_notices', [ $this, 'elementor_installion_notice' ] );
			add_action( 'network_admin_notices', [ $this, 'elementor_installion_notice' ] );
			return;
		}

		// Check for the minimum required Elementor version.
		if ( $is_elementor_outdated ) {
			if ( current_user_can( 'update_plugins' ) ) {
				add_action( 'admin_notices',
				[ $this, 'elementor_version_notice' ] );
				add_action( 'network_admin_notices', [ $this, 'elementor_installion_notice' ] );
			}
			return;
		}

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Houzez_Studio_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Houzez_Studio_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Houzez_Studio_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Houzez_Studio_Public( $this->get_plugin_name(), $this->get_version() );
		

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Register hooks for cache management.
	 *
	 * @since    1.3.2
	 * @access   private
	 */
	private function define_cache_hooks() {
		// Clear cache when FTS builder posts are saved or updated
		$this->loader->add_action( 'save_post_fts_builder', $this, 'clear_template_caches', 10, 3 );
		$this->loader->add_action( 'delete_post', $this, 'clear_template_caches_on_delete', 10, 1 );
		$this->loader->add_action( 'trash_post', $this, 'clear_template_caches_on_delete', 10, 1 );
		
		// Add action to clear cache via URL parameter (for testing)
		$this->loader->add_action( 'init', $this, 'handle_cache_clear_request' );
		
		// Clear cache when template conditions are updated
		$this->loader->add_action( 'update_post_meta', $this, 'clear_cache_on_condition_update', 10, 4 );
	}

	/**
	 * Clear template caches when a template is saved.
	 *
	 * @since    1.3.2
	 * @param    int     $post_id  The post ID.
	 * @param    WP_Post $post     The post object.
	 * @param    bool    $update   Whether this is an update.
	 */
	public function clear_template_caches( $post_id, $post, $update ) {
		// Clear all FTS template transients
		global $wpdb;
		$wpdb->query(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE '_transient_fts_template_%' 
			OR option_name LIKE '_transient_timeout_fts_template_%'
			OR option_name LIKE '_transient_fts_posts_criteria_%'
			OR option_name LIKE '_transient_timeout_fts_posts_criteria_%'
			OR option_name LIKE '_transient_fts_css_generated_%'
			OR option_name LIKE '_transient_timeout_fts_css_generated_%'"
		);
		
		// Clear object cache if available
		if (function_exists('wp_cache_flush')) {
			wp_cache_flush();
		}
	}

	/**
	 * Clear template caches when a post is deleted or trashed.
	 *
	 * @since    1.3.2
	 * @param    int $post_id The post ID.
	 */
	public function clear_template_caches_on_delete( $post_id ) {
		if ( get_post_type( $post_id ) === 'fts_builder' ) {
			$this->clear_template_caches( $post_id, null, false );
		}
	}
	
	/**
	 * Handle cache clear requests via URL parameter.
	 *
	 * @since    1.3.2
	 */
	public function handle_cache_clear_request() {
		// Check if cache clear is requested and user has permission
		if ( isset( $_GET['fts_clear_cache'] ) && current_user_can( 'manage_options' ) ) {
			$this->clear_all_template_caches();
			
			// Add admin notice
			add_action( 'admin_notices', function() {
				echo '<div class="notice notice-success is-dismissible"><p>Houzez Studio template cache has been cleared.</p></div>';
			});
			
			// Redirect to remove the parameter from URL
			$redirect_url = remove_query_arg( 'fts_clear_cache' );
			if ( ! is_admin() ) {
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}
	}
	
	/**
	 * Clear cache when template conditions are updated.
	 *
	 * @since    1.3.2
	 * @param    int    $meta_id    ID of the metadata entry.
	 * @param    int    $post_id    Post ID.
	 * @param    string $meta_key   Meta key.
	 * @param    mixed  $meta_value Meta value.
	 */
	public function clear_cache_on_condition_update( $meta_id, $post_id, $meta_key, $meta_value ) {
		// Check if this is a template condition update
		$condition_keys = ['fts_included_options', 'fts_excluded_options', 'fts_template_type', 'fts_block_hook'];
		
		if ( get_post_type( $post_id ) === 'fts_builder' && in_array( $meta_key, $condition_keys ) ) {
			$this->clear_all_template_caches();
		}
	}
	
	/**
	 * Clear all template caches.
	 *
	 * @since    1.3.2
	 */
	public function clear_all_template_caches() {
		global $wpdb;
		
		// Delete all FTS related transients
		$wpdb->query(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE '_transient_fts_%' 
			OR option_name LIKE '_transient_timeout_fts_%'"
		);
		
		// Clear object cache if available
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}
		
		// Trigger action for other plugins to hook into
		do_action( 'fts_cache_cleared' );
	}

	/**
	 * Register hooks for HTTP timeout management.
	 *
	 * @since    1.3.2
	 * @access   private
	 */
	private function define_http_timeout_hooks() {
		// Reduce timeout for external HTTP requests to prevent page load blocking
		add_filter( 'http_request_timeout', array( $this, 'reduce_http_timeout' ), 10, 2 );
		add_filter( 'http_request_args', array( $this, 'modify_http_request_args' ), 10, 2 );
	}

	/**
	 * Reduce HTTP request timeout to prevent long page loads.
	 *
	 * @since    1.3.2
	 * @param    int    $timeout  Timeout in seconds.
	 * @param    string $url      The request URL.
	 * @return   int    Modified timeout.
	 */
	public function reduce_http_timeout( $timeout, $url = '' ) {
		// Only reduce timeout for non-critical external requests
		if ( ! is_admin() && ! wp_doing_ajax() && ! wp_doing_cron() ) {
			// Check if this is a font or asset request
			if ( strpos( $url, 'fonts.googleapis.com' ) !== false 
				|| strpos( $url, 'fonts.gstatic.com' ) !== false 
				|| strpos( $url, 'cdnjs.cloudflare.com' ) !== false 
				|| strpos( $url, 'maxcdn.bootstrapcdn.com' ) !== false ) {
				return 3; // 3 second timeout for font/asset requests
			}
			
			// General timeout reduction for frontend requests
			return min( $timeout, 5 ); // Max 5 seconds for any request
		}
		
		return $timeout;
	}

	/**
	 * Modify HTTP request arguments to optimize performance.
	 *
	 * @since    1.3.2
	 * @param    array  $args HTTP request arguments.
	 * @param    string $url  The request URL.
	 * @return   array  Modified arguments.
	 */
	public function modify_http_request_args( $args, $url ) {
		// Skip SSL verification for known CDNs to speed up requests
		$trusted_domains = [
			'fonts.googleapis.com',
			'fonts.gstatic.com',
			'cdnjs.cloudflare.com',
			'maxcdn.bootstrapcdn.com'
		];
		
		foreach ( $trusted_domains as $domain ) {
			if ( strpos( $url, $domain ) !== false ) {
				$args['sslverify'] = false;
				$args['timeout'] = 3;
				$args['blocking'] = false; // Make non-blocking for assets
				break;
			}
		}
		
		return $args;
	}

	/**
	 * Admin notice if elementor plugin not installed or activated
	 */
	public function elementor_installion_notice() {

		$screen = get_current_screen();
		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		if ( ! did_action( 'elementor/loaded' ) ) {
			
			if ( ! ( current_user_can( 'activate_plugins' ) && current_user_can( 'install_plugins' ) ) ) {
				return;
			}

			$class = 'notice notice-error';
			
			$message = sprintf( __( 'The %1$sHouzez Studio%2$s plugin requires %1$sElementor%2$s plugin installed & activated.', 'houzez-studio' ), '<strong>', '</strong>' );

			$plugin = 'elementor/elementor.php';

			if ( self::fts_studio_is_elementor_installed() ) {

				$action_url   = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
				$button_label = __( 'Activate Elementor', 'houzez-studio' );

			} else {

				$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
				$button_label = __( 'Install Elementor', 'houzez-studio' );
			}

			$button = '<p><a href="' . esc_url( $action_url ) . '" class="button-primary">' . esc_html( $button_label ) . '</a></p><p></p>';

			printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), wp_kses_post( $message ), wp_kses_post( $button ) );
		}
	}

	/**
	 * Admin notice when elementor plugin out of date
	 */
	public function elementor_version_notice() {

		if ( ! ( current_user_can( 'activate_plugins' ) && current_user_can( 'install_plugins' ) ) ) {
			return;
		}

		$class = 'notice notice-error';
		$message = sprintf( __( 'The %1$sHouzez Studio%2$s plugin has stopped working because you are using an older version of %1$sElementor%2$s plugin.', 'houzez-studio' ), '<strong>', '</strong>' );

		$plugin = 'elementor/elementor.php';

		if ( self::fts_studio_is_elementor_installed() ) {

			$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&amp;plugin=' ) . $plugin . '&amp;', 'upgrade-plugin_' . $plugin );
			$button_label = __( 'Update Elementor', 'houzez-studio' );

		} else {

			$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
			$button_label = __( 'Install Elementor', 'houzez-studio' );
		}

		$button = '<p><a href="' . esc_url( $action_url ) . '" class="button-primary">' . esc_html( $button_label ) . '</a></p><p></p>';

		printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), wp_kses_post( $message ), wp_kses_post( $button ) );
	}


	/**
	 * Check if elementor plugin installed
	 */
	public static function fts_studio_is_elementor_installed() {

		return ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) ? true : false;
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Houzez_Studio_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Returns true if the request is a non-legacy REST API request.
	 *
	 * Legacy REST requests should still run some extra code for backwards compatibility.
	 *
	 * @todo: replace this function once core WP function is available: https://core.trac.wordpress.org/ticket/42061.
	 *
	 * @return bool
	 */
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return apply_filters( 'fts_is_rest_api_request', $is_rest_api_request );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
		}
	}

	/**
	 * When WP has loaded all plugins, trigger the `Houzez_studio_loaded` hook.
	 *
	 * This ensures `Houzez_studio_loaded` is called only after all other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 * the load order.
	 *
	 * @since 1.0.0
	 */
	public function on_plugins_loaded() {
		do_action( 'fts_loaded' );
	}

}
