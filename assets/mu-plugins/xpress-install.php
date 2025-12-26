<?php
/**
 * Plugin Name: Coonex Xpress Installer (Activate Only)
 * Description: Activates uiXpress plugin once. No hiding, no blocking, no self-heal.
 */

defined('ABSPATH') || exit;

// Emergency kill switch
if (getenv('COONEX_DISABLE_XPRESS_INSTALL') === '1') {
    return;
}

add_action('admin_init', function () {

    // IMPORTANT: adjust this ONLY if your path differs
    $plugin = 'xpress/uixpress.php';

    // Fail-safe: if not present, do nothing
    if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin)) {
        return;
    }

    // Load plugin functions
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    // Activate once
    if (!is_plugin_active($plugin)) {
        activate_plugin($plugin);
    }
});
