<?php
/*-----------------------------------------------------------------------------------*/
/*	Module 1
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_taxonomies_cards') ) {
	function houzez_taxonomies_cards($atts, $content = null)
	{
		extract(shortcode_atts(array(
			'cards_layout' => '',
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
			'houzez_cards_columns' => '',
			'property_city' => '',
			'property_label' => '',
			'thumb_size' => ''
		), $atts));

		ob_start();
		$module_type = '';

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

		$img_url = 'https://place-hold.it/360x360';
		$img_width = '360';
		$img_height = '360';
		$card_class = '';

		if($cards_layout == 'layout-v2') {
			$img_url = 'https://place-hold.it/800x800';
			$img_width = '800';
			$img_height = '800';
		}
		if( $houzez_cards_columns == '3cols' ) {
			$card_class = 'row-cols-1 row-cols-md-2 row-cols-lg-3 gy-4 gx-4';
		} else if( $houzez_cards_columns == '4cols' ) {
			$card_class = 'row-cols-1 row-cols-md-2 row-cols-lg-4 gy-4 gx-4';
		}
 		?>

		<div class="taxonomy-cards-module">
			<div class="d-flex row <?php echo esc_attr($card_class);?>">
				<?php
				if ( !is_wp_error( $taxonomy ) ) {
					$i = 0;
					$j = 0;

					foreach ($taxonomy as $term) {

					$i++;
					$j++;

					$attach_id = get_term_meta($term->term_id, 'fave_taxonomy_img', true);

					$attachment = wp_get_attachment_image_src( $attach_id, $thumb_size );

					if( ! empty($attachment)) {
						$img_url = $attachment['0'];
	                    $img_width = $attachment['1'];
	                    $img_height = $attachment['2'];
					}
					
					$taxonomy_custom_link = get_term_meta($term->term_id, 'fave_prop_taxonomy_custom_link', true);
					if( !empty($taxonomy_custom_link) ) {
						$term_link = $taxonomy_custom_link;
					} else {
						$term_link = get_term_link($term, $tax_name);
					}
					
					if($cards_layout == 'layout-v2') {
					?>
						<div class="taxonomy-item-card-wrap">
							<div class="taxonomy-item-card d-flex flex-column pb-3 gap-3">
								<div class="taxonomy-item-card-image">
									<a href="<?php echo esc_url($term_link);?>"><img class="img-fluid" src="<?php echo esc_url($img_url); ?>" width="<?php echo $img_width; ?>" height="<?php echo $img_height; ?>" alt="<?php echo esc_attr($term->name); ?>"></a>
								</div>
								<div class="taxonomy-item-card-content d-flex flex-column align-items-center">
									<dl class="taxonomy-item-card-content-list mb-0 text-center">
										<dt><a href="<?php echo esc_url($term_link);?>"><?php echo esc_attr($term->name); ?></a></dt>
										<?php if( $houzez_hide_count != 1 ) { ?>
										<dd class="mb-0">
											<?php echo esc_attr($term->count); ?> 
											<?php
											if ($term->count < 2) {
												echo houzez_option('cl_property', 'Property');
											} else {
												echo houzez_option('cl_properties', 'Properties');
											}
											?>
										</dd>
										<?php } ?>
									</dl>
								</div>
							</div>
						</div>
					<?php
					} else {
					?>
						<div class="taxonomy-item-card-wrap taxonomy-item-card-horizontal taxonomy-item-card-horizontal-<?php echo esc_attr($houzez_cards_columns);?>">
							<div class="taxonomy-item-card d-flex gap-3">
								<div class="taxonomy-item-card-image">
									<a href="<?php echo esc_url($term_link);?>"><img class="img-fluid" src="<?php echo esc_url($img_url); ?>" width="<?php echo $img_width; ?>" height="<?php echo $img_height; ?>" alt="<?php echo esc_attr($term->name); ?>"></a>
								</div>
								<div class="taxonomy-item-card-content d-flex flex-column justify-content-center">
									<dl class="taxonomy-item-card-content-list mb-0">
										<dt><a href="<?php echo esc_url($term_link);?>"><?php echo esc_attr($term->name); ?></a></dt>
										<?php if( $houzez_hide_count != 1 ) { ?>
										<dd class="mb-0">
											<?php echo esc_attr($term->count); ?> 
											<?php
											if ($term->count < 2) {
												echo houzez_option('cl_property', 'Property');
											} else {
												echo houzez_option('cl_properties', 'Properties');
											}
											?>
										</dd>
										<?php } ?>
									</dl>
								</div>
							</div>
						</div>
					<?php
					}
				} 
				}?>
			</div><!-- taxonomy-cards-module-row -->
		</div>
		
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;

	}

	add_shortcode('houzez_taxonomies_cards', 'houzez_taxonomies_cards');
}
?>