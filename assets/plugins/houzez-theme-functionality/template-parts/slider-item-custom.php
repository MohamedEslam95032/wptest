<?php
/**
 * Custom Properties Slider Item Template
 * Respects all the customization settings from shortcode/elementor
 */

global $houzez_slider_settings;

// Get slider settings with defaults
$show_title = isset($houzez_slider_settings['show_title']) ? $houzez_slider_settings['show_title'] : 'yes';
$show_price = isset($houzez_slider_settings['show_price']) ? $houzez_slider_settings['show_price'] : 'yes';
$show_address = isset($houzez_slider_settings['show_address']) ? $houzez_slider_settings['show_address'] : 'yes';
$show_features = isset($houzez_slider_settings['show_features']) ? $houzez_slider_settings['show_features'] : 'yes';
$show_author = isset($houzez_slider_settings['show_author']) ? $houzez_slider_settings['show_author'] : 'yes';
$show_date = isset($houzez_slider_settings['show_date']) ? $houzez_slider_settings['show_date'] : 'yes';
$show_featured_label = isset($houzez_slider_settings['show_featured_label']) ? $houzez_slider_settings['show_featured_label'] : 'yes';
$show_details_button = isset($houzez_slider_settings['show_details_button']) ? $houzez_slider_settings['show_details_button'] : 'yes';
$show_property_type = isset($houzez_slider_settings['show_property_type']) ? $houzez_slider_settings['show_property_type'] : 'yes';

// Get the image
$slider_img = get_post_meta( get_the_ID(), 'fave_prop_slider_image', true );
$img_url = wp_get_attachment_image_src( $slider_img, 'full', true );
$img_url = $img_url[0];
if(empty($slider_img)) {
	$img_url = wp_get_attachment_url( get_post_thumbnail_id() );
}

// Note: Styling is handled by Elementor's CSS selectors for real-time preview
// Only apply inline styles for non-Elementor shortcode usage

$is_elementor = isset($houzez_slider_settings['_elementor']) || \Elementor\Plugin::$instance->editor->is_edit_mode();

// Build inline styles only for non-Elementor usage
$wrap_styles = array();
$item_styles = array();

if(!$is_elementor) {
	// These styles only apply when NOT in Elementor (i.e., regular shortcode usage)

	// Box alignment
	$box_align = isset($houzez_slider_settings['box_align']) ? $houzez_slider_settings['box_align'] : 'flex-start';
	$wrap_styles[] = 'justify-content: ' . $box_align;
	$wrap_styles[] = 'align-items: center';

	// Content alignment
	$content_align = isset($houzez_slider_settings['content_align']) ? $houzez_slider_settings['content_align'] : 'left';
	$item_styles[] = 'text-align: ' . $content_align;

	// Box width
	if(!empty($houzez_slider_settings['box_width'])) {
		$width = is_array($houzez_slider_settings['box_width'])
			? $houzez_slider_settings['box_width']['size'] . $houzez_slider_settings['box_width']['unit']
			: $houzez_slider_settings['box_width'];
		$item_styles[] = 'width: ' . $width;
	}

	// Box padding
	if(!empty($houzez_slider_settings['box_padding'])) {
		$padding = $houzez_slider_settings['box_padding'];
		if(is_array($padding)) {
			$item_styles[] = 'padding: ' . $padding['top'] . ' ' . $padding['right'] . ' ' . $padding['bottom'] . ' ' . $padding['left'];
		}
	}

	// Box background
	if(!empty($houzez_slider_settings['box_bg_color'])) {
		$item_styles[] = 'background-color: ' . $houzez_slider_settings['box_bg_color'];
	}

	// Box border radius
	if(!empty($houzez_slider_settings['box_border_radius'])) {
		$radius = $houzez_slider_settings['box_border_radius'];
		if(is_array($radius)) {
			$item_styles[] = 'border-radius: ' . $radius['top'] . ' ' . $radius['right'] . ' ' . $radius['bottom'] . ' ' . $radius['left'];
		}
	}
}

?>
<div class="property-slider-item-wrap d-flex"
	style="background-image: url('<?php echo esc_url($img_url); ?>'); <?php echo implode('; ', $wrap_styles); ?>">

	<div class="property-slider-item" style="<?php echo implode('; ', $item_styles); ?>">

		<?php if($show_featured_label == 'yes'): ?>
			<?php get_template_part('template-parts/listing/partials/item-featured-label'); ?>
		<?php endif; ?>

		<?php if($show_title == 'yes'): ?>
			<?php get_template_part('template-parts/listing/partials/item-title'); ?>
		<?php endif; ?>

		<?php if($show_address == 'yes'): ?>
			<?php get_template_part('template-parts/listing/partials/item-address'); ?>
		<?php endif; ?>

		<?php if($show_price == 'yes'): ?>
		<ul class="item-price-wrap d-flex flex-column gap-2 mb-3" role="list">
			<?php echo houzez_listing_price_v1(); ?>
		</ul>
		<?php endif; ?>

		<?php if($show_features == 'yes'): ?>
			<?php get_template_part('template-parts/listing/partials/item-features-v1'); ?>
		<?php endif; ?>

		<?php if($show_details_button == 'yes'): ?>
			<?php get_template_part('template-parts/listing/partials/item-btn'); ?>
		<?php endif; ?>

		<?php if($show_author == 'yes' || $show_date == 'yes'): ?>
			<?php if(houzez_option('disable_date', 1) || houzez_option('disable_agent', 1)): ?>
			<div class="d-flex mt-3">
				<?php if($show_author == 'yes'): ?>
					<?php get_template_part('template-parts/listing/partials/item-author'); ?>
				<?php endif; ?>
				<?php if($show_date == 'yes'): ?>
					<?php get_template_part('template-parts/listing/partials/item-date'); ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		<?php endif; ?>

	</div>
</div>