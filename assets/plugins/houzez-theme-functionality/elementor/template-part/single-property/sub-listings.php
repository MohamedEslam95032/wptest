<?php
global $settings, $post, $multi_units_ids;

$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : houzez_option('sps_sub_listings', 'Sub Listings');

$multi_units_ids = houzez_get_listing_data('multi_units_ids');
if( ! empty($multi_units_ids) ) {
?>
<div class="property-sub-listings-wrap property-section-wrap" id="property-sub-listings-wrap">
	<div class="block-wrap">
		<?php if( $settings['section_header'] ) { ?>
		<div class="block-title-wrap">
			<h2><?php echo $section_title; ?></h2>
		</div><!-- block-title-wrap -->
		<?php } ?>
		<div class="block-content-wrap">
			<div class="listing-view list-view row g-4">
				<?php
                $ids = explode(',', $multi_units_ids);
                $args = array(
                    'post_type' => 'property',
                    'post__in' => $ids,
                    'posts_per_page' => -1,
                );
                $query = new WP_Query($args);

                if($query->have_posts()): 
                	while ($query->have_posts()): $query->the_post(); 
						get_template_part('template-parts/listing/item-list-v1'); 
                	endwhile; 
                endif; 
                wp_reset_query();
                ?>

			</div><!-- listing-view -->	
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- property-address-wrap -->
<?php } ?>