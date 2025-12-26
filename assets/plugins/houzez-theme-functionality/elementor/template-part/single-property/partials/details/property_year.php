<?php
global $settings;
$property_year = houzez_get_listing_data('property_year');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_year ) ) {
    echo '<li class="'.esc_attr($column_class).'">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($settings['year_title']).'</strong> <span>'.esc_attr( $property_year ).'</span>
            </div>
        </li>';
}