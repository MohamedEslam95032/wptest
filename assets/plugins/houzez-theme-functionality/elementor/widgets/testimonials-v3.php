<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Testimonials Widget.
 * @since 3.2.5
 */
class Houzez_Elementor_Testimonials_v3 extends Widget_Base {
    use Houzez_Testimonials_Traits;

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 3.2.5
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_testimonials_v3';
    }

    /**
     * Get widget title.
     * @since 3.2.5
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Testimonials v3', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 3.2.5
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-testimonial';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 3.2.5
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
     * @since 3.2.5
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

        $this->houzez_testimonials_content_controls();
        
        $this->end_controls_section();

        $this->houzez_testimonials_content_style_controls();

        $this->start_controls_section(
            'section_style_testimonial_image',
            [
                'label' => esc_html__( 'Image', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_testimonials_image_style_controls();

        $this->end_controls_section();

        $this->houzez_testimonials_name_style_controls();

        $this->houzez_testimonials_job_style_controls();


        $this->start_controls_section(
            'testimonial_button_stype',
            [
                'label' => esc_html__( 'Buttons', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style', [
            
        ] );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary:before' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__('Background Color', 'houzez-theme-functionality'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary:hover:before, {{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary:focus:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_hover',
            [
                'label' => esc_html__('Background Color', 'houzez-theme-functionality'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary:hover, {{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary:focus' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary:hover, {{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_transition_duration',
            [
                'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .testimonials-slider-buttons-wrap .btn-primary',
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 3.2.5
     * @access protected
     */
    protected function render() {

        global $houzez_local;
        $settings = $this->get_settings_for_display();
        $houzez_local = houzez_get_localization();
            
        $args['posts_limit']     =  $settings['posts_limit'];
        $args['offset']  =  $settings['offset'];
        $args['orderby']  =  $settings['orderby'];
        $args['order']  =  $settings['order'];
       
        if( function_exists( 'houzez_testimonials' ) ) {
            echo houzez_testimonials_v3( $args );
        }

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Testimonials_v3 );