<?php
/**
 * WPML Compatibility.
 *
 * @since       HFE 1.1.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Set up WPML Compatibiblity Class.
 */
class Houzez_Studio_WPML_Compatibility {

	/**
	 * Instance of Houzez_Studio_WPML_Compatibility.
	 *
	 * @since  1.1.0
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance of Houzez_Studio_WPML_Compatibility
	 *
	 * @since  1.1.0
	 * @return Houzez_Studio_WPML_Compatibility
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Setup actions and filters.
	 *
	 * @since  1.1.0
	 */
	private function __construct() {
		add_filter( 'fts_render_template_id', [ $this, 'get_wpml_object' ] );
	}

	/**
	 * @since  1.1.0
	 * @param  Int $id  Post ID of the template being rendered.
	 * @return Int $id  Post ID of the template being rendered, Passed through the `wpml_object_id` id.
	 */
	public function get_wpml_object( $id ) {
		$translated_id = apply_filters( 'wpml_object_id', $id );

		if ( defined( 'POLYLANG_BASENAME' ) ) {

			if ( null === $translated_id ) {

				// The current language is not defined yet or translation is not available.
				return $id;
			} else {

				// Return translated post ID.
				return $translated_id;
			}
		}

		if ( null === $translated_id ) {
			$translated_id = '';
		}

		return $translated_id;
	}
}

/**
 * Initiate the class.
 */
Houzez_Studio_WPML_Compatibility::instance();
