<?php
/**
 * Plugin Name: Coonex uiXpress Activator
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_init', function () {

    if (get_option('coonex_uixpress_activated')) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    $plugin = 'xpress/uixpress.php';

    if (!is_plugin_active($plugin)) {
        activate_plugin($plugin);
    }

    update_option('coonex_uixpress_activated', 1);
});
