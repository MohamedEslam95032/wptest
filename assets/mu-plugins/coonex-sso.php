<?php
/**
 * Plugin Name: Coonex JWT SSO (MU)
 * Description: Core JWT-based SSO for Coonex (MU Plugin).
 */

defined('ABSPATH') || exit;

/**
 * Main SSO handler
 */
add_action('init', function () {

    // Only trigger when token exists
    if (empty($_GET['token'])) {
        return;
    }

    $secret = getenv('COONEX_SSO_SECRET');
    if (!$secret) {
        wp_die('SSO secret not configured');
    }

    $jwt = trim($_GET['token']);
    $parts = explode('.', $jwt);

    if (count($parts) !== 3) {
        wp_die('Invalid SSO token');
    }

    [$header, $payload, $signature] = $parts;

    // Validate signature
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

    $data = json_decode(
        base64_decode(strtr($payload, '-_', '+/')),
        true
    );

    if (!is_array($data) || empty($data['email'])) {
        wp_die('Invalid SSO payload');
    }

    $email = sanitize_email($data['email']);
    $name  = sanitize_text_field($data['name'] ?? '');
    $role  = sanitize_text_field($data['role'] ?? 'subscriber');

    // Get or create user
    $user = get_user_by('email', $email);

    if (!$user) {
        $user_id = wp_create_user(
            $email,
            wp_generate_password(32),
            $email
        );

        if (is_wp_error($user_id)) {
            wp_die('Failed to create user');
        }

        $user = get_user_by('id', $user_id);

        if ($name) {
            wp_update_user([
                'ID'           => $user->ID,
                'display_name' => $name,
                'first_name'   => $name,
            ]);
        }
    }

    // Enforce role (optional – controlled by token)
    if ($role && $user->role !== $role) {
        $user->set_role($role);
    }

    // Login user
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    // Redirect
    wp_safe_redirect(admin_url());
    exit;
});
