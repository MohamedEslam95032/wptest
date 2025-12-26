<?php
/**
 * Plugin Name: Coonex uiXpress Enforcer
 * Description: Enforces uiXpress as locked admin UI for client tenants (safe, role-aware, recoverable).
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * ==================================================
 * EMERGENCY KILL SWITCH (ENV)
 * Set COONEX_DISABLE_UIXPRESS_ENFORCER=1 to disable
 * ==================================================
 */
if (getenv('COONEX_DISABLE_UIXPRESS_ENFORCER') === '1') {
    return;
}

/**
 * ==================================================
 * CONFIG
 * ==================================================
 */
define('COONEX_UIP_PLUGIN', 'xpress/uixpress.php');
define('COONEX_UIP_FLAG', 'coonex_uipress_enforced');

/**
 * Optional internal role bypass
 * (Coonex team only – clients never get this role)
 */
function coonex_is_internal_admin() {
    return current_user_can('coonex_internal_admin');
}

/**
 * ==================================================
 * 1️⃣ Auto-activate uiXpress ONCE
 * ==================================================
 */
add_action('admin_init', function () {

    if (coonex_is_internal_admin()) {
        return;
    }

    if (get_option(COONEX_UIP_FLAG)) {
        return;
    }

    $plugin_path = WP_PLUGIN_DIR . '/' . COONEX_UIP_PLUGIN;
    if (!file_exists($plugin_path)) {
        return; // fail-safe
    }

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    if (!is_plugin_active(COONEX_UIP_PLUGIN)) {
        activate_plugin(COONEX_UIP_PLUGIN);
    }

    update_option(COONEX_UIP_FLAG, 1);
});

/**
 * ==================================================
 * 2️⃣ Self-healing (SAFE)
 * Re-activate only if previously enforced
 * ==================================================
 */
add_action('admin_init', function () {

    if (coonex_is_internal_admin()) {
        return;
    }

    if (!get_option(COONEX_UIP_FLAG)) {
        return;
    }

    $plugin_path = WP_PLUGIN_DIR . '/' . COONEX_UIP_PLUGIN;
    if (!file_exists($plugin_path)) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    if (!is_plugin_active(COONEX_UIP_PLUGIN)) {
        activate_plugin(COONEX_UIP_PLUGIN);
    }
});

/**
 * ==================================================
 * 3️⃣ Hide uiXpress from Plugins list (clients only)
 * ==================================================
 */
add_filter('all_plugins', function ($plugins) {

    if (coonex_is_internal_admin()) {
        return $plugins;
    }

    unset($plugins[COONEX_UIP_PLUGIN]);
    return $plugins;
});

/**
 * ==================================================
 * 4️⃣ Remove deactivate / delete actions
 * ==================================================
 */
add_filter('plugin_action_links', function ($actions, $plugin_file) {

    if ($plugin_file === COONEX_UIP_PLUGIN && !coonex_is_internal_admin()) {
        unset($actions['deactivate'], $actions['delete']);
    }

    return $actions;
}, 10, 2);

/**
 * ==================================================
 * 5️⃣ Block uiXpress admin pages (URL access)
 * ==================================================
 */
add_action('admin_init', function () {

    if (coonex_is_internal_admin()) {
        return;
    }

    if (!isset($_GET['page'])) {
        return;
    }

    $page = sanitize_key($_GET['page']);

    if (str_contains($page, 'uip') || str_contains($page, 'xpress')) {
        wp_die(
            __('Access restricted by Coonex.', 'coonex'),
            __('Restricted', 'coonex'),
            ['response' => 403]
        );
    }
});

/**
 * ==================================================
 * 6️⃣ Hide uiXpress menu entries (UI only)
 * ==================================================
 */
add_action('admin_menu', function () {

    if (coonex_is_internal_admin()) {
        return;
    }

    global $menu, $submenu;

    foreach ((array) $menu as $key => $item) {
        if (!empty($item[2]) && str_contains($item[2], 'uip')) {
            unset($menu[$key]);
        }
    }

    foreach ((array) $submenu as $parent => $items) {
        foreach ((array) $items as $index => $sub) {
            if (!empty($sub[2]) && str_contains($sub[2], 'uip')) {
                unset($submenu[$parent][$index]);
            }
        }
    }

}, 9999);
