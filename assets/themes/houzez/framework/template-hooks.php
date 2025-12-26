<?php
/**
 * Header
 * @see houzez_template_header()
 * 
 */
add_action( 'houzez_header', 'houzez_template_header', 10 );
add_action( 'houzez_after_header', 'houzez_search_after_header', 10 );
add_action( 'houzez_after_banner', 'houzez_search_after_banner', 10 );

/**
 * Footer
 * @see houzez_template_footer()
 * 
 */
add_action( 'houzez_footer', 'houzez_template_footer', 10 );

add_action( 'houzez_after_footer', 'houzez_backtotop_compare' );
add_action( 'houzez_after_footer', 'houzez_login_password_reset' );
add_action( 'houzez_after_footer', 'houzez_listing_preview' );
add_action( 'houzez_after_footer', 'houzez_realtor_contact_form' );