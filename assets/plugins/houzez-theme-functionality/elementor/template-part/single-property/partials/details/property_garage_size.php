<?php
global $settings;
$property_garage_size = houzez_get_listing_data('property_garage_size');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_garage_size ) ) {
    echo '<li class="'.esc_attr($column_class).'">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($settings['garage_size_title']).'</strong> <span>'.esc_attr( $property_garage_size ).'</span>
            </div>
        </li>';
}