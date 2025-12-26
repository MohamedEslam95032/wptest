<?php
/**
 * Plugin Name: Coonex JWT SSO
 * Description: Secure JWT-based SSO for Coonex (SAFE VERSION)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle Coonex SSO
 * Runs ONLY on wp-login.php
 */
function coonex_handle_sso() {

    // ✅ اشتغل بس على صفحة اللوجن
    if (!isset($_GET['token']) || !isset($_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], 'wp-login.php') === false) {
        return;
    }

    $jwt = trim($_GET['token']);
    $secret = getenv('COONEX_SSO_SECRET');

    if (!$secret) {
        wp_die('SSO secret not configured');
    }

    // ✅ JWT structure check
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        wp_die('Invalid token structure');
    }

    [$header, $payload, $signature] = $parts;

    // ✅ Verify signature
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
        wp_die('Invalid SSO signature');
    }

    // ✅ Decode payload
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    if (!$data || empty($data['email'])) {
        wp_die('Invalid SSO payload');
    }

    // ✅ Expiry check
    if (!empty($data['exp']) && $data['exp'] < time()) {
        wp_die('SSO token expired');
    }

    $email = sanitize_email($data['email']);
    $name  = sanitize_text_field($data['name'] ?? 'Coonex User');
    $role  = sanitize_text_field($data['role'] ?? 'subscriber');

    // ✅ Get or create user
    $user = get_user_by('email', $email);

    if (!$user) {
        $username = sanitize_user(str_replace('@', '_', $email));

        $user_id = wp_create_user(
            $username,
            wp_generate_password(32),
            $email
        );

        if (is_wp_error($user_id)) {
            wp_die('Failed to create user');
        }

        wp_update_user([
            'ID'           => $user_id,
            'display_name' => $name,
            'role'         => $role
        ]);

        $user = get_user_by('ID', $user_id);
    }

    // ✅ Login user
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);
    do_action('wp_login', $user->user_login, $user);

    // ✅ Redirect safely
    wp_safe_redirect(admin_url());
    exit;
}

// ✅ Hook صحيح
add_action('login_init', 'coonex_handle_sso');
