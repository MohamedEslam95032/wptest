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
class Houzez_Taxonomies_List extends Widget_Base {
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
        return 'Houzez_Taxonomies_List';
    }

    /**
     * Get widget title.
     * @since v3.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Taxonomies List', 'houzez-theme-functionality' );
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
        return 'houzez-element-icon eicon-bullet-list';
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

        $this->add_control(
            'list_title',
            [
                'label'     => esc_html__( 'List Title', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::TEXT,
                'description' => '',
                'default' => 'Taxonomy Title',
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

        $this->add_control(
            'count_position',
            [
                'label'     => esc_html__( 'Count Position', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'inline'  => esc_html__( 'Inline', 'houzez-theme-functionality'),
                    'separated'    => esc_html__( 'Separated', 'houzez-theme-functionality')
                ],
                'description' => '',
                'default' => 'inline',
                'condition' => [
                    'houzez_hide_count!' => '1',
                ],
            ]
        );

        
        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Styling
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'style_secingion',
            [
                'label'     => esc_html__('Title', 'houzez-theme-functionality'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'list_title!' => ''
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'main_title_type',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .taxonomy-item-list h3',
            ]
        );

        $this->add_control(
            'main_title_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_icon_list',
            [
                'label' => esc_html__( 'List', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'space_between',
            [
                'label' => esc_html__( 'Space Between', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} .taxonomy-item-list ul li:not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2)',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_align',
            [
                'label' => esc_html__( 'Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'end' => [
                        'title' => esc_html__( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'divider',
            [
                'label' => esc_html__( 'Divider', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'Off', 'houzez-theme-functionality' ),
                'label_on' => esc_html__( 'On', 'houzez-theme-functionality' ),
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:not(:last-child):after' => 'content: ""',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'divider_style',
            [
                'label' => esc_html__( 'Style', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'solid' => esc_html__( 'Solid', 'houzez-theme-functionality' ),
                    'double' => esc_html__( 'Double', 'houzez-theme-functionality' ),
                    'dotted' => esc_html__( 'Dotted', 'houzez-theme-functionality' ),
                    'dashed' => esc_html__( 'Dashed', 'houzez-theme-functionality' ),
                ],
                'default' => 'solid',
                'condition' => [
                    'divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:not(:last-child):after' => 'border-top-style: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'divider_weight',
            [
                'label' => esc_html__( 'Weight', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'condition' => [
                    'divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'divider_width',
            [
                'label' => esc_html__( 'Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'default' => [
                    'unit' => '%',
                ],
                'condition' => [
                    'divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:not(:last-child):after' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'divider_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ddd',
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
                'condition' => [
                    'divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:not(:last-child):after' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'list_icon',
            [
                'label' => esc_html__( 'Icon', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'skin' => 'inline',
                'label_block' => false,
                'skin_settings' => [
                    'inline' => [
                        'none' => [
                            'label' => esc_html__( 'Default', 'houzez-theme-functionality' ),
                            'icon' => 'eicon-ban',
                        ],
                        'icon' => [
                            'icon' => 'eicon-chevron-right',
                        ],
                    ],
                ],
                'recommended' => [
                    'fa-solid' => [
                        'arrow-right',
                        'long-arrow-alt-right',
                        'arrow-alt-circle-right',
                        'check',
                        'chevron-right',
                        'caret-right',
                    ],
                    'fa-regular' => [
                        'arrow-righ',
                    ],
                ],
            ]
        );

        $this->start_controls_tabs( 'icon_colors' );

        $this->start_controls_tab(
            'icon_colors_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .hz-list-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .hz-list-icon svg' => 'fill: {{VALUE}};',
                ],
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_colors_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'icon_color_hover',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:hover .hz-list-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .taxonomy-item-list ul li:hover .hz-list-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color_hover_transition',
            [
                'label' => __( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                    'size' => 0.3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .hz-list-icon i' => 'transition: color {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .hz-list-icon svg' => 'transition: fill {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__( 'Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'default' => [
                    'size' => 12,
                ],
                'range' => [
                    'px' => [
                        'min' => 6,
                    ],
                    '%' => [
                        'min' => 6,
                    ],
                    'vw' => [
                        'min' => 6,
                    ],
                ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}}' => '--hz-icon-list-icon-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .hz-list-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .hz-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'text_indent',
            [
                'label' => esc_html__( 'Gap', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .hz-list-icon' => is_rtl() ? 'padding-left: {{SIZE}}{{UNIT}};' : 'padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $e_icon_list_icon_css_var = 'var(--hz-icon-list-icon-size, 1em)';
        $e_icon_list_icon_align_left = sprintf( '0 calc(%s * 0.25) 0 0', $e_icon_list_icon_css_var );
        $e_icon_list_icon_align_center = sprintf( '0 calc(%s * 0.125)', $e_icon_list_icon_css_var );
        $e_icon_list_icon_align_right = sprintf( '0 0 0 calc(%s * 0.25)', $e_icon_list_icon_css_var );

        $this->add_responsive_control(
            'icon_self_align',
            [
                'label' => esc_html__( 'Horizontal Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => '',
                'selectors_dictionary' => [
                    'left' => sprintf( '--hz-icon-list-icon-align: left; --hz-icon-list-icon-margin: %s;', $e_icon_list_icon_align_left ),
                    'center' => sprintf( '--hz-icon-list-icon-align: center; --hz-icon-list-icon-margin: %s;', $e_icon_list_icon_align_center ),
                    'right' => sprintf( '--hz-icon-list-icon-align: right; --hz-icon-list-icon-margin: %s;', $e_icon_list_icon_align_right ),
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '{{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_self_vertical_align',
            [
                'label' => esc_html__( 'Vertical Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__( 'Start', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => esc_html__( 'End', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--icon-vertical-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_vertical_offset',
            [
                'label' => esc_html__( 'Adjust Vertical Position', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -15,
                        'max' => 15,
                    ],
                    'em' => [
                        'min' => -1,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--icon-vertical-offset: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_text_style',
            [
                'label' => esc_html__( 'Text', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'icon_typography',
                'selector' => '{{WRAPPER}} .taxonomy-item-list ul li a',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .taxonomy-item-list ul li a',
            ]
        );

        $this->start_controls_tabs( 'text_colors' );

        $this->start_controls_tab(
            'text_colors_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li a' => 'color: {{VALUE}};',
                ],
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'text_colors_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'text_color_hover',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:hover a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color_hover_transition',
            [
                'label' => __( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                    'size' => 0.3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li a' => 'transition: color {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'section_count_style',
            [
                'label' => esc_html__( 'Count', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'houzez_hide_count!' => '1'
                ]
            ]
        );

        /*$this->add_control(
            'count_position',
            [
                'label'     => esc_html__( 'Position', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'none'  => esc_html__( 'Aside', 'houzez-theme-functionality'),
                    'right'  => esc_html__( 'Right', 'houzez-theme-functionality'),
                    'left'  => esc_html__( 'Left', 'houzez-theme-functionality')
                ],
                'default' => 'aside',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li span.count' => 'float: {{VALUE}};',
                ],
            ]
        );*/

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'selector' => '{{WRAPPER}} .taxonomy-item-list ul li span.count',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'count_shadow',
                'label' => esc_html__('Shadow', 'houzez-theme-functionality'),
                'selector' => '{{WRAPPER}} .taxonomy-item-list ul li span.count',
            ]
        );

        $this->start_controls_tabs( 'count_colors' );

        $this->start_controls_tab(
            'count_colors_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li span.count' => 'color: {{VALUE}};',
                ],
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'count_colors_hover',
            [
                'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'count_color_hover',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li:hover span.count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'count_color_hover_transition',
            [
                'label' => __( 'Transition Duration', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's', 'ms', 'custom' ],
                'default' => [
                    'unit' => 's',
                    'size' => 0.3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .taxonomy-item-list ul li span.count' => 'transition: color {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

    }

    private function fetch_configured_icon($key, $default) {
        $configurations = $this->get_settings_for_display();
        $configuredIcon = $configurations[$key];
        $iconRepresentation = $default;

        if (!empty($configuredIcon['value'])) {
            ob_start();
            \Elementor\Icons_Manager::render_icon($configuredIcon, ['aria-hidden' => 'true']);
            $iconRepresentation = ob_get_clean();
        } else {
            $iconRepresentation = sprintf('<i class="%s"></i>', esc_attr($default));
        }

        return $iconRepresentation;
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

        $args['list_title'] =  $settings['list_title'];
        $args['houzez_cards_from'] =  $settings['houzez_cards_from'];
        $args['houzez_show_child'] =  $settings['houzez_show_child'];
        $args['houzez_hide_count'] =  $settings['houzez_hide_count'];
        $args['orderby'] =  $settings['orderby'];
        $args['order'] =  $settings['order'];
        $args['houzez_hide_empty'] =  $settings['houzez_hide_empty'];
        $args['no_of_terms'] =  $settings['no_of_terms'];
        $args['count_position'] =  $settings['count_position'];
       
        $args['property_type']   =  $property_type;
        $args['property_status']   =  $property_status;
        $args['property_label']   =  $property_label;
        $args['property_country']   =  $property_country;
        $args['property_state']   =  $property_state;
        $args['property_city']   =  $property_city;
        $args['property_area']   =  $property_area;

        if ( ! empty( $settings['list_icon']['value'] ) ) {
            $args['icon'] = $this->fetch_configured_icon( 'list_icon', 'fas fa-chevron-right' );   
        }
       
        if( function_exists( 'houzez_taxonomies_list' ) ) {
            echo houzez_taxonomies_list( $args );
        }

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Taxonomies_List );