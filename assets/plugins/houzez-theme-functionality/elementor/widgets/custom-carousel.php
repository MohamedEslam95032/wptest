<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Text with icon Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.1
 */
class Houzez_Custom_Carousel extends Widget_Base {

    private $slide_prints_count = 0;

    /**
     * Get widget name.
     *
     * Retrieve Features Block widget name.
     *
     * @since 1.0.1
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_custom_carousel';
    }

    /**
     * Get widget title.
     *
     * Retrieve Features Block widget title.
     *
     * @since 1.0.1
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Custom Carousel', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve Features Block widget icon.
     *
     * @since 1.0.1
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
     * Retrieve the list of categories the Features Section widget belongs to.
     *
     * @since 1.0.1
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'houzez-elements' ];
    }

    public function get_keywords() {
        return [ 'custom', 'carousel' ];
    }

    /**
     * Register Features Block widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.1
     * @access protected
     */
    protected function register_controls() {

        //Content
        $this->start_controls_section(
            'section_slides',
            [
                'label' => esc_html__( 'Slides', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'carousel_title',
            [
                'label' => esc_html__( 'Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'carousel_tagline',
            [
                'label' => esc_html__( 'Tagline', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater = new Repeater();

        $this->add_repeater_controls( $repeater );

        $this->add_control(
            'slides',
            [
                'label' => esc_html__( 'Slides', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => $this->get_repeater_defaults(),
                'separator' => 'after',
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->add_control(
            'nav_position',
            [
                'label' => esc_html__( 'Nav Position', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'top-nav',
                'options' => [
                    'top-nav' => esc_html__('On Top', 'houzez-theme-functionality'),
                    'inside-nav' => esc_html__('Inside', 'houzez-theme-functionality'),
                ]
            ]
        );
        

        $this->end_controls_section();

        //Carousel Settings
        $this->start_controls_section(
            'carousel_settings',
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
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                "description" => '',
                'default' => '3',
            ]
        );
        $this->add_control(
            'slides_to_scroll',
            [
                'label'     => esc_html__('Slides To Scroll', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                "description" => '',
                'default' => '1',
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

        $this->start_controls_section(
            'section_carousel_title_style',
            [
                'label' => esc_html__( 'Carousel Title', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'nav_position',
                            'operator' => '===',
                            'value' => 'top-nav',
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'carousel_title',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                                [
                                    'name' => 'carousel_tagline',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'max_width',
            [
                'label' => esc_html__( 'Max Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 2000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-module-header' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'section_title_style',
            [
                'label' => esc_html__( 'Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'section_title_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-module-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'section_title_typography',
                'selector' => '{{WRAPPER}} .custom-carousel-module-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'section_title_text_stroke',
                'selector' => '{{WRAPPER}} .custom-carousel-module-title',
            ]
        );

        $this->add_control(
            'tagline_title_style',
            [
                'label' => esc_html__( 'Tagline', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'tagline_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-module-description' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tagline_typography',
                'selector' => '{{WRAPPER}} .custom-carousel-module-description',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_slides_style',
            [
                'label' => esc_html__( 'Slides', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_responsive_control(
            'space_between',
            [
                'label' => esc_html__( 'Space Between', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-slide' => 'padding-left:{{SIZE}}{{UNIT}};padding-right:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'slide_background_color',
            [
                'label' => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slide_border',
                'selector' => '{{WRAPPER}} .custom-carousel-slide-container',
            ]
        );

        $this->add_control(
            'slide_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'before',
                'isLinked' => 'true',
            ]
        );


        $this->add_control(
            'slide_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'before',
                'isLinked' => 'true',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_image',
            [
                'label' => esc_html__( 'Image', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__( 'Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'space',
            [
                'label' => esc_html__( 'Max Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'object-fit',
            [
                'label' => esc_html__( 'Object Fit', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'condition' => [
                    'height[size]!' => '',
                ],
                'options' => [
                    '' => esc_html__( 'Default', 'houzez-theme-functionality' ),
                    'fill' => esc_html__( 'Fill', 'houzez-theme-functionality' ),
                    'cover' => esc_html__( 'Cover', 'houzez-theme-functionality' ),
                    'contain' => esc_html__( 'Contain', 'houzez-theme-functionality' ),
                    'scale-down' => esc_html__( 'Scale Down', 'houzez-theme-functionality' ),
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'object-position',
            [
                'label' => esc_html__( 'Object Position', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'center center' => esc_html__( 'Center Center', 'houzez-theme-functionality' ),
                    'center left' => esc_html__( 'Center Left', 'houzez-theme-functionality' ),
                    'center right' => esc_html__( 'Center Right', 'houzez-theme-functionality' ),
                    'top center' => esc_html__( 'Top Center', 'houzez-theme-functionality' ),
                    'top left' => esc_html__( 'Top Left', 'houzez-theme-functionality' ),
                    'top right' => esc_html__( 'Top Right', 'houzez-theme-functionality' ),
                    'bottom center' => esc_html__( 'Bottom Center', 'houzez-theme-functionality' ),
                    'bottom left' => esc_html__( 'Bottom Left', 'houzez-theme-functionality' ),
                    'bottom right' => esc_html__( 'Bottom Right', 'houzez-theme-functionality' ),
                ],
                'default' => 'center center',
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container img' => 'object-position: {{VALUE}};',
                ],
                'condition' => [
                    'height[size]!' => '',
                    'object-fit' => [ 'cover', 'contain', 'scale-down' ],
                ],
            ]
        );

        $this->add_control(
            'separator_panel_style',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $this->start_controls_tabs( 'image_effects' );

        $this->start_controls_tab( 'normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'opacity',
            [
                'label' => esc_html__( 'Opacity', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .custom-carousel-slide-container img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab( 'hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'opacity_hover',
            [
                'label' => esc_html__( 'Opacity', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container:hover img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .custom-carousel-slide-container:hover img',
            ]
        );

        $this->add_control(
            'background_hover_transition',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ) . ' (s)',
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container img' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .custom-carousel-slide-container img',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-slide-container img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .custom-carousel-slide-container img',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_gap',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'default' => [
                    'top' => 24,
                    'right' => 24,
                    'bottom' => 24,
                    'left' => 24,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .hz-carousel-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hz-carousel-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .hz-carousel-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .hz-carousel-content',
            ]
        );

        $this->add_control(
            'name_title_style',
            [
                'label' => esc_html__( 'Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hz-carousel-content-wrap h2' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}} .hz-carousel-content-wrap h2',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'read_more_style',
            [
                'label' => esc_html__( 'Read More Button', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .btn-primary',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .btn-primary',
                
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style', [
            
        ] );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
                
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-primary' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
                
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .btn-primary',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                    ],
                ],
                
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
                
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .btn-primary:hover, {{WRAPPER}} .btn-primary:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .btn-primary:hover svg, {{WRAPPER}} .btn-primary:focus svg' => 'fill: {{VALUE}};',
                ],
                
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .btn-primary:hover, {{WRAPPER}} .btn-primary:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
                
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .btn-primary:hover, {{WRAPPER}} .btn-primary:focus' => 'border-color: {{VALUE}};',
                ],
                
            ]
        );

        $this->add_control(
            'button_hover_transition_duration',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .btn-primary' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .btn-primary',
                'separator' => 'before',
                
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-primary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .btn-primary',
                
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-primary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
                
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'navigation_button_style',
            [
                'label' => esc_html__( 'Navigation Buttons', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'nav_button_style', [
            
        ] );

        $this->start_controls_tab(
            'nav_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'nav_btn_text_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined:before' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'nav_btn_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                    ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'nav_button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'nav_btn_hover_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined:hover:before, {{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined:focus:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'nav_button_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined:hover, {{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
            ]
        );

        $this->add_control(
            'nav_button_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined:hover, {{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'nav_button_hover_transition_duration',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'nav_btn_border',
                'selector' => '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'nav_btn_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'nav_button_box_shadow',
                'selector' => '{{WRAPPER}} .custom-carousel-buttons-wrap .btn-primary-outlined',
            ]
        );

        $this->end_controls_section();

    }

    protected function add_repeater_controls( Repeater $repeater ) {
        
        $repeater->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Slide Title', 'houzez-theme-functionality' ),
                'dynamic' => [
                    'active' => true,
                ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $repeater->add_control(
            'content',
            [
                'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__( 'Image', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'show_btn',
            [
                'label' => esc_html__( 'More Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $repeater->add_control(
            'read_more_text',
            [
                'label' => esc_html__( 'Read More Text', 'houzez-theme-functionality' ),
                'type'  => Controls_Manager::TEXT,
                'default' => 'Read More',
                'condition' => [
                    'show_btn' => 'yes'
                ]
            ]   
        );
        $repeater->add_control(
            'read_more_link',
            [
                'label' => esc_html__( 'Read More Link', 'houzez-theme-functionality' ),
                'type'  => Controls_Manager::URL,
                'condition' => [
                    'show_btn' => 'yes',
                    'read_more_text!' => ''
                ]
            ]
        );
    }

    protected function get_repeater_defaults() {
        $placeholder_image_src = Utils::get_placeholder_image_src();

        return [
            [
                'content' => esc_html__( 'Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Debitis, sequi sed tempora qui ad accusantium eius, ab quo adipisci beatae illo, distinctio numquam veritatis autem obcaecati blanditiis consectetur consequatur perspiciatis..', 'houzez-theme-functionality' ),
                'title' => esc_html__( 'Slide Title', 'houzez-theme-functionality' ),
                'image' => [
                    'url' => $placeholder_image_src,
                ],
                'show_btn' => 'yes',
                'read_more_text' => 'Read More'
            ],
            [
                'content' => esc_html__( 'Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Debitis, sequi sed tempora qui ad accusantium eius, ab quo adipisci beatae illo, distinctio numquam veritatis autem obcaecati blanditiis consectetur consequatur perspiciatis..', 'houzez-theme-functionality' ),
                'title' => esc_html__( 'Slide Title', 'houzez-theme-functionality' ),
                'image' => [
                    'url' => $placeholder_image_src,
                ],
                'show_btn' => 'yes',
                'read_more_text' => 'Read More'
            ],
            [
                'content' => esc_html__( 'Lorem ipsum, dolor sit amet, consectetur adipisicing elit. Debitis, sequi sed tempora qui ad accusantium eius, ab quo adipisci beatae illo, distinctio numquam veritatis autem obcaecati blanditiis consectetur consequatur perspiciatis..', 'houzez-theme-functionality' ),
                'title' => esc_html__( 'Slide Title', 'houzez-theme-functionality' ),
                'image' => [
                    'url' => $placeholder_image_src,
                ],
                'show_btn' => 'yes',
                'read_more_text' => 'Read More'
            ],
        ];
    }
    
    protected function print_slide( array $slide, array $settings, $element_key ) {
        $lazyload = 'yes' === $this->get_settings( 'lazyload' );

        $this->add_render_attribute( $element_key . '-hz-custom-carousel', [
            'class' => 'custom-carousel-slide-container',
        ] );

        $show_btn = $slide['show_btn'];
        $read_more_link = $slide['read_more_link']['url'] ?? '';
        $readmore_text = $slide['read_more_text'];
        $is_external = $slide['read_more_link']['is_external'] ?? '';

        if ( ! empty( $slide['image']['url'] ) ) {
            $img_src = $slide['image']['url'];

            if ( $lazyload ) {
                $img_attribute['class'] = 'img-fluid';
                $img_attribute['data-src'] = $img_src;
            } else {
                $img_attribute['src'] = $img_src;
                $img_attribute['class'] = 'img-fluid';
            }

            $img_attribute['alt'] = ! empty( $slide['image']['alt'] ) ? $slide['image']['alt'] : $slide['title'];

            $this->add_render_attribute( $element_key . '-image', $img_attribute );
        }
        ?>
        <div <?php $this->print_render_attribute_string( $element_key . '-hz-custom-carousel' ); ?>>
            <div class="row">
                <?php if ( $slide['image']['url'] ) { ?>
                <div class="col-12">
                    <img <?php $this->print_render_attribute_string( $element_key . '-image' ); ?>>
                </div>
                <?php } ?>
                <div class="col-12">
                    <div class="hz-carousel-content-wrap">
                        <?php
                        if ( ! empty( $slide['title'] ) ) { ?>
                        <h2><?php echo $slide['title']; ?></h2>
                        <?php } ?>

                        <div class="hz-carousel-content">
                            <?php // PHPCS - the main text of a widget should not be escaped.
                            if ( $slide['content'] ) { ?>
                                <p><?php echo $slide['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                            <?php } ?>
                        </div>

                        <?php if( $show_btn == 'yes' && ! empty($readmore_text) ) { ?>
                        <a href="<?php echo esc_url($read_more_link); ?>" <?php if($is_external == 'on') { echo 'target="_blank"'; } ?> class="btn btn-primary"><?php echo esc_html($readmore_text); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    protected function print_slider( ?array $settings = null ) {
        if ( null === $settings ) {
            $settings = $this->get_settings_for_display();
        }

        $args = array(
            'slides_to_show' => $settings['slides_to_show'],
            'slides_to_scroll' => $settings['slides_to_scroll'],
            'slide_infinite' => $settings['slide_infinite'],
            'slide_auto' => $settings['slide_auto'],
            'auto_speed' => $settings['auto_speed'],
            'navigation' => $settings['navigation'],
            'slide_dots' => $settings['slide_dots']
        );

        $slides_count = count( $settings['slides'] );
        $token = wp_generate_password(5, false, false);
        ?>
        <div class="custom-carousel-module custom-carousel-js-wrap-<?php echo $token; ?>">
            <div class="custom-carousel-module-header">
                <div class="custom-carousel-module-header-left">
                    <?php if( $settings['carousel_title'] != "" ) { ?>
                    <h2 class="custom-carousel-module-title"><?php echo $settings['carousel_title']; ?></h2>
                    <?php } ?>
                    <?php if( $settings['carousel_tagline'] != "" ) { ?>
                    <div class="custom-carousel-module-description"><?php echo $settings['carousel_tagline']; ?></div>
                    <?php } ?>
                </div><!-- custom-carousel-module-header-left -->
                
                <?php if( $settings['navigation'] === 'true' ) { ?>
                <div class="custom-carousel-module-header-right custom-carousel-buttons-wrap <?php echo esc_attr($settings['nav_position']);?>">
                    <button type="button" class="slick-prev slick-prev-js-<?php echo $token; ?> btn-primary-outlined"></button>
                    <button type="button" class="slick-next slick-next-js-<?php echo $token; ?> btn-primary-outlined"></button>
                </div><!-- .custom-carousel-module-header-right -->
                <?php } ?>
            </div><!-- .custom-carousel-module-header -->
            
            <div class="custom-carousel-wrap">
                <div class="custom-carousel houzez-all-slider-wrap custom-carousel-js-<?php echo $token; ?>" data-token="<?php echo $token; ?>" data-carousel='<?php echo json_encode($args); ?>'>
                    <?php
                    foreach ( $settings['slides'] as $index => $slide ) :
                        $this->slide_prints_count++;
                        ?>
                        <div class="custom-carousel-slide">
                            <?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
                        </div>
                    <?php endforeach; ?>
                </div><!-- .custom-carousel -->
            </div><!-- .custom-carousel-wrap -->
        </div><!-- .custom-carousel-module -->
        <?php
    }

    protected function render() {
        $this->print_slider();
    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Custom_Carousel); 