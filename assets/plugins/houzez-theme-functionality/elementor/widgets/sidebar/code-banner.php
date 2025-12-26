<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Widget_Code_Banner extends Widget_Base {
	use Houzez_Style_Traits;
	
	public function get_name() {
		return 'houzez-code-banner';
	}

	public function get_title() {
		return __( 'Code Banner', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-sidebar eicon-featured-image';
	}

	public function get_categories() {
		return [ 'houzez-sidebar-widgets' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'sidebar', 'code banner' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Code Banner', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'banner_title',
			[
				'label' => esc_html__( 'Ads Code', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Ads Code',
			]
		);

		$this->add_control(
			'banner_code',
			[
				'label' => esc_html__( 'JS or Google AdSense Code', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__('Paste your code here...', 'houzez-theme-functionality'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Code Banner', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->Houzez_Sidebar_Widgets_Traits();

		$this->end_controls_section();
		
	}

	protected function render() {
       $settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'title', 'class', 'elementor-heading-title' );
		?>
		<div class="hzele-widget-wrap">
			<?php if( $settings['banner_title'] != "" ) { ?>
			<div class="widget-header">
				<h3 class="widget-title"><?php echo esc_attr($settings['banner_title']); ?></h3>
			</div>
			<?php } ?>
			<div class="widget-body">
				<div class="widget-content">
        			<?php echo $settings['banner_code']; ?>
        		</div>
        	</div>
		</div>
		<?php

	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Widget_Code_Banner );