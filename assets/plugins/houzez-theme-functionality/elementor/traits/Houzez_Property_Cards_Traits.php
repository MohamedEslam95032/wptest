<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Houzez_Property_Cards_Traits {

    /*--------------------------------------------------------------------------------
    * Carousel Next/Prev & Dots
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_btn_Traits() {
        $this->start_controls_section(
            'hz_action_buttons_cards',
            [
                'label' => esc_html__( 'Button', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'hz_btn_area_padding',
            [
                'label'      => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .item-wrap-v9 .item-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'style_btn_tabs'
        );

        $this->start_controls_tab(
            'style_btn_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );

        $this->add_control(
            'detail_btn_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'detail_btn_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'detail_btn_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_btn_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );

        $this->add_control(
            'detail_btn_hover',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'detail_button_bg_hover',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'detail_btn_border_hover_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border_type',
                'selector' => '{{WRAPPER}} .btn-item',
                'exclude' => ['color']
            ]
        );

        $this->add_control(
            'bttn_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /*--------------------------------------------------------------------------------
    * Carousel Next/Prev & Dots
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Carousel_Navi_Traits() {
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
                    '{{WRAPPER}} .btn-primary-outlined:before' => 'color: {{VALUE}}',
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
                    '{{WRAPPER}} .btn-primary-outlined:hover:before' => 'color: {{VALUE}}',
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
                'condition' => [
                    'slide_dots' => 'true'
                ]
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
                'default' => [
                    'unit' => 'px',
                    'size' => 7,
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
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
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


    /*--------------------------------------------------------------------------------
    * Carousel Traits
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Carousel_Traits() {
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
            'nav_position',
            [
                'label'     => esc_html__('Navigation Position', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '' => esc_html__('Top', 'houzez-theme-functionality' ),
                    'property-carousel-arrow-buttons-bottom' => esc_html__('Bottom', 'houzez-theme-functionality' )
                    
                ],
                "description" => '',
                'default' => '',
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

    }

    /*--------------------------------------------------------------------------------
    * Pagination
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Pagination_Traits() {
        $this->start_controls_section(
            'hz_pagination',
            [
                'label' => esc_html__( 'Pagination', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'style_pagination_tabs'
        );

        $this->start_controls_tab(
            'style_pagination_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );

        $this->add_control(
            'pagi_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .page-link' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .btn-load-more' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'pagi_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .page-link' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .btn-load-more' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'pagi_bg_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-load-more' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'pagination_type' => 'loadmore',
                ],
            ]
        );
        

        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_pagination_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );

        $this->add_control(
            'pagi_color_hover',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .page-link:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .btn-load-more:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'pagi_bg_color_hover',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .page-link:hover' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .btn-load-more:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'pagi_border_color_hover',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-load-more:hover' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'pagination_type' => 'loadmore',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'pagi_bg_color_active',
            [
                'label'     => esc_html__( 'Background Color Active', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .page-item.active .page-link' => 'background-color: {{VALUE}}; border-color: {{VALUE}}',
                ],
                'condition' => [
                    'pagination_type' => 'number',
                ],
                'separator' => 'before'
            ]
        );
        $this->add_control(
            'pagi_color_active',
            [
                'label'     => esc_html__( 'Color Active', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .page-item.active .page-link' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'pagination_type' => 'number',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /*--------------------------------------------------------------------------------
    * Card Colors
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Colors_Traits() {
        
        $this->add_control(
            'price_color',
            [
                'label'     => esc_html__( 'Price', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-price-wrap' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-v5-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->start_controls_tabs(
            'style_colors_tabs'
        );

        $this->start_controls_tab(
            'style_color_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__( 'Title Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-title a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .property-carousel-module-v3-4cols .item-wrap-v3 .item-wrap-outside .item-title a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'address_color',
            [
                'label'     => esc_html__( 'Address Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-address' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'icons_color',
            [
                'label'     => esc_html__( 'Icons', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-amenities i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'figure_color',
            [
                'label'     => esc_html__( 'Figure', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .hz-figure' => 'color: {{VALUE}}'
                ],
            ]
        );

        $this->add_control(
            'labels_color',
            [
                'label'     => esc_html__( 'Labels', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-amenities-text' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .h-type span' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-wrap-v2 .item-amenities li' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .area_postfix' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-v5-type' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_bg_color',
            [
                'label'     => esc_html__( 'Item Tools Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color',
            [
                'label'     => esc_html__( 'Item Tools Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_color_hover_normal_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label'     => esc_html__( 'Title Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-title a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_bg_color_hover',
            [
                'label'     => esc_html__( 'Item Tools Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color_hover',
            [
                'label'     => esc_html__( 'Item Tools Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
    }

    /*--------------------------------------------------------------------------------
    * Card card box for multi-grid
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Box_MultiGrid_Traits() {
        $this->start_controls_section(
            'hz_grid_box_shadow',
            [
                'label' => esc_html__( 'Card Box', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => '!=',
                            'value' => array('cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => '!=',
                            'value' => array('v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => '!=',
                            'value' => array('list-view-v4'),
                        ],
                    ],
                ],
            ]
        );

        $this->Property_Cards_Box_Traits();

        $this->add_control(
            'ft_border_type',
            [
                'label' => esc_html__( 'Box Footer Border Type', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => array(
                    '' => esc_html__('Default', 'houzez-theme-functionality'),
                    'none' => esc_html__('None', 'houzez-theme-functionality'),
                    'solid' => esc_html__('Solid', 'houzez-theme-functionality'),
                    'dashed' => esc_html__('Dashed', 'houzez-theme-functionality'),
                    'dotted' => esc_html__('Dotted', 'houzez-theme-functionality'),
                    'groove' => esc_html__('Groove', 'houzez-theme-functionality'),
                    'double' => esc_html__('Double', 'houzez-theme-functionality'),
                    'ridge' => esc_html__('Ridge', 'houzez-theme-functionality'),
                ),
                'selectors' => [
                    '{{WRAPPER}} .item-footer' => 'border-top-style: {{VALUE}};',
                    '{{WRAPPER}} .item-wrap-v9 .item-footer' => 'border-left-style: {{VALUE}}; border-right-style: {{VALUE}}; border-bottom-style: {{VALUE}};',
                    '{{WRAPPER}} .item-wrap-v9 .item-body' => 'border-left-style: {{VALUE}}; border-right-style: {{VALUE}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v4'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_4'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1'),
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'ft_meta_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-footer' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .item-wrap-v9 .item-body' => 'border-color: {{VALUE}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v4', 'cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_4', 'v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'grid-view-v7', 'list-view-v7', 'list-view-v4'),
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Image 
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_grid_images_styles',
            [
                'label' => esc_html__( 'Image Radius', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => '!=',
                            'value' => array('cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => '!=',
                            'value' => array('v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => '!=',
                            'value' => array('list-view-v4'),
                        ],
                    ],
                ],
            ]
        );

        $this->Property_Cards_Image_Traits();

        $this->end_controls_section();
    }

    /*--------------------------------------------------------------------------------
    * Card typography for multi-grid
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Typography_MultiGrid_Traits() {

        $this->start_controls_section(
            'typography_multi_section',
            [
                'label'     => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_property_title',
                'label'    => esc_html__( 'Property Title', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_prop_address',
                'label'    => esc_html__( 'Address', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} address.item-address',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v4', 'cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_4', 'v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2', 'list-view-v7', 'grid-view-v7', 'list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_prop_excerpt',
                'label'    => esc_html__( 'Excerpt', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-short-description',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v4', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_4', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_meta_labels',
                'label'    => esc_html__( 'Meta Labels', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-amenities-text',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v4', 'cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_4', 'v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2', 'list-view-v7', 'grid-view-v7', 'list-view-v4'),
                        ],
                    ],
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_meta_figure',
                'label'    => esc_html__( 'Meta Figure', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .hz-figure',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_price',
                'label'    => esc_html__( 'Price', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-price, {{WRAPPER}} .item-v5-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_subprice',
                'label'    => esc_html__( 'Sub Price', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-sub-price',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v4', 'cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_4', 'v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2', 'list-view-v7', 'grid-view-v7', 'list-view-v4'),
                        ],
                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_types',
                'label'    => esc_html__( 'Property Type', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .h-type span, {{WRAPPER}} .item-v5-type',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v4', 'cards-v5', 'cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_4', 'v_5', 'v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2', 'list-view-v5', 'grid-view-v5', 'list-view-v7', 'grid-view-v7', 'list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_area-postfix',
                'label'    => esc_html__( 'Area Postfix', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .area_postfix',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v3', 'cards-v4', 'cards-v5', 'cards-v7'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_3', 'v_4', 'v_5', 'v_7'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2', 'grid-view-v3', 'list-view-v5', 'grid-view-v5', 'list-view-v7', 'grid-view-v7'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_btn-item',
                'label'    => esc_html__( 'Detail Button', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .btn-item',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v4'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_4'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_agent',
                'label'    => esc_html__( 'Agent', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-author a',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v4', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_4', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2', 'list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_date',
                'label'    => esc_html__( 'Date', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-date',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v4'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_4'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_multigrid-btn-item',
                'label'    => esc_html__( 'Buttons', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .btn-item',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v7', 'grid-view-v7', 'list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

    
        $this->end_controls_section();
    }

    /*--------------------------------------------------------------------------------
    * Card show/hide for multi-grid
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Show_Hide_MultiGrid_Traits() {
        $this->start_controls_section(
            'hide_show_section',
            [
                'label'     => esc_html__( 'Show/Hide Data', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'hide_description',
            [
                'label' => esc_html__( 'Hide Description', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .item-short-description' => 'display: {{VALUE}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'list-view-v2', 'grid-view-v1', 'grid-view-v2'),
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'hide_compare',
            [
                'label' => esc_html__( 'Hide Compare Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tools .item-compare' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_favorite',
            [
                'label' => esc_html__( 'Hide Favorite Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tools .item-favorite' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_preview',
            [
                'label' => esc_html__( 'Hide Preview Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tools .item-preview' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_featured_label',
            [
                'label' => esc_html__( 'Hide Featured Label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .label-featured' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_status',
            [
                'label' => esc_html__( 'Hide Status', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .labels-wrap .label-status' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_label',
            [
                'label' => esc_html__( 'Hide Labels', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .labels-wrap .hz-label' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_button',
            [
                'label' => esc_html__( 'Hide Details Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1'),
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'hide_author_date',
            [
                'label' => esc_html__( 'Hide Date & Agent', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'list-view-v2', 'grid-view-v1', 'grid-view-v2'),
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }


    /*--------------------------------------------------------------------------------
    * Card Colors for multi-grid
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Colors_MultiGrid_Traits() {
        
        $this->start_controls_section(
            'hz_grid_colors',
            [
                'label' => esc_html__( 'Colors', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'price_color',
            [
                'label'     => esc_html__( 'Price', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-price-wrap' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-v5-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->start_controls_tabs(
            'style_colors_tabs'
        );

        $this->start_controls_tab(
            'style_color_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__( 'Title Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-title a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'address_color',
            [
                'label'     => esc_html__( 'Address Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-address' => 'color: {{VALUE}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v4', 'cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_4', 'v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2', 'list-view-v7', 'grid-view-v7', 'list-view-v4'),
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'icons_color',
            [
                'label'     => esc_html__( 'Icons', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-amenities i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'figure_color',
            [
                'label'     => esc_html__( 'Figure', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .hz-figure' => 'color: {{VALUE}}'
                ],
            ]
        );

        $this->add_control(
            'labels_color',
            [
                'label'     => esc_html__( 'Labels', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-amenities-text' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .h-type span' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-wrap-v2 .item-amenities li' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .area_postfix' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-v5-type' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_bg_color',
            [
                'label'     => esc_html__( 'Item Tools Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color',
            [
                'label'     => esc_html__( 'Item Tools Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_color_hover_normal_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label'     => esc_html__( 'Title Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-title a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_bg_color_hover',
            [
                'label'     => esc_html__( 'Item Tools Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color_hover',
            [
                'label'     => esc_html__( 'Item Tools Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'excerpt_color',
            [
                'label'     => esc_html__( 'Excerpt Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-short-description' => 'color: {{VALUE}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v4', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_4', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v4'),
                        ],

                    ],
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'author_color',
            [
                'label'     => esc_html__( 'Agent & Date', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-author a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-author' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-date' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v2', 'cards-v4', 'cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_2', 'v_4', 'v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v2', 'grid-view-v2', 'list-view-v7', 'grid-view-v7', 'list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Button
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_buttons_multigrid_cards',
            [
                'label' => esc_html__( 'Button', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v1', 'cards-v4', 'cards-v7', 'cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_1', 'v_4', 'v_7', 'v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v1', 'grid-view-v1', 'list-view-v7', 'grid-view-v7', 'list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->start_controls_tabs(
            'style_btn_tabs',
        );

        $this->start_controls_tab(
            'style_btn_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );

        $this->add_control(
            'detail_btn_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-buttons-wrap .btn' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'detail_btn_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .item-buttons-wrap .btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btc_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .item-buttons-wrap .btn' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_btn_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );

        $this->add_control(
            'detail_btn_hover',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-buttons-wrap .btn:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'detail_button_bg_hover',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .item-buttons-wrap .btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btc_border_hover_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-item:hover' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .item-buttons-wrap .btn:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'btn_360_color',
            [
                'label'     => esc_html__( '360° Button Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-header-2 .btn-360' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_control(
            'btn_360_bg_color',
            [
                'label'     => esc_html__( '360° Button Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-header-2 .btn-360' => 'background-color: {{VALUE}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_control(
            'btn_video_color',
            [
                'label'     => esc_html__( 'Video Button Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-header-2 .btn-video' => 'color: {{VALUE}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->add_control(
            'btn_video_bg_color',
            [
                'label'     => esc_html__( 'Video Button Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-header-2 .btn-video' => 'background-color: {{VALUE}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'grid_style',
                            'operator' => 'in',
                            'value' => array('cards-v8'),
                        ],
                        [
                            'name' => 'prop_grid_style',
                            'operator' => 'in',
                            'value' => array('v_8'),
                        ],
                        [
                            'name' => 'listings_layout',
                            'operator' => 'in',
                            'value' => array('list-view-v4'),
                        ],

                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    /*--------------------------------------------------------------------------------
    * Card Box
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Box_Traits() {
        
        $this->add_control(
            'box_background',
            [
                'label' => esc_html__( 'Background', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-wrap, {{WRAPPER}} .item-footer' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'padding',
            [
                'label' => __( 'Box Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .item-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_border',
                'selector' => '{{WRAPPER}} .item-wrap',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'radius',
            [
                'label' => __( 'Box Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .item-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .item-wrap',
            ]
        );
    }

    /*--------------------------------------------------------------------------------
    * Image
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Image_Traits() {

        $this->add_responsive_control(
            'image_radius',
            [
                'label' => __( 'Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .grid-view .item-wrap.item-wrap-no-frame .hover-effect' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    }

    /*--------------------------------------------------------------------------------
    * Show/Hide 
    * -------------------------------------------------------------------------------*/
    public function Property_Cards_Show_Hide_Traits() {
        $this->add_control(
            'hide_compare',
            [
                'label' => esc_html__( 'Hide Compare Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-cards-module .item-tools .item-compare' => 'display: {{VALUE}};',
                    '{{WRAPPER}} .property-carousel-module .item-tools .item-compare' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_favorite',
            [
                'label' => esc_html__( 'Hide Favorite Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-cards-module .item-tools .item-favorite' => 'display: {{VALUE}};',
                    '{{WRAPPER}} .property-carousel-module .item-tools .item-favorite' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_preview',
            [
                'label' => esc_html__( 'Hide Preview Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-cards-module .item-tools .item-preview' => 'display: {{VALUE}};',
                    '{{WRAPPER}} .property-carousel-module .item-tools .item-preview' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_featured_label',
            [
                'label' => esc_html__( 'Hide Featured Label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-cards-module .label-featured' => 'display: {{VALUE}};',
                    '{{WRAPPER}} .property-carousel-module .label-featured' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_status',
            [
                'label' => esc_html__( 'Hide Status', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-cards-module .labels-wrap .label-status' => 'display: {{VALUE}};',
                    '{{WRAPPER}} .property-carousel-module .labels-wrap .label-status' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_label',
            [
                'label' => esc_html__( 'Hide Labels', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-cards-module .labels-wrap .hz-label' => 'display: {{VALUE}};',
                    '{{WRAPPER}} .property-carousel-module .labels-wrap .hz-label' => 'display: {{VALUE}};',
                ],
            ]
        );
    }
}
