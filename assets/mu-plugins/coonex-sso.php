<?php
/**
 * Plugin Name: Coonex JWT SSO
 */

defined('ABSPATH') || exit;

add_action('init', function () {

    if (!isset($_GET['token'])) {
        return;
    }

    $secret = getenv('COONEX_SSO_SECRET');
    if (!$secret) {
        wp_die('SSO secret missing');
    }

    $jwt = $_GET['token'];
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        wp_die('Invalid token');
    }

    [$h, $p, $s] = $parts;

    $expected = rtrim(strtr(
        base64_encode(hash_hmac('sha256', "$h.$p", $secret, true)),
        '+/',
        '-_'
    ), '=');

    if (!hash_equals($expected, $s)) {
        wp_die('Invalid signature');
    }

    $data = json_decode(base64_decode(strtr($p, '-_', '+/')), true);
    if (!$data || empty($data['email'])) {
        wp_die('Invalid payload');
    }

    $user = get_user_by('email', $data['email']);
    if (!$user) {
        $uid = wp_create_user(
            $data['email'],
            wp_generate_password(),
            $data['email']
        );
        $user = get_user_by('id', $uid);
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    wp_redirect(admin_url());
    exit;
});
