<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Blog Posts Widget.
 * @since 1.5.6
 */
class Houzez_Elementor_Blog_Posts_v2 extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.5.6
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_blog_posts_v2';
    }

    /**
     * Get widget title.
     * @since 1.5.6
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Blog Posts Grid v2', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 1.5.6
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-posts-grid';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.5.6
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'houzez-elements' ];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.5.6
     * @access protected
     */
    protected function register_controls() {

        $category = array();
        
        houzez_get_terms_id_array( 'category', $category );

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'post_thumb_size',
            [
                'label' => esc_html__( 'Thumbnail Size', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => \Houzez_Image_Sizes::get_enabled_image_sizes_for_elementor(),
                'default' => 'global',
            ]
        );

        $this->add_control(
            'category_id',
            [
                'label'     => esc_html__( 'Category', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::SELECT2,
                'options'   => $category,
                'multiple'   => true,
                'description' => '',
                'default' => '',
            ]
        );

        $this->add_control(
            'post_limit',
            [
                'label'     => 'Post Limit',
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'description' => '',
                'default' => 4,
            ]
        );

        $this->add_control(
            'offset',
            [
                'label'     => 'Offset',
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'description' => '',
            ]
        );

        
        $this->end_controls_section();


        $this->start_controls_section(
            'showhide_section',
            [
                'label'     => esc_html__( 'Show/Hide', 'houzez-theme-functionality' ),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'show_date',
            [
                'label' => esc_html__( 'Post Date', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'houzez-theme-functionality' ),
                'label_off' => __( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_cat',
            [
                'label' => esc_html__( 'Post Category', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'houzez-theme-functionality' ),
                'label_off' => __( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->end_controls_section();

        /*----------------------------- Style ------------------------*/
        $this->start_controls_section(
            'style_section',
            [
                'label'     => esc_html__( 'Style', 'houzez-theme-functionality' ),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'post_box',
            [
                'label' => esc_html__( 'Post Box', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'box_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'box_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );


        $this->add_control(
            'post_image',
            [
                'label' => esc_html__( 'Image Style', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'image_margin',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-thumb' => 'padding: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'post_title',
            [
                'label' => esc_html__( 'Post Title', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-title h3 a' => 'color: {{VALUE}}',
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .blog-post-item .blog-post-title h3',
            ]
        );

        $this->add_responsive_control(
            'title_margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-title' => 'margin-top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'post_content',
            [
                'label' => esc_html__( 'Post Content', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-body' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .blog-post-body',
            ]
        );

        $this->add_responsive_control(
            'content_margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-body' => 'margin-top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'content_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-body' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'content_padding_pst',
            [
                'label' => __( 'Content Padding', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item .blog-post-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'post_meta',
            [
                'label' => esc_html__( 'Post Meta', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'postmeta_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item-v3 .blog-post-meta, .blog-post-item-v3 .blog-post-meta time' => 'color: {{VALUE}}',
                ]
            ]
        );

        $this->add_control(
            'postmeta_cat_color',
            [
                'label'     => esc_html__( 'Category Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-meta a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'postmeta_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .blog-post-item .blog-post-meta',
            ]
        );

        $this->add_responsive_control(
            'postmeta_margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item .blog-post-meta' => 'margin-top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'postmeta_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item .blog-post-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        /*----------------- Continue Reading -----------------*/
        $this->add_control(
            'continue_reading_style',
            [
                'label' => esc_html__( 'Continue Reading', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'continue_reading_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-link a' => 'color: {{VALUE}}',
                ]
            ]
        );

        $this->add_control(
            'continue_link_hover_color',
            [
                'label'     => esc_html__( 'Hover Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-link a:hover' => 'color: {{VALUE}}',
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'continue_reading_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .blog-post-link',
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.5.6
     * @access protected
     */
    protected function render() {
        global $ele_settings, $houzez_local;
        $settings = $this->get_settings_for_display();

        $ele_settings = $settings;
        
        $post_thumb_size = $settings['post_thumb_size'];
        if ($post_thumb_size === 'global' ) {
            $ele_settings['post_thumb_size'] = '';
        }
        
        $houzez_local = houzez_get_localization();

        $category_id =  $settings['category_id'];

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
        if (! empty( $settings['offset'] ) ) {
            $wp_query_args['offset'] = $settings['offset'];
        }
        $wp_query_args['post_status'] = 'publish';

        $posts_limit = $settings['post_limit'] ?? 4;

        $wp_query_args['posts_per_page'] = $posts_limit;

        $the_query = New WP_Query($wp_query_args);
        ?>

        <div class="blog-posts-module blog-posts-module-v3">
            <div class="row">
                <?php 
                $i = 0;
                if ($the_query->have_posts()): 
                    while ($the_query->have_posts()): $the_query->the_post(); 
                        $i++; 

                        // For the first post, open the left column wrapper
                        if($i == 1) {
                            echo '<div class="col-md-5 col-12"><div class="blog-posts-module-v3-left-wrap">';
                            // Pass post_position as 'first' for the first post
                            get_template_part('content-grid-3', null, array('post_position' => 'first'));
                            echo '</div></div>'; // Close the left column wrapper after the first post
                        }

                        // After the first post, open the right column wrapper
                        if($i == 2) {
                            echo '<div class="col-md-7 col-12"><div class="blog-posts-module-v3-right-wrap">';
                        }

                        // For all posts after the first, set post position and use the same template
                        if($i > 1) {
                            // Pass post_position as 'subsequent' for posts after the first
                            get_template_part('content-grid-3', null, array('post_position' => 'subsequent'));
                        }

                        // After the last post, close the right column wrapper
                        if ($the_query->current_post + 1 === $the_query->post_count && $i > 1) {
                            echo '</div></div>'; // Close the right column wrapper after the last post
                        }

                    endwhile; 
                endif;
                wp_reset_postdata(); ?>
            </div><!-- row -->
        </div><!-- blog-posts-module -->

        <?php

    }

}

\Elementor\Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Blog_Posts_v2() );