<?php
global $settings;
$agent_company_logo = get_post_meta( get_the_ID(), 'fave_agent_logo', true );
$agent_number = get_post_meta( get_the_ID(), 'fave_agent_mobile', true );
$agent_number_call = str_replace(array('(',')',' ','-'),'', $agent_number);
if( empty($agent_number) ) {
    $agent_number = get_post_meta( get_the_ID(), 'fave_agent_office_num', true );
    $agent_number_call = str_replace(array('(',')',' ','-'),'', $agent_number);
}
?>
<section class="agent-detail-page-v1">
    <div class="agent-profile-wrap">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="agent-image">
                    <?php if( $settings['show_company_logo'] && !empty( $agent_company_logo ) ) {
                    $logo_url = wp_get_attachment_url( $agent_company_logo );
                    if( !empty($logo_url) ) {
                    ?>
                    <div class="agent-company-logo bottom-0 start-0">
                        <img class="img-fluid" src="<?php echo esc_url( $logo_url ); ?>" alt="">
                    </div>
                    <?php }
                    } ?>
                    <?php get_template_part('template-parts/realtors/agent/image'); ?>
                </div><!-- agent-image -->
            </div><!-- col-lg-4 col-md-4 col-sm-12 -->

            <div class="col-lg-8 col-md-8 col-sm-12">
                <div class="agent-profile-top-wrap mb-4 pb-3">
                    <div class="agent-profile-header">
                        <h1 class="d-flex align-items-baseline gap-2">
                            <?php the_title(); ?>
                            <?php get_template_part('template-parts/realtors/agent/verified'); ?>
                        </h1>
                        
                        <?php 
                        if( houzez_option( 'agent_review', 0 ) != 0 ) {
                            get_template_part('template-parts/realtors/rating', null, ['is_single_realtor' => true]); 
                        }?>

                    </div><!-- agent-profile-content -->
                    <?php 
                    if( $settings['show_position'] ) {
                        get_template_part('template-parts/realtors/agent/position'); 
                    }?>
                </div><!-- agent-profile-header -->

                <div class="agent-profile-content">
                    <ul class="list-unstyled">
                        
                        <?php 
                        if( $settings['show_license'] ) {
                            get_template_part('template-parts/realtors/agent/license'); 
                        }
                        
                        if( $settings['show_tax'] ) {
                            get_template_part('template-parts/realtors/agent/tax-number'); 
                        }

                        if( $settings['show_service_areas'] ){
                            get_template_part('template-parts/realtors/agent/service-area'); 
                        }

                        if( $settings['show_specialties'] ) {
                            get_template_part('template-parts/realtors/agent/specialties'); 
                        }?>

                    </ul>
                </div><!-- agent-profile-content -->

                <div class="agent-profile-buttons d-flex gap-2">
                    <?php if( houzez_option('agent_form_agent_page', 1) ) { ?>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#realtor-form">
                        <?php echo esc_html__('Send Email', 'houzez'); ?>  
                    </button>
                    <?php } ?>
                    
                    <?php if(!empty($agent_number)) { ?>
                    <a href="tel:<?php echo esc_attr($agent_number_call); ?>" class="btn btn-call">
                        <span class="hide-on-click"><?php echo esc_html__('Call', 'houzez'); ?></span>
                        <span class="show-on-click"><?php echo esc_attr($agent_number); ?></span>
                    </a>
                    <?php } ?>
                </div><!-- agent-profile-buttons -->
            </div><!-- col-lg-8 col-md-8 col-sm-12 -->
        </div><!-- row -->
    </div><!-- agent-profile-wrap -->
</section>
