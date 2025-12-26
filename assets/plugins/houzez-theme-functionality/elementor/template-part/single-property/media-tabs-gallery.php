<?php 
global $settings, $map_street_view; 

if( houzez_get_listing_data('property_map') ) {
    if( $settings['btn_map'] ) { ?>
    <div class="tab-pane h-100" id="pills-map" role="tabpanel" aria-labelledby="pills-map-tab" aria-hidden="true">
        <?php get_template_part('property-details/partials/map'); ?>
    </div>
<?php } ?>

<?php if(houzez_get_map_system() == 'google' && $map_street_view != 'hide' && $settings['btn_street'] ) { ?>
    <div class="tab-pane h-100" id="pills-street-view" role="tabpanel" aria-labelledby="pills-street-view-tab" aria-hidden="true">
    </div>
    <?php } ?>
<?php } ?>

<?php if( $settings['btn_video'] ) {
    $prop_video_url = houzez_get_listing_data('video_url');
    ?>
    <div class="tab-pane h-100" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab" aria-hidden="true">
        <?php $embed_code = wp_oembed_get($prop_video_url); echo $embed_code; ?>
    </div>
<?php } ?>

<?php if( $settings['btn_360_tour'] ) { ?>
    <div class="tab-pane h-100" id="pills-360tour" role="tabpanel" aria-labelledby="pills-360-virtual-tour-view-tab" aria-hidden="true">
        <?php echo houzez_get_listing_data('virtual_tour'); ?>
        <div class="loader-360" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1;">
            <div class="loading-overlay" style="text-align: center; background: rgba(255,255,255,0.8); padding: 20px; border-radius: 5px;">
                <div class="loader-ripple">
                    <div></div>
                    <div></div>
                </div>
                <p style="margin-top: 10px;"><?php esc_html_e('Loading Virtual Tour...', 'houzez'); ?></p>
            </div>
        </div>
        <div id="virtual-tour-iframe-container" style="height: 100%; width: 100%;">
        <?php 
        $virtual_tour = houzez_get_listing_data('virtual_tour');
        if (!empty($virtual_tour)) {
            if (strpos($virtual_tour, '<iframe') !== false || strpos($virtual_tour, '<embed') !== false) {
                $virtual_tour = str_replace('<iframe', '<iframe onload="jQuery(\'.loader-360\').hide();"', $virtual_tour);
                echo $virtual_tour;
            } else { 
                echo '<iframe onload="jQuery(\'.loader-360\').hide();" src="'.$virtual_tour.'" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
            }
        }
        ?>
        </div>
    </div>
<?php } ?>