<?php
/**
 * Plugin Name: Coonex uiXpress Installer (MU)
 * Description: Ensures uiXpress plugin is installed & activated safely (no UI, no conflicts).
 */

defined('ABSPATH') || exit;

/**
 * --------------------------------------------------
 * Constants (SAFE DEFINE)
 * --------------------------------------------------
 */
if (!defined('COONEX_XPRESS_PLUGIN')) {
    define('COONEX_XPRESS_PLUGIN', 'xpress/uixpress.php');
}

if (!defined('COONEX_XPRESS_FLAG')) {
    define('COONEX_XPRESS_FLAG', 'coonex_xpress_installed');
}

/**
 * --------------------------------------------------
 * Helper: check if plugin exists
 * --------------------------------------------------
 */
function coonex_xpress_plugin_exists() {
    return file_exists(WP_PLUGIN_DIR . '/' . COONEX_XPRESS_PLUGIN);
}

/**
 * --------------------------------------------------
 * Helper: activate plugin safely
 * --------------------------------------------------
 */
function coonex_activate_xpress_plugin() {
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if (!is_plugin_active(COONEX_XPRESS_PLUGIN)) {
        activate_plugin(COONEX_XPRESS_PLUGIN, '', false, true);
    }
}

/**
 * --------------------------------------------------
 * MAIN LOGIC
 * Runs once, idempotent, safe with MU plugins
 * --------------------------------------------------
 */
add_action('admin_init', function () {

    // Already done before → exit silently
    if (get_option(COONEX_XPRESS_FLAG)) {
        return;
    }

    // Plugin files not present → do nothing
    if (!coonex_xpress_plugin_exists()) {
        return;
    }

    // Activate plugin
    coonex_activate_xpress_plugin();

    // Mark as done (VERY IMPORTANT)
    update_option(COONEX_XPRESS_FLAG, 1);
});
