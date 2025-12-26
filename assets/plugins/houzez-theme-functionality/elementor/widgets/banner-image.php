<?php

namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Grids Widget.
 * @since 3.0
 */
class Houzez_Banner_Image extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since v3.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Houzez_Banner_Image';
    }

    /**
     * Get widget title.
     * @since v3.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Image Banner', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since v3.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-image';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since v3.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'houzez-elements' ];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 3.0
     * @access public
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return [ 'image', 'photo', 'banner' ];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since v3.0
     * @access protected
     */
    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => esc_html__( 'Choose Image', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
                'exclude' => array_merge(
                    ['houzez-gallery', '1536x1536', '2048x2048'], // Always exclude 'houzez-gallery'
                    \Houzez_Image_Sizes::get_disabled_sizes_for_elementor() // Get dynamically disabled sizes
                ),
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'banner_title',
            [
                'label'     => esc_html__( 'Text', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::TEXT,
                'description' => '',
                'default' => '',
            ]
        );

        $this->add_control(
            'link_to',
            [
                'label' => esc_html__( 'Link', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__( 'None', 'houzez-theme-functionality' ),
                    'custom' => esc_html__( 'Custom URL', 'houzez-theme-functionality' ),
                ],
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__( 'Link', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'link_to' => 'custom',
                ],
                'show_label' => false,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_banner',
            [
                'label' => esc_html__( 'Image', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__( 'Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'space',
            [
                'label' => esc_html__( 'Max Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'object-fit',
            [
                'label' => esc_html__( 'Object Fit', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'condition' => [
                    'height[size]!' => '',
                ],
                'options' => [
                    '' => esc_html__( 'Default', 'houzez-theme-functionality' ),
                    'fill' => esc_html__( 'Fill', 'houzez-theme-functionality' ),
                    'cover' => esc_html__( 'Cover', 'houzez-theme-functionality' ),
                    'contain' => esc_html__( 'Contain', 'houzez-theme-functionality' ),
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'object-position',
            [
                'label' => esc_html__( 'Object Position', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'center center' => esc_html__( 'Center Center', 'houzez-theme-functionality' ),
                    'center left' => esc_html__( 'Center Left', 'houzez-theme-functionality' ),
                    'center right' => esc_html__( 'Center Right', 'houzez-theme-functionality' ),
                    'top center' => esc_html__( 'Top Center', 'houzez-theme-functionality' ),
                    'top left' => esc_html__( 'Top Left', 'houzez-theme-functionality' ),
                    'top right' => esc_html__( 'Top Right', 'houzez-theme-functionality' ),
                    'bottom center' => esc_html__( 'Bottom Center', 'houzez-theme-functionality' ),
                    'bottom left' => esc_html__( 'Bottom Left', 'houzez-theme-functionality' ),
                    'bottom right' => esc_html__( 'Bottom Right', 'houzez-theme-functionality' ),
                ],
                'default' => 'center center',
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module img' => 'object-position: {{VALUE}};',
                ],
                'condition' => [
                    'object-fit' => 'cover',
                ],
            ]
        );

        $this->add_control(
            'separator_panel_style',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $this->start_controls_tabs( 'image_effects' );

        $this->start_controls_tab( 'normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'opacity',
            [
                'label' => esc_html__( 'Opacity', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module .banner-image-module-link' => 'background-color: rgba(0, 0, 0, {{SIZE}});',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .banner-image-module img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab( 'hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'opacity_hover',
            [
                'label' => esc_html__( 'Opacity', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module .banner-image-module-link:hover' => 'background-color: rgba(0, 0, 0, {{SIZE}});',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .banner-image-module:hover img',
            ]
        );

        $this->add_control(
            'background_hover_transition',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module .banner-image-module-link:hover' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .banner-image-module',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .banner-image-module' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .banner-image-module',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_text',
            [
                'label' => esc_html__( 'Text', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .banner-image-content-wrap a',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .banner-image-content-wrap a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Retrieve image widget link URL.
     *
     * @since 3.0
     * @access protected
     *
     * @param array $settings
     *
     * @return array|string|false An array/string containing the link URL, or false if no link.
     */
    protected function get_link_url( $settings ) {
        if ( 'none' === $settings['link_to'] ) {
            return false;
        }

        if ( 'custom' === $settings['link_to'] ) {
            if ( empty( $settings['link']['url'] ) ) {
                return false;
            }

            return $settings['link'];
        }

        return [
            'url' => $settings['image']['url'],
        ];
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since v3.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();

        if ( empty( $settings['image']['url'] ) ) {
            return;
        }

        $banner_title = $settings['banner_title'];

        $image_id = $settings['image']['id']; // Get the image ID
        $image_size = Group_Control_Image_Size::get_attachment_image_src($image_id, 'image', $settings); // Get the selected size

        $link = $this->get_link_url( $settings );
        if ( $link ) {
            $this->add_link_attributes( 'link', $link );

            $this->add_render_attribute( 'link', [
                'class' => 'banner-image-module-link w-100 h-100 top-0 bottom-0 start-0 end-0',
            ] );

        }
        ?>

        <div class="banner-image-module d-flex justify-content-center align-items-center">
            <?php if( $banner_title ) { ?>
            <div class="banner-image-content-wrap p-4 text-center">
                <a><?php echo esc_html($banner_title);?></a>
            </div>
            <?php } ?>
            
            <?php
            if( $image_size ) {
                // Output the image tag
                echo '<img class="img-fluid" src="' . esc_url($image_size) . '" alt="' . esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)) . '">';
            }
            ?>

            <?php if ( $link ) : ?>
                <a <?php $this->print_render_attribute_string( 'link' ); ?>></a>
            <?php else: ?>
                <a class="banner-image-module-link w-100 h-100 top-0 bottom-0 start-0 end-0"></a>
            <?php endif; ?>
        </div>
        
   <?php
    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Banner_Image );