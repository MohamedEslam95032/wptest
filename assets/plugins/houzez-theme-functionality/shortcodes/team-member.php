<?php
if( !function_exists('houzez_team') ) {
    function houzez_team($atts, $content = null) {

        // Shortcode attributes with default values
        $atts = shortcode_atts(array(
            'team_image'                 => '',
            'team_name'                  => '',
            'team_position'              => '',
            'team_description'           => '',
            'team_link'                  => '',
            'team_social_facebook'       => '',
            'team_social_twitter'        => '',
            'team_social_linkedin'       => '',
            'team_social_pinterest'      => '',
            'team_social_facebook_target' => '_blank',
            'team_social_twitter_target' => '_blank',
            'team_social_linkedin_target' => '_blank',
            'team_social_pinterest_target'=> '_blank',
        ), $atts);

        // Buffer the output
        ob_start();

        if( !empty($atts['team_image']) ) {

            // Social media links
            $social_links = [
                'facebook'  => ['url' => $atts['team_social_facebook'], 'icon' => 'icon-social-media-facebook', 'target' => $atts['team_social_facebook_target']],
                'twitter'   => ['url' => $atts['team_social_twitter'], 'icon' => 'icon-x-logo-twitter-logo-2', 'target' => $atts['team_social_twitter_target']],
                'linkedin'  => ['url' => $atts['team_social_linkedin'], 'icon' => 'icon-professional-network-linkedin', 'target' => $atts['team_social_linkedin_target']],
                'pinterest' => ['url' => $atts['team_social_pinterest'], 'icon' => 'icon-social-pinterest', 'target' => $atts['team_social_pinterest_target']],
            ];

            $social_html = '';

            // Generate social media icons
            foreach ($social_links as $network => $data) {
                if (!empty($data['url'])) {
                    $social_html .= '<li class="list-inline-item">
                        <a target="' . esc_attr($data['target']) . '" href="' . esc_url($data['url']) . '" class="btn-' . esc_attr($network) . '">
                            <i class="houzez-icon ' . esc_attr($data['icon']) . '"></i>
                        </a>
                    </li>';
                }
            }
            ?>

            <div class="team-module hover-effect text-center">

                <?php if( !empty($atts['team_link'])) { ?>
                <a href="<?php echo esc_url($atts['team_link']); ?>" class="team-mobile-link top-0 start-0 w-100 h-100"></a>
                <?php } ?>
                
                <?php echo wp_get_attachment_image( $atts['team_image'], 'full', false, array('class' => 'img-fluid') ); ?>

                <!-- Team Content Before Hover -->
                <div class="team-content-wrap team-content-wrap-before top-0 start-0 w-100 h-100">
                    <div class="team-content bottom-0 start-0 w-100 p-4">
                        <div class="team-name">
                            <strong><?php echo esc_html($atts['team_name']); ?></strong>
                        </div><!-- team-name -->
                        <div class="team-title">
                            <?php echo esc_html($atts['team_position']); ?>
                        </div><!-- team-title -->
                    </div><!-- team-content -->
                </div><!-- team-content-wrap -->

                <!-- Team Content After Hover -->
                <div class="team-content-wrap team-content-wrap-after top-0 start-0 w-100 h-100">
                    <div class="team-content bottom-0 start-0 w-100 p-4">
                        <div class="team-name">
                            <strong><?php echo esc_html($atts['team_name']); ?></strong>
                        </div><!-- team-name -->
                        <div class="team-title">
                            <?php echo esc_html($atts['team_position']); ?>
                        </div><!-- team-title -->
                        <div class="team-description my-3">
                            <?php echo wp_kses_post($atts['team_description']); ?>
                        </div><!-- team-description -->

                        <?php if(!empty($social_html)) { ?>
                            <div class="team-social-wrap">
                                <ul class="team-social list-unstyled list-inline p-0 m-0">
                                    <?php echo $social_html; ?>
                                </ul>
                            </div><!-- team-social-wrap -->    
                        <?php } ?>    
                    </div><!-- team-content -->
                </div><!-- team-content-wrap -->

            </div><!-- team-module -->

            <?php
        }

        // Capture and return output buffer
        return ob_get_clean();
    }

    add_shortcode('houzez-team', 'houzez_team');
}