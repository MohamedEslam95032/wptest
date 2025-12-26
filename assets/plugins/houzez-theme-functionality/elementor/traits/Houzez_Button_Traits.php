<?php
namespace HouzezThemeFunctionality\Elementor\Traits;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

trait Houzez_Button_Traits {
    /**
     * Get button sizes.
     *
     * Retrieve an array of button sizes for the button widget.
     *
     * @since 3.4.0
     * @access public
     * @static
     *
     * @return array An array containing button sizes.
     */
    public static function get_button_sizes() {
        return [
            'xs' => esc_html__( 'Extra Small', 'houzez-theme-functionality' ),
            'sm' => esc_html__( 'Small', 'houzez-theme-functionality' ),
            'md' => esc_html__( 'Medium', 'houzez-theme-functionality' ),
            'lg' => esc_html__( 'Large', 'houzez-theme-functionality' ),
            'xl' => esc_html__( 'Extra Large', 'houzez-theme-functionality' ),
        ];
    }

    
    protected function register_houzez_button_style_controls( $args = [] ) {
        $default_args = [
            'section_condition' => [],
            'alignment_default' => '',
            'alignment_control_prefix_class' => 'houzez-button%s-align-',
            'content_alignment_default' => '',
        ];

        $args = wp_parse_args( $args, $default_args );

        $this->add_responsive_control(
            'align',
            [
                'label' => esc_html__( 'Position', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' => esc_html__( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'Stretch', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-stretch',
                    ],
                ],
                'prefix_class' => $args['alignment_control_prefix_class'],
                'default' => $args['alignment_default'],
                'condition' => $args['section_condition'],
            ]
        );

        $start = is_rtl() ? 'right' : 'left';
        $end = is_rtl() ? 'left' : 'right';

        $this->add_responsive_control(
            'content_align',
            [
                'label' => esc_html__( 'Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start'    => [
                        'title' => esc_html__( 'Start', 'houzez-theme-functionality' ),
                        'icon' => "eicon-text-align-{$start}",
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'end' => [
                        'title' => esc_html__( 'End', 'houzez-theme-functionality' ),
                        'icon' => "eicon-text-align-{$end}",
                    ],
                    'space-between' => [
                        'title' => esc_html__( 'Space between', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => $args['content_alignment_default'],
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button .houzez-ele-button-content-wrapper' => 'justify-content: {{VALUE}};',
                ],
                'condition' => array_merge( $args['section_condition'], [ 'align' => 'justify' ] ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'condition' => $args['section_condition'],
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style', [
            'condition' => $args['section_condition'],
        ] );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                    ],
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button:hover, {{WRAPPER}} .houzez-ele-button:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .houzez-ele-button:hover svg, {{WRAPPER}} .houzez-ele-button:focus svg' => 'fill: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .houzez-ele-button:hover, {{WRAPPER}} .houzez-ele-button:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
                'condition' => $args['section_condition'],
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
                    '{{WRAPPER}} .houzez-ele-button:hover, {{WRAPPER}} .houzez-ele-button:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
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
                    '{{WRAPPER}} .houzez-ele-button' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'condition' => $args['section_condition'],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'separator' => 'before',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => $args['section_condition'],
            ]
        );
    }

    protected function register_houzez_forms_button_style_controls( $args = [] ) {
        $default_args = [
            'section_condition' => [],
            'alignment_default' => 'justify',
            'alignment_control_prefix_class' => 'houzez-button%s-align-',
            'content_alignment_default' => '',
        ];

        $args = wp_parse_args( $args, $default_args );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'condition' => $args['section_condition'],
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style', [
            'condition' => $args['section_condition'],
        ] );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                    ],
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'button_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button, {{WRAPPER}} .houzez-ele-button:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button:hover, {{WRAPPER}} .houzez-ele-button:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .houzez-ele-button:hover svg, {{WRAPPER}} .houzez-ele-button:focus svg' => 'fill: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .houzez-ele-button:hover, {{WRAPPER}} .houzez-ele-button:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button:hover, {{WRAPPER}} .houzez-ele-button:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
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
                    '{{WRAPPER}} .houzez-ele-button' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'condition' => $args['section_condition'],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'separator' => 'before',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .houzez-ele-button',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => $args['section_condition'],
            ]
        );
    }

    protected function register_houzez_forms_call_style_controls( $args = [] ) {
        $default_args = [
            'section_condition' => [],
            'alignment_default' => 'justify',
            'alignment_control_prefix_class' => 'houzez-button%s-align-',
            'content_alignment_default' => '',
        ];

        $args = wp_parse_args( $args, $default_args );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'btncal_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .hz-btn-call',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'btncal_text_shadow',
                'selector' => '{{WRAPPER}} .hz-btn-call',
                'condition' => $args['section_condition'],
            ]
        );

        $this->start_controls_tabs( 'btncal_tabs_button_style', [
            'condition' => $args['section_condition'],
        ] );

        $this->start_controls_tab(
            'btncal_tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'btncal_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-call' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'btncal_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .hz-btn-call',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                    ],
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'btncal_tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'btncal_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-call:hover, {{WRAPPER}} .hz-btn-call:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .hz-btn-call:hover svg, {{WRAPPER}} .hz-btn-call:focus svg' => 'fill: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'btncal_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .hz-btn-call:hover, {{WRAPPER}} .hz-btn-call:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'btncal_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-call:hover, {{WRAPPER}} .hz-btn-call:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'btncal_hover_transition_duration',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-call' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'btncal_hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'condition' => $args['section_condition'],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'btncal_border',
                'selector' => '{{WRAPPER}} .hz-btn-call',
                'separator' => 'before',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_responsive_control(
            'btncal_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-call' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'btncal_box_shadow',
                'selector' => '{{WRAPPER}} .hz-btn-call',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_responsive_control(
            'btncal_text_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-call' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => $args['section_condition'],
            ]
        );
    }

    protected function register_houzez_forms_whatsapp_style_controls( $args = [] ) {
        $default_args = [
            'section_condition' => [],
            'alignment_default' => 'justify',
            'alignment_control_prefix_class' => 'houzez-button%s-align-',
            'content_alignment_default' => '',
        ];

        $args = wp_parse_args( $args, $default_args );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'btnwhatsapp_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .hz-btn-whatsapp',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'btnwhatsapp_text_shadow',
                'selector' => '{{WRAPPER}} .hz-btn-whatsapp',
                'condition' => $args['section_condition'],
            ]
        );

        $this->start_controls_tabs( 'btnwhatsapp_tabs_button_style', [
            'condition' => $args['section_condition'],
        ] );

        $this->start_controls_tab(
            'btnwhatsapp_tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'btnwhatsapp_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-whatsapp' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'btnwhatsapp_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .hz-btn-whatsapp',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                    ],
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'btnwhatsapp_tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'btnwhatsapp_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-whatsapp:hover, {{WRAPPER}} .hz-btn-whatsapp:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .hz-btn-whatsapp:hover svg, {{WRAPPER}} .hz-btn-whatsapp:focus svg' => 'fill: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'btnwhatsapp_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .hz-btn-whatsapp:hover, {{WRAPPER}} .hz-btn-whatsapp:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'btnwhatsapp_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-whatsapp:hover, {{WRAPPER}} .hz-btn-whatsapp:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_control(
            'btnwhatsapp_hover_transition_duration',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-whatsapp' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'btnwhatsapp_hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'condition' => $args['section_condition'],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'btnwhatsapp_border',
                'selector' => '{{WRAPPER}} .hz-btn-whatsapp',
                'separator' => 'before',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_responsive_control(
            'btnwhatsapp_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-whatsapp' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'btnwhatsapp_box_shadow',
                'selector' => '{{WRAPPER}} .hz-btn-whatsapp',
                'condition' => $args['section_condition'],
            ]
        );

        $this->add_responsive_control(
            'btnwhatsapp_text_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-whatsapp' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => $args['section_condition'],
            ]
        );
    }

    protected function register_houzez_leave_review_button_style_controls( $args = [] ) {
        
        $this->start_controls_tabs( 'tabs_lrbtn_style');

        $this->start_controls_tab(
            'tab_lrbtn_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'tblrbtn_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-lreview' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'tblrbtn_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .hz-btn-lreview',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                    ],
                ],
            ]
        );

        $this->add_control(
            'tblrbtn_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-lreview' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_lrbtn_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'tblrbtn_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-lreview:hover, {{WRAPPER}} .hz-btn-lreview:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .hz-btn-lreview:hover svg, {{WRAPPER}} .hz-btn-lreview:focus svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'lrbtn_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .hz-btn-lreview:hover, {{WRAPPER}} .hz-btn-lreview:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
            ]
        );

        $this->add_control(
            'tblrbtn_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-lreview:hover, {{WRAPPER}} .hz-btn-lreview:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tblrbtn_hover_transition_duration',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-lreview' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ttbtn_border',
                'selector' => '{{WRAPPER}} .hz-btn-lreview',
                'exclude' => ['width'],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'tblrbtn_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .hz-btn-lreview' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'tblrbtn_box_shadow',
                'selector' => '{{WRAPPER}} .hz-btn-lreview',
            ]
        );

    }


}
