<?php
global $sorting_settings, $settings;

// Include security helpers if not already loaded
if ( ! function_exists( 'houzez_sanitize_template_slug' ) ) {
    require_once HOUZEZ_PLUGIN_DIR . '/functions/security-helpers.php';
} 
$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : houzez_option('sps_details', 'Details');
$additional_section_title = isset($settings['additional_section_title']) && !empty($settings['additional_section_title']) ? $settings['additional_section_title'] : houzez_option('sps_additional_details', 'Additional details');

// Set column class based on the data_columns setting
$column_class = 'col-md-6'; // default
if($settings['data_columns'] == 'list-1-cols') {
    $column_class = 'col-md-12';
} elseif($settings['data_columns'] == 'list-3-cols') {
    $column_class = 'col-xl-4 col-lg-6 col-md-6 col-sm-12';
}

// Set column class for additional details
$additional_column_class = 'col-md-6'; // default
if($settings['additional_data_columns'] == 'list-1-cols') {
    $additional_column_class = 'col-md-12';
} elseif($settings['additional_data_columns'] == 'list-3-cols') {
    $additional_column_class = 'col-xl-4 col-lg-6 col-md-6 col-sm-12';
}

$default_fields = array(
		'property_id',
		'property_price',
		'property_size',
		'property_land',
		'property_bedrooms',
		'property_bathrooms',
		'property_rooms',
		'property_garage',
		'property_garage_size',
		'property_year',
		'property_status',
		'property_type',
		
	);

?>
<div class="property-detail-wrap property-section-wrap" id="property-detail-wrap">
	<div class="block-wrap">
		
		<?php if( $settings['section_header'] ) { ?>
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo $section_title; ?></h2>
		</div><!-- block-title-wrap -->
		<?php } ?>

		<div class="block-content-wrap">
			<?php
			if( !empty($sorting_settings) ) {
                $details_data = explode(',', $sorting_settings);  

                echo '<div class="detail-wrap">';
                echo '<ul class="row list-lined list-unstyled" role="list">';

                foreach ( $details_data as $data ) {

                	if( in_array( $data, $default_fields ) ) {
	                	// Sanitize the data variable before using in file path
	                	$sanitized_data = houzez_sanitize_template_slug( $data );
	                	if ( ! empty( $sanitized_data ) && houzez_is_allowed_template_field( $sanitized_data ) ) {
	                		$args = array('column_class' => $column_class);
	                		htf_get_template_part('elementor/template-part/single-property/partials/details/'. $sanitized_data, null, $args);
	                	}

	                } else {

	                	$custom_field = Houzez_Fields_Builder::get_field_title_type_by_slug($data);
	                	$field_type = $custom_field['type'] ?? '';
	                	$meta_type = true;

	                    if( $field_type == 'checkbox_list' || $field_type == 'multiselect' ) {
	                        $meta_type = false;
	                    }

	                	$data_value = get_post_meta( get_the_ID(), 'fave_'.$data, $meta_type );

	                	if( $meta_type == true ) {
	                        $data_value = houzez_wpml_translate_single_string($data_value);
	                    } else {
	                        $data_value = houzez_array_to_comma($data_value);
	                    }

	                	 
	                	$field_title = $custom_field['label'] ?? '';
	                	$field_title = houzez_wpml_translate_single_string($field_title);

	                	if( !empty($data_value) && !empty($field_title) ) {
	                        echo '<li class="'.$column_class.'">
                                    <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                                        <strong>'.esc_attr($field_title).'</strong> <span>'.esc_attr( $data_value ).'</span>
                                    </div>
                                </li>';
	                    }
	                }

	            }

                echo '</ul>';
                echo '</div>';

            } else {

            	echo '<div class="detail-wrap">';
                echo '<ul class="row list-lined list-unstyled" role="list">';

                foreach ( $default_fields as $data) {
                	// Sanitize the data variable before using in file path
                	$sanitized_data = houzez_sanitize_template_slug( $data );
                	if ( ! empty( $sanitized_data ) && houzez_is_allowed_template_field( $sanitized_data ) ) {
                		$args = array('column_class' => $column_class);
                		htf_get_template_part('elementor/template-part/single-property/partials/details/'. $sanitized_data, null, $args);
                	}
	                
	            }

                echo '</ul>';
                echo '</div>';

            }
			?>

			<?php 

			$additional_features = get_post_meta( get_the_ID(), 'additional_features', true);
			if( !empty( $additional_features[0]['fave_additional_feature_title'] ) && $settings['show_additional_details'] ) { ?>
				
				<?php if( $settings['additional_section_header'] ) { ?>
				<div class="block-title-wrap">
					<h3><?php echo esc_attr($additional_section_title); ?></h3>
				</div><!-- block-title-wrap -->
				<?php } ?>

				<ul class="row list-lined list-unstyled additional-details-ul">
					<?php
			        foreach( $additional_features as $ad_del ):
			            echo '<li class="'.$additional_column_class.'">
                                <div class="list-lined-item w-100 d-flex justify-content-between py-2">
                                    <strong>'.esc_attr( $ad_del['fave_additional_feature_title'] ).'</strong> <span>'.esc_attr( $ad_del['fave_additional_feature_value'] ).'</span>
                                </div>
                            </li>';
			        endforeach;
			        ?>
				</ul>	
			<?php } ?>
			
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- property-detail-wrap -->

