<?php
global $settings;
$property_garage = houzez_get_listing_data('property_garage');
$garage_label = ($property_garage > 1 ) ? $settings['garages_title'] : $settings['garage_title'];

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_garage ) ) {
    echo '<li class="'.esc_attr($column_class).'">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($garage_label).'</strong> <span>'.esc_attr( $property_garage ).'</span>
            </div>
        </li>';
}