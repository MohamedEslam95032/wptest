<?php
/**
 * Add media metabox tab
 *
 * @param $metabox_tabs
 *
 * @return array
 */
function houzez_project_media_metabox_tab( $metabox_tabs ) {
	if ( is_array( $metabox_tabs ) ) {

		$metabox_tabs['gallery'] = array(
			'label' => esc_html__('Media', 'houzez'),
            'icon' => 'dashicons-format-gallery',
		);

	}
	return $metabox_tabs;
}
add_filter( 'houzez_project_metabox_tabs', 'houzez_project_media_metabox_tab', 40 );


/**
 * Add media metaboxes fields
 *
 * @param $metabox_fields
 *
 * @return array
 */
function houzez_project_media_metabox_fields( $metabox_fields ) {
	$houzez_prefix = 'fave_';

	$fields = array(
		array(
            'name' => esc_html__('Select and Upload', 'houzez'),
            'id' => "{$houzez_prefix}project_images",
            'desc' => esc_html__('(Minimum size 1440x900)', 'houzez'),
            'type' => 'image_advanced',
            'max_file_uploads' => houzez_option('max_project_images', 50),
            'columns' => 12,
            'tab' => 'gallery',
        ),
        array(
            'id' => "{$houzez_prefix}project_master_plan_image",
            'name' => esc_html__('Master Plan Image', 'houzez'),
            'type' => 'image_advanced',
            'columns' => 12,
            'max_file_uploads' => 1,
            'tab' => 'gallery',
        ),
        array(
            'id' => "{$houzez_prefix}project_video_url",
            'name' => esc_html__('Video URL', 'houzez'),
            'placeholder' => esc_html__('YouTube, Vimeo are supported', 'houzez'),
            'desc' => esc_html__('For example').' https://www.youtube.com/watch?v=49d3Gn41IaA',
            'type' => 'text',
            'columns' => 12,
            'tab' => 'gallery',
        ),
	);

	return array_merge( $metabox_fields, $fields );

}
add_filter( 'houzez_project_metabox_fields', 'houzez_project_media_metabox_fields', 40 );