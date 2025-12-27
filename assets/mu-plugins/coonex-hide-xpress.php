<?php
/**
 * Plugin Name: Coonex Hide uiXpress (SAFE)
 * Description: Hides uiXpress from Plugins list only (no menu, no blocks, no crashes).
 * Author: Coonex
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Hide uiXpress from Plugins list
 * SAFE: does NOT affect dashboard, routes, menus, or assets
 */
add_filter('all_plugins', function ($plugins) {
    if (isset($plugins['xpress/uixpress.php'])) {
        unset($plugins['xpress/uixpress.php']);
    }
    return $plugins;
});
