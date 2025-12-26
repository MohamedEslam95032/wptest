<?php
global $post, $settings;
$thumb_id = get_post_thumbnail_id( get_the_ID() );
$terms_page_id = houzez_option('terms_condition');
$thumb_url_array = wp_get_attachment_image_src( $thumb_id, 'thumbnail', true );
$image_url = $thumb_url_array[0];
$name = '';
$gdpr_checkbox = houzez_option('gdpr_hide_checkbox', 1);

$hide_form_fields = houzez_option('hide_agency_agent_contact_form_fields');
$target_email = get_post_meta( $post->ID, 'fave_agent_email', true );
$agent_id = $post->ID;
$agent_type  = 'agent_info';
$source_link = get_permalink();
$name = get_the_title();

$form_shortcode = houzez_option('contact_form_agent_detail');
?>

<div class="property-form-wrap border-none">
    <?php if( $settings['agent_detail'] == 'yes' ) { ?>
    <div class="agent-details">
        <div class="d-flex align-items-center gap-3">
            <?php if(!empty($image_url)) { ?>
            <div class="agent-image">
                <img class="rounded" src="<?php echo esc_url($image_url); ?>" width="50" height="50" alt="">
            </div>
            <?php } ?>
            
            <ul class="agent-information list-unstyled mb-0">
                <li class="agent-name">
                    <i class="houzez-icon icon-single-neutral me-1"></i> <?php echo esc_attr($name); ?>
                </li>
            </ul>
        </div><!-- d-flex -->
    </div><!-- agent-details -->
    <?php } ?>

    <?php
    if(houzez_form_type()) {

        if(!empty($form_shortcode)) {
            echo '<div class="property-form mt-3">';
                echo do_shortcode($form_shortcode);
            echo '</div>';
        }

    } else {
    ?>

    <div class="property-form mt-3">
        
        <form method="post">
           
            <input type="hidden" name="contact_realtor_ajax" id="contact_realtor_ajax" value="<?php echo wp_create_nonce('contact_realtor_nonce'); ?>"/>
            <input type="hidden" name="action" value="houzez_contact_realtor" />
            <input type="hidden" name="source_link" value="<?php echo esc_url($source_link)?>">
            <input type="hidden" name="agent_id" value="<?php echo intval($agent_id)?>">
            <input type="hidden" name="agent_type" value="<?php echo esc_attr($agent_type)?>">
            <input type="hidden" name="realtor_page" value="yes">

            <?php if( $hide_form_fields['name'] != 1 ) { ?>
            <div class="form-group mb-2">
                <input class="form-control" name="name" value="" type="text" placeholder="<?php esc_html_e('Your Name', 'houzez'); ?>">
            </div><!-- form-group --> 
            <?php } ?>

            <?php if( $hide_form_fields['phone'] != 1 ) { ?>  
            <div class="form-group mb-2">
                <input class="form-control" name="mobile" value="" type="text" placeholder="<?php esc_html_e('Phone', 'houzez'); ?>">
            </div><!-- form-group -->   
            <?php } ?>

            <div class="form-group mb-2">
                <input class="form-control" name="email" value="" type="email" placeholder="<?php esc_html_e('Email', 'houzez'); ?>">
            </div><!-- form-group --> 

            <?php if( $hide_form_fields['message'] != 1 ) { ?>  
            <div class="form-group form-group-textarea mb-2">
                <textarea class="form-control" name="message" rows="4" placeholder="<?php esc_html_e('Message', 'houzez'); ?>"><?php echo sprintf(esc_html__('Hi %s, I saw your profile on %s and wanted to see if i can get some help', 'houzez'), $name, get_option('blogname')); ?></textarea>
            </div><!-- form-group -->
            <?php } ?>

            <?php if( $hide_form_fields['usertype'] != 1 ) { ?>    
            <div class="form-group mb-2">
                <select name="user_type" class="selectpicker form-control bs-select-hidden" title="<?php echo houzez_option('spl_con_select', 'Select'); ?>">
                    <?php if( houzez_option('spl_con_buyer') != "" ) { ?>
                    <option value="buyer"><?php echo houzez_option('spl_con_buyer', "I'm a buyer"); ?></option>
                    <?php } ?>

                    <?php if( houzez_option('spl_con_tennant') != "" ) { ?>
                    <option value="tennant"><?php echo houzez_option('spl_con_tennant', "I'm a tennant"); ?></option>
                    <?php } ?>

                    <?php if( houzez_option('spl_con_agent') != "" ) { ?>
                    <option value="agent"><?php echo houzez_option('spl_con_agent', "I'm an agent"); ?></option>
                    <?php } ?>

                    <?php if( houzez_option('spl_con_other') != "" ) { ?>
                    <option value="other"><?php echo houzez_option('spl_con_other', 'Other'); ?></option>
                    <?php } ?>
                </select><!-- selectpicker -->
            </div><!-- form-group -->   
            <?php } ?>

            <div class="form-group my-3">
                <label class="control control--checkbox m-0 <?php if( $gdpr_checkbox ){ echo 'p-0 hz-no-gdpr-checkbox';}?>">
                    <?php if( ! $gdpr_checkbox ) { ?>
                    <input type="checkbox" name="privacy_policy">
                    <span class="control__indicator"></span>
                    <?php } ?>
                    <div class="gdpr-text-wrap">
                        <?php echo houzez_option('spl_sub_agree', 'By submitting this form I agree to'); ?> <a target="_blank" href="<?php echo esc_url(get_permalink($terms_page_id)); ?>"><?php echo houzez_option('spl_term', 'Terms of Use'); ?></a>
                    </div>
                </label>
            </div><!-- form-group -->

            <?php get_template_part('template-parts/google', 'reCaptcha'); ?>        

            <div class="houzez-ele-button-wrapper">
                <button id="contact_realtor_btn_elementor" type="button" class="houzez-ele-button btn btn-secondary w-100 contact-realtor-btn">
                    <?php get_template_part('template-parts/loader'); ?>    
                    <?php esc_html_e('Submit', 'houzez'); ?>
                </button>
            </div>
            <div class="form_messages mt-2"></div>
        </form>
    </div><!-- property-form -->
    
    <?php } ?>
</div><!-- property-form-wrap -->