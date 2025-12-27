<?php
/**
 * Plugin Name: Coonex Hide uiXpress
 */

defined('ABSPATH') || exit;

define('COONEX_XPRESS_PLUGIN', 'xpress/uixpress.php');

add_filter('all_plugins', function ($plugins) {

    if (function_exists('current_user_can') && current_user_can('coonex_internal_admin')) {
        return $plugins;
    }

    unset($plugins[COONEX_XPRESS_PLUGIN]);
    return $plugins;
});
