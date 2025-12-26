<?php
/**
 * Plugin Name: Coonex Xpress Flag
 * Description: Marks uiXpress for safe activation.
 */

defined('ABSPATH') || exit;

if (getenv('COONEX_DISABLE_XPRESS_FLAG') === '1') {
    return;
}

define('COONEX_XPRESS_PLUGIN', 'xpress/uixpress.php');
define('COONEX_XPRESS_FLAG', 'coonex_xpress_pending');

/**
 * Mark activation needed (once)
 */
add_action('init', function () {

    if (get_option(COONEX_XPRESS_FLAG)) {
        return;
    }

    if (file_exists(WP_PLUGIN_DIR . '/' . COONEX_XPRESS_PLUGIN)) {
        update_option(COONEX_XPRESS_FLAG, 1);
    }
});
