<?php
/**
 * Plugin Name: Coonex Admin Lockdown
 */

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {

    if (getenv('COONEX_LOCKDOWN_ADMIN') !== '1') return;
    if (!is_user_logged_in() || current_user_can('administrator')) return;

    remove_menu_page('theme-editor.php');
    remove_menu_page('plugin-editor.php');
    remove_menu_page('tools.php');
    remove_menu_page('profile.php');
}, 999);
