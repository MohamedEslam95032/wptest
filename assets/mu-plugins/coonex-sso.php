<?php
/**
 * Plugin Name: Coonex JWT SSO (Safe)
 * Description: JWT SSO for Coonex - runs only on wp-login.php when token exists.
 */

if (!defined('ABSPATH')) exit;

function coonex_base64url_decode($data) {
    $data = strtr($data, '-_', '+/');
    $pad = strlen($data) % 4;
    if ($pad) $data .= str_repeat('=', 4 - $pad);
    return base64_decode($data);
}

function coonex_sso_handle() {
    if (getenv('COONEX_ENABLE_SSO') !== '1') {
        return;
    }

    // Only on wp-login.php
    $req = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($req, 'wp-login.php') === false) {
        return;
    }

    // Only if token exists
    if (empty($_GET['token'])) {
        return;
    }

    $jwt = trim((string)$_GET['token']);
    $secret = getenv('COONEX_SSO_SECRET');

    if (!$secret) {
        // keep it safe: show minimal error only on login page
        wp_die('SSO not configured');
    }

    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        wp_die('Invalid token');
    }

    [$header64, $payload64, $sig64] = $parts;

    $expected = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header64.$payload64", $secret, true)), '+/', '-_'), '=');
    if (!hash_equals($expected, $sig64)) {
        wp_die('Invalid token signature');
    }

    $payloadJson = coonex_base64url_decode($payload64);
    $data = json_decode($payloadJson, true);

    if (!is_array($data) || empty($data['email'])) {
        wp_die('Invalid token payload');
    }

    if (!empty($data['exp']) && (int)$data['exp'] < time()) {
        wp_die('Token expired');
    }

    $email = sanitize_email($data['email']);
    $name  = sanitize_text_field($data['name'] ?? 'Coonex User');
    $role  = sanitize_text_field($data['role'] ?? getenv('COONEX_DEFAULT_ROLE') ?: 'subscriber');

    $user = get_user_by('email', $email);

    if (!$user) {
        $username = sanitize_user(str_replace(['@', '.'], '_', $email));
        $user_id = wp_create_user($username, wp_generate_password(32), $email);

        if (is_wp_error($user_id)) {
            wp_die('User creation failed');
        }

        wp_update_user([
            'ID'           => $user_id,
            'display_name' => $name,
            'role'         => $role,
        ]);

        $user = get_user_by('ID', $user_id);
    }

    // Login
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);
    do_action('wp_login', $user->user_login, $user);

    wp_safe_redirect(admin_url());
    exit;
}

add_action('login_init', 'coonex_sso_handle');
