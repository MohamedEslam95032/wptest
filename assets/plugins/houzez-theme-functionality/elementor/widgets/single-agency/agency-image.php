<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agency_Picture extends Widget_Base {
	use Houzez_Style_Traits;
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agency-picture';
	}

	public function get_title() {
		return __( 'Agency Picture', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-agency eicon-featured-image';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agency')  {
            return ['houzez-single-agency-builder']; 
        }

		return [ 'houzez-single-agency' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'agency picture' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Agency Picture', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'listing_thumb_size',
            [
                'label' => esc_html__( 'Thumbnail Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => \Houzez_Image_Sizes::get_enabled_image_sizes_for_elementor(),
                'default' => 'global',
            ]
        );

		$this->end_controls_section();

		$this->Houzez_Image_Settings_Traits();
		
	}

	protected function render() {
        $this->single_agency_preview_query(); // Only for preview
 
        $settings = $this->get_settings_for_display(); 
		$image_size = houzez_get_image_size_for('agency_profile');
        $thumb_size = ($settings['listing_thumb_size'] === 'global') ? $image_size : $settings['listing_thumb_size'];
        ?>

		<div class="agency-ele-image-wrap">
            <?php 
            if( has_post_thumbnail() && get_the_post_thumbnail() != '' ) {
				the_post_thumbnail($thumb_size, array('class' => 'img-fluid'));
			} else {
				houzez_image_placeholder( $thumb_size );
			}
            ?>
        </div><!-- agency-image -->
       <?php
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agency_Picture );