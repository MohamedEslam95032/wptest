<?php
if( ! function_exists( 'houzez_template_header' ) ) {

	function houzez_template_header() {
		get_template_part('template-parts/header/main');
	}
}

if( ! function_exists( 'houzez_template_header_mobile' ) ) {
	function houzez_template_header_mobile() {
		get_template_part('template-parts/header/nav-mobile');
	}
}

if( ! function_exists( 'houzez_template_footer' ) ) {

	function houzez_template_footer() {
		get_template_part('template-parts/footer/main'); 
	}
}

/**
 * Core function to handle search form display logic
 * 
 * @param string $position The position where search should be displayed ('under_nav' or 'under_banner')
 * @return void
 */
if( ! function_exists('houzez_render_search') ) {
	function houzez_render_search($position) {
		global $post;
		if (!houzez_search_needed()) {
			return;
		}

		// Get page level advanced search settings
		$adv_settings = array(
			'enable' => get_post_meta(houzez_postid(), 'fave_adv_search_enable', true),
			'visibility' => get_post_meta(houzez_postid(), 'fave_adv_search', true),
			'position' => get_post_meta(houzez_postid(), 'fave_adv_search_pos', true)
		);

		// Check page level settings first
		if (!empty($adv_settings['enable']) && $adv_settings['enable'] === 'current_page') {
			// Handle transparent logo case for page level
			if (houzez_is_transparent_logo()) {
				$adv_settings['position'] = 'under_banner';
			}

			// Show search based on page level visibility and position
			if (in_array($adv_settings['visibility'], array('show', 'hide_show'))) {
				// For page level, compare under_menu with under_nav position
				$should_display = ($position === 'under_nav' && $adv_settings['position'] === 'under_menu') || 
								($position === 'under_banner' && $adv_settings['position'] === 'under_banner');
				
				if ($should_display) {
					houzez_get_search_templates();
				}
			}
			return; // Return after handling page level settings
		}

		// If page level settings are not set or set to global, use theme options
		if ($adv_settings['enable'] === 'global' || empty($adv_settings['enable'])) {
			$settings = array(
				'search_enable' => isset($_GET['search_pos']) ? 1 : houzez_option('main-search-enable'),
				'search_position' => isset($_GET['search_pos']) ? sanitize_text_field($_GET['search_pos']) : houzez_option('search_position'),
				'search_pages' => houzez_option('search_pages'),
				'search_selected_pages' => houzez_option('header_search_selected_pages')
			);

			// Handle transparent logo case for theme options
			if (houzez_is_transparent_logo()) {
				$settings['search_position'] = 'under_banner';
			}

			// For theme options, use under_nav directly
			if ($settings['search_enable'] == 0 || $settings['search_position'] != $position) {
				return;
			}

			// Determine if search should be displayed based on page settings
			$should_display = false;

			switch ($settings['search_pages']) {
				case 'only_home':
					$should_display = is_front_page();
					break;
				case 'all_pages':
					$should_display = true;
					break;
				case 'only_innerpages':
					$should_display = !is_front_page();
					break;
				case 'specific_pages':
					$should_display = is_page($settings['search_selected_pages']);
					break;
			}

			if ($should_display) {
				houzez_get_search_templates();
			}
		}
	}
}

/**
 * Renders the search form after header based on various conditions and settings
 * 
 * @since 1.0.0
 * @return void
 */
if( ! function_exists('houzez_search_after_header') ) {
	function houzez_search_after_header() {
		houzez_render_search('under_nav');
	}
}

/**
 * Renders the search form after banner based on various conditions and settings
 * 
 * @since 1.0.0
 * @return void
 */
if( ! function_exists('houzez_search_after_banner') ) {
	function houzez_search_after_banner() {
		houzez_render_search('under_banner');
	}
}

/**
 * Helper function to load search template parts
 */
if( ! function_exists('houzez_get_search_templates') ) {
	function houzez_get_search_templates() {
		get_template_part('template-parts/search/main');
		get_template_part('template-parts/search/search-mobile-nav');
	}
}


if( ! function_exists( 'houzez_realtor_contact_form' ) ) {

    function houzez_realtor_contact_form() {

        if( ( is_singular('houzez_agency') && houzez_option('agency_form_agency_page', 1) ) || ( is_singular('houzez_agent') && houzez_option('agent_form_agent_page', 1) ) ) {
            get_template_part('template-parts/realtors/contact', 'form'); 
        }
    }
}


if( ! function_exists( 'houzez_listing_preview' ) ) {

    function houzez_listing_preview() {
        
        get_template_part('template-parts/listing/listing-lightbox'); 
    }
}

if( ! function_exists( 'houzez_login_password_reset' ) ) {

    function houzez_login_password_reset() {
        
        if( !houzez_is_login_page() ) { 
			get_template_part('template-parts/login-register/modal-login-register'); 
		}
		get_template_part('template-parts/login-register/modal-reset-password-form'); 
    }
}


if( ! function_exists( 'houzez_backtotop_compare' ) ) {

    function houzez_backtotop_compare() {
        
        if ( ! houzez_is_splash() && ! houzez_is_dashboard() ) {
        	if( houzez_option('backtotop') ) {
				get_template_part('template-parts/footer/back-to-top'); 
			}

			if( houzez_option('disable_compare', 1) ) {
		        get_template_part('template-parts/listing/compare-properties'); 
		    }
        }
    }
}


if( !function_exists('houzez_setup_loop') ) {
    /**
     * Sets up the houzez_loop global from the passed args.
     *
     * @since 1.1.0
     * @param array $args Args to pass into the global.
     */
    function houzez_setup_loop( $args = array() ) {
        

        $default_args = array();

        if( is_page_template('template/template-search.php') ) {
            $default_args['isSearch'] = 'Yes';
        }

        // Merge any existing values.
        if ( isset( $GLOBALS['houzez_loop'] ) ) {
            $default_args = array_merge( $default_args, $GLOBALS['houzez_loop'] );
        }

        $GLOBALS['houzez_loop'] = wp_parse_args( $args, $default_args );
    }
    add_action( 'wp', 'houzez_setup_loop', 50 );
    add_action( 'loop_start', 'houzez_setup_loop', 10 );
}

if ( ! function_exists( 'houzez_reset_loop' ) ) {
    /**
     * Resets the houzez_loop global.
     *
     * @since 1.0.0
     */
    function houzez_reset_loop() {
        unset( $GLOBALS['houzez_loop'] );
        houzez_setup_loop();
    }
    add_action( 'loop_end', 'houzez_reset_loop', 1000 );
}


if( !function_exists( 'houzez_get_loop_prop' ) ) {
    /**
     * Gets a property from the houzez_loop global.
     *
     * @since 1.0.0
     * @param string $prop Prop to get.
     * @param string $default Default if the prop does not exist.
     * @return mixed
     */
    function houzez_get_loop_prop( $prop, $default = '' ) {
        houzez_setup_loop(); // Ensure shop loop is setup.

        return isset( $GLOBALS['houzez_loop'], $GLOBALS['houzez_loop'][ $prop ] ) ? $GLOBALS['houzez_loop'][ $prop ] : $default;
    }
}

if( !function_exists( 'houzez_set_loop_prop' ) ) {
    /**
     * Sets a property in the houzez_loop global.
     *
     * @since 1.0.0
     * @param string $prop Prop to set.
     * @param string $value Value to set.
     */
    function houzez_set_loop_prop( $prop, $value = '' ) {
        if ( ! isset( $GLOBALS['houzez_loop'] ) ) {
            houzez_setup_loop();
        }
        $GLOBALS['houzez_loop'][ $prop ] = $value;
    }
}