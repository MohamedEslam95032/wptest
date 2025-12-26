<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://themeforest.net/user/favethemes
 * @since      1.0.0
 *
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/includes
 * @author     Waqas Riaz <waqas@favethemes.com>
 */
namespace HouzezStudio;

class Houzez_Studio_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option('houzez_studio_plugin_active');
	}

}
