<?php 
global $settings; 
$tools_position = houzez_option('property_tools_mobile_pos', 'under_banner');
$media_tabs = houzez_get_media_tabs();
$tabs_count = count($media_tabs);
$tabs_count = $tabs_count + 2; //add 2 for mobile
$show_media_tabs = isset($args['media_tabs']) ? $args['media_tabs'] : true;
?>
<div class="property-view">
    <div class="d-block d-md-none">
        <div class="mobile-top-wrap">
        
            <div class="mobile-property-tools block-wrap">
                <div class="houzez-media-tabs-<?php esc_attr_e($tabs_count);?> d-flex justify-content-between">
                    <?php 
                    if($show_media_tabs) {
                        htf_get_template_part('elementor/template-part/single-property/media-btns'); 
                    } ?>

                    <?php 
                    if( ($settings['hide_favorite'] || $settings['hide_social'] || $settings['hide_print']) && $tools_position == 'under_banner' ) {
                        get_template_part('property-details/partials/tools'); 
                    } ?>  
                </div>
            </div>

            <div class="mobile-property-title block-wrap">
                <div class="d-flex align-items-center mb-3">
                    <?php 
                    if( $settings['show_labels'] ) {
                        get_template_part('property-details/partials/item-labels');
                    }
                    ?>
                </div>
                <div class="page-title mb-1">
                    <h1><?php the_title(); ?></h1>
                </div>
                <?php 
                if( $settings['show_address'] ) {
                    get_template_part('property-details/partials/item-address');
                } 
                ?>
                <ul class="item-price-wrap" role="list">
                    <?php 
                    if( $settings['show_price'] ) {
                        echo houzez_listing_price_v1(); 
                    } ?>
                </ul>
                <?php if( ($settings['hide_favorite'] || $settings['hide_social'] || $settings['hide_print']) && $tools_position == 'under_title' ) { ?>
                <div class="mobile-property-tools mobile-property-tools-bottom mt-4">
                    <?php get_template_part('property-details/partials/tools'); ?> 
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>