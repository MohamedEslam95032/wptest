<?php
namespace HouzezStudio;

defined( 'ABSPATH' ) || exit;

class Houzez_Studio_Query_Switcher {
	private $switched_data = [];

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
	 * Switch to a custom query.
	 *
	 * @param array $query_vars New query variables.
	 * @param bool  $force_global_post Whether to force setting the global post.
	 */
	public function switch_to_query( $query_vars, $force_global_post = false ) {
		global $wp_query;
		$current_query_vars = $wp_query->query;

		// If the query is already switched or is the same query, return.
		if ( $current_query_vars === $query_vars ) {
			$this->switched_data[] = false;
			return;
		}

		$new_query = new \WP_Query( $query_vars );

		$switched_data = [
			'switched' => $new_query,
			'original' => $wp_query,
		];

		if ( ! empty( $GLOBALS['post'] ) ) {
			$switched_data['post'] = $GLOBALS['post'];
		}

		$this->switched_data[] = $switched_data;

		$wp_query = $new_query;

		unset( $GLOBALS['post'] );

		if ( isset( $new_query->posts[0] ) ) {
			if ( $force_global_post || $new_query->is_singular() ) {
				$GLOBALS['post'] = $new_query->posts[0];
				setup_postdata( $GLOBALS['post'] );
			}
		}

		if ( $new_query->is_author() ) {
			$GLOBALS['authordata'] = get_userdata( $new_query->get( 'author' ) );
		}
	}

	/**
	 * Restore the original query.
	 */
	public function restore_current_query() {
		$data = array_pop( $this->switched_data );

		if ( ! $data ) {
			return;
		}

		global $wp_query;

		$wp_query = $data['original'];

		unset( $GLOBALS['post'] );
		unset( $GLOBALS['authordata'] );

		if ( ! empty( $data['post'] ) ) {
			$GLOBALS['post'] = $data['post'];
			setup_postdata( $GLOBALS['post'] );
		}

		if ( $wp_query->is_author() ) {
			$GLOBALS['authordata'] = get_userdata( $wp_query->get( 'author' ) );
		}
	}
}

Houzez_Studio_Query_Switcher::instance();
