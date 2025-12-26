<?php
/**
 * Retuns theme options data
 *
 * @package	Houzez
 * @author Waqas Riaz
 * @copyright Copyright (c) 2016, Favethemes
 * @link http://favethemes.com
 * @since Houzez 1.0
 */

if ( ! function_exists( 'houzez_option' ) ) {
	function houzez_option( $id, $fallback = false, $param = false ) {
		global $houzez_options;
		if ( $fallback == false ) $fallback = '';
		$output = ( isset($houzez_options[$id]) && $houzez_options[$id] !== '' ) ? $houzez_options[$id] : $fallback;
		if ( !empty($houzez_options[$id]) && $param ) {
			$output = $houzez_options[$id][$param];
		}
		return $output;
	}
}