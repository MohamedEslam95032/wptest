<?php
if( !function_exists('houzez_testimonials_v3') ) {
    function houzez_testimonials_v3($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'posts_limit' => '',
            'offset' => '',
            'orderby' => '',
            'order' => ''
        ), $atts));

        ob_start();
        
        $args = array(
            'post_type' => 'houzez_testimonials',
            'posts_per_page' => $posts_limit,
            'orderby' => $orderby,
            'order' => $order,
            'offset' => $offset
        );
        $testi_qry = new WP_Query($args);
        ?>

        <div class="testimonials-module testimonials-module-slider-v3 testimonials-module-v3">
            <div class="testimonials-slider-wrap-v3">
                <?php
                if ($testi_qry->have_posts()): 
                    while ($testi_qry->have_posts()): $testi_qry->the_post(); 

                        get_template_part('template-parts/testimonials/item-v3'); 
                        
                    endwhile;
                endif;
                wp_reset_postdata();
                ?>
            </div><!-- testimonials-slider -->
            <div class="testimonials-slider-buttons-wrap testimonials-slider-wrap-v3-arrows"></div><!-- .custom-carousel-module-header-right -->
        </div><!-- testimonials-module -->

        <?php
        $result = ob_get_contents();
        ob_end_clean();
        return $result;

    }

    add_shortcode('houzez-testimonials-v3', 'houzez_testimonials_v3');
}
?>