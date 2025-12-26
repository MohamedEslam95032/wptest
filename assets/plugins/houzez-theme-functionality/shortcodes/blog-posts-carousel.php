<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 23/01/16
 * Time: 11:33 PM
 */
if( !function_exists('houzez_blog_posts_carousel') ) {
    function houzez_blog_posts_carousel($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'grid_style' => '',
            'category_id' => '',
            'posts_limit' => '',
            'offset' => '',
            'navigation' => '',
            'slides_to_show' => '',
            'slide_auto' => '',
            'auto_speed' => '',
            'slide_dots' => '',
            'slide_infinite' => '',
        ), $atts));

        global $houzez_local;
        $houzez_local = houzez_get_localization();

        ob_start();

        $wp_query_args = array(
            'ignore_sticky_posts' => 1,
            'post_type' => 'post'
        );
        if (!empty($category_id)) {
            if (is_array($category_id)) {
                $wp_query_args['category__in'] = $category_id;
            } else {
                $wp_query_args['cat'] = $category_id;
            }
        }
        if (!empty($offset)) {
            $wp_query_args['offset'] = $offset;
        }
        $wp_query_args['post_status'] = 'publish';

        if (empty($posts_limit)) {
            $posts_limit = get_option('posts_per_page');
        }
        $wp_query_args['posts_per_page'] = $posts_limit;

        $module_class = "blog-posts-module-v1";
        if($grid_style == "style_2") {
            $module_class = "blog-posts-module-v2";
        }

        $the_query = New WP_Query($wp_query_args);

        $token = wp_generate_password(5, false, false);
        if (is_rtl()) {
            $houzez_rtl = "true";
        } else {
            $houzez_rtl = "false";
        }
        ?>
        <script>
            jQuery(document).ready(function ($) {

                var slides_to_show = <?php echo absint($slides_to_show); ?>,
                    navigation = <?php echo ($navigation === 'true') ? 'true' : 'false'; ?>,
                    auto_play = <?php echo ($slide_auto === 'true') ? 'true' : 'false'; ?>,
                    auto_play_speed = <?php echo absint($auto_speed); ?>,
                    dots = <?php echo ($slide_dots === 'true') ? 'true' : 'false'; ?>,
                    slide_infinite = <?php echo ($slide_infinite === 'true') ? 'true' : 'false'; ?>;

                var owl_post_card = $('#carousel-post-card-<?php echo esc_attr( $token ); ?>');

                owl_post_card.slick({
                    rtl: <?php echo esc_attr($houzez_rtl); ?>,
                    lazyLoad: 'ondemand',
                    infinite: slide_infinite,
                    autoplay: auto_play,
                    autoplaySpeed: auto_play_speed,
                    speed: 300,
                    slidesToShow: slides_to_show,
                    arrows: navigation,
                    adaptiveHeight: true,
                    dots: dots,
                    appendArrows: '.blog-posts-slider',
                    prevArrow: $('.blog-prev-js-<?php echo esc_attr($token);?>'),
                    nextArrow: $('.blog-next-js-<?php echo esc_attr($token);?>'),
                    responsive: [{
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 769,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            });
        </script>

        <div class="blog-posts-module blog-posts-slider <?php echo esc_attr($module_class); ?>">

            <?php if( $navigation == 'true' ) { ?>
            <div class="property-carousel-buttons-wrap">
                <button type="button" class="blog-prev-js-<?php echo esc_attr($token);?> slick-prev btn-primary-outlined"><?php esc_html_e('Prev', 'houzez'); ?></button>
                <button type="button" class="blog-next-js-<?php echo esc_attr($token);?> slick-next btn-primary-outlined"><?php esc_html_e('Next', 'houzez'); ?></button>
            </div><!-- property-carousel-buttons-wrap -->
            <?php } ?>

            <div id="carousel-post-card-<?php echo esc_attr($token); ?>" class="blog-posts-slider-wrap blog-posts-slide-wrap-js houzez-all-slider-wrap">
                <?php 
                if ($the_query->have_posts()): 
                    while ($the_query->have_posts()): $the_query->the_post(); ?>
                    <div class="blog-posts-slide-wrap">
                        <?php 
                        if($grid_style == "style_1") {
                            get_template_part('content-grid-1'); 
                        } else {
                            get_template_part('content-grid-2');
                        }
                        ?>
                    </div>
                <?php endwhile; 
                endif;
                wp_reset_postdata(); ?>
            </div>
        </div><!-- blog-posts-module -->

        <?php
        $result = ob_get_contents();
        ob_end_clean();
        return $result;

    }

    add_shortcode('houzez-blog-posts-carousel', 'houzez_blog_posts_carousel');
}
?>