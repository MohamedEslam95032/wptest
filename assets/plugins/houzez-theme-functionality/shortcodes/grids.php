<?php
if( !function_exists('houzez_grids') ) {
	function houzez_grids($atts, $content = null) {

		// Use shortcode_atts to assign default values to attributes
		$atts = shortcode_atts(array(
			'houzez_grid_type'    => '',
			'houzez_grid_from'    => '',
			'houzez_show_child'   => '',
			'houzez_hide_count'   => '',
			'orderby'             => '',
			'order'               => '',
			'houzez_hide_empty'   => '',
			'no_of_terms'         => '',
			'property_type'       => '',
			'property_status'     => '',
			'property_area'       => '',
			'property_state'      => '',
			'property_country'    => '',
			'get_lazyload'        => '',
			'property_city'       => '',
			'property_label'      => ''
		), $atts);

		ob_start();

		// Determine the slugs based on houzez_grid_from attribute
		$slugs = isset($atts['houzez_grid_from']) ? $atts[$atts['houzez_grid_from']] : $atts['property_type'];

		// Set custom link based on houzez_grid_from
		$custom_link_for = ($atts['houzez_grid_from'] == 'property_type') ? 'fave_prop_type_custom_link' : 'fave_prop_taxonomy_custom_link';

		// Determine grid class based on houzez_grid_type
		$grid_wrap_class = 'v' . substr($atts['houzez_grid_type'], -1);

		?>
		<div class="taxonomy-grids-module taxonomy-grids-module-<?php echo esc_attr($grid_wrap_class); ?>">
    		<div class="taxonomy-grids-module-grid">
				<?php
				$taxonomy = get_terms(array(
					'hide_empty' => $atts['houzez_hide_empty'],
					'parent'     => $atts['houzez_show_child'] == 1 ? '' : $atts['houzez_show_child'],
					'slug'       => houzez_traverse_comma_string($slugs),
					'number'     => $atts['no_of_terms'],
					'orderby'    => $atts['orderby'],
					'order'      => $atts['order'],
					'taxonomy'   => $atts['houzez_grid_from'],
				));

				$i = 0;
				if ( !is_wp_error( $taxonomy ) ) {
					foreach ($taxonomy as $term) {
						$i++;

						// Grid v1 structure: alternating square and rectangle, reset every 4 items
						if ($atts['houzez_grid_type'] == 'grid_v1') {
							$item_class = ($i == 1 || $i == 4) ? 'taxonomy-item-square' : 'taxonomy-item-rectangle';
							if ($i == 4) $i = 0;

						// Grid v4 structure: rectangle first and last, squares in between
						} elseif ($atts['houzez_grid_type'] == 'grid_v4') {
							if ($i == 1 || $i == 6) {
								$item_class = 'taxonomy-item-rectangle';
							} else {
								$item_class = 'taxonomy-item-square';
							}
							// Reset $i after every 6 items to repeat the pattern
							if ($i == 6) $i = 0;

						// Other grids can have default structures (square)
						} else {
							$item_class = 'taxonomy-item-square';
						}

						$term_img_id = get_term_meta($term->term_id, 'fave_taxonomy_img', true);
						$taxonomy_custom_link = get_term_meta($term->term_id, $custom_link_for, true);

						$img_url = wp_get_attachment_url($term_img_id);
						$term_link = !empty($taxonomy_custom_link) ? $taxonomy_custom_link : get_term_link($term, $atts['houzez_grid_from']);
						
						// Output HTML for the term
						?>
						<div class="<?php echo esc_attr($item_class); ?>">
							<div class="taxonomy-item-wrap h-100">	
								<div class="taxonomy-item <?php echo esc_attr($atts['get_lazyload']); ?>" style="background-image: url(<?php echo esc_url($img_url); ?>);">
									<a class="taxonomy-link hover-effect-flat" href="<?php echo esc_url($term_link); ?>">
										<div class="taxonomy-text-wrap d-flex flex-column justify-content-end align-items-start h-100 w-100">
											<div class="taxonomy-title"><?php echo esc_html($term->name); ?></div>
											<?php if( $atts['houzez_hide_count'] != 1 ) { ?>
												<div class="taxonomy-subtitle">
													<?php echo esc_html($term->count); ?>
													<?php echo esc_html($term->count < 2 ? houzez_option('cl_property', 'Property') : houzez_option('cl_properties', 'Properties')); ?>
												</div>
											<?php } ?>
										</div><!-- taxonomy-text-wrap -->
									</a>
								</div>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	add_shortcode('hz-grids', 'houzez_grids');
}
?>
