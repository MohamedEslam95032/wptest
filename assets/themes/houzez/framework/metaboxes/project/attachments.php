<?php
/**
 * Add attachments metabox tab
 *
 * @param $metabox_tabs
 *
 * @return array
 */
function houzez_project_attachments_metabox_tab( $metabox_tabs ) {
	if ( is_array( $metabox_tabs ) ) {

		$metabox_tabs['attachments'] = array(
			'label' => esc_html__('Project Documents', 'houzez'),
            'icon' => 'dashicons-book',
		);

	}
	return $metabox_tabs;
}
add_filter( 'houzez_project_metabox_tabs', 'houzez_project_attachments_metabox_tab', 30 );


/**
 * Add attachments metaboxes fields
 *
 * @param $metabox_fields
 *
 * @return array
 */
function houzez_project_attachments_metabox_fields( $metabox_fields ) {
	$houzez_prefix = 'fave_';

	$fields = array(
		array(
            'id' => "{$houzez_prefix}attachments",
            'name' => esc_html__('Project Documents', 'houzez'),
            'desc' => esc_html__('You can attach PDF files, Map images OR other documents.', 'houzez'),
            'type' => 'file_advanced',
            'mime_type' => '',
            'columns' => 12,
            'tab' => 'attachments',
        ),
	);

	return array_merge( $metabox_fields, $fields );

}
add_filter( 'houzez_project_metabox_fields', 'houzez_project_attachments_metabox_fields', 70 );
