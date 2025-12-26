<?php
global $houzez_opt_name, $allowed_html_array;
Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__( 'Captcha Protection', 'houzez' ),
    'id'     => 'captcha-settings',
    'desc'   => '',
    'icon'   => 'el-icon-envelope el-icon-small',
    'fields'        => array(
        array(
            'id'       => 'captcha_provider',
            'type'     => 'select',
            'title'    => esc_html__( 'Captcha Provider', 'houzez' ),
            'desc'     => esc_html__( 'Choose which captcha service to use for form protection', 'houzez' ),
            'options'  => array(
                'none' => esc_html__( 'Disabled', 'houzez' ),
                'recaptcha' => esc_html__( 'Google reCaptcha', 'houzez' ),
                'turnstile' => esc_html__( 'Cloudflare Turnstile', 'houzez' )
            ),
            'default'  => 'none',
        ),
        array(
            'id' => 'captcha_info',
            'type' => 'info',
            'title' => esc_html__('Captcha Services', 'houzez'),
            'style' => 'info',
            'desc' => __('<p><strong>Google reCaptcha:</strong> Get keys at <a href="https://www.google.com/recaptcha/admin" target="_blank">google.com/recaptcha/admin</a></p>
                <p><strong>Cloudflare Turnstile:</strong> Get keys at <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Dashboard</a></p>', 'houzez'),
        ),

        // Rate Limiting
        array(
            'id'       => 'enable_rate_limiting',
            'type'     => 'switch',
            'title'    => esc_html__( 'Rate Limiting', 'houzez' ),
            'desc'     => esc_html__( 'Limit form submissions per IP address to prevent abuse', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Enabled', 'houzez' ),
            'off'      => esc_html__( 'Disabled', 'houzez' ),
        ),
        array(
            'id'       => 'rate_limit_attempts',
            'type'     => 'text',
            'title'    => esc_html__( 'Max Attempts', 'houzez' ),
            'desc'     => esc_html__( 'Maximum number of form submissions allowed', 'houzez' ),
            'default'  => '5',
            'required'  => array('enable_rate_limiting', '=', 1)
        ),
        array(
            'id'       => 'rate_limit_timeframe',
            'type'     => 'text',
            'title'    => esc_html__( 'Timeframe (minutes)', 'houzez' ),
            'desc'     => esc_html__( 'Time window for rate limiting in minutes', 'houzez' ),
            'default'  => '5',
            'required'  => array('enable_rate_limiting', '=', 1)
        ),

        // Google reCaptcha Section
        array(
            'id' => 'google_recaptcha_section',
            'type' => 'section',
            'title' => esc_html__('Google reCaptcha Settings', 'houzez'),
            'indent' => true,
            'required'  => array('captcha_provider', '=', 'recaptcha')
        ),
        array(
            'id'       => 'recaptha_type',
            'type'     => 'radio',
            'title'    => esc_html__( 'reCaptcha Version', 'houzez' ),
            'desc'     => esc_html__('Get new keys for V3 as V2 keys will not work!', 'houzez'),
            'options'  => array(
                'v2' => 'V2 (Checkbox)',
                'v3' => 'V3 (Invisible)'
            ),
            'default'  => 'v2',
            'required'  => array('captcha_provider', '=', 'recaptcha')
        ),

        array(
            'id'       => 'recaptcha_v3_threshold',
            'type'     => 'text',
            'title'    => esc_html__( 'V3 Score Threshold', 'houzez' ),
            'desc'     => esc_html__('Minimum score required (0.0 to 1.0). Lower scores indicate likely bots. Recommended: 0.5', 'houzez'),
            'default'  => '0.5',
            'required'  => array(
                array('captcha_provider', '=', 'recaptcha'),
                array('recaptha_type', '=', 'v3')
            )
        ),

        array(
            'id'       => 'recaptha_site_key',
            'type'     => 'text',
            'title'    => esc_html__( 'Site Key', 'houzez' ),
            'desc'     => esc_html__('Enter your Google reCaptcha site key.', 'houzez'),
            'default'  => '',
            'required'  => array('captcha_provider', '=', 'recaptcha')
        ),

        array(
            'id'       => 'recaptha_secret_key',
            'type'     => 'text',
            'title'    => esc_html__( 'Secret Key', 'houzez' ),
            'desc'     => esc_html__('Enter your Google reCaptcha Secret key.', 'houzez'),
            'default'  => '',
            'required'  => array('captcha_provider', '=', 'recaptcha')
        ),

        array(
            'id' => 'google_recaptcha_section_end',
            'type' => 'section',
            'indent' => false,
            'required'  => array('captcha_provider', '=', 'recaptcha')
        ),

        // Cloudflare Turnstile Section
        array(
            'id' => 'turnstile_section',
            'type' => 'section',
            'title' => esc_html__('Cloudflare Turnstile Settings', 'houzez'),
            'indent' => true,
            'required'  => array('captcha_provider', '=', 'turnstile')
        ),

        array(
            'id'       => 'turnstile_site_key',
            'type'     => 'text',
            'title'    => esc_html__( 'Turnstile Site Key', 'houzez' ),
            'desc'     => esc_html__('Enter your Cloudflare Turnstile site key.', 'houzez'),
            'default'  => '',
            'required'  => array('captcha_provider', '=', 'turnstile')
        ),

        array(
            'id'       => 'turnstile_secret_key',
            'type'     => 'text',
            'title'    => esc_html__( 'Turnstile Secret Key', 'houzez' ),
            'desc'     => esc_html__('Enter your Cloudflare Turnstile secret key.', 'houzez'),
            'default'  => '',
            'required'  => array('captcha_provider', '=', 'turnstile')
        ),

        array(
            'id'       => 'turnstile_theme',
            'type'     => 'select',
            'title'    => esc_html__( 'Turnstile Theme', 'houzez' ),
            'desc'     => esc_html__('Choose the appearance of Turnstile widget', 'houzez'),
            'options'  => array(
                'auto' => esc_html__( 'Auto (matches site)', 'houzez' ),
                'light' => esc_html__( 'Light', 'houzez' ),
                'dark' => esc_html__( 'Dark', 'houzez' )
            ),
            'default'  => 'auto',
            'required'  => array('captcha_provider', '=', 'turnstile')
        ),

        array(
            'id' => 'turnstile_section_end',
            'type' => 'section',
            'indent' => false,
            'required'  => array('captcha_provider', '=', 'turnstile')
        ),
    ),
));