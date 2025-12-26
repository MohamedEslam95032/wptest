<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agency_Contact extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agency-contact';
	}

	public function get_title() {
		return __( 'Agency Contact', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-single-agency eicon-featured-image';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agency')  {
            return ['houzez-single-agency-builder']; 
        }

        return [ 'houzez-single-agency' ];
	}

	public function get_keywords() {
		return ['agency', 'contact', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__( 'Show Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_map',
            [
                'label' => esc_html__( 'Map', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_address',
            [
                'label' => esc_html__( 'Address', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_phone',
            [
                'label' => esc_html__( 'Phone', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_mobile',
            [
                'label' => esc_html__( 'Mobile', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_fax',
            [
                'label' => esc_html__( 'Fax', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_email',
            [
                'label' => esc_html__( 'Email', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_website',
            [
                'label' => esc_html__( 'Website', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_social',
            [
                'label' => esc_html__( 'Social Links', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_styling',
            [
                'label' => __( 'Box', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'agent_contact_bg',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .agent-contacts-wrap' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'agent_contact_padding',
            [
                'label'      => esc_html__( 'Padding', 'houzez-theme-functionality' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .agent-contacts-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'agent_contact_border',
                'selector' => '{{WRAPPER}} .agent-contacts-wrap',
            ]
        );

        $this->add_responsive_control(
            'agent_contact_radius',
            [
                'label'      => esc_html__( 'Radius', 'houzez-theme-functionality' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .agent-contacts-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => 'true',

            ]
        );

        $this->add_responsive_control(
            'agent_contact_margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-contacts-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'agent_contact_margin_bottom',
            [
                'label' => esc_html__( 'Margin Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-contacts-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_styling',
            [
                'label' => __( 'Section Title', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'contact_title_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .widget-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'contact_title_typo',
                'selector' => '{{WRAPPER}} .widget-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_address_styling',
            [
                'label' => __( 'Address', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_address' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'address_sty_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} address' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'address_sty_typo',
                'selector' => '{{WRAPPER}} address',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_list_styling',
            [
                'label' => __( 'Contact Info', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'agent_contact_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-contacts-wrap .list-unstyled, .agent-contacts-wrap .list-unstyled li a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label'     => esc_html__( 'Title Typography', 'houzez-theme-functionality' ),
                'name' => 'agent_contact_label_typo',
                'selector' => '{{WRAPPER}} .agent-contacts-wrap .list-unstyled li strong',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label'     => esc_html__( 'Values Typography', 'houzez-theme-functionality' ),
                'name' => 'agent_contact_val_typo',
                'selector' => '{{WRAPPER}} .agent-contacts-wrap .list-unstyled li a',
            ]
        );

        $this->add_control(
            'icon_indent',
            [
                'label' => esc_html__( 'Line Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-contacts-wrap li' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'line_height_border',
                'selector' => '{{WRAPPER}} .agent-contacts-wrap li',
                'separator' => 'before',
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_social_styling',
            [
                'label' => __( 'Social', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_social' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'social_text_color',
            [
                'label'     => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent-contacts-wrap p' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'social_text_typo',
                'selector' => '{{WRAPPER}} .agent-contacts-wrap p',
            ]
        );

        $this->end_controls_section();

	}

	protected function render() {
		
		global $settings, $post, $houzez_local;

        $houzez_local = houzez_get_localization();

		$settings = $this->get_settings_for_display();

        $this->single_agency_preview_query(); // Only for preview
        ?>

        <div class="agent-contacts-wrap">

            <?php if( $settings['show_title'] ) { ?>
            <h3 class="widget-title mb-4"><?php echo houzez_option( 'agency_lb_contact', esc_html__('Contact', 'houzez') ); ?></h3>
            <?php } ?>

            <?php if( $settings['show_map'] || $settings['show_address'] ) { ?>
            <div class="agent-map">
                <?php 
                if( $settings['show_map'] ) {
                    if ( Plugin::$instance->editor->is_edit_mode() ) {?>
                        <style>
                            #houzez-agent-sidebar-map {
                                border: 1px solid #ccc;
                                display: flex;
                                justify-content: center; /* Horizontal centering */
                                align-items: center; /* Vertical centering */
                                height: 300px; /* Set a height for the container */
                                text-align: center; /* Center the text inside the div */
                            }
                        </style>
                        <div id="houzez-agent-sidebar-map"><?php esc_html_e( 'Map will show here on frontend', 'houzez-theme-functionality' );?></div>
                    <?php
                    } else {
                        get_template_part('template-parts/realtors/agency/map');  
                    }
                }
                if( $settings['show_address'] ) {
                    get_template_part('template-parts/realtors/agency/address'); 
                }?>
            </div>
            <?php } ?>

            <ul class="list-unstyled d-flex flex-column">

                <?php 
                if( $settings['show_phone'] ) {
                    get_template_part('template-parts/realtors/agency/office-phone', null, array('version' => 'v2'));
                } 

                if( $settings['show_mobile'] ) {
                    get_template_part('template-parts/realtors/agency/mobile', null, array('version' => 'v2')); 
                }

                if( $settings['show_fax'] ) {
                    get_template_part('template-parts/realtors/agency/fax', null, array('version' => 'v2'));
                } 

                if( $settings['show_email'] ) {
                    get_template_part('template-parts/realtors/agency/email', null, array('version' => 'v2')); 
                }
                if( $settings['show_website'] ) {
                    get_template_part('template-parts/realtors/agency/website'); 
                }?>
            </ul>

            <?php 
            if( $settings['show_social'] ) { 
                get_template_part('template-parts/realtors/agency/social', 'v2'); 
            } ?>

        </div><!-- agent-bio-wrap -->

        <?php
        $this->reset_preview_query(); // Only for preview

	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agency_Contact );