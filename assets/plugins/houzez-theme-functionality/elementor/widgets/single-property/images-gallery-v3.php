<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Property_Images_Gallery_v3 extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-detail-gallery-v3';
	}

	public function get_title() {
		return __( 'Property Images Gallery v3', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-slider-push';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

		return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['gallery', 'property images', 'property images gallery', 'property gallery', 'houzez', 'top' ];
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
            'gallery_type',
            [
                'label' => esc_html__( 'Popup Gallery Type', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
				'options' => [
					'builtin' => esc_html__( 'Built In', 'houzez-theme-functionality' ),
					'photoswipe' => esc_html__( 'Photo Swipe', 'houzez-theme-functionality' )
				],
				'default' => 'builtin',
            ]
        );

        $this->add_control(
            'btn_gallery',
            [
                'label' => esc_html__( 'Gallery Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'gallery_note',
            [
                'label' => __( 'Gallery button can only be disabled if all other buttons (Video, 360° Tour, Map, Street View) are disabled', 'houzez-theme-functionality' ),
                'type' => 'houzez-info-note',
                'condition' => [
                    'btn_gallery!' => 'true'
                ]
            ]
        );

        $this->add_control(
            'btn_video',
            [
                'label' => esc_html__( 'Video Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'btn_360_tour',
            [
                'label' => esc_html__( '360° Virtual Tour', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'btn_map',
            [
                'label' => esc_html__( 'Map Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'map_note',
            [
                'label' => __( 'Map will only show if you have enabled when add/edit property', 'houzez-theme-functionality' ),
                'type' => 'houzez-warning-note',
                'condition' => [
                    'btn_map' => 'true'
                ]
            ]
        );

        $this->add_control(
            'btn_street',
            [
                'label' => esc_html__( 'Street View Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'street_note',
            [
                'label' => __( 'Street view will only show if you have enabled when add/edit property and map type set to Google', 'houzez-theme-functionality' ),
                'type' => 'houzez-warning-note',
                'condition' => [
                    'btn_street' => 'true'
                ]
            ]
        );


		$this->end_controls_section();
		
	}

	protected function render() {
		global $settings, $map_street_view, $post, $property_gallery_popup_type;

		$settings = $this->get_settings_for_display();
        $this->single_property_preview_query(); // Only for preview
		$map_street_view = get_post_meta( $post->ID, 'fave_property_map_street_view', true );

        $property_gallery_popup_type = $settings['gallery_type'];
        $images_ids = get_post_meta($post->ID, 'fave_property_images', false);
        $images_ids = is_array($images_ids) ? $images_ids : array();
        $total_images = count($images_ids); 
        ?>

		<div class="hs-gallery-v3-wrap hs-property-gallery-wrap">
            <div class="hs-gallery-v3-top-wrap property-top-wrap">
                <div class="property-banner">
                    <div class="d-none d-md-block">
                        <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                    </div>
                    <div class="tab-content" id="pills-tabContent">
                        
                        <div class="tab-pane h-100 show active" id="pills-gallery" role="tabpanel" aria-labelledby="pills-gallery-tab">
                            <div class="property-image-count"><i class="houzez-icon icon-picture-sun"></i> <?php echo $total_images; ?></div>
                            <?php get_template_part('property-details/partials/gallery-variable-width'); ?>
                        </div>
                        
                        <?php htf_get_template_part('elementor/template-part/single-property/media-tabs-gallery'); ?>

                    </div>
                </div>
            </div>

            <div class="hs-gallery-v1-bottom-wrap">
                <div class="d-md-none d-sm-block">
                    <div class="mobile-top-wrap">
                        <div class="mobile-property-tools d-flex align-items-center justify-content-center">
                            <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php 
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Images_Gallery_v3 );