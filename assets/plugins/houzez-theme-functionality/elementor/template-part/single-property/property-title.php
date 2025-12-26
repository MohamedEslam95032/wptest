<?php global $settings; ?>
<div class="page-title-wrap d-none d-md-block">
    <nav class="d-flex align-items-center justify-content-between" role="navigation">
        <div class="breadcrumb-wrap">
            <?php if( $settings['show_breadcrumb'] ) { ?>
                <nav><?php houzez_breadcrumbs(); ?></nav>
            <?php } ?>
        </div>
        <?php 
        if( $settings['hide_favorite'] || $settings['hide_social'] || $settings['hide_print'] ) {
            htf_get_template_part('elementor/template-part/single-property/tools');
        }
        ?>
    </nav>
    
    <div class="property-header-wrap d-flex align-items-start justify-content-between mt-3" role="main">
        <div class="property-title-wrap d-flex flex-column gap-2">
            <?php 
            if( $settings['show_title'] ) {
                get_template_part('property-details/partials/title'); 
            }
        
            if( $settings['show_address'] ) {
                $temp_array = array();

                    echo '<address class="item-address mb-2" role="contentinfo"><i class="houzez-icon icon-pin me-1" aria-hidden="true"></i>';
                    foreach ( $settings['address_fields'] as $item_index => $item ) :
                        
                        $key = $item['field_type'];
                        
                        if( $key == 'address' ) {
                            $temp_array[] = houzez_get_listing_data('property_map_address');

                        } else if( $key == 'streat-address' ) {
                            $temp_array[] = houzez_get_listing_data('property_address');

                        } else if( $key == 'zip-code' ) {
                            $temp_array[] = houzez_get_listing_data('property_zip');

                        } else if( $key == 'country' ) {
                            $temp_array[] = houzez_taxonomy_simple('property_country');

                        } else if( $key == 'state' ) {
                            $temp_array[] = houzez_taxonomy_simple('property_state');

                        } else if( $key == 'city' ) {
                            $temp_array[] = houzez_taxonomy_simple('property_city');

                        } else if( $key == 'area' ) {
                            $temp_array[] = houzez_taxonomy_simple('property_area');

                        }
                        
                    endforeach;

                    if( !empty($temp_array)) {
                        $result = join( ", ", $temp_array );
                        echo $result;
                    }

                    echo '</address>'; 
            }

            if( $settings['show_labels'] ) {
                get_template_part('property-details/partials/item-labels');
            }
         ?>
        </div>
        <?php 
        if( $settings['show_price'] ) {
            get_template_part('property-details/partials/item-price'); 
        }?>
    </div>
</div><!-- page-title-wrap -->