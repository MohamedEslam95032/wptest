<?php
/*-----------------------------------------------------------------------------------*/
/*	Module 1
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_taxonomies_list') ) {
	function houzez_taxonomies_list($atts, $content = null)
	{
		extract(shortcode_atts(array(
			'list_title' => '',
			'icon' => '',
			'count_position' => '',
			'houzez_cards_from' => '',
			'houzez_show_child' => '',
			'houzez_hide_count' => '',
			'orderby' 			=> '',
			'order' 			=> '',
			'houzez_hide_empty' => '',
			'no_of_terms' 		=> '',
			'property_type' => '',
			'property_status' => '',
			'property_area' => '',
			'property_state' => '',
			'property_country' => '',
			'property_city' => '',
			'property_label' => '',
		), $atts));

		ob_start();
		
		$slugs = '';

		if( $houzez_cards_from == 'property_city' ) {
			$slugs = $property_city;

		} else if ( $houzez_cards_from == 'property_area' ) {
			$slugs = $property_area;

		} else if ( $houzez_cards_from == 'property_label' ) {
			$slugs = $property_label;

		} else if ( $houzez_cards_from == 'property_country' ) {
			$slugs = $property_country;

		} else if ( $houzez_cards_from == 'property_state' ) {
			$slugs = $property_state;

		} else if ( $houzez_cards_from == 'property_status' ) {
			$slugs = $property_status;

		} else {
			$slugs = $property_type;
		}

		if ($houzez_show_child == 1) {
			$houzez_show_child = '';
		}

		if( $houzez_cards_from == 'property_type' ) {
			$custom_link_for = 'fave_prop_type_custom_link';
		} else {
			$custom_link_for = 'fave_prop_taxonomy_custom_link';
		}

		if( $count_position == 'separated' ) {
			$count_position = 'ms-auto';
		} else {
			$count_position = 'ms-2';
		}

		$tax_name = $houzez_cards_from;
		$taxonomy = get_terms(array(
			'hide_empty' => $houzez_hide_empty,
			'parent' => $houzez_show_child,
			'slug' => houzez_traverse_comma_string($slugs),
			'number' => $no_of_terms,
			'orderby' => $orderby,
			'order' => $order,
			'taxonomy' => $tax_name,
		));
		?>

		<div class="taxonomy-list-module">

			<div class="taxonomy-item-list">
				<?php if( $list_title != "" ) { ?>
				<h3><?php echo esc_html( $list_title )?></h3>
				<?php } ?>
				<ul class="list-unstyled d-flex flex-column">
				<?php
				if ( !is_wp_error( $taxonomy ) ) {

					foreach ($taxonomy as $term) {

					
					$taxonomy_custom_link = get_term_meta($term->term_id, 'fave_prop_taxonomy_custom_link', true);
					if( !empty($taxonomy_custom_link) ) {
						$term_link = $taxonomy_custom_link;
					} else {
						$term_link = get_term_link($term, $tax_name);
					}
					?>
					
					<li class="d-flex align-items-center">
						<?php if( !$icon == '' ) { ?>

							<span class="hz-list-icon">
								<?php echo $icon; ?>
							</span>

						<?php
						} else { ?>
						<span class="hz-list-icon">
							<i class="houzez-icon icon-arrow-right-1 me-1"></i>
						</span>
						<?php } ?>
						<a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($term->name); ?></a> 
						<?php if( $houzez_hide_count != 1 ) { ?>
						<span class="count <?php echo esc_attr($count_position);?>">(<?php esc_attr_e( $term->count );?>)</span>
						<?php } ?>
					</li>

				<?php } 
				}?>
				</ul>
			</div><!-- taxonomy-item-card -->
		</div>

		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;

	}

	add_shortcode('houzez_taxonomies_list', 'houzez_taxonomies_list');
}
?>