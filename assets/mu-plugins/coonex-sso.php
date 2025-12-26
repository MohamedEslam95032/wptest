<?php
/**
 * Plugin Name: Coonex SSHO (JWT SSO)
 * Description: Mandatory JWT-based SSO for Coonex CMS (MU Plugin)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Force SSO – block normal wp-login
 */
add_action('login_init', function () {
    if (!isset($_GET['token'])) {
        wp_die('Login via Coonex only');
    }
});

/**
 * Handle JWT SSO
 */
add_action('init', function () {

    if (!isset($_GET['token'])) {
        return;
    }

    $jwt = trim($_GET['token']);
    $secret = getenv('COONEX_SSO_SECRET');

    if (!$secret) {
        wp_die('SSO secret not configured');
    }

    // Validate JWT structure
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        wp_die('Invalid SSO token');
    }

    [$header, $payload, $signature] = $parts;

    // Verify signature
    $expected = rtrim(strtr(
        base64_encode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        ),
        '+/',
        '-_'
    ), '=');

    if (!hash_equals($expected, $signature)) {
        wp_die('Invalid SSO signature');
    }

    // Decode payload
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    if (!$data || empty($data['email'])) {
        wp_die('Invalid SSO payload');
    }

    $email = sanitize_email($data['email']);
    $name  = sanitize_text_field($data['name'] ?? 'Coonex User');
    $role  = sanitize_text_field($data['role'] ?? 'subscriber');

    // Get or create user
    $user = get_user_by('email', $email);

    if (!$user) {
        $user_id = wp_create_user(
            $email,
            wp_generate_password(),
            $email
        );

        wp_update_user([
            'ID'           => $user_id,
            'display_name' => $name,
            'role'         => $role,
        ]);

        $user = get_user_by('id', $user_id);
    }

    // Login user
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    // Redirect after login
    wp_redirect(admin_url());
    exit;
});
