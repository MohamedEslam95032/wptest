<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_Block_Gallery extends \Elementor\Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;
    
	public function get_name() {
		return 'houzez-property-section-block-gallery';
	}

	public function get_title() {
		return __( 'Section Block Gallery', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-gallery-grid';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['property', 'Block Gallery', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();


		$this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'visible_images',
            [
                'label' => esc_html__( 'Visible Images', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => '9',
            ]
        );

        $this->add_responsive_control(
            'images_in_row',
            [
                'label' => esc_html__( 'Images in a row', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => array(
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ),
            ]
        );

        $this->add_control(
            'popup_type',
            [
                'label' => esc_html__( 'Popup Type', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'elementor',
                'options' => array(
                    'elementor' => 'Photoswipe',
                    'houzez' => 'Houzez',
                ),
            ]
        );

        $this->add_control(
            'grid_gap',
            [
                'label' => esc_html__( 'Gap', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 5,
                'step' => 1,
                'default' => 0,
            ]
        );

        $this->add_responsive_control(
            'section_margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-gallery-grid' => 'margin-top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->end_controls_section();


	}

	protected function render() {
        global $post;
		$settings = $this->get_settings_for_display();
        $visible_images = $settings['visible_images'];

        $grid_gap = $settings['grid_gap'] ?? 0;

        if( is_array($grid_gap) || $grid_gap == '') {
            $grid_gap = 0;
        }

        // Get the value for desktop
        $images_in_row_desktop = $settings['images_in_row'];

        // Get the value for tablet
        $images_in_row_tablet = isset( $settings['images_in_row_tablet'] ) ? $settings['images_in_row_tablet'] : $images_in_row_desktop;

        // Get the value for mobile
        $images_in_row_mobile = isset( $settings['images_in_row_mobile'] ) ? $settings['images_in_row_mobile'] : $images_in_row_tablet;

        $this->single_property_preview_query(); // Only for preview

        if( empty($visible_images) ) {
            $visible_images = 9;
        }

        $token = wp_generate_password(5, false, false);
        $size = 'houzez-item-image-6';
        
        $builtin_gallery_class = ' houzez-trigger-popup-slider-js';
        $dataModal = 'href="#" data-bs-toggle="modal" data-bs-target="#property-lightbox"';
        $images_ids = get_post_meta($post->ID, 'fave_property_images', false);
        $images_ids = is_array($images_ids) ? $images_ids : array();

        $i = 0;

        if( !empty($images_ids) && count($images_ids)) {

            $total_images = count($images_ids);
            $remaining_images = $total_images - $visible_images;
        ?>
        <div class="property-gallery-grid property-section-wrap" id="property-gallery-grid">
        <div class="property-gallery-grid-wrap row row-cols-<?php echo esc_attr($images_in_row_mobile);?> row-cols-md-<?php echo esc_attr($images_in_row_tablet); ?> row-cols-lg-<?php echo esc_attr($images_in_row_desktop); ?> g-<?php echo esc_attr($grid_gap); ?>">  
                <?php 
                foreach( $images_ids as $image_id ) { 
                    $image_data = wp_get_attachment_image_src( $image_id, $size );
                    $image_url = $image_data[0] ?? '';
                    // Skip this iteration if image_data is false
                    if(!$image_data || empty($image_url)) {
                        continue;
                    }
                    $i++; 
                    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

                    if( $settings['popup_type'] == 'elementor' ) {
                        $full_image = wp_get_attachment_image_src( $image_id, 'full' );
                        $full_image_url = $full_image[0] ?? '';
                        $dataModal = 'href="#" data-src="'.esc_url($full_image_url).'" data-houzez-fancybox data-fancybox="block-gallery"';
                        $builtin_gallery_class = '';
                    }
                ?>
                <div class="col">
                <a <?php echo $dataModal; ?> data-slider-no="<?php echo esc_attr($i); ?>" class="gallery-grid-item<?php echo $builtin_gallery_class; ?><?php if($i == $visible_images && $remaining_images > 0 ){ echo ' more-images'; } elseif($i > $visible_images) {echo ' gallery-hidden'; } ?>">
                        <?php if( $i == $visible_images && $remaining_images > 0 ){ echo '<span>'.$remaining_images.'+</span>'; } ?>
                        <img class="img-fluid" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                    </a>
                </div>
                <?php } ?>
                
            </div>
        </div><!-- property-gallery-grid -->
        <?php 
        }
        $this->reset_preview_query(); // Only for preview 
	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new Property_Section_Block_Gallery() );