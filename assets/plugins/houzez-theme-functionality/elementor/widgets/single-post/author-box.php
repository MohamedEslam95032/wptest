<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Author_Box extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_enqueue_style( 'houzez-author-box', HOUZEZ_PLUGIN_URL . 'elementor/assets/css/author-box.css', [], '1.0.0' );
	}

	public function get_style_depends() {
		return [ 'houzez-author-box' ];
	}

	public function get_name() {
		return 'houzez-author-box';
	}

	public function get_title() {
		return __( 'Author Box', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-post eicon-person';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-post')  {
            return ['houzez-single-post-builder']; 
        }

		return [ 'houzez-single-post' ];
	}

	public function get_keywords() {
		return [ 'author', 'user', 'profile', 'biography', 'testimonial', 'avatar' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Author Info', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_avatar',
			[
				'label' => esc_html__( 'Profile Picture', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'houzez-author-box--avatar-',
				'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
				'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
				'default' => 'yes',
				'separator' => 'before',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'avatar_size',
			[
				'label' => esc_html__( 'Picture Size', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 300,
				'condition' => [
					'show_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_name',
			[
				'label' => esc_html__( 'Display Name', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'houzez-author-box--name-',
				'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
				'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
				'default' => 'yes',
				'render_type' => 'template',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'author_name_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
				],
				'default' => 'h4',
			]
		);

		$this->add_control(
			'link_to',
			[
				'label' => esc_html__( 'Link', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'houzez-theme-functionality' ),
					'website' => esc_html__( 'Website', 'houzez-theme-functionality' ),
					'posts_archive' => esc_html__( 'Posts Archive', 'houzez-theme-functionality' ),
				],
				'description' => esc_html__( 'Link for the Author Name and Image', 'houzez-theme-functionality' ),
			]
		);

		$this->add_control(
			'show_biography',
			[
				'label' => esc_html__( 'Biography', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'houzez-author-box--biography-',
				'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
				'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
				'default' => 'yes',
				'render_type' => 'template',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_link',
			[
				'label' => esc_html__( 'Archive Button', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'houzez-author-box--link-',
				'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
				'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
				'default' => 'no',
				'render_type' => 'template',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link_text',
			[
				'label' => esc_html__( 'Archive Text', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'All Posts', 'houzez-theme-functionality' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'houzez-theme-functionality' ),
						'icon' => 'eicon-h-align-left',
					],
					'above' => [
						'title' => esc_html__( 'Above', 'houzez-theme-functionality' ),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'houzez-theme-functionality' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'separator' => 'before',
				'prefix_class' => 'houzez-author-box--layout-image-',
			]
		);

		$this->add_control(
			'alignment',
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
				],
				//'prefix_class' => 'houzez-author-box--align-',
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box' => 'text-align: {{VALUE}}',
				],
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__( 'Image', 'houzez-theme-functionality' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'image_vertical_align',
			[
				'label' => esc_html__( 'Vertical Align', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'houzez-theme-functionality' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'houzez-theme-functionality' ),
						'icon' => 'eicon-v-align-middle',
					],
				],
				'prefix_class' => 'houzez-author-box--image-valign-',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'layout',
							'operator' => '!==',
							'value' => 'above',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => esc_html__( 'Image Size', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 200,
					],
					'em' => [
						'max' => 20,
					],
					'rem' => [
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'image_gap',
			[
				'label' => esc_html__( 'Gap', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}}.houzez-author-box--layout-image-left .houzez-author-box__avatar,
					 body:not(.rtl) {{WRAPPER}}:not(.houzez-author-box--layout-image-above) .houzez-author-box__avatar' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: 0;',

					'body:not(.rtl) {{WRAPPER}}.houzez-author-box--layout-image-right .houzez-author-box__avatar,
					 body.rtl {{WRAPPER}}:not(.houzez-author-box--layout-image-above) .houzez-author-box__avatar' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right:0;',

					'{{WRAPPER}}.houzez-author-box--layout-image-above .houzez-author-box__avatar' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'image_border',
			[
				'label' => esc_html__( 'Border', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__avatar img' => 'border-style: solid',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label' => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__avatar img' => 'border-color: {{VALUE}}',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'image_border',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'image_border_width',
			[
				'label' => esc_html__( 'Border Width', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 20,
					],
					'em' => [
						'max' => 2,
					],
					'rem' => [
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__avatar img' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'image_border',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__avatar img' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_box_shadow',
				'selector' => '{{WRAPPER}} .houzez-author-box__avatar img',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_avatar',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => esc_html__( 'Author', 'houzez-theme-functionality' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_name_style',
			[
				'label' => esc_html__( 'Name', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__name' => 'color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .houzez-author-box__name',
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'name_gap',
			[
				'label' => esc_html__( 'Gap', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__name' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'heading_bio_style',
			[
				'label' => esc_html__( 'Biography', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_biography',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'bio_color',
			[
				'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__bio' => 'color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_biography',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bio_typography',
				'selector' => '{{WRAPPER}} .houzez-author-box__bio',
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_biography',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'bio_gap',
			[
				'label' => esc_html__( 'Gap', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__bio' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'show_biography',
							'operator' => '===',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => 'Button',
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__button' => 'color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__button' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .houzez-author-box__button',
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__button:hover' => 'border-color: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label' => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__button:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'link_text!' => '',
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
					'unit' => 'ms',
				],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__button' => 'transition-duration: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__( 'Animation', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 20,
					],
					'em' => [
						'max' => 2,
					],
					'rem' => [
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_text_padding',
			[
				'label' => esc_html__( 'Padding', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .houzez-author-box__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'link_text!' => '',
				],
			]
		);

		$this->end_controls_section();
		
	}

	protected function render() {
        $this->single_post_preview_query(); // Only for preview
        $settings = $this->get_settings_for_display();

		$author = [];
		$link_tag = 'div';
		$link_url = '';
		$link_target = '';
		$author_name_tag = Utils::validate_html_tag( $settings['author_name_tag'] );


		$avatar_args['size'] = $settings['avatar_size'];

		$user_id = get_the_author_meta( 'ID' );
		$author_avatar = get_avatar_url( $user_id, $avatar_args );
		$author_display_name = get_the_author_meta( 'display_name' );
		$author_website = get_the_author_meta( 'user_url' );
		$author_bio = get_the_author_meta( 'description' );
		$author_posts_url = get_author_posts_url( $user_id );


		$print_avatar = ( 'yes' === $settings['show_avatar'] && ! empty( $author_avatar ) );
		$print_name = ( 'yes' === $settings['show_name'] && ! empty( $author_display_name ) );
		$print_bio = ( 'yes' === $settings['show_biography'] && ! empty( $author_bio ) );
		$print_link = ( 'yes' === $settings['show_link'] && ! empty( $settings['link_text'] ) && ! empty( $author_posts_url ) );


		if ( ! empty( $settings['link_to'] ) ) {
		    if ( 'website' === $settings['link_to'] && ! empty( $author_website ) ) {
		        $link_tag = 'a';
		        $link_url = $author_website;
		        $link_target = '_blank';
		    } elseif ( 'posts_archive' === $settings['link_to'] && ! empty( $author_posts_url ) ) {
		        $link_tag = 'a';
		        $link_url = $author_posts_url;
		    }

		    if ( ! empty( $link_url ) ) {
		        $this->add_render_attribute( 'author_link', 'href', esc_url( $link_url ) );

		        if ( ! empty( $link_target ) ) {
		            $this->add_render_attribute( 'author_link', 'target', $link_target );
		        }
		    }
		}


		$this->add_render_attribute(
			'button',
			'class', [
				'houzez-author-box__button',
				'elementor-button',
				'elementor-size-xs',
			]
		);

		if ( $print_link ) {
			$this->add_render_attribute( 'button', 'href', esc_url( $author_posts_url ) );
		}

		if ( $print_link && ! empty( $settings['button_hover_animation'] ) ) {
			$this->add_render_attribute(
				'button',
				'class',
				'elementor-animation-' . $settings['button_hover_animation']
			);
		}

		if ( $print_avatar ) {
			$this->add_render_attribute(
				'avatar',
				[
					'src' => esc_url( $author_avatar ),
					'alt' => ( ! empty( $author_display_name ) )
						? sprintf(
							/* translators: %s: Author display name. */
							esc_attr__( 'Picture of %s', 'houzez-theme-functionality' ),
							$author_display_name
						)
						: esc_html__( 'Author picture', 'houzez-theme-functionality' ),
					'loading' => 'lazy',
				]
			);
		}

		?>
		<div class="houzez-author-box">
			<?php if ( $print_avatar ) { ?>
				<<?php Utils::print_validated_html_tag( $link_tag ); ?> <?php $this->print_render_attribute_string( 'author_link' ); ?> class="houzez-author-box__avatar">
					<img <?php $this->print_render_attribute_string( 'avatar' ); ?>>
				</<?php Utils::print_validated_html_tag( $link_tag ); ?>>
			<?php } ?>

			<div class="houzez-author-box__text">
				<?php if ( $print_name ) : ?>
					<<?php Utils::print_validated_html_tag( $link_tag ); ?> <?php $this->print_render_attribute_string( 'author_link' ); ?>>
						<<?php Utils::print_validated_html_tag( $author_name_tag ); ?> class="houzez-author-box__name">
							<?php Utils::print_unescaped_internal_string( $author_display_name ); ?>
						</<?php Utils::print_validated_html_tag( $author_name_tag ); ?>>
					</<?php Utils::print_validated_html_tag( $link_tag ); ?>>
				<?php endif; ?>

				<?php if ( $print_bio ) : ?>
					<div class="houzez-author-box__bio">
						<?php Utils::print_unescaped_internal_string( $author_bio ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $print_link ) : ?>
					<a <?php $this->print_render_attribute_string( 'button' ); ?>>
						<?php $this->print_unescaped_setting( 'link_text' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php

		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Author_Box );