<?php
/*
Plugin Name: MLS On The Fly &reg;
Plugin URI: https://realtyna.com/mls-on-the-fly/
Description: This plugin is designed to replace the default WordPress database with data fetched from RealtyFeed services. Instead of relying on the local database, it dynamically loads and displays real estate data directly from RealtyFeed, providing a seamless integration with external property information.
Version: 1.6.0.5
Author: Realtyna
Author URI: mailto:info@realtyna.net
License: LGPL-3.0-or-later
License URI: https://www.gnu.org/licenses/lgpl-3.0.html
Text Domain: mls-on-the-fly
Domain Path: /assets/langs
Requires PHP: 8.1
*/
require_once(__DIR__ . '/vendor/autoload.php');

use Realtyna\Core\Config;
use Realtyna\MlsOnTheFly\Main;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
if ( !defined( 'REALTYNA_MLS_ON_THE_FLY_VERSION' ) ) {
    define( 'REALTYNA_MLS_ON_THE_FLY_VERSION', '1.6.0.4' );  // Version number for your plugin.
}

if ( !defined( 'REALTYNA_MLS_ON_THE_FLY_NAME' ) ) {
    define( 'REALTYNA_MLS_ON_THE_FLY_NAME', 'Realtyna Mls On The Fly' );  // Name of your plugin.
}

if ( !defined( 'REALTYNA_MLS_ON_THE_FLY_SLUG' ) ) {
    define( 'REALTYNA_MLS_ON_THE_FLY_SLUG', 'realtyna_mls_on_the_fly' );  // Unique slug for the plugin, used for namespacing.
}

if ( !defined( 'REALTYNA_MLS_ON_THE_FLY_DIR' ) ) {
    define( 'REALTYNA_MLS_ON_THE_FLY_DIR', plugin_dir_path( __FILE__ ) );  // Directory path for the plugin files.
}

if ( !defined( 'REALTYNA_MLS_ON_THE_FLY_URL' ) ) {
    define( 'REALTYNA_MLS_ON_THE_FLY_URL', plugin_dir_url( __FILE__ ) );  // URL to the plugin directory.
}

if ( !defined( 'REALTYNA_MLS_ON_THE_FLY_CONFIG_FILE' ) ) {
    define( 'REALTYNA_MLS_ON_THE_FLY_CONFIG_FILE', REALTYNA_MLS_ON_THE_FLY_DIR . 'src/Config/config.php' );  // Path to the configuration file.
}

if ( !defined( 'REALTYNA_MLS_ON_THE_FLY_TEMPLATES_PATH' ) ) {
    define( 'REALTYNA_MLS_ON_THE_FLY_TEMPLATES_PATH', REALTYNA_MLS_ON_THE_FLY_DIR . 'templates/' );  // Path to the templates directory.
}

if ( !defined( 'REALTYNA_RF_SHELL_BASE_PATH' ) ) {
    define( 'REALTYNA_RF_SHELL_BASE_PATH', plugin_dir_path( __FILE__ ) );  // Base path for the shell script.
}


// Autoload classes (if using Composer).
if ( file_exists( REALTYNA_MLS_ON_THE_FLY_DIR . 'vendor/autoload.php' ) ) {
    require_once REALTYNA_MLS_ON_THE_FLY_DIR . 'vendor/autoload.php';
}

try {
    // Instantiate the configuration class.
    $config = new Config(REALTYNA_MLS_ON_THE_FLY_CONFIG_FILE);
    // Instantiate the main plugin class with the config.
    $pluginClass = new Main($config);

    // Register activation hook to handle tasks during plugin activation.
    register_activation_hook(__FILE__, [$pluginClass, 'activation']);

    // Register deactivation hook to handle tasks during plugin deactivation.
    register_deactivation_hook(__FILE__, [$pluginClass, 'deactivation']);

    // Register uninstall hook to handle tasks during plugin uninstallation.
    register_uninstall_hook(__FILE__, [Main::class, 'uninstallation']);

} catch (Exception $e) {
    // Display an error notice in the WordPress admin if an exception occurs.
    $html = '<div class="notice notice-error">
                <p>
                ' . $e->getMessage() . '
                </p>
            </div>';
    add_action('admin_notices', function () use ($html) {
        echo $html;
    });
}
