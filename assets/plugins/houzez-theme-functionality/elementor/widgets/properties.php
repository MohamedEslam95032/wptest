<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Properties Widget.
 * @since 1.5.6
 */
class Houzez_Elementor_Properties extends Widget_Base {
    use Houzez_Filters_Traits;
     
    /**
     * DEPRECATED: This module is deprecated. Please use Property Cards V1 or Property Cards V2 instead.
     */

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.5.6
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_properties';
    }

    /**
     * Get widget title.
     * @since 1.5.6
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Properties', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 1.5.6
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-post-list';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.5.6
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
     * @since 1.5.6
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
            'deprecated_notice',
            [
                'label' => '',
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('<span style="color: red; font-weight: bold;">DEPRECATED:</span> This widget is deprecated. Please use Property Cards V1 or Property Cards V2 instead.', 'houzez-theme-functionality'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
            ]
        );

        $this->add_control(
            'prop_grid_style',
            [
                'label'     => esc_html__( 'Grid/List Style', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'v_1'  => esc_html__( 'Version 1', 'houzez-theme-functionality'),
                    'v_2'    => esc_html__( 'Version 2', 'houzez-theme-functionality')
                ],
                'description' => esc_html__('Choose grid/list style, default will be version 1', 'homey'),
                'default' => 'v_1',
            ]
        );

        $this->add_control(
            'module_type',
            [
                'label'     => esc_html__( 'Layout', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'grid_3_cols'  => esc_html__( 'Grid 3 Columns', 'houzez-theme-functionality'),
                    'grid_2_cols'    => esc_html__( 'Grid 2 Columns', 'houzez-theme-functionality'),
                    'list'    => esc_html__( 'List View', 'houzez-theme-functionality')
                ],
                'description' => '',
                'default' => 'grid_3_cols',
            ]
        );

        $this->listings_cards_thumb_size_control();

        $this->listings_cards_general_filters();

        $this->add_control(
            'pagination_type',
            [
                'label'     => esc_html__( 'Pagination', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => houzez_pagination_type(),
                'description' => '',
                'default' => 'loadmore',
            ]
        );
        
        $this->end_controls_section();

        //Filters
        $this->start_controls_section(
            'filters_section',
            [
                'label'     => esc_html__( 'Filters', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->listings_cards_filters();

        $this->end_controls_section();

    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.5.6
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();
        $card_version = $settings['prop_grid_style'];
        // Convert Elementor settings to shortcode attributes format
        $args = $this->listings_cards_args($settings);
        $args['thumb_size'] = 'large';
        $module_type = $settings['module_type'];
        
        // Use the core function to render property cards
        echo houzez_get_property_cards($args, $module_type, $card_version);

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Properties );