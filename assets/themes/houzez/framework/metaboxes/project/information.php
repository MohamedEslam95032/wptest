<?php
/**
 * Add information metabox tab
 *
 * @param $metabox_tabs
 *
 * @return array
 */
function houzez_project_information_metabox_tab( $metabox_tabs ) {
	if ( is_array( $metabox_tabs ) ) {

		$metabox_tabs['project_details'] = array(
			'label' => esc_html__('Information', 'houzez'),
			'icon'  => 'dashicons-admin-home',
		);

	}
	return $metabox_tabs;
}
add_filter( 'houzez_project_metabox_tabs', 'houzez_project_information_metabox_tab', 10 );


/**
 * Add information metaboxes fields
 *
 * @param $metabox_fields
 *
 * @return array
 */
function houzez_project_information_metabox_fields( $metabox_fields ) {
	$houzez_prefix = 'fave_';

	$fields = array(
		array(
			'id' => "{$houzez_prefix}project_price",
			'name' => esc_html__('Price', 'houzez'),
			'desc' => '',
			'placeholder' => esc_html__('Enter the starting price', 'houzez'),
			'type' => 'text',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => "{$houzez_prefix}project_price_prefix",
			'name' => esc_html__('Price Prefix', 'houzez'),
			'desc' => esc_html__('For example: Start from', 'houzez'),
			'placeholder' => esc_html__('Enter the price prefix', 'houzez'),
			'type' => 'text',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => "{$houzez_prefix}project_price_postfix",
			'name' => esc_html__('After The Price Label', 'houzez'),
			'desc' => esc_html__('For example: Monthly', 'houzez'),
			'placeholder' => esc_html__('Enter the label after price', 'houzez'),
			'type' => 'text',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => "{$houzez_prefix}project_price_term",
			'name' => esc_html__('Price Term', 'houzez'),
			'desc' => '',
			'placeholder' => esc_html__('Enter the price term', 'houzez'),
			'type' => 'text',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => "{$houzez_prefix}project_delivery_date",
			'name' => esc_html__('Delivery Date', 'houzez'),
			'desc' => '',
			'placeholder' => esc_html__('Enter delivery date', 'houzez'),
			'type' => 'text',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => "{$houzez_prefix}project_sales_starts",
			'name' => esc_html__('Sales Starts', 'houzez'),
			'desc' => '',
			'placeholder' => esc_html__('Enter sales start date', 'houzez'),
			'type' => 'text',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => "{$houzez_prefix}project_payment_plan",
			'name' => esc_html__('Payment Plan', 'houzez'),
			'desc' => '',
			'placeholder' => esc_html__('Enter payment plan details', 'houzez'),
			'type' => 'text',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => "{$houzez_prefix}project_number_of_buildings",
			'name' => esc_html__('Number of Buildings', 'houzez'),
			'desc' => '',
			'placeholder' => esc_html__('Enter number of buildings', 'houzez'),
			'type' => 'text',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => "{$houzez_prefix}project_featured",
			'name' => esc_html__('Set Project as Featured', 'houzez'),
			'desc' => esc_html__('Check to set project as featured', 'houzez'),
			'type' => 'checkbox',
			'std' => "",
			'columns' => 6,
			'tab' => 'project_details',
		),
		array(
			'id' => 'project_additional_features',
			'name' => esc_html__('Additional Information', 'houzez'),
			'type' => 'group',
			'clone' => true,
			'sort_clone' => true,
			'fields' => array(
				array(
					//'name' => esc_html__('Title', 'houzez'),
					'id' => "{$houzez_prefix}project_additional_feature_title",
					'placeholder' => esc_html__('Title', 'houzez'),
					'type' => 'text',
					'columns' => 6,
				),
				array(
					//'name' => esc_html__('Value', 'houzez'),
					'id' => "{$houzez_prefix}project_additional_feature_value",
					'placeholder' => esc_html__('Value', 'houzez'),
					'type' => 'text',
					'columns' => 6,
				)
			),
			'tab' => 'project_details',
		),
	);

	return array_merge( $metabox_fields, $fields );
}
add_filter( 'houzez_project_metabox_fields', 'houzez_project_information_metabox_fields', 10 );
