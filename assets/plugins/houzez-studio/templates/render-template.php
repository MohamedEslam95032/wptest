<?php
/**
 * Single template for render 
 * @package Favethemes Studio
 * @since 1.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
    <meta name="format-detection" content="telephone=no">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main-wrap" <?php houzez_main_wrap_class('main-wrap'); ?>>

	<?php 
	do_action( 'houzez_before_header' );

	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
		
		if( function_exists('fts_header_enabled') && fts_header_enabled() ) {
			do_action( 'houzez_header_studio' );
		} else { 
			do_action( 'houzez_header' );
		}
	}

	do_action( 'houzez_after_header' );
	?>

	<div class="houzez-themebuilder-wrapper houzez-themebuilder-<?php the_ID(); ?>">
	    <?php \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->print_content(); ?>
	</div>

</main><!-- .main-wrap start in header.php-->

<?php 
do_action( 'houzez_before_footer' );

if ((!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('footer'))) {
    
    if( function_exists('fts_footer_enabled') && fts_footer_enabled() ) {
        do_action( 'houzez_footer_studio' );
    } else { 
        do_action( 'houzez_footer' );
    }
}

do_action( 'houzez_after_footer' );

do_action( 'houzez_before_wp_footer' );

wp_footer(); ?>

</body>
</html>


