<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Agents Widget.
 * @since 4.0.0
 */
class Houzez_Elementor_Agent_Card extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 4.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_agent_card';
    }

    /**
     * Get widget title.
     * @since 4.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Agent Card', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 4.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-user-circle-o';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 4.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {

        if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-elements', 'houzez-single-property' ];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 4.0.0
     * @access protected
     */
    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'agent_card_layout',
            [
                'label' => esc_html__( 'Layout', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'v1',
                'options' => [
                    'v1' => esc_html__( 'Variation 1', 'houzez-theme-functionality' ),
                    'v2' => esc_html__( 'Variation 2', 'houzez-theme-functionality' ),
                    'v3' => esc_html__( 'Variation 3', 'houzez-theme-functionality' ),
                ],
            ]
        );

        $this->add_control(
            'agent_source',
            [
                'label' => esc_html__( 'Agent Source', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => esc_html__( 'Auto', 'houzez-theme-functionality' ),
                    'specific' => esc_html__( 'Specific Agent', 'houzez-theme-functionality' ),
                ],
                'description' => esc_html__( 'Select "Auto" to display the agent associated with the current property (for property detail pages). Select "Specific Agent" to choose an agent.', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'selected_agent_id',
            [
                'label'    => esc_html__('Select Agent', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => false,
                'label_block' => true,
                'options'  => array_slice( houzez_get_agents_array(), 1, null, true ),
                'condition' => [
                    'agent_source' => 'specific',
                ],
            ]
        );
        
        $this->add_control(
            'view_listings_text',
            [
                'label' => esc_html__('View Listings Text', 'houzez-theme-functionality'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('View my listings', 'houzez-theme-functionality'),
                'placeholder' => esc_html__('View my listings', 'houzez-theme-functionality'),
                'description' => esc_html__('Customize the "View my listings" link text', 'houzez-theme-functionality'),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_verification_badge',
            [
                'label' => esc_html__('Show Verification Badge', 'houzez-theme-functionality'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__('Yes', 'houzez-theme-functionality'),
                'label_off' => esc_html__('No', 'houzez-theme-functionality'),
                'description' => esc_html__('Show verification badge for verified agents', 'houzez-theme-functionality'),
            ]
        );

        $this->add_control(
            'hide_verified_text',
            [
                'label' => esc_html__( 'Hide "Verified" Text', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => '',
                'description' => esc_html__( 'Show only the verification icon without text', 'houzez-theme-functionality' ),
                'condition' => [
                    'show_verification_badge' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_phone',
            [
                'label' => esc_html__('Show Phone Number', 'houzez-theme-functionality'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__('Yes', 'houzez-theme-functionality'),
                'label_off' => esc_html__('No', 'houzez-theme-functionality'),
            ]
        );

        $this->add_control(
            'show_mobile',
            [
                'label' => esc_html__('Show Mobile Number', 'houzez-theme-functionality'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__('Yes', 'houzez-theme-functionality'),
                'label_off' => esc_html__('No', 'houzez-theme-functionality'),
            ]
        );

        $this->add_control(
            'click_to_reveal',
            [
                'label' => esc_html__('Click to Reveal', 'houzez-theme-functionality'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => esc_html__('Yes', 'houzez-theme-functionality'),
                'label_off' => esc_html__('No', 'houzez-theme-functionality'),
                'description' => esc_html__('Enable to hide phone numbers until clicked', 'houzez-theme-functionality'),
            ]
        );
        
        $this->add_control(
            'buttons_header',
            [
                'label' => esc_html__('Action Buttons', 'houzez-theme-functionality'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_call_button',
            [
                'label' => esc_html__('Show Call Button', 'houzez-theme-functionality'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__('Yes', 'houzez-theme-functionality'),
                'label_off' => esc_html__('No', 'houzez-theme-functionality'),
            ]
        );
        $this->add_control(
            'show_whatsapp_button',
            [
                'label' => esc_html__('Show WhatsApp Button', 'houzez-theme-functionality'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__('Yes', 'houzez-theme-functionality'),
                'label_off' => esc_html__('No', 'houzez-theme-functionality'),
            ]
        );

        $this->add_control(
            'show_telegram_button',
            [
                'label' => esc_html__('Show Telegram Button', 'houzez-theme-functionality'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => esc_html__('Yes', 'houzez-theme-functionality'),
                'label_off' => esc_html__('No', 'houzez-theme-functionality'),
            ]
        );

        $this->add_control(
            'show_line_button',
            [
                'label' => esc_html__('Show Line Button', 'houzez-theme-functionality'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => esc_html__('Yes', 'houzez-theme-functionality'),
                'label_off' => esc_html__('No', 'houzez-theme-functionality'),
            ]
        );
        
        

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Card Styling', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'card_background',
                'label' => esc_html__( 'Background', 'houzez-theme-functionality' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .agent-information-module',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'default' => '#ffffff',
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'rem', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information-module' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '1',
                    'right' => '1',
                    'bottom' => '1',
                    'left' => '1',
                    'unit' => 'rem',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => esc_html__( 'Border', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-information-module',
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information-module' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => esc_html__( 'Box Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-information-module',
            ]
        );

        $this->end_controls_section();

        // Agent Image Style Section
        $this->start_controls_section(
            'section_agent_image_style',
            [
                'label' => esc_html__( 'Agent Image', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'agent_image_width',
            [
                'label' => esc_html__( 'Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-image img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
                    '{{WRAPPER}} .agent-image' => 'width: {{SIZE}}{{UNIT}}; flex-shrink: 0;', // for flexbox
                ],
            ]
        );
        $this->add_responsive_control(
            'agent_image_height',
            [
                'label' => esc_html__( 'Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-image img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                ],
                'condition' => [
                     'agent_image_width[size]!' => '' // Only show if width is set
                ]
            ]
        );

        $this->add_control(
            'agent_image_border_radius',
            [
                'label' => esc_html__( 'Image Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
                'description' => esc_html__('Override card border radius for the image. For Variation 2, use 50% for a circle.', 'houzez-theme-functionality'),
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'agent_image_border',
                'label' => esc_html__( 'Image Border', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-image img',
            ]
        );

        $this->add_responsive_control(
            'agent_image_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Agent Name Style Section
        $this->start_controls_section(
            'section_agent_name_style',
            [
                'label' => esc_html__( 'Agent Name', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'agent_name_typography',
                'selector' => '{{WRAPPER}} .agent-information .agent-name .agent-name a',
            ]
        );

        $this->add_control(
            'agent_name_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-name .agent-name a' => 'color: {{VALUE}}',
                ],
                'default' => '#333333',
            ]
        );

        $this->add_control(
            'agent_name_hover_color',
            [
                'label' => esc_html__( 'Color Hover', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-name a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'agent_name_spacing',
            [
                'label' => esc_html__( 'Bottom Spacing', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-name .agent-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Verification Badge Style Section
        $this->start_controls_section(
            'section_verification_badge_style',
            [
                'label' => esc_html__( 'Verification Badge', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_verification_badge' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'verification_badge_typography',
                'label' => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-verified-badge, {{WRAPPER}} .agent-verified-icon',
            ]
        );

        $this->add_control(
            'verification_badge_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-verified-badge' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .agent-verified-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'verification_badge_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-verified-badge' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .agent-verified-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'verification_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-verified-badge i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .agent-verified-icon i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'verification_badge_padding',
            [
                'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
					'top' => 3,
					'right' => 4,
					'bottom' => 3,
					'left' => 4,
					'unit' => 'px',
				],
                'selectors' => [
                    '{{WRAPPER}} .agent-verified-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .agent-verified-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'verification_badge_border',
                'label' => esc_html__( 'Border', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-verified-badge, {{WRAPPER}} .agent-verified-icon',
            ]
        );

        $this->add_responsive_control(
            'verification_badge_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-verified-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .agent-verified-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'verification_badge_shadow',
                'label' => esc_html__( 'Box Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-verified-badge, {{WRAPPER}} .agent-verified-icon',
            ]
        );

        $this->end_controls_section();

        // Agent Designation Style Section
        $this->start_controls_section(
            'section_view_listing_text_style',
            [
                'label' => esc_html__( 'View Listing Text', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'view_listings_text!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'view_listing_text_typography',
                'selector' => '{{WRAPPER}} .agent-information .agent-link a',
            ]
        );

        $this->add_control(
            'view_listing_text_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-link a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Contact Info Style Section
        $this->start_controls_section(
            'section_contact_info_style',
            [
                'label' => esc_html__( 'Contact Info Text', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'contact_info_typography',
                'selector' => '{{WRAPPER}} .agent-information .agent-phone-wrap span.agent-phone, {{WRAPPER}} .agent-information .agent-phone-wrap a',
            ]
        );

        $this->add_control(
            'contact_info_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-phone-wrap span.agent-phone a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'contact_info_color_hover',
            [
                'label' => esc_html__( 'Text Color Hover', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-phone-wrap span.agent-phone a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'contact_item_spacing',
            [
                'label' => esc_html__( 'Item Bottom Spacing', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-phone-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .agent-information .agent-phone-wrap:last-of-type' => 'margin-bottom: 0;', // for v1, v2 where items are stacked
                    '{{WRAPPER}} .agent-information-module.layout-v1 .agent-information .agent-phone-wrap' => 'gap: {{SIZE}}{{UNIT}};', // for v1 where items are flex-column gap
                    '{{WRAPPER}} .agent-information-module.layout-v2 .agent-information .agent-phone-wrap' => 'gap: {{SIZE}}{{UNIT}};', // for v2 where items are flex-column gap

                ],
            ]
        );

        $this->end_controls_section();

        // Icons Style Section
        $this->start_controls_section(
            'section_icons_style',
            [
                'label' => esc_html__( 'Contact Icons', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'contact_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-phone-wrap i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'contact_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-phone-wrap i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'contact_icon_spacing',
            [
                'label' => esc_html__( 'Icon Right Spacing', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'rem', 'em', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                    ],
                ],
                'default' => [
                    'size' => 4,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information .agent-phone-wrap i' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Action Buttons Style Section
        $this->start_controls_section(
            'section_action_buttons_style',
            [
                'label' => esc_html__( 'Action Buttons', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'buttons_margin_top',
            [
                'label' => esc_html__( 'Buttons Top Spacing', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-buttons-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_icon_size',
            [
                'label' => esc_html__( 'Button Icon Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__( 'Button Padding', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'button_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-information-btn i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'label' => esc_html__( 'Background', 'houzez-theme-functionality' ),
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .agent-information-btn',
                'fields_options' => [
                    'background' => ['default' => 'classic'],
                    'color' => ['default' => ''], // Example primary color
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => esc_html__( 'Border', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-information-btn',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            'button_icon_color_hover',
            [
                'label' => esc_html__( 'Icon Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent-information-btn:hover i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background_hover',
                'label' => esc_html__( 'Background', 'houzez-theme-functionality' ),
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .agent-information-btn:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border_hover',
                'label' => esc_html__( 'Border', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .agent-information-btn:hover',
            ]
        );

        $this->add_control(
            'button_border_radius_hover',
            [
                'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .agent-information-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
     * @since 4.0.0
     * @access protected
     */
    protected function _render_agent_image_display( $agent_data, $width = '100', $height = '100', $img_class = 'rounded', $img_styles = '' ) {
        if ( empty( $agent_data ) || empty($agent_data['profile_photo']) ) return '';
        
        $alt_text = sprintf(esc_html__('Profile photo of agent %s', 'houzez-theme-functionality'), esc_attr($agent_data['name']));
        $style_attr = !empty($img_styles) ? ' style="'.esc_attr($img_styles).'"' : '';
        ob_start();
        ?>
        <div class="agent-image">
            <a href="<?php echo esc_url($agent_data['permalink']); ?>">
                <img class="<?php echo esc_attr( $img_class ); ?>" src="<?php echo esc_url( $agent_data['profile_photo'] ); ?>" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" alt="<?php echo $alt_text; ?>"<?php echo $style_attr; // WPCS: XSS ok. ?>>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
    
    protected function _render_agent_name_designation_html( $agent_data ) {
        if ( empty( $agent_data ) ) return '';

        // Get settings
        $settings = $this->get_settings_for_display();
        $view_listings_text = !empty($settings['view_listings_text']) ? $settings['view_listings_text'] : '';
        $show_verification_badge = $settings['show_verification_badge'] ?? 'no';
        
        ob_start();
        ?>
        <ul class="agent-information list-unstyled mb-0" role="list">
            <li class="agent-name" role="listitem">
                <a href="<?php echo esc_url($agent_data['permalink']); ?>"><?php echo esc_html( $agent_data['name'] ); ?></a>
                <?php if ( $show_verification_badge === 'yes' && isset($agent_data['verified']) && $agent_data['verified'] == 1 ) : ?>
                    <?php if( $settings['hide_verified_text'] === 'yes' ) : ?>
                        <span class="ms-1 d-inline badge btn-secondary agent-verified-badge"><i class="houzez-icon icon-check-circle-1"></i></span>
                    <?php else : ?>
                        <span class="ms-1 d-inline badge btn-secondary agent-verified-badge"><i class="houzez-icon icon-check-circle-1 me-1"></i> <?php echo esc_html__( 'Verified', 'houzez' ); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </li>
            <?php if ( !empty($view_listings_text) ) : ?>
                <li class="agent-link" role="listitem">
                    <a href="<?php echo esc_url($agent_data['permalink']); ?>"><?php echo esc_html( $view_listings_text ); ?></a>
                </li>
            <?php endif; ?>
        </ul>
        <?php
        return ob_get_clean();
    }
    
    protected function _render_office_phone_content_html( $agent_data, $settings ) {
        if ( empty( $agent_data['office_phone'] ) ) return '';
        
        // Check if office phone should be shown
        if ( isset($settings['show_phone']) && $settings['show_phone'] !== 'yes' ) return '';

        $click_to_reveal = isset($settings['click_to_reveal']) && $settings['click_to_reveal'] === 'yes';
        
        ob_start();
        ?>
        <i class="houzez-icon icon-phone" aria-hidden="true"></i>
        <span class="agent-phone <?php echo $click_to_reveal ? 'agent-show-onClick agent-phone-hidden' : ''; ?> me-1">
            <a href="tel:<?php echo esc_html( $agent_data['office_phone_call'] ); ?>"><?php echo esc_html( $agent_data['office_phone'] ); ?></a>
        </span>
        <?php
        return ob_get_clean();
    }
    
    protected function _render_mobile_phone_content_html( $agent_data, $settings ) {
        if ( empty( $agent_data['mobile_phone'] ) ) return '';
        
        // Check if mobile phone should be shown
        if ( isset($settings['show_mobile']) && $settings['show_mobile'] !== 'yes' ) return '';

        $click_to_reveal = isset($settings['click_to_reveal']) && $settings['click_to_reveal'] === 'yes';
        
        ob_start();
        ?>
        <i class="houzez-icon icon-mobile-phone" aria-hidden="true"></i>
        <span class="agent-phone <?php echo $click_to_reveal ? 'agent-show-onClick agent-phone-hidden' : ''; ?> me-1">
            <a href="tel:<?php echo esc_html( $agent_data['mobile_phone_call'] ); ?>"><?php echo esc_html( $agent_data['mobile_phone'] ); ?></a>
        </span>
        <?php
        return ob_get_clean();
    }
    
    protected function _render_action_buttons_html( $settings, $agent_data ) {
        ob_start();
        $random_token = wp_generate_password(5, false, false);
        // Call button
        if ( isset($settings['show_call_button']) && $settings['show_call_button'] === 'yes' ) {
            ?>
            <button type="button" class="agent-information-btn btn btn-primary-outlined p-0 d-flex align-items-center justify-content-center flex-fill gap-1" data-bs-toggle="modal" data-bs-target="#modal-phone-<?php echo esc_attr($random_token); ?>">
                <span class="d-block" data-bs-toggle="tooltip" title="<?php echo esc_html__('Call', 'houzez-theme-functionality'); ?>">
                    <i class="houzez-icon icon-phone-actions-ring"></i>
                </span>
            </button>
            <?php
        }

        // WhatsApp button
        if ( isset($settings['show_whatsapp_button']) && $settings['show_whatsapp_button'] === 'yes' && !empty($agent_data['whatsapp']) ) {
            ?>
            <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $agent_data['whatsapp'])); ?>" target="_blank" class="agent-information-btn btn btn-primary-outlined p-0 d-flex align-items-center justify-content-center flex-fill gap-1">
                <span class="d-block" data-bs-toggle="tooltip" title="<?php echo esc_html__('WhatsApp', 'houzez-theme-functionality'); ?>">
                    <i class="houzez-icon icon-messaging-whatsapp"></i>
                </span>
            </a>
            <?php
        }

        // Telegram button
        if ( isset($settings['show_telegram_button']) && $settings['show_telegram_button'] === 'yes' && !empty($agent_data['telegram']) ) {
            ?>
            <a href="<?php echo houzezStandardizeTelegramURL($agent_data['telegram']);?>" target="_blank" class="agent-information-btn btn btn-primary-outlined p-0 d-flex align-items-center justify-content-center flex-fill gap-1">
                <span class="d-block" data-bs-toggle="tooltip" title="<?php echo esc_html__('Telegram', 'houzez-theme-functionality'); ?>">
                    <i class="houzez-icon icon-telegram-logos-24"></i>
                </span>
            </a>
            <?php
        }

        // Line button
        if ( isset($settings['show_line_button']) && $settings['show_line_button'] === 'yes' && !empty($agent_data['lineapp']) ) {
            ?>
            <a href="https://line.me/ti/p/~<?php echo esc_attr($agent_data['lineapp']); ?>" target="_blank" class="agent-information-btn btn btn-primary-outlined p-0 d-flex align-items-center justify-content-center flex-fill gap-1">
                <span class="d-block" data-bs-toggle="tooltip" title="<?php echo esc_html__('Line', 'houzez-theme-functionality'); ?>">
                    <i class="houzez-icon icon-lineapp-5"></i>
                </span>
            </a>
            <?php
        }

        self::generate_phone_modal_html( $agent_data, $random_token );

        return ob_get_clean();
    }

    public function generate_phone_modal_html( $agent_data, $random_token ) {
        
        if( is_singular('property') ) {
            $post_id = get_the_ID();
            $prop_id = houzez_get_listing_data('property_id');
        } else {
            $post_id = null;
        }
        ?>
        <div class="modal fade modal-phone-number" id="modal-phone-<?php echo esc_attr($random_token); ?>" tabindex="-1" aria-labelledby="phoneNumberModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="phoneNumberModalLabel"><?php esc_html_e('Contact us', 'houzez-theme-functionality'); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_html_e('Close', 'houzez-theme-functionality'); ?>"></button>
                    </div><!-- modal-header -->
                    <div class="modal-body">

                        <?php if( !empty($post_id) ) : ?>
                        <p class="modal-body-phone-number-text">
                            <?php esc_html_e('Please quote property reference', 'houzez-theme-functionality'); ?><br>

                            <strong><?php echo houzez_propperty_id_prefix($prop_id); ?></strong>
                        </p>
                        <?php endif; ?>

                        <p class="modal-body-phone-number-number">
                            <a class="btn btn-primary-outlined" href="tel:<?php echo esc_attr($agent_data['mobile_phone_call']); ?>"><i class="houzez-icon icon-phone-actions-ring"></i> <?php echo esc_attr($agent_data['mobile_phone']); ?></a>
                        </p>
                    </div><!-- modal-body -->
                </div><!-- modal-content -->
            </div><!-- modal-dialog -->
        </div><!-- login-register-form -->
        <?php
    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $show_verification_badge = $settings['show_verification_badge'] ?? 'no';
        $agent_card_layout = $settings['agent_card_layout'];
        $agent_source = $settings['agent_source'];
        $selected_agent_id = $settings['selected_agent_id'];

        $agent_id = null;
        $agent_data = null;
        $photo_url = null;

        if ( 'auto' === $agent_source ) {
            $this->single_property_preview_query(); // Only for preview

            global $post;
            $agent_display_option = get_post_meta( $post->ID, 'fave_agent_display_option', true );

            if ( $agent_display_option == 'agency_info' ) {
                $agent_id = get_post_meta( $post->ID, 'fave_property_agency', true );
            } else if ( $agent_display_option == 'agent_info' ) {
                $agent_id = get_post_meta( $post->ID, 'fave_agents', true );
            } else {
                $agent_id = get_post_field( 'post_author', $post->ID );
            }
            $this->reset_preview_query(); // Only for preview
        } elseif ( 'specific' === $agent_source && !empty( $selected_agent_id ) ) {
            $agent_id = $selected_agent_id;
        }

        if ( !empty( $agent_id ) && $agent_id != '-1' && get_post_status($agent_id) == 'publish' ) {

            $agent_type = get_post_type($agent_id);

            if ( $agent_type == 'houzez_agency' ) {

                $name = get_the_title($agent_id);
                $office_phone = get_post_meta( $agent_id, 'fave_agency_phone', true );
                $mobile_phone = get_post_meta( $agent_id, 'fave_agency_mobile', true );
                $whatsapp = get_post_meta( $agent_id, 'fave_agency_whatsapp', true );
                $telegram = get_post_meta( $agent_id, 'fave_agency_telegram', true );
                $lineapp = get_post_meta( $agent_id, 'fave_agency_line_id', true );
                $email = get_post_meta( $agent_id, 'fave_agency_email', true );
                $verified = get_post_meta( $agent_id, 'fave_agency_verified', true );
                $office_phone_call = str_replace(array('(',')',' ','-'),'', $office_phone);
                $mobile_phone_call = str_replace(array('(',')',' ','-'),'', $mobile_phone);
                $permalink = get_post_permalink( $agent_id );

                $thumb_id = get_post_thumbnail_id( $agent_id );
                if($thumb_id) {
                    $thumb_url_array = wp_get_attachment_image_src( $thumb_id, 'full', true );
                    $photo_url = $thumb_url_array[0];
                }

                if( empty( $photo_url )) {

                    $placeholder_url = houzez_option( 'houzez_agency_placeholder', false, 'url' );
                    if( ! empty($placeholder_url) ) {
                        $photo_url = $placeholder_url;
                    } else {
                        $photo_url = HOUZEZ_IMAGE. 'profile-avatar.png';
                    }
                }
                
            } else if ( $agent_type == 'houzez_agent' ) {
                
                $name = get_the_title($agent_id);
                $office_phone = get_post_meta( $agent_id, 'fave_agent_office_num', true );
                $mobile_phone = get_post_meta( $agent_id, 'fave_agent_mobile', true );
                $whatsapp = get_post_meta( $agent_id, 'fave_agent_whatsapp', true );
                $telegram = get_post_meta( $agent_id, 'fave_agent_telegram', true );
                $lineapp = get_post_meta( $agent_id, 'fave_agent_line_id', true );
                $verified = get_post_meta( $agent_id, 'fave_agent_verified', true );
                $email = get_post_meta( $agent_id, 'fave_agent_email', true );
                $office_phone_call = str_replace(array('(',')',' ','-'),'', $office_phone);
                $mobile_phone_call = str_replace(array('(',')',' ','-'),'', $mobile_phone);
                $permalink = get_post_permalink( $agent_id );

                $thumb_id = get_post_thumbnail_id( $agent_id );
                if($thumb_id) {
                    $thumb_url_array = wp_get_attachment_image_src( $thumb_id, 'full', true );
                    $photo_url = $thumb_url_array[0];
                }

                if( empty( $photo_url )) {

                    $placeholder_url = houzez_option( 'houzez_agent_placeholder', false, 'url' );
                    if( ! empty($placeholder_url) ) {
                        $photo_url = $placeholder_url;
                    } else {
                        $photo_url = HOUZEZ_IMAGE. 'profile-avatar.png';
                    }
                } 
                
            } else {

                $verified = 0;
                $name = get_the_author_meta( 'display_name', $agent_id );
                $office_phone = get_the_author_meta( 'fave_author_phone', $agent_id );
                $office_phone_call = str_replace(array('(',')',' ','-'),'', $office_phone);
                $mobile_phone = get_the_author_meta( 'fave_author_mobile', $agent_id );
                $mobile_phone_call = str_replace(array('(',')',' ','-'),'', $mobile_phone);
                $whatsapp = get_the_author_meta( 'fave_author_whatsapp', $agent_id );
                $telegram = get_the_author_meta( 'fave_author_telegram', $agent_id );
                $lineapp = get_the_author_meta( 'fave_author_line_id', $agent_id );
                $photo_url = get_the_author_meta( 'fave_author_custom_picture', $agent_id );
                $email = get_the_author_meta( 'email', $agent_id );
                $verified = get_the_author_meta( 'houzez_verification_status', $agent_id );
                if($verified == 'approved') {
                    $verified = 1;
                }
                $permalink = get_author_posts_url($agent_id);

                if( empty( $photo_url )) {
                    $photo_url = HOUZEZ_IMAGE. 'profile-avatar.png';
                }
                
                
            }

            $agent_data = [
                'name' => $name,
                'profile_photo' => $photo_url,
                'office_phone' => $office_phone,
                'mobile_phone' => $mobile_phone,
                'office_phone_call' => $office_phone_call,
                'mobile_phone_call' => $mobile_phone_call,
                'whatsapp' => $whatsapp,
                'telegram' => $telegram,
                'lineapp' => $lineapp,
                'show_verification_badge' => $show_verification_badge,
                'email' => $email,
                'permalink' => $permalink,
                'verified' => $verified,
                'profile_photo' => $photo_url,
            ];
            
        } else {

            $placeholder_url = houzez_option( 'houzez_agent_placeholder', false, 'url' );
            if( ! empty($placeholder_url) ) {
                $photo_url = $placeholder_url;
            } else {
                $photo_url = HOUZEZ_IMAGE. 'profile-avatar.png';
            }
             $agent_data = [ 
                'name' => 'Agent Name',
                'profile_photo' => $photo_url,
                'office_phone' => '987 6543 321',
                'mobile_phone' => '987 6543 321',
                'office_phone_call' => '987 6543 321',
                'mobile_phone_call' => '987 6543 321',
                'whatsapp' => '987 6543 321',
                'telegram' => '987 6543 321',
                'lineapp' => '987 6543 321',
                'email' => '987 6543 321',
                'permalink' => '#',
            ];
        }

        if (empty($agent_data)) { // Should not happen with the fallback, but as a safeguard
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p>' . esc_html__( 'Please select an agent or ensure the widget is on a property page with an assigned agent.', 'houzez-theme-functionality' ) . '</p>';
            }
            return;
        }

        // Get HTML parts from helper functions
        $agent_name_designation_html = $this->_render_agent_name_designation_html( $agent_data );
        $office_phone_content_html = $this->_render_office_phone_content_html( $agent_data, $settings );
        $mobile_phone_content_html = $this->_render_mobile_phone_content_html( $agent_data, $settings );
        $action_buttons_html = $this->_render_action_buttons_html( $settings, $agent_data );

        if ( $agent_card_layout === 'v1' ) { 
            ?>
            <div class="agent-information-module">
                <div class="property-form-wrap border-none" role="complementary">
                    <div class="agent-details" role="region">
                        <div class="d-flex align-items-start gap-3">

                            <?php echo $this->_render_agent_image_display( $agent_data, '100', '100', 'rounded' ); ?>

                            <ul class="agent-information list-unstyled d-flex flex-column gap-1 m-0">
                                <li class="agent-name mb-2">
                                    <?php echo $agent_name_designation_html; // WPCS: XSS ok. ?>
                                </li>
                                <?php if ( !empty( $office_phone_content_html ) ) : ?>
                                <li class="agent-phone-wrap d-flex flex-column gap-1">
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php echo $office_phone_content_html; // WPCS: XSS ok. ?>
                                    </div>
                                </li>
                                <?php endif; ?>
                                <?php if ( !empty( $mobile_phone_content_html ) ) : ?>
                                <li class="agent-phone-wrap d-flex flex-column gap-1">
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php echo $mobile_phone_content_html; // WPCS: XSS ok. ?>
                                    </div>
                                </li>
                                <?php endif; ?>
                                <?php if ( !empty( $action_buttons_html ) ) : ?>
                                <li class="item-buttons-wrap d-flex gap-1 mt-3 mb-2">
                                    <?php echo $action_buttons_html; // WPCS: XSS ok. ?>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ( $agent_card_layout === 'v2' ) { 
            ?>
            <div class="agent-information-module">
                <div class="property-form-wrap border-none" role="complementary">
                    <div class="agent-details" role="region">
                        <div class="d-flex flex-column align-items-center gap-3">
                            
                            <?php echo $this->_render_agent_image_display( $agent_data, '120', '120', 'rounded-circle' ); ?>

                            <ul class="agent-information list-unstyled d-flex flex-column align-items-center gap-1 m-0">
                                <li class="agent-name mb-2 text-center">
                                    <?php echo $agent_name_designation_html; // WPCS: XSS ok. ?>
                                </li>
                                <?php if ( !empty( $office_phone_content_html ) || !empty( $mobile_phone_content_html ) ) : ?>
                                <li class="agent-phone-wrap d-flex gap-1">
                                    <?php if ( !empty( $office_phone_content_html ) ) : ?>
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php echo $office_phone_content_html; // WPCS: XSS ok. ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ( !empty( $mobile_phone_content_html ) ) : ?>
                                    <div class="d-flex gap-2 align-items-center">
                                        <?php echo $mobile_phone_content_html; // WPCS: XSS ok. ?>
                                    </div>
                                    <?php endif; ?>
                                </li>
                                <?php endif; ?>
                                <?php if ( !empty( $action_buttons_html ) ) : ?>
                                <li class="item-buttons-wrap d-flex gap-1 mt-3 mb-2">
                                    <?php echo $action_buttons_html; // WPCS: XSS ok. ?>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php } elseif ( $agent_card_layout === 'v3' ) { 
            ?>
            <div class="agent-information-module">
                <div class="agent-details">
                    <div class="d-flex flex-column flex-md-row align-items-sm-start align-items-md-center gap-3">

                        <?php echo $this->_render_agent_image_display( $agent_data, '120', '120', 'rounded' ); ?>

                        <ul class="agent-information list-unstyled d-flex flex-column gap-1 m-0">
                            <li class="agent-name">
                                <?php echo $agent_name_designation_html; // WPCS: XSS ok. ?>
                            </li>
                            <?php if ( !empty( $office_phone_content_html ) || !empty( $mobile_phone_content_html ) ) : ?>
                            <li class="agent-phone-wrap d-flex gap-2 align-items-center">
                                <?php echo $office_phone_content_html; // WPCS: XSS ok. ?>
                                <?php echo $mobile_phone_content_html; // WPCS: XSS ok. ?>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <?php if ( !empty( $action_buttons_html ) ) : ?>
                        <div class="item-buttons-wrap d-flex gap-2 ms-md-auto me-4">
                            <?php echo $action_buttons_html; // WPCS: XSS ok. ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
        }
        
    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Agent_Card );