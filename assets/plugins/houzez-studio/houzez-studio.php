<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function 
 * that starts the plugin.
 *
 * @link              https://themeforest.net/user/favethemes
 * @since             1.0.0
 * @package           Houzez_Studio
 *
 * @wordpress-plugin
 * Plugin Name:       Houzez Studio
 * Plugin URI:        https://studio.houzez.co
 * Description:       Add header, footer, menu builder for favethemes themes
 * Version:           1.3.2
 * Author:            Favethemes, Waqas Riaz
 * Author URI:        https://themeforest.net/user/favethemes/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       houzez-studio
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FTS_VERSION', '1.3.2' );
define( 'FTS_NOTICE_MIN_PHP_VERSION', '7.0' );
define( 'FTS_NOTICE_MIN_WP_VERSION', '6.0' );
define( 'FTS_DELIMITER', '|' );

define( 'FTS_FILE', __FILE__ );
define( 'FTS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'FTS_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'FTS_PHP_MIN_REQUIREMENTS_NOTICE', 'wp_php_min_requirements_' . FTS_NOTICE_MIN_PHP_VERSION . '_' . FTS_NOTICE_MIN_WP_VERSION );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-houzez-studio-activator.php
 */
function activate_fts() {
	require_once FTS_DIR_PATH . 'includes/class-houzez-studio-activator.php';
	HouzezStudio\Houzez_Studio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-houzez-studio-deactivator.php
 */
function deactivate_fts() {
	require_once FTS_DIR_PATH . 'includes/class-houzez-studio-deactivator.php';
	HouzezStudio\Houzez_Studio_Deactivator::deactivate();
}

register_activation_hook( FTS_FILE, 'activate_fts' );
register_deactivation_hook( FTS_FILE, 'deactivate_fts' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require FTS_DIR_PATH . 'includes/class-houzez-studio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_houzez_studio() {

	$plugin = new HouzezStudio\Houzez_Studio();
	$plugin->run();

}
run_houzez_studio();
