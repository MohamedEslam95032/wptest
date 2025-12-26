<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_Contact_Form2 extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Button_Traits;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-section-contact-form2';
	}

	public function get_title() {
		return __( 'Section Agent Contact Form v2', 'houzez-theme-functionality' );
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
		return ['property', 'agent contact form v2', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();


        $this->start_controls_section(
            'box_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'form_tabs_enabled',
            [
                'label' => esc_html__( 'Schedule Tour Tab', 'elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'schedule_tour_title',
            [
                'label' => esc_html__( 'Schedule Tour Title', 'elementor' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Schedule a Tour',
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'request_info_title',
            [
                'label' => esc_html__( 'Request Info Title', 'elementor' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Request Info',
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'default_tab',
            [
                'label' => esc_html__( 'Default Active Tab', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'schedule_tour',
                'options' => [
                    'schedule_tour' => esc_html__('Schedule a Tour', 'houzez-theme-functionality'),
                    'request_info' => esc_html__('Request Info', 'houzez-theme-functionality'),
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'agent_detail',
            [
                'label' => esc_html__( 'Agent Info', 'elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'view_listing',
            [
                'label' => esc_html__( 'View Listing Link', 'elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
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
                    '{{WRAPPER}} .hzele-form-wrap' => 'margin-top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding_bottom',
            [
                'label' => esc_html__( 'Title Padding Bottom', 'houzez-theme-functionality' ),
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
                    '{{WRAPPER}} .agent-details' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'agent_detail' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ftabs_section_typography',
            [
                'label' => esc_html__( 'Form Tabs', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ftabs_color',
            [
                'label'     => esc_html__( 'Tabs Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-form-tabs .nav-tabs .nav-link' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ftabs_active_color',
            [
                'label'     => esc_html__( 'Tabs Active Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-form-tabs .nav-tabs .nav-link.active' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ftabs_bg_color',
            [
                'label'     => esc_html__( 'Tabs Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-form-tabs .nav-tabs .nav-link' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ftabs_active_bg_color',
            [
                'label'     => esc_html__( 'Active Tabs Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-form-tabs .nav-tabs .nav-link.active' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'ftabs_border',
                'label' => esc_html__( 'Border', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-form-tabs .nav-tabs .nav-link',
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ftabs_margin',
            [
                'label' => __( 'Margin', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .property-form-tabs .nav-tabs .nav-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ftabs_padding',
            [
                'label' => __( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .property-form-tabs .nav-tabs .nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ftabs_radius',
            [
                'label' => __( 'Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .property-form-tabs .nav-tabs .nav-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ttabs_section_style',
            [
                'label' => esc_html__( 'Tour Tabs', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'ttabs_text_typography',
                'selector' => '{{WRAPPER}} .property-schedule-tour-form-title',
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'ttabs_border',
                'label' => esc_html__( 'Border', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-schedule-tour-type-form .control--radio .control__indicator',
                'exclude' => ['color'],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
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
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Agent Detail', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'agent_detail' => 'yes'
                ]
            ]
        );

        $this->houzez_agent_detail_style_controls();
      
        $this->end_controls_section();

        $this->start_controls_section(
            'content_style',
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
                'selector' => '{{WRAPPER}} .hzele-form-wrap .property-form-wrap, .hzele-form-wrap .property-schedule-tour-form-wrap',
            ]
        );

        $this->add_control(
            'box_text_color',
            [
                'label' => __( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .property-schedule-tour-form-title' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'form_tabs_enabled' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'padding',
            [
                'label' => __( 'Box Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .hzele-form-wrap .property-form-wrap, .hzele-form-wrap .property-schedule-tour-form-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_border',
                'selector' => '{{WRAPPER}} .hzele-form-wrap .property-form-wrap, .hzele-form-wrap .property-schedule-tour-form-wrap',
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
                    '{{WRAPPER}} .hzele-form-wrap .property-form-wrap, .hzele-form-wrap .property-schedule-tour-form-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .hzele-form-wrap .property-form-wrap, .hzele-form-wrap .property-schedule-tour-form-wrap',
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
                'label' => esc_html__( 'Send Email Button', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_houzez_forms_button_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'call_button_style',
            [
                'label' => esc_html__( 'Call Button', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_houzez_forms_call_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'whatsapp_button_style',
            [
                'label' => esc_html__( 'WhatsApp Button', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_houzez_forms_whatsapp_style_controls();

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
		
		global $post, $settings, $ele_settings;

        $settings = $this->get_settings_for_display();
        $ele_settings = $settings;

        $this->single_property_preview_query(); // Only for preview

        echo '<div class="hzele-form-wrap">';
        htf_get_template_part('elementor/template-part/single-property/agent-contact-form');
        echo '</div>';

        if ( Plugin::$instance->editor->is_edit_mode() ) :  ?>
            <script>
                jQuery('.selectpicker').selectpicker('refresh');
            </script>
        <?php
        endif;

        $this->reset_preview_query(); // Only for preview

	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_Contact_Form2 );