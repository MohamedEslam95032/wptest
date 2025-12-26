<?php
global $settings;
$property_rooms = houzez_get_listing_data('property_rooms');
$rooms_label = ($property_rooms > 1 ) ? $settings['rooms_title'] : $settings['room_title'];

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_rooms ) ) {
    echo '<li class="'.esc_attr($column_class).'">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($rooms_label).'</strong> <span>'.esc_attr( $property_rooms ).'</span>
            </div>
        </li>';
}