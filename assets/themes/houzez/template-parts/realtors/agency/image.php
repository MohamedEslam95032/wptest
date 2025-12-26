<?php
global $agency_id;
$image_size = houzez_get_image_size_for('agency_profile');
if( has_post_thumbnail($agency_id) ) {
	$thumbnail_id = get_post_thumbnail_id($agency_id);
	$image_url = wp_get_attachment_image_url($thumbnail_id, $image_size);
	if ($image_url) {
		echo '<meta itemprop="logo" content="' . esc_url($image_url) . '">';
		echo '<meta itemprop="image" content="' . esc_url($image_url) . '">';
	}
	echo get_the_post_thumbnail($agency_id, $image_size, array('class' => 'img-fluid'));
} else {
	houzez_image_placeholder( $image_size );
}
?>