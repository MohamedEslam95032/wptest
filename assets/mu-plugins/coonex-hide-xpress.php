<?php
/**
 * Plugin Name: Coonex Hide uiXpress
 * Description: Hides uiXpress from Plugins list and Admin Bar (no activation, no blocking).
 */

defined('ABSPATH') || exit;

/**
 * Optional internal admin bypass
 */
function coonex_is_internal_admin() {
    return function_exists('current_user_can') && current_user_can('coonex_internal_admin');
}

/**
 * Plugin main file (SAFE)
 */
if (!defined('COONEX_XPRESS_PLUGIN')) {
    define('COONEX_XPRESS_PLUGIN', 'xpress/uixpress.php');
}

/**
 * 1️⃣ Hide uiXpress from Plugins list
 */
add_filter('all_plugins', function ($plugins) {

    if (coonex_is_internal_admin()) {
        return $plugins;
    }

    unset($plugins[COONEX_XPRESS_PLUGIN]);

    return $plugins;
});

/**
 * 2️⃣ Remove uiXpress from Admin Bar
 */
add_action('admin_bar_menu', function ($wp_admin_bar) {

    if (coonex_is_internal_admin()) {
        return;
    }

    foreach ($wp_admin_bar->get_nodes() as $node) {
        if (
            isset($node->id) &&
            (strpos($node->id, 'uip') !== false || strpos($node->id, 'xpress') !== false)
        ) {
            $wp_admin_bar->remove_node($node->id);
        }
    }

}, 999);

/**
 * 3️⃣ Hide uiXpress menu from sidebar
 */
add_action('admin_menu', function () {

    if (coonex_is_internal_admin()) {
        return;
    }

    global $menu;

    foreach ((array) $menu as $key => $item) {
        if (
            isset($item[2]) &&
            (strpos($item[2], 'uip') !== false || strpos($item[2], 'xpress') !== false)
        ) {
            unset($menu[$key]);
        }
    }

}, 999);
