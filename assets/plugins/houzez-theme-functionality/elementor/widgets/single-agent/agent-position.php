<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agent_Position extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agent-position';
	}

	public function get_title() {
		return __( 'Agent Position', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-agent eicon-person';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agent')  {
            return ['houzez-single-agent-builder']; 
        }

		return [ 'houzez-single-agent' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'agent position', 'agent' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Agent Position', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'position_text',
            [
                'label' => esc_html__( 'Text', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
				'default' => 'at',
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

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'agent_position_typography',
                'selector' => '{{WRAPPER}} .agent-list-position',
            ]
        );

        $this->add_control(
            'poistion_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .agent-list-position' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'company_color',
            [
                'label' => esc_html__( 'Company Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .agent-list-position a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'company_color_hover',
            [
                'label' => esc_html__( 'Company Color Hover', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .agent-list-position a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->end_controls_section();
		
	}

	protected function render() {
		global $settings;
        $this->single_agent_preview_query(); // Only for preview

        $settings = $this->get_settings_for_display();

		$agent_position = get_post_meta( get_the_ID(), 'fave_agent_position', true );
		$agent_company = get_post_meta( get_the_ID(), 'fave_agent_company', true );
		$agent_agency_id = get_post_meta( get_the_ID(), 'fave_agent_agencies', true );

		$href = "";
		if(!empty($agent_agency_id)) {
			$href = ' href="'.esc_url(get_permalink($agent_agency_id)).'"';
		}

		if(!empty($agent_position) || !empty($agent_company)) {
		?>
		<p class="agent-list-position mb-1"> <?php echo esc_attr($agent_position); ?>
			<?php if(!empty($agent_company)) { ?>
				
				<span itemprop="jobTitle"><?php echo $settings['position_text']; ?></span>
				<a<?php echo $href; ?>>
					<span itemprop="name"><?php echo esc_attr( $agent_company ); ?></span>		
				</a>

			<?php } ?>
		</p>
		<?php } 

		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agent_Position );