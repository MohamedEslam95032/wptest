<?php
/**
 * Plugin Name: Coonex JWT SSO (Safe)
 */

if (!defined('ABSPATH')) exit;

add_action('login_init', function () {

    if (getenv('COONEX_ENABLE_SSO') !== '1') return;
    if (empty($_GET['token'])) return;

    $jwt = trim($_GET['token']);
    $secret = getenv('COONEX_SSO_SECRET');
    if (!$secret) wp_die('SSO not configured');

    $parts = explode('.', $jwt);
    if (count($parts) !== 3) wp_die('Invalid token');

    [$h, $p, $s] = $parts;

    $expected = rtrim(strtr(
        base64_encode(hash_hmac('sha256', "$h.$p", $secret, true)),
        '+/', '-_'
    ), '=');

    if (!hash_equals($expected, $s)) wp_die('Invalid token');

    $data = json_decode(base64_decode(strtr($p, '-_', '+/')), true);
    if (!$data || empty($data['email'])) wp_die('Invalid payload');

    if (!empty($data['exp']) && $data['exp'] < time()) wp_die('Token expired');

    $email = sanitize_email($data['email']);
    $role  = sanitize_text_field($data['role'] ?? 'subscriber');
    $name  = sanitize_text_field($data['name'] ?? 'Coonex User');

    $user = get_user_by('email', $email);
    if (!$user) {
        $uid = wp_create_user(
            sanitize_user(str_replace('@', '_', $email)),
            wp_generate_password(32),
            $email
        );
        wp_update_user([
            'ID' => $uid,
            'display_name' => $name,
            'role' => $role
        ]);
        $user = get_user_by('ID', $uid);
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);
    wp_safe_redirect(admin_url());
    exit;
});
