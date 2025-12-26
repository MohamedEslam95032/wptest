<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Agents Widget.
 * @since 1.5.6
 */
class Houzez_Elementor_Agents extends Widget_Base {

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
        return 'houzez_elementor_agents';
    }

    /**
     * Get widget title.
     * @since 1.5.6
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Agents', 'houzez-theme-functionality' );
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

        $agent_category = array();
        $agent_city = array();
        
        houzez_get_terms_array( 'agent_category', $agent_category );
        houzez_get_terms_array( 'agent_city', $agent_city );

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'agents_type',
            [
                'label'     => esc_html__( 'Module Type', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'grid'  => esc_html__('Grid', 'houzez-theme-functionality'),
                    'Carousel'    => esc_html__('Carousel', 'houzez')
                ],
                "description" => '',
                'default' => 'grid',
            ]
        );

        $this->add_control(
            'columns',
            [
                'label'     => esc_html__( 'Columns', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '4'  => esc_html__('4 Columns', 'houzez-theme-functionality'),
                    '3'  => esc_html__('3 Columns', 'houzez-theme-functionality')
                ],
                "description" => '',
                'default' => '3',
            ]
        );

        $this->add_control(
            'agent_category',
            [
                'label'     => esc_html__( 'Category', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => $agent_category,
                'description' => '',
                'default' => '',
            ]
        );

        $this->add_control(
            'agent_city',
            [
                'label'     => esc_html__('City', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => $agent_city,
                'description' => '',
                'default' => '',
            ]
        );

        $this->add_control(
            'posts_limit',
            [
                'label'     => esc_html__('Number of Agents', 'houzez-theme-functionality'),
                'type'      => Controls_Manager::TEXT,
                'description' => '',
                'default' => '9',
            ]
        );

        $this->add_control(
            'offset',
            [
                'label'     => 'Offset',
                'type'      => Controls_Manager::TEXT,
                'description' => '',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'     => esc_html__( 'Order By', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'none'  => esc_html__( 'None', 'houzez-theme-functionality'),
                    'ID'  => esc_html__( 'ID', 'houzez-theme-functionality'),
                    'title'   => esc_html__( 'Title', 'houzez-theme-functionality'),
                    'date'   => esc_html__( 'Date', 'houzez-theme-functionality'),
                    'rand'   => esc_html__( 'Random', 'houzez-theme-functionality'),
                    'menu_order'   => esc_html__( 'Menu Order', 'houzez-theme-functionality'),
                ],
                'default' => 'none',
            ]
        );
        $this->add_control(
            'order',
            [
                'label'     => esc_html__( 'Order', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'ASC'  => esc_html__( 'ASC', 'houzez-theme-functionality'),
                    'DESC'  => esc_html__( 'DESC', 'houzez-theme-functionality')
                ],
                'default' => 'ASC',
            ]
        );

        $this->end_controls_section();

        /*----------------------------------------------------------
        * Styling
        **---------------------------------------------------------*/
        $this->start_controls_section(
            'styling_section',
            [
                'label'     => esc_html__( 'Box', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'agent_box_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .agent-module .agent-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'agent_box_border',
                'selector' => '{{WRAPPER}} .agent-module .agent-item',
            ]
        );

        $this->add_control(
            'agent_box_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-module .agent-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'agent_box_shadow',
                'selector' => '{{WRAPPER}} .agent-module .agent-item',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'typo_section',
            [
                'label'     => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'agent_title',
                'label'    => esc_html__( 'Agent Name', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-name',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'agent_position',
                'label'    => esc_html__( 'Position', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-company',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'agent_content_bio',
                'label'    => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-body',
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'agent_button_typo',
                'label'    => esc_html__( 'Button', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-link a',
            ]
        );

        $this->end_controls_section(); 


        $this->start_controls_section(
            'button_section',
            [
                'label'     => esc_html__( 'Button', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs(
            'button_style_tabs'
        );

        $this->start_controls_tab(
            'style_normal_botton',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );

        $this->add_control(
            'view_button_bg',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-link a' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'view_button_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-link a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'agent_button_border',
                'selector' => '{{WRAPPER}} .agent-link a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_hover_botton',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );

        $this->add_control(
            'view_button_hover_bg',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-link a:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'view_button_hover_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-link a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'agent_button_hover_border',
                'selector' => '{{WRAPPER}} .agent-link a:hover',
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
     * @since 1.5.6
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();

        $agent_category = $agent_city = array();

        if(!empty($settings['agent_category'])) {
            $agent_category = $settings['agent_category'];
        }

        if(!empty($settings['agent_city'])) {
            $agent_city = $settings['agent_city'];
        }

        $args['agent_category']   =  $agent_category;
        $args['agent_city']   =  $agent_city;

        $args['agents_type'] =  $settings['agents_type'];
        $args['orderby'] =  $settings['orderby'];
        $args['posts_limit'] =  $settings['posts_limit'];
        $args['columns'] =  $settings['columns'];
        $args['order'] =  $settings['order'];
        $args['offset'] =  $settings['offset'];
        
        if( function_exists( 'houzez_agents' ) ) {
            echo houzez_agents( $args );
        }
    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Agents );