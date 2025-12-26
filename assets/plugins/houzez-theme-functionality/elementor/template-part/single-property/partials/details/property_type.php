<?php
global $settings;
$property_type = houzez_taxonomy_simple('property_type');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_type ) ) {
    echo '<li class="'.esc_attr($column_class).' prop_type">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($settings['type_title']).'</strong> <span>'.esc_attr( $property_type ).'</span>
            </div>
        </li>';
}