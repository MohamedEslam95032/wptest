<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Section Title Widget.
 * @since 2.0
 */
class Houzez_Elementor_Page_Title extends Widget_Base {
    use Houzez_Style_Traits;

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 2.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_page_title';
    }

    /**
     * Get widget title.
     * @since 2.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Page Title', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 2.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-post-title';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 2.0
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
     * @since 2.0
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
            'show_breadcrumb',
            [
                'label' => __( 'Show Breadcrumb', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'houzez-theme-functionality' ),
                'label_off' => __( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => esc_html__( 'Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'Justified', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title, .elementor-heading-title a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .page-title h1' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .elementor-heading-title, {{WRAPPER}} .page-title h1',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .elementor-heading-title, {{WRAPPER}} .page-title h1',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .elementor-heading-title, {{WRAPPER}} .page-title',
            ]
        );

        $this->add_control(
            'blend_mode',
            [
                'label' => esc_html__( 'Blend Mode', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
                    'multiply' => esc_html__( 'Multiply', 'houzez-theme-functionality' ),
                    'screen' => esc_html__( 'Screen', 'houzez-theme-functionality' ),
                    'overlay' => esc_html__( 'Overlay', 'houzez-theme-functionality' ),
                    'darken' => esc_html__( 'Darken', 'houzez-theme-functionality' ),
                    'lighten' => esc_html__( 'Lighten', 'houzez-theme-functionality' ),
                    'color-dodge' => esc_html__( 'Color Dodge', 'houzez-theme-functionality' ),
                    'saturation' => esc_html__( 'Saturation', 'houzez-theme-functionality' ),
                    'color' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                    'difference' => esc_html__( 'Difference', 'houzez-theme-functionality' ),
                    'exclusion' => esc_html__( 'Exclusion', 'houzez-theme-functionality' ),
                    'hue' => esc_html__( 'Hue', 'houzez-theme-functionality' ),
                    'luminosity' => esc_html__( 'Luminosity', 'houzez-theme-functionality' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title' => 'mix-blend-mode: {{VALUE}}',
                    '{{WRAPPER}} .page-title h1' => 'mix-blend-mode: {{VALUE}}',
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
     * @since 2.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();

        if( $settings['show_breadcrumb'] == 'yes') {
            get_template_part('template-parts/page/breadcrumb'); 
        }?> 
        <div class="d-flex align-items-center">
            <?php get_template_part('template-parts/page/page-title'); ?> 
        </div><!-- d-flex -->  
        <?php   
    }
}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Page_Title );