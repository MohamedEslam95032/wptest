<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_Address extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;


	public function get_name() {
		return 'houzez-property-section-address';
	}

	public function get_title() {
		return __( 'Section Address', 'houzez-theme-functionality' );
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
		return ['property', 'address', 'houzez' ];
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
            'data_columns',
            [
                'label' => esc_html__( 'Data Columns', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'list-2-cols',
                'options' => array(
                    'list-1-cols' => esc_html__('1 Column', 'houzez-theme-functionality'),
                    'list-2-cols' => esc_html__('2 Columns', 'houzez-theme-functionality'),
                    'list-3-cols' => esc_html__('3 Columns', 'houzez-theme-functionality'),
                ),
            ]
        );

        $this->add_responsive_control(
            'meta_line_height',
            [
                'label' => esc_html__( 'Line Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap li' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'houzez_address_fields_section',
            [
                'label' => esc_html__( 'Address Data', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'houzez_address_sort',
            [
                'label' => __( 'Control Name', 'houzez-theme-functionality' ),
                'type'  => 'houzez-address-sort-control',
            ]
        );


        $this->end_controls_section();


        //Titles
        $this->start_controls_section(
            'section_titles',
            [
                'label' => __( 'Titles', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'address_title',
            [
                'label' => esc_html__( 'Address', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Address',
            ]
        );
        $this->add_control(
            'country_title',
            [
                'label' => esc_html__( 'Country', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Country',
            ]
        );

        $this->add_control(
            'state_title',
            [
                'label' => esc_html__( 'State', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'State',
            ]
        );

        $this->add_control(
            'city_title',
            [
                'label' => esc_html__( 'City', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'City',
            ]
        );

        $this->add_control(
            'area_title',
            [
                'label' => esc_html__( 'Area', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Area',
            ]
        );

        $this->add_control(
            'zip_title',
            [
                'label' => esc_html__( 'Zip/Postal Code', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Zip/Postal Code',
            ]
        );

        $this->end_controls_section();

	   
        //Style
		$this->start_controls_section(
            'box_style',
            [
                'label' => __( 'Section Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_single_property_section_styling_traits();

		$this->end_controls_section();

        //Section title
		$this->start_controls_section(
            'sec_title_style',
            [
                'label' => __( 'Section Title', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
            'sec_title_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap h2' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .block-title-wrap h2',
            ]
        );
      
		$this->end_controls_section();


        // Meta Titles
        $this->start_controls_section(
            'meta_titles_style',
            [
                'label' => __( 'Meta Titles', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'meta_title_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap li strong' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_title_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .block-content-wrap li strong',
            ]
        );

        $this->end_controls_section();

        // Meta Values
        $this->start_controls_section(
            'meta_values_style',
            [
                'label' => __( 'Meta Values', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'meta_value_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap li span' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_value_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .block-content-wrap li span',
            ]
        );

        $this->end_controls_section();

        //Border
        $this->start_controls_section(
            'section_border_style',
            [
                'label' => __( 'Border', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'border_type',
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
                    '{{WRAPPER}} .block-content-wrap ul li .list-lined-item' => 'border-bottom-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'meta_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap ul li .list-lined-item' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'border_type!' => 'none'
                ]
            ]
        );

        $this->end_controls_section();

	}

	protected function render() {
		
		global $sorting_settings, $settings, $post;

        $sorting_settings = '';
		$settings = $this->get_settings_for_display();
        if ( isset( $settings['houzez_address_sort'] ) && ! empty( $settings['houzez_address_sort'] ) ) {
            $sorting_settings = $settings['houzez_address_sort'];
        }

        $this->single_property_preview_query(); // Only for preview

        htf_get_template_part('elementor/template-part/single-property/section', 'address');

        $this->reset_preview_query(); // Only for preview

	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_Address );