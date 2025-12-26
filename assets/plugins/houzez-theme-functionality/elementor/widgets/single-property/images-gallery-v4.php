<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Property_Images_Gallery_v4 extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-detail-gallery-v4';
	}

	public function get_title() {
		return __( 'Property Images Gallery v4', 'houzez-theme-functionality' );
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
            'image_size_size',
            [
                'label' => esc_html__( 'Image Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => \Houzez_Image_Sizes::get_enabled_image_sizes_for_elementor(),
                'default' => 'houzez-gallery',
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

		$this->end_controls_section();
		
	}

	protected function render() {
		global $settings, $map_street_view, $post;

		$settings = $this->get_settings_for_display();
        $this->single_property_preview_query(); // Only for preview

        $image_size = houzez_get_image_size_for('property_detail_v6');
		
        $size = ($settings['image_size_size'] === 'global') ? $image_size : $settings['image_size_size'];
        $i = 0; $j = 0;
        $images_ids = get_post_meta($post->ID, 'fave_property_images', false);
        $images_ids = is_array($images_ids) ? $images_ids : array();

        $total_images = count($images_ids);
        $map_street_view = get_post_meta( $post->ID, 'fave_property_map_street_view', true );

        $gallery_type = $settings['gallery_type'];
        $token = wp_generate_password(5, false, false);

        $dataModal = 'href="#" data-bs-toggle="modal" data-bs-target="#property-lightbox"';
        $css_class = 'houzez-trigger-popup-slider-js';
		?>
        <div class="hs-gallery-v4-wrap hs-property-gallery-wrap">
            <div class="hs-gallery-v4-top-wrap property-top-wrap">
                
                <?php
                if(!empty($images_ids)) { ?>
                <div class="property-banner">
                    <div class="hs-gallery-v4-grid-wrap">
                        
                        <?php if($total_images > 3 ) { ?>
                        <div class="img-wrap-3-text">
                            <i class="houzez-icon icon-picture-sun me-1"></i> 
                            <?php echo $total_images-3; ?> <?php echo esc_html__('More', 'houzez'); ?>
                        </div>
                        <?php } ?>

                        <div class="hs-gallery-v4-grid">
                            <?php
                            foreach( $images_ids as $image_id ) { $i++; 
                                $image_data = wp_get_attachment_image_src( $image_id, $size );

                                // Skip this iteration if image_data is false
                                if(!$image_data) {
                                    continue;
                                }

                                if( $gallery_type == 'photoswipe' ) {
                                    $full_image = wp_get_attachment_image_src($image_id, 'full');
                                    $full_image_url = $full_image[0] ?? '';
                                    $dataModal = 'href="#" data-src="'.esc_url($full_image_url).'" data-houzez-fancybox data-fancybox="gallery-'.$token.'"';
                                    $css_class = '';
                                }

                                if($i == 1) {
                                ?>
                                <div class="hs-gallery-v4-grid-item hs-gallery-v4-grid-item-01">
                                    <a <?php echo $dataModal; ?> class="img-wrap-1 <?php echo esc_attr($css_class);?>">
                                        <img class="img-fluid" src="<?php echo esc_url($image_data[0] ?? ''); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
                                    </a>
                                </div>
                                <?php } elseif($i == 2 || $i == 3) { ?>

                                <?php if($i == 2) { ?>
                                <div class="hs-gallery-v4-grid-item hs-gallery-v4-grid-item-02">
                                <?php } ?>
                                    <a <?php echo $dataModal; ?> class="img-wrap-<?php echo esc_attr($i); ?> <?php echo esc_attr($css_class);?>">
                                        <img class="img-fluid" src="<?php echo esc_url($image_data[0]); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
                                    </a>
                                <?php if( ($i == 3 && $total_images == 3) || ( $i == 2 && $total_images == 2 ) || ( $i == 1 && $total_images == 1 ) || $i == 3 ) { ?>
                                </div>
                                <?php } ?>
                                <?php } else { ?>
                                    <a <?php echo $dataModal; ?> class="img-wrap-1 gallery-hidden">
                                        <img class="img-fluid" src="<?php echo esc_url($image_data[0]); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
                                    </a>
                                <?php
                                }
                                $j++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
		<?php 
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Images_Gallery_v4 );