<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Grids Widget.
 * @since 3.0
 */
class Houzez_Taxonomies_Grids extends Widget_Base {
    use Houzez_Filters_Traits;

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
        return 'Houzez_Taxonomies_Grids';
    }

    /**
     * Get widget title.
     * @since v3.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Taxonomies Grids', 'houzez-theme-functionality' );
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
        return 'houzez-element-icon eicon-gallery-grid';
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

        $this->add_responsive_control(
            'houzez_cards_columns',
            [
                'label'     => esc_html__( 'Columns', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '1cols'  => '1 Column',
                    '2cols'  => '2 Columns',
                    '3cols'  => '3 Columns',
                    '4cols'  => '4 Columns',
                    '5cols'  => '5 Columns',
                    '6cols'  => '6 Columns',
                ],
                'description' => '',
                'default' => '4cols',
                'tablet_default' => '2cols',
                'mobile_default' => '1cols',
            ]
        );

        $this->add_control(
            'tax_thumb_size',
            [
                'label' => esc_html__( 'Image Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => \Houzez_Image_Sizes::get_enabled_image_sizes_for_elementor(),
                'default' => 'houzez-top-v7',
            ]
        );

        $this->add_control(
            'houzez_cards_from',
            [
                'label'     => esc_html__( 'Choose Taxonomy', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'property_type' => 'Types',
                    'property_status' => 'Status',
                    'property_label' => 'Label',
                    'property_country' => 'Country',
                    'property_state' => 'State',
                    'property_city' => 'City',
                    'property_area' => 'Area',
                ],
                'description' => '',
                'default' => 'property_type',
            ]
        );

        $this->listing_taxonomies_controls();
        
        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Styling
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'style_secingion',
            [
                'label'     => esc_html__('Content', 'houzez-theme-functionality'),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_type',
                'label'    => esc_html__( 'Title Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .taxonomy-text-wrap .taxonomy-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'subtitle_type',
                'label'    => esc_html__( 'Count Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .taxonomy-text-wrap .taxonomy-subtitle',
            ]
        );

        $this->add_control(
            'tax_title_color',
            [
                'label'     => esc_html__( 'Title Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-grids-module .taxonomy-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'tax_count_color',
            [
                'label'     => esc_html__( 'Count Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-grids-module .taxonomy-subtitle' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid-height',
            [
                'label'          => __( 'Height', 'houzez-theme-functionality' ),
                'type'           => Controls_Manager::SLIDER,
                'default'        => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units'     => [ '%', 'px', 'vw' ],
                'range'          => [
                    '%'  => [
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
                'selectors'      => [
                    '{{WRAPPER}} .taxonomy-grids-module-v5 .taxonomy-item' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid-gap',
            [
                'label' => esc_html__( 'Grid Gap', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-grids-module-grid' => 'grid-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid-padding',
            [
                'label' => esc_html__( 'Grid Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-grids-module .taxonomy-text-wrap' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tax_box_radius',
            [
                'label'      => esc_html__( 'Radius', 'houzez-theme-functionality' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .taxonomy-grids-module .taxonomy-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'box_shadow',
                'label'    => esc_html__( 'Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .taxonomy-grids-module .taxonomy-item',
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
                    '{{WRAPPER}} .hover-effect-flat:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'opacity_color',
            [
                'label'     => esc_html__( 'Opacity Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .hover-effect-flat:before' => 'background-color: {{VALUE}}',
                ],
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
                    '{{WRAPPER}} .hover-effect-flat:hover:before' => 'opacity: {{SIZE}};',
                ],
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
                    '{{WRAPPER}} .hover-effect-flat:hover:before' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

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

        global $houzez_local;
        $settings = $this->get_settings_for_display();
        $houzez_local = houzez_get_localization();
        
        $property_type = $property_status = $property_label = $property_country = $property_state = $property_city = $property_area = array();

        if(!empty($settings['property_type'])) {
            $property_type = implode (",", $settings['property_type']);
        }

        if(!empty($settings['property_status'])) {
            $property_status = implode (",", $settings['property_status']);
        }

        if(!empty($settings['property_label'])) {
            $property_label = implode (",", $settings['property_label']);
        }

        if(!empty($settings['property_state'])) {
            $property_state = implode (",", $settings['property_state']);
        }

        if(!empty($settings['property_country'])) {
            $property_country = implode (",", $settings['property_country']);
        }

        if(!empty($settings['property_city'])) {
            $property_city = implode (",", $settings['property_city']);
        }

        if(!empty($settings['property_area'])) {
            $property_area = implode (",", $settings['property_area']);
        }

        $args['houzez_cards_from'] =  $settings['houzez_cards_from'];
        $args['houzez_cards_columns'] =  $settings['houzez_cards_columns'];
        $args['houzez_cards_columns_tablet'] =  $settings['houzez_cards_columns_tablet'] ?? $settings['houzez_cards_columns'];
        $args['houzez_cards_columns_mobile'] =  $settings['houzez_cards_columns_mobile'] ?? $settings['houzez_cards_columns'];
        $args['houzez_show_child'] =  $settings['houzez_show_child'];
        $args['houzez_hide_count'] =  $settings['houzez_hide_count'];
        $args['orderby'] =  $settings['orderby'];
        $args['order'] =  $settings['order'];
        $args['houzez_hide_empty'] =  $settings['houzez_hide_empty'];
        $args['no_of_terms'] =  $settings['no_of_terms'];
        $args['thumb_size'] = $settings['tax_thumb_size'] === 'global' ? 'houzez-top-v7' : $settings['tax_thumb_size'];

        $args['property_type']   =  $property_type;
        $args['property_status']   =  $property_status;
        $args['property_label']   =  $property_label;
        $args['property_country']   =  $property_country;
        $args['property_state']   =  $property_state;
        $args['property_city']   =  $property_city;
        $args['property_area']   =  $property_area;
       
        if( function_exists( 'houzez_taxonomies_grids' ) ) {
            echo houzez_taxonomies_grids( $args );
        }

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Taxonomies_Grids );