<?php
global $settings;
$agency_position = get_post_meta( get_the_ID(), 'fave_agency_position', true );

$agency_number = get_post_meta( get_the_ID(), 'fave_agency_phone', true );
$agency_number_call = str_replace(array('(',')',' ','-'),'', $agency_number);
?>
<section class="agent-detail-page-v2">
    <div class="agent-profile-wrap">
        <div class="container">
            <div class="agent-profile-top-wrap d-flex align-items-start flex-column flex-sm-row gap-4">
                <div class="agent-image">
                    <?php get_template_part('template-parts/realtors/agency/image'); ?>
                </div><!-- agent-image -->
                <div class="agent-profile-header">
                    <h1 class="d-flex align-items-center gap-2 my-2" itemprop="name">
                        <?php the_title(); ?> 
                        <?php get_template_part('template-parts/realtors/agency/verified'); ?>
                    </h1>
                    <?php 
                    if( houzez_option( 'agency_review', 0 ) != 0 ) {
                        get_template_part('template-parts/realtors/rating', null, array('is_single_realtor' => true)); 
                    }?> 

                    <?php if( $settings['show_address'] ) {?>
                    <div class="agent-profile-address">
                        <?php get_template_part('template-parts/realtors/agency/address'); ?> 
                    </div><!-- agent-profile-address -->
                    <?php } ?>

                    <div class="agent-profile-cta mt-3">
                        <ul class="list-inline m-0">
                            <?php if( houzez_option('agency_form_agency_page', 1) ) { ?>
                            <li class="list-inline-item"><a href="#" data-bs-toggle="modal" data-bs-target="#realtor-form"><i class="houzez-icon icon-messages-bubble me-1"></i> <?php echo houzez_option('agency_lb_ask_question', esc_html__('Ask a question', 'houzez')); ?></a></li>
                            <?php } ?>
                            <?php if(!empty($agency_number)) { ?>
                            <li class="list-inline-item">
                                <a href="tel:<?php echo esc_attr($agency_number_call); ?>"><i class="houzez-icon icon-phone me-1"></i> <?php echo esc_attr($agency_number); ?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div><!-- agent-profile-header -->
            </div><!-- agent-profile-top-wrap -->
        </div><!-- container -->
    </div><!-- agent-profile-wrap -->
</section>