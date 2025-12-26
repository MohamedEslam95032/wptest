<?php
global $settings;
$property_id = houzez_get_listing_data('property_id');

// Set default column class if not provided
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-md-6';

if( !empty( $property_id ) ) {
    echo '<li class="'.esc_attr($column_class).'">
            <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                <strong>'.esc_attr($settings['id_title']).'</strong> <span>'.houzez_propperty_id_prefix($property_id).'</span>
            </div>
        </li>';
}
