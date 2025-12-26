<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Property_Images_Gallery_v1 extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-detail-gallery-v1';
	}

	public function get_title() {
		return __( 'Property Images Gallery v1', 'houzez-theme-functionality' );
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

        $this->add_responsive_control(
            'gallery_height',
            [
                'label' => esc_html__( 'Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .hs-gallery-v1-wrap .tab-content' => 'height: {{SIZE}}{{UNIT}};',
                ],
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
		global $settings, $map_street_view, $post;

		$settings = $this->get_settings_for_display();
        $this->single_property_preview_query(); // Only for preview

		$map_street_view = get_post_meta( $post->ID, 'fave_property_map_street_view', true );

		$images_ids = get_post_meta($post->ID, 'fave_property_images', false);
		$images_ids = is_array($images_ids) ? $images_ids : array();

        $total_images = count($images_ids);
        $featured_image_id = get_post_thumbnail_id($post->ID);
        if (($key = array_search($featured_image_id, $images_ids)) !== false) {
            unset($images_ids[$key]);
        }
        $featured_image = wp_get_attachment_image_src( $featured_image_id, 'full', true );
        $featured_image_url = $featured_image[0] ?? '';
        $token = wp_generate_password(5, false, false);

		$gallery_type = $settings['gallery_type'];
		?>
		<div class="hs-gallery-v1-wrap hs-property-gallery-wrap">
            <div class="hs-gallery-v1-top-wrap property-top-wrap">
                <div class="property-banner" role="region">
                    <div class="container d-none d-md-block">
                        <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                    </div>
                    <div class="tab-content" id="pills-tabContent" role="tablist">
                        <div class="tab-pane show active h-100" id="pills-gallery" role="tabpanel" aria-labelledby="pills-gallery-tab" aria-hidden="false">
                            <div class="property-image-count" role="status">
                                <i class="houzez-icon icon-picture-sun" aria-hidden="true"></i> <span><?php echo $total_images; ?></span>
                            </div>
                            <?php 
                            if( $gallery_type == 'photoswipe' ) { 
                            ?>
                            <div itemscope itemtype="http://schema.org/ImageGallery">
                                <a href="#" class="property-banner-trigger position-absolute top-0 start-0 w-100 h-100" data-src="<?php echo esc_url($featured_image_url); ?>" data-houzez-fancybox data-fancybox="gallery-<?php echo esc_attr($token); ?>" itemprop="contentUrl" role="button"></a>
                                <img class="property-featured-image w-100 h-100 left-0 top-0" src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo get_the_title($post->ID); ?>" role="img">
                                <?php
                                if(!empty($images_ids)) {
                                    foreach( $images_ids as $image_id ) {
                                        $image_data = wp_get_attachment_image_src($image_id, 'full');

                                        // Skip this iteration if image_data is false
                                        if(!$image_data) {
                                            continue;
                                        }

                                        $image_url = $image_data[0] ?? '';
                                        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                                        ?>
                                        <div itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" class="gallery-hidden">
                                            <a href="#" data-src="<?php echo esc_url($image_url); ?>" data-houzez-fancybox data-fancybox="gallery-<?php echo esc_attr($token); ?>" itemprop="contentUrl">
                                                <img class="img-fluid" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" itemprop="thumbnail">
                                            </a>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            
                            <?php } else { ?>
                            <a class="property-banner-trigger position-absolute top-0 start-0 w-100 h-100" data-bs-toggle="modal" data-bs-target="#property-lightbox" href="#" role="button"></a>
                            <img class="property-featured-image w-100 h-100 left-0 top-0" src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo get_the_title($post->ID); ?>" role="img">
                        <?php } ?>
                        </div>
                        
                        <?php htf_get_template_part('elementor/template-part/single-property/media-tabs-gallery'); ?>
                    </div>
                </div>
            </div>

            <div class="hs-gallery-v1-bottom-wrap">
                <div class="container d-md-none d-sm-block">
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
Plugin::instance()->widgets_manager->register( new Houzez_Property_Images_Gallery_v1 );