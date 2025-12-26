<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use PHPUnit\Framework\Constraint\IsFalse;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Toparea_v6 extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-toparea-v6';
	}

	public function get_title() {
		return __( 'Section Top Area v6', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-featured-image';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['property', 'toparea v6', 'houzez', 'gallery' ];
	}

	protected function register_controls() {
		parent::register_controls();


        $repeater = new Repeater();
        $field_types = array();

        $field_types = [
            'address' => esc_html__( 'Address', 'houzez-theme-functionality' ),
            'streat-address' => esc_html__( 'Streat Address', 'houzez-theme-functionality' ),
            'country' => esc_html__( 'Country', 'houzez-theme-functionality' ),
            'state' => esc_html__( 'State', 'houzez-theme-functionality' ),
            'city' => esc_html__( 'City', 'houzez-theme-functionality' ),
            'area' => esc_html__( 'area', 'houzez-theme-functionality' ),
            
        ];
        /**
         * field types.
         */
        $field_types = apply_filters( 'houzez/address_title', $field_types );

        $repeater->add_control(
            'field_type',
            [
                'label' => esc_html__( 'Field', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => $field_types,
                'default' => 'text',
            ]
        );

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

        $this->add_responsive_control(
            'padding_bottom',
            [
                'label' => __( 'Padding Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-detail-v6 .property-banner' => 'padding-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();


        //Breadcrumb
        $this->start_controls_section(
            'section_breadcrumb',
            [
                'label' => __( 'Breadcrumb', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_breadcrumb',
            [
                'label' => esc_html__( 'Show Breadcrumb', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->end_controls_section();

        $this->houzez_property_topareas_style_traits();

        // Property Title
        $this->start_controls_section(
            'section_prop_title',
            [
                'label' => __( 'Property Title', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__( 'Show Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'prop_title_color',
            [
                'label' => __( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .page-title h1' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'prop_title_typography',
                'selector' => '{{WRAPPER}} .page-title h1',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'prop_title_text_shadow',
                'label' => __( 'Text Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .page-title h1',
            ]
        );

        $this->add_responsive_control(
            'title_info_margin_top',
            [
                'label' => __( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .page-title-wrap .page-title, .mobile-property-title .page-title' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_info_margin_bottom',
            [
                'label' => __( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .page-title-wrap .page-title, .mobile-property-title .page-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->end_controls_section();

        // Property labels
        $this->start_controls_section(
            'section_prop_labels',
            [
                'label' => __( 'Property Labels', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_labels',
            [
                'label' => esc_html__( 'Show Labels', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->end_controls_section();

        // Property Address
        $this->start_controls_section(
            'section_prop_address',
            [
                'label' => __( 'Property Address', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_address',
            [
                'label' => esc_html__( 'Show Address', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'address_fields',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        '_id' => 'address',
                        'field_type' => 'address',
                    ],
                ],
                'title_field' => '{{{ field_type }}}',
            ]
        );

        $this->add_control(
            'hide_icon',
            [
                'label' => esc_html__( 'Hide Icon', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .item-address .icon-pin' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'address_color',
            [
                'label' => __( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-address' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'address_typography',
                'selector' => '{{WRAPPER}} .item-address',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'address_text_shadow',
                'label' => __( 'Text Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-address',
            ]
        );

        $this->end_controls_section();


        // Property Price
        $this->start_controls_section(
            'section_prop_price',
            [
                'label' => __( 'Property Price', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => esc_html__( 'Show Price', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'item_price_color',
            [
                'label' => __( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_price_top',
            [
                'label' => __( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-price' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_price_bottom',
            [
                'label' => __( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-price' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .item-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'item_price_text_shadow',
                'label' => __( 'Text Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-price',
            ]
        );

        $this->add_control(
            'item_sub_price_heading',
            [
                'label' => __( 'Second Price', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'item_sub_price_color',
            [
                'label' => __( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-sub-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_sub_price_typography',
                'selector' => '{{WRAPPER}} .item-sub-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'item_sub_price_text_shadow',
                'label' => __( 'Text Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-sub-price',
            ]
        );

        $this->end_controls_section();

        // Tools
        $this->start_controls_section(
            'section_Tools',
            [
                'label' => __( 'Tools', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'hide_favorite',
            [
                'label' => esc_html__( 'Favorite', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'hide_social',
            [
                'label' => esc_html__( 'Social Share', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'hide_print',
            [
                'label' => esc_html__( 'Print', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'buttons_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_bg_color_hover',
            [
                'label'     => esc_html__( 'Background Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color_hover',
            [
                'label'     => esc_html__( 'Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_border_color_hover',
            [
                'label'     => esc_html__( 'Border Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'border-color: {{VALUE}}',
                ],
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
        $featured_image_id = get_post_thumbnail_id($post->ID);
        $total_images = count($images_ids);

        $mobile_images_ids = $images_ids;
        if (($key = array_search($featured_image_id, $mobile_images_ids)) !== false) {
            unset($mobile_images_ids[$key]);
        }

        $featured_image = wp_get_attachment_image_src( $featured_image_id, $size, true );
        $featured_image_url = $featured_image[0] ?? '';
        $map_street_view = get_post_meta( $post->ID, 'fave_property_map_street_view', true );

        $gallery_type = houzez_get_popup_gallery_type();
        $token = wp_generate_password(5, false, false);
        $dataModal = 'href="#" data-bs-toggle="modal" data-bs-target="#property-lightbox"';
        $css_class = 'houzez-trigger-popup-slider-js';
        ?>

        <style>
            .elementor-widget-houzez-property-toparea-v6 .property-detail-v6 .page-title-wrap, .elementor-widget-houzez-property-toparea-v6 .property-detail-v6 .property-banner {
                background: none;
            }
        </style>

        <div class="property-wrap property-detail-v6">
            <?php 
            if( $settings['show_breadcrumb'] || $settings['show_title'] || $settings['show_price'] || $settings['show_address'] || $settings['show_labels'] ) {
                htf_get_template_part('elementor/template-part/single-property/property-title'); 
            }?>

            <div class="property-top-wrap">
                <div class="property-banner">
                    <div class="d-block d-md-none" role="region">
                        <div class="property-image-count d-block d-md-none" role="status">
                            <i class="houzez-icon icon-picture-sun" aria-hidden="true"></i> <span><?php echo esc_attr($total_images); ?></span>
                        </div>

                        <?php if( $gallery_type == 'photoswipe' ) {?> 
                            <div itemscope itemtype="http://schema.org/ImageGallery">
                                <a href="#" class="property-banner-trigger position-absolute top-0 start-0 w-100 h-100" data-src="<?php echo esc_url($featured_image_url); ?>" data-houzez-fancybox data-fancybox="gallery-mobile-<?php echo esc_attr($token); ?>" itemprop="contentUrl"></a>
                                <?php
                                if(!empty($mobile_images_ids)) {
                                    foreach( $mobile_images_ids as $image_id ) {
                                        $image_data = wp_get_attachment_image_src($image_id, 'full');

                                        // Skip this iteration if image_data is false
                                        if(!$image_data) {
                                            continue;
                                        }

                                        $image_url = $image_data[0] ?? '';
                                        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                                        ?>
                                        <div itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" class="gallery-hidden">
                                            <a href="#" data-src="<?php echo esc_url($image_url); ?>" itemprop="contentUrl" data-houzez-fancybox data-fancybox="gallery-mobile-<?php echo esc_attr($token); ?>">
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
                        <?php } ?>
                        <img class="property-featured-image w-100 h-100 left-0 top-0" src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo get_the_title($post->ID); ?>" role="img">
                    </div><!-- visible-on-mobile -->

                    <div class="d-none d-md-block" role="region">
                        <div class="hs-gallery-v4-grid pb-4">
                            <?php
                            if(!empty($images_ids)) {
                                foreach( $images_ids as $image ) { $i++; 

                                    $image_id = $image;
                                    $image_data = wp_get_attachment_image_src( $image_id, $size );

                                    // Skip this iteration if image_data is false
                                    if(!$image_data) {
                                        continue;
                                    }

                                    if( $gallery_type == 'photoswipe' ) {
                                        $full_image = wp_get_attachment_image_src( $image_id, 'full' );
                                        $full_image_url = $full_image[0] ?? '';
                                        $dataModal = 'href="#" data-src="'.esc_url($full_image_url).'" data-houzez-fancybox data-fancybox="gallery-<?php echo esc_attr($token); ?>"';
                                        $css_class = '';
                                    }
                                
                                    if($i == 1) {
                                    ?>
                                    <div class="hs-gallery-v4-grid-item hs-gallery-v4-grid-item-01">
                                        <a data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" class="img-wrap-1<?php echo esc_attr($css_class); ?>" <?php echo $dataModal; ?>>
                                            <img class="img-fluid" src="<?php echo esc_url($image_data[0]); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
                                        </a>
                                    </div><!-- hs-gallery-v4-grid-item -->
                                    <?php } elseif($i == 2 || $i == 3) { ?>

                                    <?php if($i == 2) { ?>
                                    <div class="hs-gallery-v4-grid-item hs-gallery-v4-grid-item-02">
                                        <?php } ?>
                                            <a data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" <?php echo $dataModal; ?> class="<?php echo esc_attr($css_class); ?> img-wrap-<?php echo esc_attr($i); ?>">
                                                <?php if($total_images > 3 && $i == 3) { ?>
                                                <div class="img-wrap-3-text"><i class="houzez-icon icon-picture-sun me-1" aria-hidden="true"></i> <?php echo $total_images-3; ?> <?php echo esc_html__('More', 'houzez'); ?></div>
                                                <?php } ?>

                                                <img class="img-fluid" src="<?php echo esc_url($image_data[0]); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
                                            </a>
                                        <?php if( ($i == 3 && $total_images == 3) || ( $i == 2 && $total_images == 2 ) || ( $i == 1 && $total_images == 1 ) || $i == 3 ) { ?>
                                    </div><!-- hs-gallery-v4-grid-item -->
                                    <?php } ?>
                                    <?php } else { ?>
                                        <a class="img-wrap-1 gallery-hidden" <?php echo $dataModal; ?>>
                                            <img class="img-fluid" src="<?php echo esc_url($image_data[0]); ?>" alt="<?php echo esc_attr($image_data[1]); ?>">
                                        </a>
                                    <?php
                                    }
                                    $j++;
                                }
                            }?>
                        </div><!-- hs-gallery-v4-grid -->
                    </div><!-- hidden-on-mobile -->
                </div><!-- property-banner -->
            </div><!-- property-top-wrap -->
            <?php htf_get_template_part('elementor/template-part/single-property/mobile', 'view', array('media_tabs' => false));?>
        </div><!-- property-wrap -->
        <?php
        $this->reset_preview_query(); // Only for preview
	}
}
Plugin::instance()->widgets_manager->register( new Property_Toparea_v6 );