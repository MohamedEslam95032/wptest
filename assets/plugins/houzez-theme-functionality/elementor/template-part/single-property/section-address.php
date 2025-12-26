<?php
global $sorting_settings, $settings;

// Include security helpers if not already loaded
if ( ! function_exists( 'houzez_sanitize_template_slug' ) ) {
    require_once HOUZEZ_PLUGIN_DIR . '/functions/security-helpers.php';
} 
$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : houzez_option('sps_address', 'Address');
?>
<div class="property-address-wrap property-section-wrap" id="property-address-wrap">
    <div class="block-wrap">

        <?php if( $settings['section_header'] ) { ?>
        <div class="block-title-wrap d-flex justify-content-between align-items-center">
            <h2><?php echo $section_title; ?></h2>
        </div><!-- block-title-wrap -->
        <?php } ?>

        <div class="block-content-wrap">
            <?php

            $columns = $settings['data_columns'];
            $column_class = 'col-md-6'; // default
            if($columns == 'list-1-cols') {
                $column_class = 'col-md-12';
            } elseif($columns == 'list-3-cols') {
                $column_class = 'col-xl-4 col-lg-6 col-md-6 col-sm-12';
            }
            $args = array('column_class' => $column_class);

            if( !empty($sorting_settings) ) {
                $address_data = explode(',', $sorting_settings);

                // Define allowed address fields to prevent LFI attacks
                $allowed_fields = array(
                    'property_address',
                    'property_zip',
                    'property_country',
                    'property_state',
                    'property_city',
                    'property_area'
                );

                echo '<ul class="row list-lined list-unstyled">';

                foreach ($address_data as $key) {
                    // Sanitize and validate the key
                    $key = trim($key);
                    $sanitized_key = houzez_sanitize_template_slug($key);

                    // Skip if not in allowed fields or if sanitization failed
                    if( empty($sanitized_key) || !in_array($sanitized_key, $allowed_fields, true) ) {
                        continue;
                    }

                    if( $sanitized_key == 'property_address' ) {
                        htf_get_template_part('elementor/template-part/single-property/partials/address', null, $args);

                    } elseif( $sanitized_key == 'property_zip' ) {
                        htf_get_template_part('elementor/template-part/single-property/partials/zip', null, $args);

                    } elseif ( $sanitized_key == 'property_country' ) {
                        htf_get_template_part('elementor/template-part/single-property/partials/country', null, $args);

                    } elseif ( $sanitized_key == 'property_state' ) {
                        htf_get_template_part('elementor/template-part/single-property/partials/state', null, $args);

                    } elseif ( $sanitized_key == 'property_city' ) {
                        htf_get_template_part('elementor/template-part/single-property/partials/city', null, $args);

                    } elseif ( $sanitized_key == 'property_area' ) {
                        htf_get_template_part('elementor/template-part/single-property/partials/area', null, $args);
                    }

                }

                echo '</ul>';

            } else {

                echo '<ul class="row list-lined list-unstyled">';

                htf_get_template_part('elementor/template-part/single-property/partials/address', null, $args);
                htf_get_template_part('elementor/template-part/single-property/partials/zip', null, $args);
                htf_get_template_part('elementor/template-part/single-property/partials/country', null, $args);
                htf_get_template_part('elementor/template-part/single-property/partials/state', null, $args);
                htf_get_template_part('elementor/template-part/single-property/partials/city', null, $args);
                htf_get_template_part('elementor/template-part/single-property/partials/area', null, $args);
                    
                echo '</ul>';

            }
            ?>
               
        </div><!-- block-content-wrap -->

    </div><!-- block-wrap -->
</div><!-- property-address-wrap -->