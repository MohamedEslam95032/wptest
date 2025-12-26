<?php
/**
 * Unified Captcha Functions
 *
 * Provides abstraction layer for multiple captcha providers:
 * - Google reCaptcha v2
 * - Google reCaptcha v3
 * - Cloudflare Turnstile
 *
 * @package Houzez
 * @since 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if any captcha provider is enabled
 *
 * @return bool
 */
function houzez_is_captcha_enabled() {
    $provider = houzez_option('captcha_provider', 'none');

    if ( $provider === 'none' ) {
        return false;
    }

    // Check if required keys are configured
    if ( $provider === 'recaptcha' ) {
        $site_key = houzez_option('recaptha_site_key');
        $secret_key = houzez_option('recaptha_secret_key');
        return !empty($site_key) && !empty($secret_key);
    }

    if ( $provider === 'turnstile' ) {
        $site_key = houzez_option('turnstile_site_key');
        $secret_key = houzez_option('turnstile_secret_key');
        return !empty($site_key) && !empty($secret_key);
    }

    return false;
}

/**
 * Get current captcha provider
 *
 * @return string 'recaptcha', 'turnstile', or 'none'
 */
function houzez_get_captcha_provider() {
    // Backward compatibility: if old reCaptcha is enabled, use it
    $legacy_enabled = houzez_option('enable_reCaptcha');
    if ( $legacy_enabled == 1 ) {
        return 'recaptcha';
    }

    return houzez_option('captcha_provider', 'none');
}

/**
 * Legacy function for backward compatibility
 * Maps to new unified function
 *
 * @return bool
 */
function houzez_show_google_reCaptcha() {
    $provider = houzez_get_captcha_provider();
    return ( $provider === 'recaptcha' && houzez_is_captcha_enabled() );
}

/**
 * Check if Cloudflare Turnstile is enabled
 *
 * @return bool
 */
function houzez_is_turnstile_enabled() {
    $provider = houzez_get_captcha_provider();
    return ( $provider === 'turnstile' && houzez_is_captcha_enabled() );
}

/**
 * Get captcha site key for current provider
 *
 * @return string
 */
function houzez_get_captcha_site_key() {
    $provider = houzez_get_captcha_provider();

    if ( $provider === 'recaptcha' ) {
        return houzez_option('recaptha_site_key', '');
    }

    if ( $provider === 'turnstile' ) {
        return houzez_option('turnstile_site_key', '');
    }

    return '';
}

/**
 * Get captcha secret key for current provider
 *
 * @return string
 */
function houzez_get_captcha_secret_key() {
    $provider = houzez_get_captcha_provider();

    if ( $provider === 'recaptcha' ) {
        return houzez_option('recaptha_secret_key', '');
    }

    if ( $provider === 'turnstile' ) {
        return houzez_option('turnstile_secret_key', '');
    }

    return '';
}

/**
 * Get user's real IP address
 * Handles proxies, load balancers, and CDNs
 *
 * @return string Sanitized IP address
 */
function houzez_get_user_ip() {
    $ip_keys = array(
        'HTTP_CF_CONNECTING_IP', // CloudFlare
        'HTTP_X_REAL_IP',        // Nginx proxy
        'HTTP_X_FORWARDED_FOR',  // Most proxies
        'HTTP_CLIENT_IP',        // Proxy servers
        'REMOTE_ADDR'            // Direct connection (fallback)
    );

    foreach ( $ip_keys as $key ) {
        if ( array_key_exists( $key, $_SERVER ) === true ) {
            $ip_list = explode( ',', $_SERVER[ $key ] );

            foreach ( $ip_list as $ip ) {
                $ip = trim( $ip );

                // Validate IP address
                if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                    return $ip;
                }
            }
        }
    }

    // Fallback to REMOTE_ADDR if no valid public IP found
    return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '0.0.0.0';
}

/**
 * Unified captcha validation
 * Routes to appropriate provider validation
 *
 * @return bool|void True on success, exits with JSON error on failure
 */
function houzez_validate_captcha() {
    if ( ! houzez_is_captcha_enabled() ) {
        return true;
    }

    $provider = houzez_get_captcha_provider();

    // Route to appropriate provider
    if ( $provider === 'recaptcha' ) {
        return houzez_validate_recaptcha();
    }

    if ( $provider === 'turnstile' ) {
        return houzez_validate_turnstile();
    }

    return true;
}

/**
 * Validate Google reCaptcha (v2 or v3)
 *
 * @return bool|void True on success, exits with JSON error on failure
 */
function houzez_validate_recaptcha() {
    $site_key = houzez_option('recaptha_site_key');
    $secret_key = houzez_option('recaptha_secret_key');

    if ( empty( $site_key ) || empty( $secret_key ) ) {
        return true;
    }

    // Check if reCaptcha response exists
    if ( ! isset( $_POST['g-recaptcha-response'] ) || empty( $_POST['g-recaptcha-response'] ) ) {
        echo json_encode( array(
            'success' => false,
            'msg' => esc_html__( 'Please complete the captcha verification.', 'houzez' )
        ) );
        wp_die();
    }

    // Load Google reCaptcha library
    $recaptcha_lib = HOUZEZ_PLUGIN_DIR . 'includes/reCaptcha/autoload.php';
    if ( ! file_exists( $recaptcha_lib ) ) {
        // Log error but don't block submission if library missing
        error_log( 'Houzez: reCaptcha library not found at ' . $recaptcha_lib );
        return true;
    }

    require_once( $recaptcha_lib );

    try {
        // Create reCaptcha instance
        $recaptcha = new \ReCaptcha\ReCaptcha( $secret_key, new \ReCaptcha\RequestMethod\CurlPost() );

        // Get user IP and response
        $user_ip = houzez_get_user_ip();
        $recaptcha_response = sanitize_text_field( $_POST['g-recaptcha-response'] );

        // Verify with Google
        $resp = $recaptcha->verify( $recaptcha_response, $user_ip );

        if ( $resp->isSuccess() ) {
            // For v3, check score threshold
            $recaptcha_type = houzez_option('recaptha_type', 'v2');
            if ( $recaptcha_type === 'v3' ) {
                $score = $resp->getScore();
                $threshold = houzez_option('recaptcha_v3_threshold', 0.5);

                if ( $score < $threshold ) {
                    // Log low score attempts
                    do_action( 'houzez_recaptcha_low_score', $score, $user_ip );

                    echo json_encode( array(
                        'success' => false,
                        'msg' => esc_html__( 'Captcha verification failed. Please try again.', 'houzez' )
                    ) );
                    wp_die();
                }
            }

            // Success
            do_action( 'houzez_recaptcha_success', $user_ip );
            return true;

        } else {
            // Get error codes
            $error_codes = $resp->getErrorCodes();
            $error_message = houzez_get_recaptcha_error_message( $error_codes );

            // Log failed attempt
            do_action( 'houzez_recaptcha_failed', $error_codes, $user_ip );

            echo json_encode( array(
                'success' => false,
                'msg' => esc_html__( 'Captcha verification failed:', 'houzez' ) . ' ' . $error_message
            ) );
            wp_die();
        }

    } catch ( Exception $e ) {
        // Log exception but don't block submission
        error_log( 'Houzez reCaptcha Exception: ' . $e->getMessage() );
        return true;
    }
}

/**
 * Validate Cloudflare Turnstile
 *
 * @return bool|void True on success, exits with JSON error on failure
 */
function houzez_validate_turnstile() {
    $secret_key = houzez_option('turnstile_secret_key');

    if ( empty( $secret_key ) ) {
        return true;
    }

    // Check if Turnstile response exists
    if ( ! isset( $_POST['cf-turnstile-response'] ) || empty( $_POST['cf-turnstile-response'] ) ) {
        echo json_encode( array(
            'success' => false,
            'msg' => esc_html__( 'Please complete the captcha verification.', 'houzez' )
        ) );
        wp_die();
    }

    // Prepare validation request
    $token = sanitize_text_field( $_POST['cf-turnstile-response'] );
    $user_ip = houzez_get_user_ip();

    $response = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
        'body' => array(
            'secret'   => $secret_key,
            'response' => $token,
            'remoteip' => $user_ip
        ),
        'timeout' => 15,
        'sslverify' => true
    ) );

    // Check for request errors
    if ( is_wp_error( $response ) ) {
        error_log( 'Houzez Turnstile API Error: ' . $response->get_error_message() );
        echo json_encode( array(
            'success' => false,
            'msg' => esc_html__( 'Captcha verification failed. Please try again.', 'houzez' )
        ) );
        wp_die();
    }

    // Parse response
    $body = wp_remote_retrieve_body( $response );
    $result = json_decode( $body, true );

    if ( isset( $result['success'] ) && $result['success'] === true ) {
        // Success
        do_action( 'houzez_turnstile_success', $user_ip );
        return true;

    } else {
        // Get error codes
        $error_codes = isset( $result['error-codes'] ) ? $result['error-codes'] : array( 'unknown-error' );
        $error_message = houzez_get_turnstile_error_message( $error_codes );

        // Log failed attempt
        do_action( 'houzez_turnstile_failed', $error_codes, $user_ip );

        echo json_encode( array(
            'success' => false,
            'msg' => esc_html__( 'Captcha verification failed:', 'houzez' ) . ' ' . $error_message
        ) );
        wp_die();
    }
}

/**
 * Get user-friendly error message for reCaptcha error codes
 *
 * @param array $error_codes
 * @return string
 */
function houzez_get_recaptcha_error_message( $error_codes ) {
    if ( empty( $error_codes ) ) {
        return esc_html__( 'Unknown error', 'houzez' );
    }

    $error_code = $error_codes[0];

    $messages = array(
        'missing-input-secret'   => esc_html__( 'Configuration error', 'houzez' ),
        'invalid-input-secret'   => esc_html__( 'Configuration error', 'houzez' ),
        'missing-input-response' => esc_html__( 'Please complete the verification', 'houzez' ),
        'invalid-input-response' => esc_html__( 'Verification expired, please try again', 'houzez' ),
        'bad-request'            => esc_html__( 'Invalid request', 'houzez' ),
        'timeout-or-duplicate'   => esc_html__( 'Verification expired, please try again', 'houzez' ),
    );

    return isset( $messages[ $error_code ] ) ? $messages[ $error_code ] : esc_html__( 'Verification failed', 'houzez' );
}

/**
 * Get user-friendly error message for Turnstile error codes
 *
 * @param array $error_codes
 * @return string
 */
function houzez_get_turnstile_error_message( $error_codes ) {
    if ( empty( $error_codes ) ) {
        return esc_html__( 'Unknown error', 'houzez' );
    }

    $error_code = $error_codes[0];

    $messages = array(
        'missing-input-secret'   => esc_html__( 'Configuration error', 'houzez' ),
        'invalid-input-secret'   => esc_html__( 'Configuration error', 'houzez' ),
        'missing-input-response' => esc_html__( 'Please complete the verification', 'houzez' ),
        'invalid-input-response' => esc_html__( 'Verification expired, please try again', 'houzez' ),
        'bad-request'            => esc_html__( 'Invalid request', 'houzez' ),
        'timeout-or-duplicate'   => esc_html__( 'Verification expired, please try again', 'houzez' ),
        'internal-error'         => esc_html__( 'Verification service error', 'houzez' ),
    );

    return isset( $messages[ $error_code ] ) ? $messages[ $error_code ] : esc_html__( 'Verification failed', 'houzez' );
}

/**
 * Check rate limiting for form submissions
 *
 * @param string $action Action identifier (e.g., 'contact_form', 'login')
 * @param string $identifier Optional identifier for per-item tracking (e.g., property_id, agent_id)
 * @return bool|void True if allowed, exits with JSON error if rate limited
 */
function houzez_check_rate_limit( $action, $identifier = '' ) {
    // Skip if rate limiting is disabled
    if ( ! houzez_option('enable_rate_limiting', 1) ) {
        return true;
    }

    // Get settings from admin options
    $limit = intval( houzez_option('rate_limit_attempts', 5) );
    $timeframe = intval( houzez_option('rate_limit_timeframe', 5) ) * 60; // convert minutes to seconds

    $user_ip = houzez_get_user_ip();
    $user_id = get_current_user_id();

    // Create unique key for this user/IP and action
    // Include identifier if provided (for per-property/per-agent tracking)
    $key_base = $action . ( $identifier ? '_' . $identifier : '' );
    $transient_key = 'houzez_rate_' . $key_base . '_' . md5( $user_ip . $user_id );

    // Get current attempt count
    $attempts = get_transient( $transient_key );

    if ( $attempts === false ) {
        // First attempt, set counter
        set_transient( $transient_key, 1, $timeframe );
        return true;
    }

    if ( $attempts >= $limit ) {
        // Rate limit exceeded
        $wait_time = ceil( $timeframe / 60 );

        do_action( 'houzez_rate_limit_exceeded', $action, $user_ip, $user_id, $attempts );

        echo json_encode( array(
            'success' => false,
            'msg' => sprintf(
                esc_html__( 'Too many attempts. Please wait %d minutes before trying again.', 'houzez' ),
                $wait_time
            )
        ) );
        wp_die();
    }

    // Increment counter
    set_transient( $transient_key, $attempts + 1, $timeframe );
    return true;
}

/**
 * Generate captcha widget HTML/JavaScript initialization
 * Called in footer via wp_footer hook
 */
function houzez_render_captcha_init() {
    if ( ! houzez_is_captcha_enabled() ) {
        return;
    }

    $provider = houzez_get_captcha_provider();

    if ( $provider === 'recaptcha' ) {
        houzez_render_recaptcha_init();
    } elseif ( $provider === 'turnstile' ) {
        houzez_render_turnstile_init();
    }
}

/**
 * Generate reCaptcha initialization JavaScript
 */
function houzez_render_recaptcha_init() {
    $site_key = houzez_option('recaptha_site_key');
    $recaptcha_type = houzez_option('recaptha_type', 'v2');
    ?>
    <script type="text/javascript">
    var reCaptchaIDs = [];
    var houzezReCaptchaType = '<?php echo esc_js( $recaptcha_type ); ?>';
    var houzezReCaptchaSiteKey = '<?php echo esc_js( $site_key ); ?>';

    function houzezReCaptchaLoad() {
        if (typeof grecaptcha === 'undefined') {
            return;
        }

        var reCaptchaContainers = document.querySelectorAll('.houzez_google_reCaptcha');

        reCaptchaContainers.forEach(function(el) {
            if (el.innerHTML.trim() !== '') {
                return; // Already initialized
            }

            if (houzezReCaptchaType === 'v3') {
                // reCaptcha v3 - invisible
                grecaptcha.ready(function() {
                    grecaptcha.execute(houzezReCaptchaSiteKey, {action: 'submit'}).then(function(token) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.className = 'g-recaptcha-response';
                        input.name = 'g-recaptcha-response';
                        input.value = token;
                        el.appendChild(input);
                    });
                });
            } else {
                // reCaptcha v2 - checkbox
                var widgetId = grecaptcha.render(el, {
                    'sitekey': houzezReCaptchaSiteKey
                });
                reCaptchaIDs.push(widgetId);
            }
        });
    }

    function houzezReCaptchaReset() {
        if (typeof grecaptcha === 'undefined') {
            return;
        }

        if (houzezReCaptchaType === 'v3') {
            // For v3, regenerate token
            houzezReCaptchaLoad();
        } else {
            // For v2, reset all widgets
            reCaptchaIDs.forEach(function(widgetId) {
                grecaptcha.reset(widgetId);
            });
        }
    }
    </script>
    <?php
}

/**
 * Generate Turnstile initialization JavaScript
 */
function houzez_render_turnstile_init() {
    $site_key = houzez_option('turnstile_site_key');
    $theme = houzez_option('turnstile_theme', 'auto');
    ?>
    <script type="text/javascript">
    var houzezTurnstileSiteKey = '<?php echo esc_js( $site_key ); ?>';
    var houzezTurnstileTheme = '<?php echo esc_js( $theme ); ?>';

    function houzezTurnstileLoad() {
        if (typeof turnstile === 'undefined') {
            return;
        }

        var turnstileContainers = document.querySelectorAll('.houzez-turnstile');

        turnstileContainers.forEach(function(el) {
            if (el.innerHTML.trim() !== '') {
                return; // Already initialized
            }

            turnstile.render(el, {
                sitekey: houzezTurnstileSiteKey,
                theme: houzezTurnstileTheme,
                callback: function(token) {
                    // Token automatically added to form
                }
            });
        });
    }

    function houzezTurnstileReset() {
        if (typeof turnstile === 'undefined') {
            return;
        }

        var turnstileContainers = document.querySelectorAll('.houzez-turnstile');
        turnstileContainers.forEach(function(el) {
            turnstile.reset(el);
        });
    }

    // Auto-initialize when Turnstile script is ready
    function initTurnstileWhenReady() {
        if (typeof turnstile !== 'undefined') {
            houzezTurnstileLoad();
        } else {
            // Wait for Turnstile script to load
            setTimeout(initTurnstileWhenReady, 100);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTurnstileWhenReady);
    } else {
        initTurnstileWhenReady();
    }
    </script>
    <?php
}

// Hook initialization function to footer
add_action( 'wp_footer', 'houzez_render_captcha_init', 99 );
