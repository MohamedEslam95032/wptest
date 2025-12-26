<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class Property_Section_ScheduleTour2 extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Button_Traits;
	use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-section-schedule-tour-v2';
	}

	public function get_title() {
		return __( 'Section Schedule Tour v2', 'houzez-theme-functionality' );
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
		return ['property', 'Schedule', 'houzez', 'Tour' ];
	}

	protected function register_controls() {
		parent::register_controls();

	
		$this->start_controls_section(
            'box_style',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
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
            'content_style',
            [
                'label' => __( 'Form Title', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
            'sec_title_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} h2.sch-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} h2.sch-title',
            ]
        );
      
		$this->end_controls_section();

		$this->start_controls_section(
            'ttabs_section_style',
            [
                'label' => esc_html__( 'Tour Tabs', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ttabs_text_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-form-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'ttabs_text_typography',
                'selector' => '{{WRAPPER}} .property-schedule-tour-form-title',
            ]
        );


        $this->add_control(
            'ttabs_buttons_options',
            [
                'label' => esc_html__( 'Buttons', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'ttabs_color',
            [
                'label'     => esc_html__( 'Tabs Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-type-form .control--radio .control__indicator' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ttabs_active_color',
            [
                'label'     => esc_html__( 'Tabs Active Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-form-wrap .control input:checked ~ .control__indicator' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ttabs_bg_color',
            [
                'label'     => esc_html__( 'Tabs Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-type-form .control--radio .control__indicator' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ttabs_active_bg_color',
            [
                'label'     => esc_html__( 'Active Tabs Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-form-wrap .control input:checked ~ .control__indicator' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'ttabs_border',
                'label' => esc_html__( 'Border', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-schedule-tour-type-form .control--radio .control__indicator',
                'exclude' => ['color'],
            ]
        );

        $this->add_control(
            'ttabs_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-type-form .control--radio .control__indicator' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ttabs_active_border_color',
            [
                'label'     => esc_html__( 'Active Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-form-wrap .control input:checked ~ .control__indicator' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ttabs_padding',
            [
                'label' => __( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-type-form .control .control__indicator' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ttabs_radius',
            [
                'label' => __( 'Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-type-form .control .control__indicator' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		
		global $post;

		$settings = $this->get_settings_for_display();

		$this->single_property_preview_query(); // Only for preview
        ?>

        <div class="property-schedule-tour-wrap property-schedule-tour-wrap-v2 property-section-wrap" id="property-schedule-tour-wrap">
            <div class="block-wrap">
                <div class="block-content-wrap">
                    <?php get_template_part('property-details/partials/schedule-tour-form-v2'); ?>
                </div>
                <!-- block-content-wrap -->
            </div>
            <!-- block-wrap -->
        </div>
        <?php 
        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_ScheduleTour2 );