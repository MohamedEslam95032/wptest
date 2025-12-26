<?php
/**
 * Plugin Name: Coonex Xpress Installer (Safe Stub)
 * Description: Optional helper for uiXpress setup. Does nothing unless enabled.
 */

if (!defined('ABSPATH')) exit;

add_action('admin_init', function () {

    if (getenv('COONEX_ENABLE_XPRESS_INSTALL') !== '1') {
        return;
    }

    if (!current_user_can('administrator')) {
        return;
    }

    // Only run on a deliberate admin page to avoid random changes
    if (empty($_GET['coonex_xpress_setup'])) {
        return;
    }

    // Example: ensure plugin is active (only if installed)
    if (function_exists('is_plugin_active')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin = 'xpress/uixpress.php';

    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin)) {
        if (!is_plugin_active($plugin)) {
            activate_plugin($plugin);
        }
        wp_safe_redirect(admin_url('admin.php?page=uixpress'));
        exit;
    } else {
        wp_safe_redirect(admin_url());
        exit;
    }
});
