<?php
global $settings, $houzez_local;
$agency_phone = get_post_meta( get_the_ID(), 'fave_agency_phone', true );
$agency_phone_call = str_replace(array('(',')',' ','-'),'', $agency_phone);
?>
<section class="agent-detail-page-v1">
    <div class="agent-profile-wrap">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="agent-image">
                    <?php get_template_part('template-parts/realtors/agency/image'); ?>
                </div>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12">
                <div class="agent-profile-top-wrap mb-4 pb-3">
                    <div class="agent-profile-header">
                        <h1 class="d-flex align-items-center gap-2 my-2">
                            <?php the_title(); ?>
                            <?php get_template_part('template-parts/realtors/agency/verified'); ?>
                        </h1>
                        <?php 
                        if( houzez_option( 'agency_review', 0 ) != 0 ) {
                            get_template_part('template-parts/realtors/rating', null, array('is_single_realtor' => true)); 
                        }?>
                    </div>
                    <?php 
                    if( $settings['show_agency_address'] ) {
                        get_template_part('template-parts/realtors/agency/address'); 
                    }?>
                </div>

                <div class="agent-profile-content">
                    <ul class="list-unstyled">
                        <?php 
                        if( $settings['show_agency_license'] ) {
                            get_template_part('template-parts/realtors/agency/license'); 
                        }
                        
                        if( $settings['show_agency_tax'] ) {
                            get_template_part('template-parts/realtors/agency/tax-number'); 
                        }

                        if( $settings['show_agency_service_areas'] ) {
                            get_template_part('template-parts/realtors/agency/service-area'); 
                        }

                        if( $settings['show_agency_specialties'] ) {
                            get_template_part('template-parts/realtors/agency/specialties');
                        } 

                        ?>
                    </ul>
                </div><!-- agent-profile-content -->
                <div class="agent-profile-buttons d-flex gap-2">

                    <?php if( houzez_option('agency_form_agency_page', 1) ) { ?>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#realtor-form">
                        <?php echo houzez_option('agency_send_email', esc_html__('Send Email', 'houzez')); ?>
                    </button>
                    <?php } ?>

                    <?php if(!empty($agency_phone)) { ?>
                    <a href="tel:<?php echo esc_attr($agency_phone_call); ?>" class="btn btn-call">
                        <span class="hide-on-click"><?php echo houzez_option('agency_lb_call', esc_html__('Call', 'houzez')); ?></span>
                        <span class="show-on-click"><?php echo esc_attr($agency_phone); ?></span>
                    </a>
                    <?php } ?>
                </div><!-- agent-profile-buttons -->
            </div><!-- col-lg-8 col-md-8 col-sm-12 -->
        </div><!-- row -->
    </div><!-- agent-profile-wrap -->
</section>