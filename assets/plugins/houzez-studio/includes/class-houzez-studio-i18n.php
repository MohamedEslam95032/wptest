<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://themeforest.net/user/favethemes
 * @since      1.0.0
 *
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/includes
 * @author     Waqas Riaz <waqas@favethemes.com>
 */
namespace HouzezStudio;

class Houzez_Studio_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'houzez-studio',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
