<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agency_Agents extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agency-agents';
	}

	public function get_title() {
		return __( 'Agency Agents', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-single-agency eicon-gallery-grid';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agency')  {
            return ['houzez-single-agency-builder']; 
        }

        return [ 'houzez-single-agency' ];
	}

	public function get_keywords() {
		return ['agency', 'agency agents', 'houzez' ];
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


        $this->end_controls_section();


	}

	protected function render() {
		
		global $settings, $post, $houzez_local;

        $houzez_local = houzez_get_localization();

		$settings = $this->get_settings_for_display();

        $this->single_agency_preview_query(); // Only for preview 

        htf_get_template_part('elementor/template-part/single-agency/agency-agents');

        $this->reset_preview_query(); // Only for preview

	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agency_Agents );