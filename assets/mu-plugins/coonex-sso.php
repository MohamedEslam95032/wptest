<?php
/**
 * Plugin Name: Coonex JWT SSO (MU)
 * Description: Core JWT-based SSO for Coonex.
 */

defined('ABSPATH') || exit;

add_action('init', function () {

    // Only handle wp-login.php
    if (!isset($GLOBALS['pagenow']) || $GLOBALS['pagenow'] !== 'wp-login.php') {
        return;
    }

    // Allow WordPress internal reauth / interim login
    if (isset($_GET['reauth']) || isset($_GET['interim-login'])) {
        return;
    }

    // No token? redirect safely
    if (empty($_GET['token'])) {
        wp_safe_redirect(home_url());
        exit;
    }

    $secret = getenv('COONEX_SSO_SECRET');
    if (!$secret) {
        wp_die('SSO secret not configured');
    }

    $jwt = trim($_GET['token']);
    $parts = explode('.', $jwt);

    if (count($parts) !== 3) {
        wp_safe_redirect(home_url());
        exit;
    }

    [$header, $payload, $signature] = $parts;

    $expected = rtrim(strtr(
        base64_encode(hash_hmac(
            'sha256',
            "$header.$payload",
            $secret,
            true
        )),
        '+/',
        '-_'
    ), '=');

    if (!hash_equals($expected, $signature)) {
        wp_safe_redirect(home_url());
        exit;
    }

    $data = json_decode(
        base64_decode(strtr($payload, '-_', '+/')),
        true
    );

    if (!is_array($data) || empty($data['email'])) {
        wp_safe_redirect(home_url());
        exit;
    }

    $email = sanitize_email($data['email']);
    $name  = sanitize_text_field($data['name'] ?? '');
    $role  = sanitize_text_field($data['role'] ?? 'subscriber');

    $user = get_user_by('email', $email);

    if (!$user) {
        $user_id = wp_create_user(
            $email,
            wp_generate_password(32),
            $email
        );
        $user = get_user_by('id', $user_id);

        if ($name) {
            wp_update_user([
                'ID'           => $user->ID,
                'display_name' => $name,
                'first_name'   => $name,
            ]);
        }
    }

    if ($role && !$user->has_cap($role)) {
        $user->set_role($role);
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    wp_safe_redirect(admin_url());
    exit;
});
