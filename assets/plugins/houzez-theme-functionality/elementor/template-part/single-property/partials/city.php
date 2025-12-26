<?php
global $settings;
$city = houzez_taxonomy_simple('property_city');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

echo '<li class="'.esc_attr($column_class).' d-flex justify-content-between detail-city"><div class="list-lined-item w-100 d-flex justify-content-between py-2"><strong>'.esc_attr($settings['city_title']).'</strong> <span>'.esc_attr($city).'</span></div></li>';