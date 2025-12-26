<?php
/*-----------------------------------------------------------------------------------*/
/*	Properties
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_properties_slider') ) {
	function houzez_properties_slider($atts, $content = null)
	{
		extract(shortcode_atts(array(
			'property_type' => '',
			'property_status' => '',
			'posts_limit' => '',
			// Content Display Settings
			'show_title' => 'yes',
			'show_price' => 'yes',
			'show_address' => 'yes',
			'show_features' => 'yes',
			'show_author' => 'yes',
			'show_date' => 'yes',
			'show_featured_label' => 'yes',
			'show_details_button' => 'yes',
			'show_property_type' => 'yes',
			// Box Layout Settings
			'box_align' => 'flex-start',
			'content_align' => 'left',
			'box_width' => '',
			'box_padding' => '',
			'box_bg_color' => '',
			'box_border_radius' => '',
			// Typography Settings
			'title_typography' => '',
			'price_typography' => '',
			'address_typography' => '',
			'features_typography' => '',
			'author_typography' => '',
			'date_typography' => '',
			'button_typography' => '',
			// Color Settings
			'title_color' => '',
			'title_hover_color' => '',
			'price_color' => '',
			'address_color' => '',
			'features_color' => '',
			'author_color' => '',
			'date_color' => '',
			'overlay_color' => '',
			'overlay_opacity' => '',
			// Button Settings
			'button_bg_color' => '',
			'button_color' => '',
			'button_border_color' => '',
			'button_bg_color_hover' => '',
			'button_color_hover' => '',
			'button_border_color_hover' => '',
			'button_padding' => '',
			'button_border_radius' => '',
		), $atts));

		ob_start();
		global $paged;
		if (is_front_page()) {
			$paged = (get_query_var('page')) ? get_query_var('page') : 1;
		}

		//do the query
		$the_query = Houzez_Data_Source::get_wp_query($atts, $paged); //by ref  do the query

		// Pass settings to global variable for access in template
		global $houzez_slider_settings;
		$houzez_slider_settings = $atts;
		?>
		
		<section class="top-banner-wrap <?php houzez_banner_fullscreen(); ?> property-slider-wrap">
			<div class="property-slider property-banner-slider houzez-all-slider-wrap" data-autoplay="<?php echo esc_attr(houzez_option('banner_slider_autoplay', 1)); ?>" data-loop="<?php echo esc_attr(houzez_option('banner_slider_loop', 1)); ?>" data-speed="<?php echo esc_attr(houzez_option('banner_slider_autoplayspeed', '4000')); ?>">
				<?php 
				if( $the_query->have_posts() ): 
					while( $the_query->have_posts() ): $the_query->the_post();
						
						// Use custom template from plugin
						include( plugin_dir_path( dirname(__FILE__) ) . 'template-parts/slider-item-custom.php' );
						?>
				<?php
					endwhile;
				endif;
				wp_reset_postdata();
				?>
			</div><!-- property-slider -->

		</section><!-- property-slider-wrap -->

		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;

	}

	add_shortcode('houzez_properties_slider', 'houzez_properties_slider');
}
?>