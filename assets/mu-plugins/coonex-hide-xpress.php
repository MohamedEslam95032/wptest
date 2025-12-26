<?php
/**
 * Plugin Name: Coonex Admin UI Restrictions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hide dangerous admin menus for non-admins
 */
function coonex_hide_admin_menus() {

    if (!current_user_can('administrator')) {

        remove_menu_page('theme-editor.php');
        remove_menu_page('plugin-editor.php');
        remove_menu_page('tools.php');
        remove_menu_page('profile.php');

        remove_submenu_page('themes.php', 'theme-editor.php');
        remove_submenu_page('plugins.php', 'plugin-editor.php');
    }
}
add_action('admin_menu', 'coonex_hide_admin_menus', 999);

/**
 * Block direct access to profile edit
 */
function coonex_block_profile_access() {
    if (!current_user_can('administrator') && isset($_GET['profile'])) {
        wp_die('Access denied');
    }
}
add_action('admin_init', 'coonex_block_profile_access');
