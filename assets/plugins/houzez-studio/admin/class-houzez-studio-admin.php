<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themeforest.net/user/favethemes
 * @since      1.0.0
 *
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/admin
 * @author     Waqas Riaz <waqas@favethemes.com>
 */
namespace HouzezStudio;

class Houzez_Studio_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string  $plugin_name  The name of this plugin.
	 * @param    string  $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
	    $this->plugin_name = $plugin_name;
	    $this->version = $version;

	    $this->load_dependencies();
	    $this->define_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {
	    include FTS_DIR_PATH . 'admin/class-post-type.php';
	    include FTS_DIR_PATH . 'admin/class-field-manager.php';
	    include FTS_DIR_PATH . 'admin/class-metaboxes.php';
	}

	/**
	 * Register all of the hooks related to the functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_hooks() {
	    add_action( 'template_redirect', [ $this, 'restrict_template_access' ] );
	    add_filter( 'single_template', [ $this, 'select_canvas_template' ], 10, 1 );
	    // Removed update_block_hooks from plugins_loaded to prevent timeout issues
	    // This is now handled in the activation hook only
	}

	/**
	 * Select the single template for 'fts_builder' post type.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $single_template The path to the single template.
	 * @return string The modified template path.
	 */
	public function select_fts_builder_template( $single_template ) {
	    if ( 'fts_builder' == get_post_type() ) { // phpcs:ignore
	        return FTS_DIR_PATH . 'templates/render-template.php';
	    }
	    return $single_template;
	}

	/**
	 * Select the canvas template for 'fts_builder' post type.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $single_template The path to the single template.
	 * @return string The modified template path.
	 */
	public function select_canvas_template( $single_template ) {
	    global $post;

	    if ( 'fts_builder' == $post->post_type ) {
	    	$field_type = get_post_meta($post->ID, 'fts_template_type', true);

	    	if( $field_type == 'single-listing' || $field_type == 'single-agent' || $field_type == 'single-agency' || $field_type == 'single-post' ) {
	    		return FTS_DIR_PATH . 'templates/render-template.php';
	    		//return $single_template;
	    	}

	        $template = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';
	        if ( file_exists( $template ) ) {
	            return $template;
	        }
	        return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
	    }
	    return $single_template;
	}

	/**
	 * Restrict access to Elementor Header & Footer Builder templates for users without 'edit_posts' capability.
	 *
	 * @since  1.0.0
	 */
	public function restrict_template_access() {
	    if ( is_singular( 'fts_builder' ) && ! current_user_can( 'edit_posts' ) ) {
	        wp_redirect( home_url(), 301 );
	        exit;
	    }
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Houzez_Studio_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Houzez_Studio_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/houzez-studio-admin.css', array(), $this->version, 'all' );

		wp_enqueue_style( 'houzez-select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Houzez_Studio_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Houzez_Studio_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'houzez-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, true );

		$wp_lang  = get_locale();
		$language = '';
		if ( '' !== $wp_lang ) {
			$select2_lang = array(
				''               => 'en',
				'hi_IN'          => 'hi',
				'mr'             => 'mr',
				'af'             => 'af',
				'ar'             => 'ar',
				'ary'            => 'ar',
				'as'             => 'as',
				'azb'            => 'az',
				'az'             => 'az',
				'bel'            => 'be',
				'bg_BG'          => 'bg',
				'bn_BD'          => 'bn',
				'bo'             => 'bo',
				'bs_BA'          => 'bs',
				'ca'             => 'ca',
				'ceb'            => 'ceb',
				'cs_CZ'          => 'cs',
				'cy'             => 'cy',
				'da_DK'          => 'da',
				'de_CH'          => 'de',
				'de_DE'          => 'de',
				'de_DE_formal'   => 'de',
				'de_CH_informal' => 'de',
				'dzo'            => 'dz',
				'el'             => 'el',
				'en_CA'          => 'en',
				'en_GB'          => 'en',
				'en_AU'          => 'en',
				'en_NZ'          => 'en',
				'en_ZA'          => 'en',
				'eo'             => 'eo',
				'es_MX'          => 'es',
				'es_VE'          => 'es',
				'es_CR'          => 'es',
				'es_CO'          => 'es',
				'es_GT'          => 'es',
				'es_ES'          => 'es',
				'es_CL'          => 'es',
				'es_PE'          => 'es',
				'es_AR'          => 'es',
				'et'             => 'et',
				'eu'             => 'eu',
				'fa_IR'          => 'fa',
				'fi'             => 'fi',
				'fr_BE'          => 'fr',
				'fr_FR'          => 'fr',
				'fr_CA'          => 'fr',
				'gd'             => 'gd',
				'gl_ES'          => 'gl',
				'gu'             => 'gu',
				'haz'            => 'haz',
				'he_IL'          => 'he',
				'hr'             => 'hr',
				'hu_HU'          => 'hu',
				'hy'             => 'hy',
				'id_ID'          => 'id',
				'is_IS'          => 'is',
				'it_IT'          => 'it',
				'ja'             => 'ja',
				'jv_ID'          => 'jv',
				'ka_GE'          => 'ka',
				'kab'            => 'kab',
				'km'             => 'km',
				'ko_KR'          => 'ko',
				'ckb'            => 'ku',
				'lo'             => 'lo',
				'lt_LT'          => 'lt',
				'lv'             => 'lv',
				'mk_MK'          => 'mk',
				'ml_IN'          => 'ml',
				'mn'             => 'mn',
				'ms_MY'          => 'ms',
				'my_MM'          => 'my',
				'nb_NO'          => 'nb',
				'ne_NP'          => 'ne',
				'nl_NL'          => 'nl',
				'nl_NL_formal'   => 'nl',
				'nl_BE'          => 'nl',
				'nn_NO'          => 'nn',
				'oci'            => 'oc',
				'pa_IN'          => 'pa',
				'pl_PL'          => 'pl',
				'ps'             => 'ps',
				'pt_BR'          => 'pt',
				'pt_PT_ao90'     => 'pt',
				'pt_PT'          => 'pt',
				'rhg'            => 'rhg',
				'ro_RO'          => 'ro',
				'ru_RU'          => 'ru',
				'sah'            => 'sah',
				'si_LK'          => 'si',
				'sk_SK'          => 'sk',
				'sl_SI'          => 'sl',
				'sq'             => 'sq',
				'sr_RS'          => 'sr',
				'sv_SE'          => 'sv',
				'szl'            => 'szl',
				'ta_IN'          => 'ta',
				'te'             => 'te',
				'th'             => 'th',
				'tl'             => 'tl',
				'tr_TR'          => 'tr',
				'tt_RU'          => 'tt',
				'tah'            => 'ty',
				'ug_CN'          => 'ug',
				'uk'             => 'uk',
				'ur'             => 'ur',
				'uz_UZ'          => 'uz',
				'vi'             => 'vi',
				'zh_CN'          => 'zh',
				'zh_TW'          => 'zh',
				'zh_HK'          => 'zh',
			);

			if ( isset( $select2_lang[ $wp_lang ] ) && file_exists( plugin_dir_url( __FILE__ ) . 'js/i18n/' . $select2_lang[ $wp_lang ] . '.js' ) ) {
				$language = $select2_lang[ $wp_lang ];
				wp_enqueue_script(
					'houzez-select2-lang',
					plugin_dir_url( __FILE__ ) . 'js/i18n/' . $select2_lang[ $wp_lang ] . '.js',
					array(
						'jquery',
						'houzez-select2',
					),
					$this->version,
					true
				);
			}
		}
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/houzez-studio-admin.js', array( 'jquery', 'suggest' ), $this->version, true );

		$admin_vars = array(
			'url'   => admin_url( 'admin-ajax.php' ),
			'language' => $language,
			'search' => esc_html__('Search pages / post / categories', 'houzez-studio'),
			'more_char'     => esc_html__( 'or more characters', 'houzez-studio' ),
			'searching'     => esc_html__( 'Searching…', 'houzez-studio' ),
			'nonce' => wp_create_nonce( 'fts_nonce' ),
			'edit'  => admin_url( 'edit.php?post_type=fts_builder' ),
		);

		wp_localize_script(
			$this->plugin_name,
			'admin',
			$admin_vars
		);

	}

}
