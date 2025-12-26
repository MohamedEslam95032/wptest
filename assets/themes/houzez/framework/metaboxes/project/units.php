<?php
/**
 * Add media metabox tab
 *
 * @param $metabox_tabs
 *
 * @return array
 */
function houzez_project_units_metabox_tab( $metabox_tabs ) {
	if ( is_array( $metabox_tabs ) ) {

		$metabox_tabs['units'] = array(
			'label' => esc_html__('Units', 'houzez'),
            'icon' => 'dashicons-building',
		);

	}
	return $metabox_tabs;
}
add_filter( 'houzez_project_metabox_tabs', 'houzez_project_units_metabox_tab', 50 );


/**
 * Add media metaboxes fields with nested groups
 *
 * @param array $metabox_fields
 * @return array
 */
function houzez_project_units_metabox_fields( $metabox_fields ) {
    $houzez_prefix = 'fave_';

    $fields = [
        [
            'id'         => 'project_units',
            'name'       => esc_html__('Units', 'houzez'),
            'type'       => 'group',
            'clone'      => true,
            'sort_clone' => true,
            'fields'     => [
                [
                    'name'        => esc_html__('Title', 'houzez'),
                    'id'          => "{$houzez_prefix}unit_title",
                    'placeholder' => esc_html__('Enter title', 'houzez'),
                    'type'        => 'text',
                    'columns'     => 4,
                ],
                [
                    'name'        => esc_html__('Price', 'houzez'),
                    'id'          => "{$houzez_prefix}unit_price", 
                    'placeholder' => esc_html__('Enter price', 'houzez'),
                    'type'        => 'text',
                    'columns'     => 4,
                ],
                [
                    'name'        => esc_html__('Area Size', 'houzez'),
                    'id'          => "{$houzez_prefix}area_size",
                    'placeholder' => esc_html__('Enter area size', 'houzez'),
                    'type'        => 'text',
                    'columns'     => 4,
                ],
                [
                    'id'         => 'unit_details',
                    //'name'       => esc_html__('Unit Details', 'houzez'),
                    'type'       => 'group',
                    'clone'      => true,
                    'collapsible' => true,
                    'group_title' => 'Unit Details',
                    'fields'     => [
                        [
                            'name'        => esc_html__('Layout', 'houzez'),
                            'id'          => "{$houzez_prefix}layout",
                            'placeholder' => esc_html__('Enter layout', 'houzez'),
                            'type'        => 'text',
                            'columns'     => 4,
                        ],
                        [
                            'name'        => esc_html__('Size', 'houzez'),
                            'id'          => "{$houzez_prefix}size",
                            'placeholder' => esc_html__('Enter size', 'houzez'),
                            'type'        => 'text',
                            'columns'     => 4,
                        ],
                        [
                            'name'             => esc_html__('Picture', 'houzez'),
                            'id'               => "{$houzez_prefix}picture",
                            'type'             => 'image_advanced',
                            'max_file_uploads' => 1,
                            'columns'          => 4,
                        ],
                    ],
                ],
            ],
            'tab' => 'units',
        ],
    ];

    return array_merge( $metabox_fields, $fields );
}
add_filter( 'houzez_project_metabox_fields', 'houzez_project_units_metabox_fields', 40 );
