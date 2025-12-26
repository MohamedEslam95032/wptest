<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Properties Widget.
 * @since 2.0
 */
class Houzez_Elementor_properties_slider extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 2.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_properties_slider';
    }

    /**
     * Get widget title.
     * @since 2.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Properties Slider', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 2.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-slides';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 2.0
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
     * @since 2.0
     * @access protected
     */
    protected function register_controls() {
        $prop_types = array();
        $prop_status = array();
        
        houzez_get_terms_array( 'property_status', $prop_status );
        houzez_get_terms_array( 'property_type', $prop_types );

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'posts_limit',
            [
                'label'     => esc_html__('Number of properties', 'houzez-theme-functionality'),
                'type'      => Controls_Manager::TEXT,
                'description' => '',
                'default' => '9',
            ]
        );

        $this->add_control(
            'property_type',
            [
                'label'     => esc_html__( 'Property Type', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT2,
                'options'   => $prop_types,
                'description' => '',
                'multiple' => true,
                'default' => '',
            ]
        );

        $this->add_control(
            'property_status',
            [
                'label'     => esc_html__( 'Property Status', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT2,
                'options'   => $prop_status,
                'description' => '',
                'multiple' => true,
                'default' => '',
            ]
        );
        $this->end_controls_section();

        // Content Display Controls
        $this->start_controls_section(
            'content_display_section',
            [
                'label' => esc_html__( 'Content Display', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__( 'Show Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => esc_html__( 'Show Price', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'show_address',
            [
                'label' => esc_html__( 'Show Address', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'show_features',
            [
                'label' => esc_html__( 'Show Features/Metadata', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'show_author',
            [
                'label' => esc_html__( 'Show Author', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => esc_html__( 'Show Date', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'show_featured_label',
            [
                'label' => esc_html__( 'Show Featured Label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'show_details_button',
            [
                'label' => esc_html__( 'Show Details Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'features_icon_size',
            [
                'label' => esc_html__( 'Features Icon Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .item-amenities i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_features' => 'yes',
                ],
            ]
        );


        $this->end_controls_section();

        // Box Layout
        $this->start_controls_section(
            'box_layout_section',
            [
                'label' => esc_html__( 'Box Layout', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'box_align',
            [
                'label' => esc_html__( 'Box Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'flex-start',
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item-wrap' => 'justify-content: {{VALUE}}; align-items: center;',
                ],
            ]
        );

        $this->add_control(
            'box_margin',
            [
                'label' => esc_html__( 'Box Margin From Edge', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item-wrap' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'content_align',
            [
                'label' => esc_html__( 'Content Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .property-slider-item .item-amenities' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .property-slider-item .h-type' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'box_width',
            [
                'label' => esc_html__( 'Box Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 800,
                        'step' => 10,
                    ],
                    '%' => [
                        'min' => 30,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 500,
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item' => 'width: {{SIZE}}{{UNIT}}; margin-left: 0; margin-right: 0;',
                ],
            ]
        );

        $this->add_control(
            'box_padding',
            [
                'label' => esc_html__( 'Box Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'box_bg_color',
            [
                'label' => esc_html__( 'Box Background Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'box_border_radius',
            [
                'label' => esc_html__( 'Box Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Typography Controls
        $this->start_controls_section(
            'typography_section',
            [
                'label' => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__( 'Title Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-slider-item .item-title',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => esc_html__( 'Price Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-slider-item .item-price-wrap',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'address_typography',
                'label' => esc_html__( 'Address Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-slider-item .item-address',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'features_typography',
                'label' => esc_html__( 'Features Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-slider-item .item-amenities',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'author_typography',
                'label' => esc_html__( 'Author Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-slider-item .item-author a',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'date_typography',
                'label' => esc_html__( 'Date Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-slider-item .item-date',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'property_type_typography',
                'label' => esc_html__( 'Property Type Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-slider-item .h-type span',
            ]
        );

        $this->end_controls_section();

        // Color Controls
        $this->start_controls_section(
            'color_section',
            [
                'label' => esc_html__( 'Colors', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .item-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__( 'Title Hover Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .item-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => esc_html__( 'Price Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .item-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'address_color',
            [
                'label' => esc_html__( 'Address Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .item-address' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'features_color',
            [
                'label' => esc_html__( 'Features Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .item-amenities' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'author_color',
            [
                'label' => esc_html__( 'Author Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .item-author a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'date_color',
            [
                'label' => esc_html__( 'Date Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .item-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'property_type_color',
            [
                'label' => esc_html__( 'Property Type Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .h-type' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__( 'Overlay Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item-wrap::before' => 'background-color: {{VALUE}}; content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none;',
                ],
            ]
        );

        $this->add_control(
            'overlay_opacity',
            [
                'label' => esc_html__( 'Overlay Opacity', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 0.3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item-wrap::before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Button Styling
        $this->start_controls_section(
            'button_style_section',
            [
                'label' => esc_html__( 'Details Button', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => esc_html__( 'Button Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-slider-item .btn-item',
            ]
        );

        $this->start_controls_tabs( 'button_effects' );

        $this->start_controls_tab( 'button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .btn-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .btn-item' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .btn-item' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab( 'button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'button_bg_color_hover',
            [
                'label' => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .btn-item:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color_hover',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .btn-item:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_hover',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .btn-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'button_padding',
            [
                'label' => esc_html__( 'Button Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .btn-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .property-slider-item .btn-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

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
                    '{{WRAPPER}} .property-slider button' => 'background-color: {{VALUE}}',
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
                    '{{WRAPPER}} .property-slider button::before' => 'color: {{VALUE}}',
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
                    '{{WRAPPER}} .property-slider button' => 'border: 1px solid {{VALUE}}',
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
                    '{{WRAPPER}} .property-slider button:hover' => 'background-color: {{VALUE}}',
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
                    '{{WRAPPER}} .property-slider button:hover::before' => 'color: {{VALUE}}',
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
                    '{{WRAPPER}} .property-slider button:hover' => 'border: 1px solid {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();

    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 2.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();
        $property_type = $property_status = '';

        if(!empty($settings['property_type'])) {
            $property_type = implode (",", $settings['property_type']);
        }

        if(!empty($settings['property_status'])) {
            $property_status = implode (",", $settings['property_status']);
        }

        $args['posts_limit'] =  $settings['posts_limit'];
        $args['property_type']   =  $property_type;
        $args['property_status']   =  $property_status;

        // Flag to indicate this is from Elementor
        $args['_elementor'] = true;

        // Content Display Settings
        $args['show_title'] = $settings['show_title'] ?? '';
        $args['show_price'] = $settings['show_price'] ?? '';
        $args['show_address'] = $settings['show_address'] ?? '';
        $args['show_features'] = $settings['show_features'] ?? '';
        $args['show_author'] = $settings['show_author'] ?? '';
        $args['show_date'] = $settings['show_date'] ?? '';
        $args['show_featured_label'] = $settings['show_featured_label'] ?? '';
        $args['show_details_button'] = $settings['show_details_button'] ?? '';

        // Box Layout Settings
        $args['box_align'] = $settings['box_align'] ?? '';
        $args['content_align'] = $settings['content_align'] ?? '';
        $args['box_width'] = $settings['box_width'] ?? '';
        $args['box_padding'] = $settings['box_padding'] ?? '';
        $args['box_bg_color'] = $settings['box_bg_color'] ?? '';
        $args['box_border_radius'] = $settings['box_border_radius'] ?? '';

        // Typography Settings - these are handled by Elementor's selectors, no need to pass them

        // Color Settings
        $args['title_color'] = $settings['title_color'] ?? '';
        $args['title_hover_color'] = $settings['title_hover_color'] ?? '';
        $args['price_color'] = $settings['price_color'] ?? '';
        $args['address_color'] = $settings['address_color'] ?? '';
        $args['features_color'] = $settings['features_color'] ?? '';
        $args['author_color'] = $settings['author_color'] ?? '';
        $args['date_color'] = $settings['date_color'] ?? '';
        $args['overlay_color'] = $settings['overlay_color'] ?? '';
        $args['overlay_opacity'] = $settings['overlay_opacity'] ?? '';

        // Button Settings
        $args['button_bg_color'] = $settings['button_bg_color'] ?? '';
        $args['button_color'] = $settings['button_color'] ?? '';
        $args['button_border_color'] = $settings['button_border_color'] ?? '';
        $args['button_bg_color_hover'] = $settings['button_bg_color_hover'] ?? '';
        $args['button_color_hover'] = $settings['button_color_hover'] ?? '';
        $args['button_border_color_hover'] = $settings['button_border_color_hover'] ?? '';
        $args['button_padding'] = $settings['button_padding'] ?? '';
        $args['button_border_radius'] = $settings['button_border_radius'] ?? '';


        if( function_exists( 'houzez_properties_slider' ) ) {
            echo houzez_properties_slider( $args );
        }

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_properties_slider );