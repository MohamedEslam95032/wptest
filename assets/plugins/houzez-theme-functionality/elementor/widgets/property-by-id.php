<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Property by ID Widget.
 * @since 1.5.6
 */
class Houzez_Elementor_Property_By_ID extends Widget_Base {
    use Houzez_Property_Cards_Traits;

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
        return 'houzez_elementor_property_by_id';
    }

    /**
     * Get widget title.
     * @since 1.5.6
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Property by ID', 'houzez-theme-functionality' );
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
            'prop_grid_style',
            [
                'label'     => esc_html__( 'Grid Style', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'v1'  => esc_html__( 'Property Card v1', 'houzez-theme-functionality'),
                    'v2'    => esc_html__( 'Property Card v2', 'houzez-theme-functionality'),
                    'v3'    => esc_html__( 'Property Card v3', 'houzez-theme-functionality'),
                    'v4'    => esc_html__( 'Property Card v4', 'houzez-theme-functionality'),
                    'v5'    => esc_html__( 'Property Card v5', 'houzez-theme-functionality'),
                    'v6'    => esc_html__( 'Property Card v6', 'houzez-theme-functionality'),
                    'v7'    => esc_html__( 'Property Card v7', 'houzez-theme-functionality'),
                    'v8'    => esc_html__( 'Property Card v8', 'houzez-theme-functionality'),
                ],
                'description' => esc_html__('Choose grid style, default will be propety card v1', 'homey'),
                'default' => 'v1',
                'selectors_dictionary' => [  // Map old keys to new keys for compatibility
                    'v_1' => 'v1',
                    'v_2' => 'v2',
                    'v_3' => 'v3',
                    'v_4' => 'v4',
                    'v_5' => 'v5',
                    'v_6' => 'v6',
                    'v_7' => 'v7',
                    'v_8' => 'v8',
                ],
            ]
        );

        $this->add_control(
            'grid_style',
            [
                'label' => esc_html__( 'only for hack', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->add_control(
            'listings_layout',
            [
                'label' => esc_html__( 'only for hack', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->add_control(
            'property_id',
            [
                'label'     => esc_html__( 'Property ID', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => esc_html__( 'Enter property ID. Ex 305', 'houzez-theme-functionality' ),
            ]
        );
        
        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Show/Hide 
        * -------------------------------------------------------------------------------*/

        $this->Property_Cards_Show_Hide_MultiGrid_Traits();


        /*--------------------------------------------------------------------------------
        * Typography
        * -------------------------------------------------------------------------------*/

        $this->Property_Cards_Typography_MultiGrid_Traits();

        /*--------------------------------------------------------------------------------
        * Margin and Spacing
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_spacing_margin_section',
            [
                'label'     => esc_html__( 'Spaces & Sizes', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'hz_title_margin_bottom',
            [
                'label' => esc_html__( 'Title Margin Bottom(px)', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-title' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'hz_address_margin_bottom',
            [
                'label' => esc_html__( 'Address Margin Bottom(px)', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-address' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'hz_meta_icons',
            [
                'label' => esc_html__( 'Meta Icons Size(px)', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-amenities i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hz_content_padding',
            [
                'label'      => esc_html__( 'Content Area Padding', 'houzez-theme-functionality' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .item-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Card Box
        * -------------------------------------------------------------------------------*/

        $this->Property_Cards_Box_MultiGrid_Traits();

        /*--------------------------------------------------------------------------------
        * Colors
        * -------------------------------------------------------------------------------*/
        
        $this->Property_Cards_Colors_MultiGrid_Traits();

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
                
        $prop_grid_style = $settings['prop_grid_style'] ? $settings['prop_grid_style'] : $settings['grid_style'];
        $args['prop_grid_style'] =  $prop_grid_style;
        $args['property_id']     =  $settings['property_id'];
        $module_type = 'grid_1_col';
        $card_version = $prop_grid_style;
        
        // Use the core function to render property cards
        echo houzez_get_property_cards($args, $module_type, $card_version);

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Property_By_ID );