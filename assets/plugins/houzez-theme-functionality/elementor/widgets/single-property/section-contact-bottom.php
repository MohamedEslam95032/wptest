<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_Contact_Form extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Button_Traits;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-section-contact-bottom';
	}

	public function get_title() {
		return __( 'Section Agent Contact Form', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-featured-image';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['property', 'agent contact form', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'section_header',
            [
                'label' => esc_html__( 'Section Header', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'section_title',
            [
                'label' => esc_html__( 'Section Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => '',
                'condition' => [
                    'section_header' => 'true'
                ],
            ]
        );

        $this->add_control(
            'agent_details',
            [
                'label' => esc_html__( 'Agent Details', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'content_style',
            [
                'label' => __( 'Section Header', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'section_header' => 'true'
                ]
            ]
        );

        $this->add_control(
            'sec_title_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap h2' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .block-title-wrap h2',
            ]
        );

        $this->add_control(
            'section_title_border',
            [
                'label' => esc_html__( 'Border Type', 'houzez-theme-functionality' ),
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
                    '{{WRAPPER}} .block-title-wrap' => 'border-bottom-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'seched_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'section_title_border!' => 'none'
                ]
            ]
        );

        $this->add_responsive_control(
            'title_padding_bottom',
            [
                'label' => esc_html__( 'Padding Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'view_listing_btn',
            [
                'label' => esc_html__( 'View Listing Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'viewlist_typography',
                'selector' => '{{WRAPPER}} .block-title-wrap .btn',
            ]
        );

        $this->start_controls_tabs( 'tabs_view_listing_style');

        $this->start_controls_tab(
            'viewlist_tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'viewlist_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap .btn' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'viewlist_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .block-title-wrap .btn',
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
            'viewlist_tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'viewlist_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap .btn:hover, {{WRAPPER}} .block-title-wrap .btn:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .block-title-wrap .btn:hover svg, {{WRAPPER}} .block-title-wrap .btn:focus svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'viewlist_button_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .block-title-wrap .btn:hover, {{WRAPPER}} .block-title-wrap .btn:focus',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
            ]
        );

        $this->add_control(
            'viewlist_button_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap .btn:hover, {{WRAPPER}} .block-title-wrap .btn:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'viewlist_button_hover_transition_duration',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap .btn' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'viewlist_border',
                'selector' => '{{WRAPPER}} .block-title-wrap .btn',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'viewlist_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
      

        $this->end_controls_section();

        $this->start_controls_section(
            'section_agentd_style',
            [
                'label' => esc_html__( 'Agent Detail', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_agentd',
            [
                'label' => esc_html__( 'Text', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'agentd_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-details, .agent-details li a' => 'color: {{VALUE}}',
                ],
            ]
        );
    
        $this->end_controls_section();

		$this->start_controls_section(
            'box_style',
            [
                'label' => __( 'Box', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'box_background',
                'label' => __( 'Background', 'houzez-theme-functionality' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .block-wrap',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_border',
                'selector' => '{{WRAPPER}} .block-wrap',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'section_margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-wrap' => 'margin-top: {{SIZE}}{{UNIT}};'
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
					'{{WRAPPER}} .block-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'radius',
			[
				'label' => __( 'Box Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .block-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'section_field_style',
            [
                'label' => esc_html__( 'Fields', 'houzez-theme-functionality' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_form_fields_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_label_style',
            [
                'label' => esc_html__( 'Labels', 'houzez-theme-functionality' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_form_labels_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_form_title_style',
            [
                'label' => esc_html__( 'Form Title', 'houzez-theme-functionality' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_title_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'form_title_typography',
                'selector' => '{{WRAPPER}} .block-content-wrap .block-title-wrap',
            ]
        );

        $this->add_responsive_control(
            'form_title__margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_title__padding',
            [
                'label' => esc_html__( 'Padding Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'section_form_title_border',
            [
                'label' => esc_html__( 'Hide Title Border', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'border-bottom: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'form_title_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'section_form_title_border!' => 'none'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'submit_button_style',
            [
                'label' => esc_html__( 'Submit Button', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_houzez_forms_button_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'terms_style',
            [
                'label' => esc_html__( 'Terms of use', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_form_terms_style_controls();

        $this->end_controls_section();

	}

	protected function render() {
		
		global $post, $ele_settings;

        $settings = $this->get_settings_for_display();
        $ele_settings = $settings;

        $this->single_property_preview_query(); // Only for preview

		get_template_part('property-details/agent-form-bottom');

        if ( Plugin::$instance->editor->is_edit_mode() ) :  ?>
            <script>
                jQuery('.selectpicker').selectpicker('refresh');
            </script>
        <?php
        endif;
        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_Contact_Form );