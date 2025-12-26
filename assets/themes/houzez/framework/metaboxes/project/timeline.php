<?php
/**
 * Add media metabox tab
 *
 * @param $metabox_tabs
 *
 * @return array
 */
function houzez_project_timeline_metabox_tab( $metabox_tabs ) {
	if ( is_array( $metabox_tabs ) ) {

		$metabox_tabs['timeline'] = array(
			'label' => esc_html__('Timeline', 'houzez'),
            'icon' => 'dashicons-clock',
		);

	}
	return $metabox_tabs;
}
add_filter( 'houzez_project_metabox_tabs', 'houzez_project_timeline_metabox_tab', 40 );


/**
 * Add media metaboxes fields
 *
 * @param $metabox_fields6k990p[] *
 * @return array
 */
function houzez_project_timeline_metabox_fields( $metabox_fields ) {
	$houzez_prefix = 'fave_';

	$fields = array(
		array(
			'id'       => 'project_timeline',
			'name'     => esc_html__('Timeline', 'houzez'),
			'type'     => 'group',
			'clone'    => true,
			'sort_clone' => true,
			'fields'   => array(
				array(
					'name'        => esc_html__('Title', 'houzez'),
					'id'          => "{$houzez_prefix}timeline_title",
					'placeholder' => esc_html__('Enter title', 'houzez'),
					'type'        => 'text',
					'columns'     => 6,
				),
				array(
					'name'        => esc_html__('Date', 'houzez'),
					'id'          => "{$houzez_prefix}timeline_date", 
					'placeholder' => esc_html__('Enter date', 'houzez'),
					'type'        => 'date',
					'columns'     => 6,
				),
			),
			'tab' => 'timeline',
		),
	);

	return array_merge( $metabox_fields, $fields );

}
add_filter( 'houzez_project_metabox_fields', 'houzez_project_timeline_metabox_fields', 40 );
