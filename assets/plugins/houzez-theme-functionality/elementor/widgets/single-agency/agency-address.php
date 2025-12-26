<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agency_Address extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agency-address';
	}

	public function get_title() {
		return __( 'Agency Address', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-agency eicon-person';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agency')  {
            return ['houzez-single-agency-builder']; 
        }

		return [ 'houzez-single-agency' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'agency address', 'agency' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Agency Address', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'agency_position_typography',
                'selector' => '{{WRAPPER}} .agency-ele-address',
            ]
        );

        $this->add_control(
            'poistion_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .agency-ele-address' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->end_controls_section();
		
	}

	protected function render() {
		global $settings;

        $this->single_agency_preview_query(); // Only for preview
        $settings = $this->get_settings_for_display();
		?> 

		<div class="agency-ele-address">
			<?php 
			$agency_address = get_post_meta( get_the_ID(), 'fave_agency_address', true );
			if(!empty($agency_address)) {
				echo '<i class="houzez-icon icon-pin"></i> '.$agency_address;
			}
			?>
		</div>
		<?php
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agency_Address );