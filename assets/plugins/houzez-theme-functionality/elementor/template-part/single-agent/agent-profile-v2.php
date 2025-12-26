<?php
global $settings;
$agent_company_logo = get_post_meta( get_the_ID(), 'fave_agent_logo', true );
$agent_position = get_post_meta( get_the_ID(), 'fave_agent_position', true );
$agent_company = get_post_meta( get_the_ID(), 'fave_agent_company', true );
$agent_number = get_post_meta( get_the_ID(), 'fave_agent_mobile', true );
$agent_agency_id = get_post_meta( get_the_ID(), 'fave_agent_agencies', true );
$agent_number_call = str_replace(array('(',')',' ','-'),'', $agent_number);
if( empty($agent_number) ) {
    $agent_number = get_post_meta( get_the_ID(), 'fave_agent_office_num', true );
    $agent_number_call = str_replace(array('(',')',' ','-'),'', $agent_number);
}

$href = "";
if( !empty($agent_agency_id) ) {
    $href = ' href="'.esc_url(get_permalink($agent_agency_id)).'"';
    $agent_company = get_the_title( $agent_agency_id );
}
?>
<section class="agent-detail-page-v2">
    <div class="agent-profile-wrap">
        <div class="container">
            <div class="agent-profile-top-wrap d-flex align-items-start flex-column flex-sm-row gap-4">
                <div class="agent-image">
                    <?php get_template_part('template-parts/realtors/agent/image'); ?>
                </div><!-- agent-image -->
                <div class="agent-profile-header">
                    <?php if( $settings['show_position'] && $agent_position != '' ) { ?>
                    <div class="agent-list-position">
                        <a><?php echo esc_attr($agent_position); ?></a>
                    </div>
                    <?php } ?>
                    
                    <h1 class="d-flex align-items-center gap-2 my-2">
                        <?php the_title(); ?>
                        <?php get_template_part('template-parts/realtors/agent/verified'); ?>
                    </h1>
                    
                    <?php 
                    if( houzez_option( 'agent_review', 0 ) != 0 ) {
                        get_template_part('template-parts/realtors/rating', null, ['is_single_realtor' => true]); 
                    }?>

                    <?php if( $settings['show_agency'] && $agent_company != "" ) { ?>
                    <div class="agent-list-position">
                        <a <?php echo $href; ?>><?php echo esc_attr( $agent_company ); ?></a>
                    </div><!-- agent-list-position -->
                    <?php } ?>

                    <div class="agent-profile-cta mt-3">
                        <ul class="list-inline m-0">
                            <?php if( houzez_option('agent_form_agent_page', 1) ) { ?>
                            <li class="list-inline-item"><a href="#" data-bs-toggle="modal" data-bs-target="#realtor-form"><i class="houzez-icon icon-messages-bubble me-1"></i> <?php esc_html_e( 'Ask a question', 'houzez' ); ?></a></li>
                            <?php } ?>

                            <?php if(!empty($agent_number)) { ?>
                            <li class="list-inline-item">
                                <a href="tel:<?php echo esc_attr($agent_number_call); ?>">
                                    <i class="houzez-icon icon-phone me-1"></i> <?php echo esc_attr($agent_number); ?>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div><!-- agent-profile-header -->
            </div><!-- agent-profile-top-wrap -->
        </div><!-- container -->
    </div><!-- agent-profile-wrap -->
</section>