<?php
global $settings;
$property_status = houzez_taxonomy_simple('property_status');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_status ) ) {
    echo '<li class="'.esc_attr($column_class).' prop_status">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($settings['status_title']).'</strong> <span>'.esc_attr( $property_status ).'</span>
            </div>
        </li>';
}