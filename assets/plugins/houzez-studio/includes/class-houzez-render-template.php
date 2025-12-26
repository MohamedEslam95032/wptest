<?php
namespace HouzezStudio;

use HouzezStudio\admin\fieldsManager as FieldManager;

defined( 'ABSPATH' ) || exit;

class FTS_Render_Template {

	/**
	 * FTS_Render_Template version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * post id
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * post type
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * The single instance of the class.
	 *
	 * @var FTS_Render_Template
	 * @since 1.0
	 */
	public static $_instance;

	/**
	 * Main FTS_Render_Template Instance.
	 *
	 * Ensures only one instance of FTS_Render_Template is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return FTS_Render_Template - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}



	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'houzez-studio' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'houzez-studio' ), '1.0' );
	}


	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'houzez_header_studio', array( $this, 'render_header' ), 10 );
		add_action( 'houzez_before_header', array( $this, 'render_before_header' ), 10 );
		add_action( 'houzez_after_header', array( $this, 'render_after_header' ), 10 );

		add_action( 'houzez_footer_studio', array( $this, 'render_footer' ), 10 );
		add_action( 'houzez_before_footer', array( $this, 'render_before_footer' ), 10 );
		add_action( 'houzez_after_footer', array( $this, 'render_after_footer' ), 10 );

		add_action( 'houzez_single_listing', array( $this, 'render_single_listing' ), 10 );
		add_action( 'houzez_single_agent', array( $this, 'render_single_agent' ), 10 );
		add_action( 'houzez_single_agency', array( $this, 'render_single_agency' ), 10 );
		add_action( 'houzez_single_post', array( $this, 'render_single_post' ), 10 );
		
	}

	public function single_template( $single_template ) {
		if ( 'fts_builder' == get_post_type() ) { // phpcs:ignore
			$single_template = FTS_DIR_PATH . '/templates/render-template.php';
		}

		return $single_template;
	}

	/**
	 * Retrieve the header.
	 */
	public function render_header() {

		fts_render_header();
	}

	/**
	 * Retrieve the before header.
	 */
	public function render_before_header() {

		fts_render_before_header();
	}

	/**
	 * Retrieve the after header.
	 */
	public function render_after_header() {

		fts_render_after_header();
	}

	/**
	 * Retrieve the footer.
	 */
	public function render_footer() {

		fts_render_footer();
	}

	/**
	 * Retrieve the before footer.
	 */
	public function render_before_footer() {

		fts_render_before_footer();
	}

	/**
	 * Retrieve the after footer.
	 */
	public function render_after_footer() {

		fts_render_after_footer();
	}

	/**
	 * Retrieve the single listing.
	 */
	public function render_single_listing() {

		fts_render_single_listing();
	}

	/**
	 * Retrieve the single agent.
	 */
	public function render_single_agent() {

		fts_render_single_agent();
	}

	/**
	 * Retrieve the single agency.
	 */
	public function render_single_agency() {

		fts_render_single_agency();
	}

	/**
	 * Retrieve the single post.
	 */
	public function render_single_post() {

		fts_render_single_post();
	}


	/**
	 * Retrieves plugin settings based on the provided option name.
	 *
	 * @param string $setting Option name.
	 * @param mixed  $default Default value if the option is not set.
	 *
	 * @return mixed Setting value or default value.
	 */
	public function fetch_plugin_settings($setting = '', $hook = '', $default = '') {
	    if (in_array($setting, ['tmp_header', 'tmp_before_header', 'tmp_after_header', 'tmp_footer', 'tmp_before_footer', 'tmp_after_footer', 'tmp_megamenu', 'tmp_custom_block', 'single-listing', 'single-agent', 'single-agency', 'single-post'])) {
	        $templateId = $this->fetch_template_id($setting, $hook);
	        return apply_filters("fts_fetch_plugin_settings_{$setting}", $templateId, $default);
	    }

	    return $default;
	}

	/**
	 * Fetches the template ID based on the specified type.
	 *
	 * @param string $type The type of template (e.g., header, footer).
	 *
	 * @return mixed Template ID if found, else returns an empty string.
	 */
	public static function fetch_template_id($type, $hook) {
	    // Skip caching in admin or when editing with Elementor
	    $skip_cache = is_admin() ||
	                  (isset($_GET['elementor-preview']) || isset($_GET['action']) && $_GET['action'] === 'elementor') ||
	                  (defined('DOING_AJAX') && DOING_AJAX);

	    if (!$skip_cache) {
	        // Create a unique cache key that includes page context
	        $cache_context = self::get_cache_context();
	        $cache_key = 'fts_template_' . md5($type . '_' . $hook . '_' . get_locale() . '_' . $cache_context);

	        // Try to get the cached template ID
	        $cached_id = get_transient($cache_key);

	        if ($cached_id !== false) {
	            // Return cached value (even if it's an empty string)
	            return $cached_id;
	        }
	    }

	    // If not cached or cache skipped, fetch from database
	    $options = [
	        'included'  => 'fts_included_options',
	        'exclusion' => 'fts_excluded_options',
	    ];

	    $templates = FieldManager\Favethemes_Field_Manager::instance()->fetch_posts_by_criteria('fts_builder', $options);

	    $template_id = '';
	    foreach ($templates as $template) {
	        if (self::is_matching_template($template['id'], $type, $hook)) {
	            $template_id = $template['id'];
	            break;
	        }
	    }

	    // Only cache if not skipping cache
	    if (!$skip_cache && isset($cache_key)) {
	        // Cache the result for 1 hour
	        set_transient($cache_key, $template_id, HOUR_IN_SECONDS);
	    }

	    return $template_id;
	}
	
	/**
	 * Get cache context based on current page
	 */
	private static function get_cache_context() {
	    $context = '';
	    
	    // Add page type
	    if (is_front_page()) {
	        $context .= 'front_';
	    } elseif (is_home()) {
	        $context .= 'home_';
	    } elseif (is_singular()) {
	        $context .= 'single_' . get_post_type() . '_' . get_the_ID() . '_';
	    } elseif (is_archive()) {
	        if (is_post_type_archive()) {
	            $context .= 'archive_' . get_query_var('post_type') . '_';
	        } elseif (is_tax()) {
	            $term = get_queried_object();
	            $context .= 'tax_' . $term->taxonomy . '_' . $term->term_id . '_';
	        } elseif (is_category()) {
	            $context .= 'cat_' . get_query_var('cat') . '_';
	        } elseif (is_tag()) {
	            $context .= 'tag_' . get_query_var('tag_id') . '_';
	        }
	    } elseif (is_search()) {
	        $context .= 'search_';
	    } elseif (is_404()) {
	        $context .= '404_';
	    }
	    
	    // Add page template if applicable
	    if (is_page_template()) {
	        $template = get_page_template_slug();
	        $context .= 'template_' . $template . '_';
	    }
	    
	    // Add Houzez specific contexts
	    if (function_exists('houzez_is_listings_template') && houzez_is_listings_template()) {
	        $context .= 'listings_';
	    }
	    if (function_exists('houzez_is_agents_template') && houzez_is_agents_template()) {
	        $context .= 'agents_';
	    }
	    if (function_exists('houzez_is_agencies_template') && houzez_is_agencies_template()) {
	        $context .= 'agencies_';
	    }
	    
	    return $context;
	}

	private static function is_matching_template($templateId, $type, $hook = '') {
	    $template_type = get_post_meta(absint($templateId), 'fts_template_type', true);
	    $block_hook = get_post_meta(absint($templateId), 'fts_block_hook', true);

	    // Check if template type matches and, if a hook is provided, check for a hook match as well
	    if ($template_type !== $type || ($hook && $block_hook !== $hook)) {
	        return false;
	    }

	    if (function_exists('pll_current_language') && pll_current_language('slug') !== pll_get_post_language($templateId, 'slug')) {
	        return false;
	    }

	    return true;
	}

}

FTS_Render_Template::instance();

