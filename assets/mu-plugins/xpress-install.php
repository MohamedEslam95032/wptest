<?php
/**
 * Plugin Name: Coonex uiXpress Enforcer
 */

defined('ABSPATH') || exit;

/**
 * Emergency kill switch
 */
if (getenv('COONEX_DISABLE_UIXPRESS_ENFORCER') === '1') {
    return;
}

define('COONEX_UIP_PLUGIN', 'xpress/uixpress.php');
define('COONEX_UIP_FLAG', 'coonex_uipress_enforced');

function coonex_is_internal_admin() {
    return function_exists('current_user_can') && current_user_can('coonex_internal_admin');
}

/**
 * Auto activate uiXpress once
 */
add_action('admin_init', function () {

    if (coonex_is_internal_admin()) return;
    if (get_option(COONEX_UIP_FLAG)) return;

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    if (file_exists(WP_PLUGIN_DIR . '/' . COONEX_UIP_PLUGIN)) {
        if (!is_plugin_active(COONEX_UIP_PLUGIN)) {
            activate_plugin(COONEX_UIP_PLUGIN);
        }
        update_option(COONEX_UIP_FLAG, 1);
    }
});

/**
 * Self heal (safe)
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
 * Hide uiXpress from plugin list (clients)
 */
add_filter('all_plugins', function ($plugins) {

    if (coonex_is_internal_admin()) return $plugins;

    unset($plugins[COONEX_UIP_PLUGIN]);
    return $plugins;
});
