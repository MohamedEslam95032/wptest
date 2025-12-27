<?php
/**
 * Plugin Name: Coonex uiXpress Auto Activator
 * Description: Activates uiXpress once after install
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_init', function () {

    // Run only once
    if (get_option('coonex_uixpress_done')) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    $plugin = 'xpress/uixpress.php';

    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin)) {
        if (!is_plugin_active($plugin)) {
            activate_plugin($plugin);
        }
    }

    update_option('coonex_uixpress_done', 1);
});
