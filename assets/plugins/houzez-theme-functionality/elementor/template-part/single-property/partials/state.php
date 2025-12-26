<?php
global $settings;
$state = houzez_taxonomy_simple('property_state');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

echo '<li class="'.esc_attr($column_class).' d-flex justify-content-between detail-state"><div class="list-lined-item w-100 d-flex justify-content-between py-2"><strong>'.esc_attr($settings['state_title']).'</strong> <span>'.esc_attr($state).'</span></div></li>';