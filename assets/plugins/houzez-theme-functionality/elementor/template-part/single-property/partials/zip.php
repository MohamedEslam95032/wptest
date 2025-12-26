<?php
global $settings;
$zipcode = houzez_get_listing_data('property_zip');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

echo '<li class="'.esc_attr($column_class).' d-flex justify-content-between detail-zip"><div class="list-lined-item w-100 d-flex justify-content-between py-2"><strong>'.esc_attr($settings['zip_title']).'</strong> <span>'.esc_attr( $zipcode ).'</span></div></li>';