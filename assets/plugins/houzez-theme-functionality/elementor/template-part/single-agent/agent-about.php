<?php global $settings; ?>
<section class="agent-detail-page-v1">
    <div class="agent-bio-wrap">
        <?php if( ! empty( $settings['title_text'] ) || $settings['show_name'] ) { ?>
            <h2 class="mb-3">
                <?php echo esc_attr($settings['title_text']); ?>
                
                <?php
                if( $settings['show_name'] ) {
                    the_title(); 
                }
                ?>       
            </h2>
        <?php } 

        the_content();

        if( $settings['show_languages'] == 'yes' ) {
            get_template_part('template-parts/realtors/agent/languages');
        } ?>
    </div><!-- agent-bio-wrap -->  
</section>