<?php
/**
 * Plugin Name: Coonex Admin Lockdown (Safe)
 * Description: Hide risky admin areas for non-admins without breaking the site.
 */

if (!defined('ABSPATH')) exit;

function coonex_lockdown_enabled(): bool {
    return (getenv('COONEX_LOCKDOWN_ADMIN') === '1');
}

function coonex_is_admin_user(): bool {
    return is_user_logged_in() && current_user_can('administrator');
}

add_action('admin_menu', function () {
    if (!coonex_lockdown_enabled()) return;
    if (coonex_is_admin_user()) return;

    // Hide risky menus
    remove_menu_page('theme-editor.php');
    remove_menu_page('plugin-editor.php');
    remove_menu_page('tools.php');
    remove_menu_page('profile.php');

    // Hide submenus
    remove_submenu_page('themes.php', 'theme-editor.php');
    remove_submenu_page('plugins.php', 'plugin-editor.php');
}, 999);

add_action('admin_init', function () {
    if (!coonex_lockdown_enabled()) return;
    if (coonex_is_admin_user()) return;

    // Block direct editor access safely (redirect instead of wp_die)
    $pagenow = $GLOBALS['pagenow'] ?? '';
    $blocked = ['plugin-editor.php', 'theme-editor.php', 'profile.php', 'user-edit.php'];

    if (in_array($pagenow, $blocked, true)) {
        wp_safe_redirect(admin_url());
        exit;
    }
});
