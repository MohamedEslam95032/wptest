<?php
global $settings;
$country = houzez_taxonomy_simple('property_country');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

echo '<li class="'.esc_attr($column_class).' d-flex justify-content-between detail-country"><div class="list-lined-item w-100 d-flex justify-content-between py-2"><strong>'.esc_attr($settings['country_title']).'</strong> <span>'.esc_attr($country).'</span></div></li>';