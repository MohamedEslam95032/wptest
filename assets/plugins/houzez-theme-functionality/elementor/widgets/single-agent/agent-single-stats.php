<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class Houzez_Agent_Single_Stat extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agent-single-stat';
	}

	public function get_title() {
		return __( 'Agent Taxonomy Stats', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-single-agent eicon-featured-image';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agent')  {
            return ['houzez-single-agent-builder']; 
        }

        return [ 'houzez-single-agent' ];
	}

	public function get_keywords() {
		return ['agent', 'stats', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();


		$this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Agent Stats', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'     => esc_html__( 'Title', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => 'Enter Title',
            ]
        );

        $this->add_control(
            'stats_taxonomy',
            [
                'label'     => esc_html__( 'Taxonomy', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'property_type' => esc_html__('Property Type', 'houzez-theme-functionality'),
                    'property_status' => esc_html__('Property Status', 'houzez-theme-functionality'),
                    'property_city' => esc_html__('Property City', 'houzez-theme-functionality'),
                    'property_state' => esc_html__('Property State', 'houzez-theme-functionality'),
                    'property_country' => esc_html__('Property Country', 'houzez-theme-functionality'),
                    'property_label' => esc_html__('Property Label', 'houzez-theme-functionality'),
                ],
                'default'   => 'property_type',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'agent_stats_bg',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-profile-chart-wrap' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'agent_stats_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-profile-chart-wrap' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'agent_stats_border',
                'selector' => '{{WRAPPER}} .agent-profile-chart-wrap',
            ]
        );

        $this->add_responsive_control(
            'agent_stats_radius',
            [
                'label'      => esc_html__( 'Radius', 'houzez-theme-functionality' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .agent-profile-chart-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'agent_stats_padding',
            [
                'label'      => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .agent-profile-chart-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

	}

	protected function render() {
		
		global $settings, $token, $post, $houzez_local;

        $houzez_local = houzez_get_localization();

        $token = wp_generate_password(5, false, false);

		$settings = $this->get_settings_for_display();

        $this->single_agent_preview_query(); // Only for preview

        htf_get_template_part('elementor/template-part/single-agent/agent-single-stats');

        $this->reset_preview_query(); // Only for preview

	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agent_Single_Stat );