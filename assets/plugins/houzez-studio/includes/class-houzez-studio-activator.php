<?php

/**
 * Fired during plugin activation
 *
 * @link       https://themeforest.net/user/favethemes
 * @since      1.0.0
 *
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Houzez_Studio
 * @subpackage Houzez_Studio/includes
 * @author     Waqas Riaz <waqas@favethemes.com>
 */
namespace HouzezStudio;

class Houzez_Studio_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		update_option('houzez_studio_plugin_active', true);
		
		// Clear any cached templates on activation
		self::clear_template_cache();
	}
	
	/**
	 * Clear all template caches when plugin is activated.
	 *
	 * @since  1.3.2
	 */
	private static function clear_template_cache() {
		global $wpdb;
		
		// Delete all transients related to FTS templates
		$wpdb->query(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE '_transient_fts_template_%' 
			OR option_name LIKE '_transient_timeout_fts_template_%'"
		);
		
		// Clear object cache if available
		if (function_exists('wp_cache_flush')) {
			wp_cache_flush();
		}
	}

}
