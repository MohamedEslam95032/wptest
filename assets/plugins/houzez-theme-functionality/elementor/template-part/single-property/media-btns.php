<?php 
global $settings, $map_street_view; 
$prop_video_url = houzez_get_listing_data('video_url');
$virtual_tour = houzez_get_listing_data('virtual_tour');
?>
<ul class="nav nav-pills gap-1" id="pills-tab" role="tablist">
                        
    <?php 
    $show_gallery = isset($settings['btn_gallery']) ? $settings['btn_gallery'] : false;
    $show_map = isset($settings['btn_map']) ? $settings['btn_map'] : false;
    $show_street = isset($settings['btn_street']) ? $settings['btn_street'] : false;
    $show_video = isset($settings['btn_video']) ? $settings['btn_video'] : false;
    $show_360 = isset($settings['btn_360_tour']) ? $settings['btn_360_tour'] : false;

    if( $show_gallery || $show_map || $show_street || $show_video || $show_360 ) { ?>
    <li class="nav-item">
        <a class="nav-link active p-0 text-center" id="pills-gallery-tab" data-bs-toggle="pill" href="#pills-gallery" role="tab" aria-controls="pills-gallery" aria-selected="true">
            <i class="houzez-icon icon-picture-sun"></i>
        </a>
    </li>
    <?php } ?>

    <?php if( houzez_get_listing_data('property_map') ) { ?>
        
        <?php if( $settings['btn_map'] ) { ?>
        <li class="nav-item">
            <a class="nav-link map-media-tab p-0 text-center" id="pills-map-tab" data-bs-toggle="pill" href="#pills-map" role="tab" aria-controls="pills-map" aria-selected="true">
                <i class="houzez-icon icon-maps"></i>
            </a>
        </li>
        <?php } ?>

        <?php if( houzez_get_map_system() == 'google' && $map_street_view != 'hide' && $settings['btn_street'] ) { ?>
        <li class="nav-item">
            <a class="nav-link p-0 text-center" id="pills-street-view-tab" data-bs-toggle="pill" href="#pills-street-view" role="tab" aria-controls="pills-street-view" aria-selected="false">
                <i class="houzez-icon icon-location-user"></i>
            </a>
        </li>
        <?php } ?>
    <?php } ?>

    <?php if ( $settings['btn_video'] == 'true' && ! empty( $prop_video_url ) ) { ?>
    <li class="nav-item">
        <a class="nav-link p-0 text-center" id="pills-video-tab" data-bs-toggle="pill" href="#pills-video" role="tab" aria-controls="pills-video" aria-selected="true">
            <i class="houzez-icon icon-video-player-movie-1"></i>
        </a>
    </li>
    <?php } ?>

    <?php if( $settings['btn_360_tour'] == 'true' && ! empty( $virtual_tour ) ) { ?>
    <li class="nav-item">
        <a class="nav-link p-0 text-center houzez-360-virtual-media-tab" id="pills-360-virtual-tour-view-tab" data-bs-toggle="pill" href="#pills-360tour" role="tab" aria-controls="pills-360tour" aria-selected="true">
            <i class="houzez-icon icon-surveillance-360-camera"></i>
        </a>
    </li>
    <?php } ?>
</ul>