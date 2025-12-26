<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_FloorPlan_v2 extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-section-floorplan-v2';
	}

	public function get_title() {
		return __( 'Section Floor Plan v2', 'houzez-theme-functionality' );
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
		return ['property', 'Floor Plan', 'houzez' ];
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
            'hide_size',
            [
                'label' => esc_html__( 'Hide Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fpl-size' => 'display: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'hide_beds',
            [
                'label' => esc_html__( 'Hide Beds', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fpl-rooms' => 'display: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'hide_bath',
            [
                'label' => esc_html__( 'Hide Bath', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fpl-baths' => 'display: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'hide_price',
            [
                'label' => esc_html__( 'Hide Price', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fpl-price' => 'display: {{VALUE}}!important;',
                ],
            ]
        );


        $this->end_controls_section();
	
		$this->start_controls_section(
            'box_style',
            [
                'label' => __( 'Section Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_single_property_section_styling_traits();

		$this->end_controls_section();

        // Section title
		$this->start_controls_section(
            'content_style',
            [
                'label' => __( 'Section Title', 'houzez-theme-functionality' ),
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
      

		$this->end_controls_section();

        $this->start_controls_section(
            'ftabs_section_style',
            [
                'label' => esc_html__( 'Tabs', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'tabs_typography',
                'selector' => '{{WRAPPER}} .fw-property-floor-plans-wrap .floor-plans-tabs a',
            ]
        );

        $this->add_control(
            'ftabs_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .fw-property-floor-plans-wrap .floor-plans-tabs a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .nav-tabs' => 'border-bottom-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ftabs_active_color',
            [
                'label'     => esc_html__( 'Active Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .fw-property-floor-plans-wrap .floor-plans-tabs a.active' => 'color: {{VALUE}}; border-bottom-color: {{VALUE}};',
                    '{{WRAPPER}} .fw-property-floor-plans-wrap .floor-plans-tabs a.active:before' =>'border-top-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tabs_padding',
            [
                'label' => __( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .fw-property-floor-plans-wrap .floor-plans-tabs a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Floor Plan title
        $this->start_controls_section(
            'floorplan_style',
            [
                'label' => __( 'Floor Plan Title', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'fp_title_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} h3.floor-plan-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'fp_title_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} h3.floor-plan-title',
            ]
        );
      

        $this->end_controls_section();

        // Meta
        $this->start_controls_section(
            'floorplanmeta_style',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'fp_meta_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .floor-plan-description p' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'fp_meta_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .floor-plan-description p',
            ]
        );
      

        $this->end_controls_section();

	}

	protected function render() {
		
		global $ele_settings, $post;

		$settings = $this->get_settings_for_display();

		$ele_settings = $settings;

		$this->single_property_preview_query(); // Only for preview
        
        get_template_part('property-details/luxury-homes/floor-plans');
         
        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_FloorPlan_v2 );