<?php
/**
 * Plugin Name: Coonex uiXpress Enforcer (Safe)
 */

defined('ABSPATH') || exit;

/**
 * Emergency kill switch
 */
if (getenv('COONEX_DISABLE_UIXPRESS_ENFORCER') === '1') {
    return;
}

/**
 * Config
 */
define('COONEX_UIP_PLUGIN', 'xpress/uixpress.php');
define('COONEX_UIP_FLAG', 'coonex_uipress_enforced');

/**
 * Safe string contains (PHP < 8 compatible)
 */
function coonex_str_contains($haystack, $needle) {
    return $needle !== '' && strpos($haystack, $needle) !== false;
}

/**
 * Internal admin bypass
 */
function coonex_is_internal_admin() {
    if (!function_exists('current_user_can')) {
        return false;
    }
    return current_user_can('coonex_internal_admin');
}

/**
 * 1) Auto activate once
 */
add_action('admin_init', function () {

    if (coonex_is_internal_admin()) return;
    if (get_option(COONEX_UIP_FLAG)) return;

    $plugin_path = WP_PLUGIN_DIR . '/' . COONEX_UIP_PLUGIN;
    if (!file_exists($plugin_path)) return;

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    if (!is_plugin_active(COONEX_UIP_PLUGIN)) {
        activate_plugin(COONEX_UIP_PLUGIN);
    }

    update_option(COONEX_UIP_FLAG, 1);
});

/**
 * 2) Self heal
 */
add_action('admin_init', function () {

    if (coonex_is_internal_admin()) return;
    if (!get_option(COONEX_UIP_FLAG)) return;

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    if (!is_plugin_active(COONEX_UIP_PLUGIN)) {
        activate_plugin(COONEX_UIP_PLUGIN);
    }
});

/**
 * 3) Hide from plugin list
 */
add_filter('all_plugins', function ($plugins) {

    if (coonex_is_internal_admin()) return $plugins;

    unset($plugins[COONEX_UIP_PLUGIN]);
    return $plugins;
});

/**
 * 4) Block uiXpress pages safely
 */
add_action('admin_init', function () {

    if (coonex_is_internal_admin()) return;
    if (!isset($_GET['page'])) return;

    $page = sanitize_key($_GET['page']);

    if (
        coonex_str_contains($page, 'uip') ||
        coonex_str_contains($page, 'xpress')
    ) {
        wp_die('Access restricted by Coonex', 'Restricted', ['response' => 403]);
    }
});
