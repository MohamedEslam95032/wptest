<?php
/**
 * Add media metabox tab
 *
 * @param $metabox_tabs
 *
 * @return array
 */
function houzez_project_payment_plan_metabox_tab( $metabox_tabs ) {
	if ( is_array( $metabox_tabs ) ) {

		$metabox_tabs['payment_plan'] = array(
			'label' => esc_html__('Payment Plan', 'houzez'),
            'icon' => 'dashicons-chart-area',
		);

	}
	return $metabox_tabs;
}
add_filter( 'houzez_project_metabox_tabs', 'houzez_project_payment_plan_metabox_tab', 60 );


/**
 * Add media metaboxes fields
 *
 * @param $metabox_fields6k990p[] *
 * @return array
 */
function houzez_project_payment_plan_metabox_fields( $metabox_fields ) {
	$houzez_prefix = 'fave_';

	$fields = array(
		array(
			'name' => esc_html__('Images', 'houzez'),
			'id' => "{$houzez_prefix}project_payment_plan_images",
			'type' => 'image_advanced',
			'max_file_uploads' => 3,
			'tab' => 'payment_plan',
		),
		array(
			'id'       => 'project_payment_plan',
			'name'     => esc_html__('Payment Plan', 'houzez'),
			'type'     => 'group',
			'clone'    => true,
			'sort_clone' => true,
			'fields'   => array(
				array(
					'name'        => esc_html__('Title', 'houzez'),
					'id'          => "{$houzez_prefix}payment_plan_title",
					'placeholder' => esc_html__('Enter title', 'houzez'),
					'type'        => 'text',
					'columns'     => 4,
				),
				array(
					'name'        => esc_html__('Subtitle', 'houzez'),
					'id'          => "{$houzez_prefix}payment_plan_subtitle",
					'placeholder' => esc_html__('Enter subtitle', 'houzez'),
					'type'        => 'text',
					'columns'     => 4,
				),
				array(
					'name'        => esc_html__('Payment Percentage', 'houzez'),
					'id'          => "{$houzez_prefix}payment_plan_percentage", 
					'placeholder' => esc_html__('Enter payment percentage. Ex 20%', 'houzez'),
					'type'        => 'text',
					'columns'     => 4,
				),
			),
			'tab' => 'payment_plan',
		),
	);

	return array_merge( $metabox_fields, $fields );

}
add_filter( 'houzez_project_metabox_fields', 'houzez_project_payment_plan_metabox_fields', 40 );
