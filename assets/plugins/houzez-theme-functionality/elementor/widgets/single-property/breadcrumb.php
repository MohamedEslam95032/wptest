<?php
namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Property_Breadcrumbs extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-breadcrumb';
	}

	public function get_title() {
		return __( 'Breadcrumbs', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-product-breadcrumbs';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

		return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'breadcrumbs', 'property' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_product_rating_style',
			[
				'label' => __( 'Style', 'houzez-theme-functionality' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumb-wrap li.breadcrumb-item.active' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => __( 'Link Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumb-wrap li.breadcrumb-item a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => __( 'Separator Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumb-item+.breadcrumb-item::before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .breadcrumb-wrap li.breadcrumb-item',
			]
		);


		$this->end_controls_section();
	}

	protected function render() {
		
		// Only for preview
        $this->single_property_preview_query();

		echo '<div class="breadcrumb-wrap">';
			houzez_breadcrumbs();
		echo '</div>';

		$this->reset_preview_query(); // Only for preview
	}

	public function render_plain_content() {}
}
Plugin::instance()->widgets_manager->register( new Property_Breadcrumbs );
