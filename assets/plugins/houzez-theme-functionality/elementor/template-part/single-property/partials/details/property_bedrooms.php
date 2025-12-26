<?php
global $settings;
$property_bedrooms = houzez_get_listing_data('property_bedrooms');
$bedrooms_label = ($property_bedrooms > 1 ) ? $settings['bedrooms_title'] : $settings['bedroom_title'];

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_bedrooms ) ) {
    echo '<li class="'.esc_attr($column_class).'">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($bedrooms_label).'</strong> <span>'.esc_attr( $property_bedrooms ).'</span>
            </div>
        </li>';
}