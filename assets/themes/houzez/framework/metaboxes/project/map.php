<?php
/**
 * Add map metabox tab
 *
 * @param $metabox_tabs
 *
 * @return array
 */
function houzez_project_map_metabox_tab( $metabox_tabs ) {
	if ( is_array( $metabox_tabs ) ) {

		$metabox_tabs['project_map'] = array(
			'label' => houzez_option('cls_map', 'Map'),
            'icon' => 'dashicons-location',
		);

	}
	return $metabox_tabs;
}
add_filter( 'houzez_project_metabox_tabs', 'houzez_project_map_metabox_tab', 20 );


/**
 * Add map metaboxes fields
 *
 * @param $metabox_fields
 *
 * @return array
 */
function houzez_project_map_metabox_fields( $metabox_fields ) {
	$houzez_prefix = 'fave_';

	$fields = array(
		array(
            'name' => esc_html__('Map', 'houzez'),
            'id' => "{$houzez_prefix}project_map",
            'type' => 'radio',
            'std' => 1,
            'options' => array(
                1 => houzez_option('cl_show', 'Show '),
                0 => houzez_option('cl_hide', 'Hide')
            ),
            'columns' => 12,
            'tab' => 'project_map',
        ),
        array(
            'id' => "{$houzez_prefix}project_map_address",
            'name' => esc_html__('Address', 'houzez'),
            'placeholder' => esc_html__('Enter your property address', 'houzez'),
            'desc' => '',
            'type' => 'text',
            'std' => '',
            'columns' => 12,
            'tab' => 'project_map',
        ),
        array(
            'id' => "{$houzez_prefix}property_location",
            'name' => '',
            'desc' => esc_html__('Drag and drop the pin on map to find exact location', 'houzez'),
            'type' => houzez_metabox_map_type(),
            'std' => houzez_option('map_default_lat', 25.686540).','.houzez_option('map_default_long', -80.431345).',15',
            'style' => 'width: 100%; height: 410px',
            'address_field' => "{$houzez_prefix}project_map_address",
            'api_key'       => houzez_map_api_key(),
            'language' => get_locale(),
            'columns' => 12,
            'tab' => 'project_map',
        ),


        array(
            'name' => esc_html__('Street View', 'houzez'),
            'id' => "{$houzez_prefix}project_map_street_view",
            'type' => 'select',
            'std' => 'hide',
            'options' => array(
                'hide' => houzez_option('cl_hide', 'Hide'),
                'show' => houzez_option('cl_show', 'Show')
            ),
            'columns' => 12,
            'tab' => 'project_map',
        ),
	);

	return array_merge( $metabox_fields, $fields );

}
add_filter( 'houzez_project_metabox_fields', 'houzez_project_map_metabox_fields', 20 );