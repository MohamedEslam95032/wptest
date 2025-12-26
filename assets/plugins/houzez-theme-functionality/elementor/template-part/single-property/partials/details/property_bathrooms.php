<?php
global $settings;
$property_bathrooms = houzez_get_listing_data('property_bathrooms');
$bathrooms_label = ($property_bathrooms > 1 ) ? $settings['bathrooms_title'] : $settings['bathroom_title'];

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_bathrooms ) ) {
    echo '<li class="'.esc_attr($column_class).'">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($bathrooms_label).'</strong> <span>'.esc_attr( $property_bathrooms ).'</span>
            </div>
        </li>';
}