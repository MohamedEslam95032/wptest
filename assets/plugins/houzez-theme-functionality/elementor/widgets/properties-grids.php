<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Properties Grids Widget.
 * @since 1.5.6
 */
class Houzez_Elementor_Properties_Grids extends Widget_Base {
    use Houzez_Filters_Traits;
     

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
        return 'houzez_elementor_properties_grids';
    }

    /**
     * Get widget title.
     * @since 1.5.6
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Property Grids', 'houzez-theme-functionality' );
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
        return 'houzez-element-icon eicon-posts-grid';
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
            'prop_grid_type',
            [
                'label'     => esc_html__( 'Grid Style', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'grid_1'  => 'Grid v1',
                    'grid_2'    => 'Grid v2',
                    'grid_3'    => 'Grid v3',
                    'grid_4'    => 'Grid v4',
                ],
                'description' => '',
                'default' => 'grid_1',
            ]
        );

        $this->listings_cards_general_filters();

        
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

        $this->start_controls_section(
            'property_grids_settings',
            [
                'label' => esc_html__( 'Settings', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'hide_tools',
            [
                'label' => esc_html__( 'Hide Tools', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-grids-module .item-tools' => 'display: {{VALUE}};',
                ],
            ]
        );


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
        global $ele_lazyloadbg; 
        
        $settings = $this->get_settings_for_display();
        // Convert Elementor settings to shortcode attributes format
        $args = $this->listings_cards_args($settings);

        $ele_lazyloadbg = '';
        if ( ! Plugin::$instance->editor->is_edit_mode() ) {
            $ele_lazyloadbg = houzez_get_lazyload_for_bg();
        }
        $args['ele_lazyloadbg']   =  $ele_lazyloadbg;
        $args['prop_grid_type']   =  $settings['prop_grid_type'];
        
        if( function_exists( 'houzez_prop_grids' ) ) {
            echo houzez_prop_grids( $args );
        }

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Properties_Grids );