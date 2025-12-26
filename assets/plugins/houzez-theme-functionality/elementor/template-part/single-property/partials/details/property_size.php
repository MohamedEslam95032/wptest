<?php
global $settings;
$property_size = houzez_get_listing_data('property_size');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_size ) ) {
    echo '<li class="'.esc_attr($column_class).'">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($settings['size_title']).'</strong> <span>'.houzez_property_size( 'after' ).'</span>
            </div>
        </li>';
}