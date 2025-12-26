<?php 
global $settings; 
?>
<div class="d-block d-md-none">
    <div class="mobile-top-wrap">
        <div class="mobile-property-title block-wrap">
            
            <div class="d-flex align-items-center mb-3">
                <?php 
                get_template_part('property-details/partials/item-labels');?>
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
                <?php echo houzez_listing_price_v1(); ?>
            </ul>
            <?php if( $settings['hide_favorite'] || $settings['hide_social'] || $settings['hide_print'] ) { ?>
            <div class="mobile-property-tools mobile-property-tools-bottom mt-4">
                <?php get_template_part('property-details/partials/tools'); ?> 
            </div>
            <?php } ?>
        </div><!-- mobile-property-title -->
    </div><!-- mobile-top-wrap -->
</div>