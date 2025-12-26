<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_Similar extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Filters_Traits;
    use Houzez_Property_Cards_Traits;

	public function get_name() {
		return 'houzez-property-section-similar';
	}

	public function get_title() {
		return __( 'Section Similar Listings', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-featured-image';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['property', 'similar listings', 'houzez' ];
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
            'section_header',
            [
                'label' => esc_html__( 'Section Header', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'section_title',
            [
                'label' => esc_html__( 'Section Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => '',
                'condition' => [
                	'section_header' => 'true'
                ],
            ]
        );

        $this->add_control(
            'listing_from',
            [
                'label' => esc_html__( 'Similar Properties Criteria', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT2,
                'options'  => array(
                    'property_type' => esc_html__('Property Type', 'houzez'),
                    'property_status' => esc_html__('Property Status', 'houzez'),
                    'property_label' => esc_html__('Property Label', 'houzez'),
                    'property_feature' => esc_html__('Property Feature', 'houzez'),
                    'property_country' => esc_html__('Property Country', 'houzez'),
                    'property_state' => esc_html__('Property State', 'houzez'),
                    'property_city' => esc_html__('Property City', 'houzez'),
                    'property_area' => esc_html__('Property Area', 'houzez'),
                ),
                'multiple' => true,
                'label_block' => true,
                'default' => 'property_type'
            ]
        );

        $this->add_control(
            'listings_layout',
            [
                'label' => esc_html__( 'Listings Layout', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options'  => array(
                    'list-view-v1' => 'List View v1',
                    'grid-view-v1' => 'Grid View v1',
                    'list-view-v2' => 'List View v2',
                    'grid-view-v2' => 'Grid View v2',
                    'grid-view-v3' => 'Grid View v3',
                    'list-view-v4' => 'List View v4',
                    'list-view-v5' => 'List View v5',
                    'grid-view-v5' => 'Grid View v5',
                    'grid-view-v6' => 'Grid View v6',
                    'list-view-v7' => 'List View v7',
                    'grid-view-v7' => 'Grid View v7',
                ),
                'default' => 'list-view-v1'
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
            'prop_grid_style',
            [
                'label' => esc_html__( 'only for hack', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->add_control(
            'layout_columns',
            [
                'label' => esc_html__( 'Columns', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options'  => array(
                    'grid-view-2-cols' => '2 Columns',
                    'grid-view-3-cols' => '3 Columns',
                    'grid-view-4-cols' => '4 Columns',
                ),
                'default' => 'grid-view-2-cols',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'listings_layout',
                            'operator' => '!in',
                            'value' => [
                                'list-view-v1',
                                'list-view-v2',
                                'list-view-v4',
                                'list-view-v5',
                                'list-view-v7',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__( 'Default Order', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options'  => array(
                    'd_date' => esc_html__( 'Date New to Old', 'houzez' ),
                    'a_date' => esc_html__( 'Date Old to New', 'houzez' ),
                    'd_price' => esc_html__( 'Price (High to Low)', 'houzez' ),
                    'a_price' => esc_html__( 'Price (Low to High)', 'houzez' ),
                    'featured_first' => esc_html__( 'Show Featured Listings on Top', 'houzez' ),
                    'random' => esc_html__( 'Random', 'houzez' ),
                ),
                'default' => 'd_date'
            ]
        );

        $this->add_control(
            'no_of_posts',
            [
                'label' => esc_html__( 'Limit', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '4',
            ]
        );


        $this->end_controls_section();

	
		$this->start_controls_section(
            'box_style',
            [
                'label' => __( 'Section Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
            'section_title_border',
            [
                'label' => esc_html__( 'Border Type', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => array(
                    '' => esc_html__('Default', 'houzez-theme-functionality'),
                    'none' => esc_html__('None', 'houzez-theme-functionality'),
                    'solid' => esc_html__('Solid', 'houzez-theme-functionality'),
                    'dashed' => esc_html__('Dashed', 'houzez-theme-functionality'),
                    'dotted' => esc_html__('Dotted', 'houzez-theme-functionality'),
                    'groove' => esc_html__('Groove', 'houzez-theme-functionality'),
                    'double' => esc_html__('Double', 'houzez-theme-functionality'),
                    'ridge' => esc_html__('Ridge', 'houzez-theme-functionality'),
                ),
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap' => 'border-bottom: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                	'section_title_border!' => 'none'
                ]
            ]
        );

        $this->add_responsive_control(
            'section_margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} #similar-listings-wrap' => 'margin-top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding_bottom',
            [
                'label' => esc_html__( 'Title Padding Bottom', 'houzez-theme-functionality' ),
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
                    '{{WRAPPER}} .block-title-wrap' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin_bottom',
            [
                'label' => esc_html__( 'Title Margin Bottom', 'houzez-theme-functionality' ),
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
                    '{{WRAPPER}} .block-title-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
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
                    '{{WRAPPER}} .item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .item-address' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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

	protected function render() {
		
		global $settings, $post;

		$settings = $this->get_settings_for_display();

        $this->single_property_preview_query(); // Only for preview

		htf_get_template_part('elementor/template-part/single-property/similar', 'properties');
        
        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_Similar );