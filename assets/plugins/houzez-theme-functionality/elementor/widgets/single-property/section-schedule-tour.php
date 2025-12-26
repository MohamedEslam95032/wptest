<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Property_Section_ScheduleTour extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Button_Traits;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-section-schedule-tour';
	}

	public function get_title() {
		return __( 'Section Schedule Tour', 'houzez-theme-functionality' );
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
		return ['property', 'Schedule', 'houzez', 'Tour' ];
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


        $this->end_controls_section();

	
		$this->start_controls_section(
            'box_style',
            [
                'label' => __( 'Section Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_single_property_section_styling_traits();

		$this->end_controls_section();

		$this->start_controls_section(
            'content_style',
            [
                'label' => __( 'Section Title', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'heading_section_title',
			[
				'label' => esc_html__( 'Section Title', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
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

        $this->start_controls_section(
            'section_field_style',
            [
                'label' => esc_html__( 'Fields', 'houzez-theme-functionality' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_form_fields_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_label_style',
            [
                'label' => esc_html__( 'Labels', 'houzez-theme-functionality' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_form_labels_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_form_title_style',
            [
                'label' => esc_html__( 'Form Title', 'houzez-theme-functionality' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_title_text_color',
            [
                'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'form_title_typography',
                'selector' => '{{WRAPPER}} .block-content-wrap .block-title-wrap',
            ]
        );

        $this->add_responsive_control(
            'form_title__padding',
            [
                'label' => esc_html__( 'Padding Bottom', 'houzez-theme-functionality' ),
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
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'section_form_title_border',
            [
                'label' => esc_html__( 'Hide Title Border', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'border-bottom: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'form_title_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-content-wrap .block-title-wrap' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'section_form_title_border!' => 'none'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'submit_button_style',
            [
                'label' => esc_html__( 'Submit Button', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_houzez_forms_button_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'terms_style',
            [
                'label' => esc_html__( 'Terms of use', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_form_terms_style_controls();

        $this->end_controls_section();

	}

	protected function render() {
		
		global $post;

		$settings = $this->get_settings_for_display();

		$ele_settings = $settings;

		$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : houzez_option('sps_schedule_tour', 'Schedule a Tour');
        
        $this->single_property_preview_query(); // Only for preview
        ?>

        <div class="property-schedule-tour-wrap property-section-wrap" id="property-schedule-tour-wrap">
            <div class="block-wrap">

                <?php if( $settings['section_header'] ) { ?>
                <div class="block-title-wrap d-flex justify-content-between align-items-center">
                    <h2><?php echo esc_attr($section_title); ?></h2>
                </div><!-- block-title-wrap -->
                <?php } ?>

                <div class="block-content-wrap">
                    <?php get_template_part('property-details/partials/schedule-tour-form'); ?>
                </div><!-- block-content-wrap -->
            </div><!-- block-wrap -->
        </div><!-- property-schedule-tour-wrap -->

        <?php 

        if ( Plugin::$instance->editor->is_edit_mode() ) :  ?>
            <script>
                jQuery('.selectpicker').selectpicker('refresh');
            </script>
        <?php
        endif;
        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_ScheduleTour );