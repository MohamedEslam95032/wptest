<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Blog Posts Widget.
 * @since 1.5.6
 */
class Houzez_Elementor_Blog_Posts_Carousel extends Widget_Base {

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
        return 'houzez_elementor_blog_posts_carousel';
    }

    /**
     * Get widget title.
     * @since 1.5.6
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Blog Posts Carousel', 'houzez-theme-functionality' );
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
        return 'houzez-element-icon eicon-posts-carousel';
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
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'grid_style',
            [
                'label'     => esc_html__( 'Grid Version', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'style_1'  => 'Version 1',
                    'style_2'    => 'Version 2'
                ],
                "description" => '',
                'default' => 'style_1',
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
            'posts_limit',
            [
                'label'     => esc_html__('Number of posts to show', 'houzez-theme-functionality'),
                'type'      => Controls_Manager::TEXT,
                'description' => '',
                'default' => '9',
            ]
        );

        $this->add_control(
            'offset',
            [
                'label'     => 'Offset',
                'type'      => Controls_Manager::TEXT,
                'description' => '',
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'showhide_section',
            [
                'label'     => esc_html__( 'Show/Hide', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'show_author',
            [
                'label' => esc_html__( 'Post Author', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'houzez-theme-functionality' ),
                'label_off' => __( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );
        $this->add_control(
            'show_date',
            [
                'label' => esc_html__( 'Post Date', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
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
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'houzez-theme-functionality' ),
                'label_off' => __( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->end_controls_section();

        //Carousel Settings
        $this->start_controls_section(
            'carousels_section',
            [
                'label'     => esc_html__( 'Carousel Settings', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'slides_to_show',
            [
                'label'     => esc_html__('Slides To Show', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                "description" => '',
                'default' => '4',
            ]
        );
        $this->add_control(
            'slide_infinite',
            [
                'label'     => esc_html__('Infinite Scroll', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'true' => esc_html__('Yes', 'houzez-theme-functionality' ),
                    'false' => esc_html__('No', 'houzez-theme-functionality' )
                ],
                "description" => '',
                'default' => 'true',
            ]
        );
        $this->add_control(
            'slide_auto',
            [
                'label'     => esc_html__('Auto Play', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'false' => esc_html__('No', 'houzez-theme-functionality' ),
                    'true' => esc_html__('Yes', 'houzez-theme-functionality' )
                    
                ],
                "description" => '',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'auto_speed',
            [
                'label'     => 'Auto Play Speed',
                'type'      => Controls_Manager::TEXT,
                'description' => esc_html__("Autoplay Speed in milliseconds. Default 3000", 'houzez-theme-functionality'),
                'default' => '3000'
            ]
        );
        $this->add_control(
            'navigation',
            [
                'label'     => esc_html__('Next/Prev Navigation', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'false' => esc_html__('No', 'houzez-theme-functionality' ),
                    'true' => esc_html__('Yes', 'houzez-theme-functionality' )
                    
                ],
                "description" => '',
                'default' => 'true',
            ]
        );
        $this->add_control(
            'slide_dots',
            [
                'label'     => esc_html__('Dots Nav', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'false' => esc_html__('No', 'houzez-theme-functionality' ),
                    'true' => esc_html__('Yes', 'houzez-theme-functionality' )
                    
                ],
                "description" => '',
                'default' => 'false',
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
                'condition' => [
                    'grid_style' => 'style_1'
                ],
            ]
        );

        $this->add_control(
            'box_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item-v1' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'grid_style' => 'style_1'
                ],
            ]
        );

        $this->add_responsive_control(
            'box_padding_top',
            [
                'label' => esc_html__( 'Padding Top', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 8,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item-v1' => 'padding-top: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'grid_style' => 'style_1'
                ],
            ]
        );

        $this->add_responsive_control(
            'box_padding_bottom',
            [
                'label' => esc_html__( 'Padding Bottom', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 8,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item-v1' => 'padding-bottom: {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'grid_style' => 'style_1'
                ],
            ]
        );

        $this->add_control(
            'post_image',
            [
                'label' => esc_html__( 'Image Style', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'grid_style' => 'style_1'
                ],
            ]
        );

        $this->add_responsive_control(
            'image_margin',
            [
                'label' => esc_html__( 'Margin left & right', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 8,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item-v1 .blog-post-thumb' => 'margin: 0px {{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'grid_style' => 'style_1'
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
                'condition' => [
                    'grid_style' => 'style_1'
                ],
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
                'condition' => [
                    'grid_style' => 'style_1'
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .blog-post-body',
                'condition' => [
                    'grid_style' => 'style_1'
                ],
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
                'condition' => [
                    'grid_style' => 'style_1'
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
                    '{{WRAPPER}} .blog-post-body' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;'
                ],
                'condition' => [
                    'grid_style' => 'style_1'
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
                'condition' => [
                    'grid_style' => 'style_1'
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
                    '{{WRAPPER}} .blog-post-item-v1 .blog-post-meta, .blog-post-item-v1 .blog-post-meta time' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .blog-post-item-v2 .blog-post-meta a, .blog-post-item-v2 .blog-post-meta time' => 'color: {{VALUE}}',
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
                'condition' => [
                    'grid_style' => 'style_1'
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
                'condition' => [
                    'grid_style' => 'style_1'
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

        $this->add_control(
            'continue_link_heading',
            [
                'label' => esc_html__( 'Continue Link', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'continue_link_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item .blog-post-link a' => 'color: {{VALUE}}',
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
                    '{{WRAPPER}} .blog-post-item .blog-post-link a:hover' => 'color: {{VALUE}}',
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'continue_link_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .blog-post-item .blog-post-link',
            ]
        );

        $this->add_control(
            'post_footer',
            [
                'label' => esc_html__( 'Footer', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'footer_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item-v1 .blog-post-author' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'grid_style' => 'style_1'
                ],
            ]
        );

        $this->add_control(
            'author_color',
            [
                'label'     => esc_html__( 'Author Text Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-author' => 'color: {{VALUE}}',
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'postauthor_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .blog-post-author',
            ]
        );

        $this->add_responsive_control(
            'author_padding',
            [
                'label' => __( 'Padding', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .blog-post-author' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'footer_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .blog-post-item-v2' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'grid_style' => 'style_2'
                ],
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Next Prev button
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_next_prev',
            [
                'label' => esc_html__( 'Next/Prev buttons', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'image_effects' );

        $this->start_controls_tab( 'normal',
            array(
                'label' => __( 'Normal', 'houzez-theme-functionality' ),
            )
        );
        $this->add_control(
            'np_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-carousel-buttons-wrap button' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .btn-view-all' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'np_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-carousel-buttons-wrap button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .btn-view-all' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'np_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-carousel-buttons-wrap button' => 'border: 1px solid {{VALUE}}',
                    '{{WRAPPER}} .btn-view-all' => 'border: 1px solid {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab( 'hover',
            array(
                'label' => __( 'Hover', 'houzez-theme-functionality' ),
            )
        );
    
        $this->add_control(
            'np_bg_color_hover',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-carousel-buttons-wrap button:hover' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .btn-view-all:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'np_color_hover',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-carousel-buttons-wrap button:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .btn-view-all:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'np_border_color_hover',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-carousel-buttons-wrap button:hover' => 'border: 1px solid {{VALUE}}',
                    '{{WRAPPER}} .btn-view-all:hover' => 'border: 1px solid {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();

        $this->start_controls_section(
            'hz_dots',
            [
                'label' => esc_html__( 'Carousel Dots', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'dots_size',
            [
                'label' => esc_html__( 'Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_space',
            [
                'label' => esc_html__( 'Space Between', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => -50,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_opacity',
            [
                'label' => esc_html__( 'Opacity', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'          => [
                    'px' => [
                        'min' => 1,
                        'max' => 99,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'opacity: 0.{{SIZE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_active_opacity',
            [
                'label' => esc_html__( 'Opacity Active', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'          => [
                    'px' => [
                        'min' => 1,
                        'max' => 99,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li.slick-active button:before' => 'opacity: 0.{{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'np_dots_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'np_dots_active_color',
            [
                'label'     => esc_html__( 'Active Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li.slick-active button:before' => 'color: {{VALUE}}',
                ],
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
        $houzez_local = houzez_get_localization();

        $args['grid_style'] =  $settings['grid_style'];
        $args['category_id'] =  $settings['category_id'];
        $args['posts_limit'] =  $settings['posts_limit'];
        $args['offset'] =  $settings['offset'];
        $args['slides_to_show'] =  $settings['slides_to_show'];
        $args['navigation'] =  $settings['navigation'];
        $args['slide_auto'] =  $settings['slide_auto'];
        $args['auto_speed'] =  $settings['auto_speed'];
        $args['slide_dots'] =  $settings['slide_dots'];
        $args['slide_infinite'] =  $settings['slide_infinite'];
       
        if( function_exists( 'houzez_blog_posts_carousel' ) ) {
            echo houzez_blog_posts_carousel( $args );
        }

        if ( Plugin::$instance->editor->is_edit_mode() ) : 
            $token = wp_generate_password(5, false, false);
            if (is_rtl()) {
                $houzez_rtl = "true";
            } else {
                $houzez_rtl = "false";
            }
            ?>

            <style>
                .slide-animated {
                    opacity: 1;
                }
            </style>
            <script>

                var slides_to_show = <?php echo absint($settings['slides_to_show']); ?>,
                    navigation = <?php echo ($settings['navigation'] === 'true') ? 'true' : 'false'; ?>,
                    auto_play = <?php echo ($settings['slide_auto'] === 'true') ? 'true' : 'false'; ?>,
                    auto_play_speed = <?php echo absint($settings['auto_speed']); ?>,
                    dots = <?php echo ($settings['slide_dots'] === 'true') ? 'true' : 'false'; ?>,
                    slide_infinite = <?php echo ($settings['slide_infinite'] === 'true') ? 'true' : 'false'; ?>;

                var owl_post_card = jQuery('#carousel-post-card-<?php echo esc_attr( $token ); ?>');

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
                    prevArrow: jQuery('.blog-prev-js'),
                    nextArrow: jQuery('.blog-next-js'),
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
            
            </script>
        
        <?php endif;
    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Blog_Posts_Carousel );