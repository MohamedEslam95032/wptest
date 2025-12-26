<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Houzez_Agent_Rating extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agent-rating';
	}

	public function get_title() {
		return __( 'Agent Rating', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-agent eicon-rating';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agent')  {
            return ['houzez-single-agent-builder']; 
        }

		return [ 'houzez-single-agent' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'agent rating', 'agent' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Agent Rating', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'show_rating_count',
            [
                'label' => esc_html__( 'Rating Count', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating_stars',
            [
                'label' => esc_html__( 'Rating Stars', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating_text',
            [
                'label' => esc_html__( 'See All Review', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control(
            'all_review_text',
            [
                'label' => esc_html__( 'See All Review Text', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
				'default' => 'See all reviews',
				'condition' => [
					'show_rating_text' => 'yes'
				]
            ]
        );


		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_rating',
			[
				'label' => esc_html__( 'Agent Rating', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
            'agent_rating_stars_size',
            [
                'label' => esc_html__( 'Stars Size', 'houzez-theme-functionality' ),
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
				],
                'selectors' => [
                    '{{WRAPPER}} .rating-score-ele-wrap .star .icon-rating' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; background-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'agent_rating_stars_spacing',
            [
                'label' => esc_html__( 'Spacing', 'houzez-theme-functionality' ),
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
				],
                'selectors' => [
                    '{{WRAPPER}} .rating-score-ele-wrap .star .icon-rating' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'agent_rating_count_margin',
            [
                'label' => esc_html__( 'Rating Count Margin Right', 'houzez-theme-functionality' ),
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
                'selectors' => [
                    '{{WRAPPER}} .rating-score-ele-wrap .rating-score-text' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'rating_count_typography',
                'label' => esc_html__( 'Rating Count Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .rating-score-text',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'review_text_typography',
                'label' => esc_html__( 'Review Text Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .rating-score-ele-wrap .all-reviews',
            ]
        );

        $this->add_control(
            'rating_count_color',
            [
                'label' => esc_html__( 'Rating Count Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .rating-score-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'review_text_color',
            [
                'label' => esc_html__( 'Review Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .rating-score-ele-wrap .all-reviews' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'review_text_color_hover',
            [
                'label' => esc_html__( 'Review Text Color Hover', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .rating-score-ele-wrap .all-reviews:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->end_controls_section();
		
	}

	protected function render() {
		global $settings;
        $this->single_agent_preview_query(); // Only for preview

        $settings = $this->get_settings_for_display();

		htf_get_template_part('elementor/template-part/rating');

		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agent_Rating );