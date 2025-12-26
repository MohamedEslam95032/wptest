<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Post_Navigation extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-post-navigation';
	}

	public function get_title() {
		return __( 'Post Navigation', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-single-post eicon-post-navigation';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-post')  {
            return ['houzez-single-post-builder']; 
        }

		return [ 'houzez-single-post' ];
	}

	public function get_keywords() {
		return [ 'post', 'navigation', 'menu', 'links' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Post Navigation', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_label',
			[
				'label' => esc_html__( 'Label', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
				'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'prev_label',
			[
				'label' => esc_html__( 'Previous Label', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'default' => esc_html__( 'Previous', 'houzez-theme-functionality' ),
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->add_control(
			'next_label',
			[
				'label' => esc_html__( 'Next Label', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Next', 'houzez-theme-functionality' ),
				'condition' => [
					'show_label' => 'yes',
				],
				'ai' => [
					'active' => false,
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => esc_html__( 'Post Title', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'houzez-theme-functionality' ),
				'label_off' => esc_html__( 'Hide', 'houzez-theme-functionality' ),
				'default' => 'yes',
			]
		);
		

		$this->end_controls_section();

		$this->start_controls_section(
			'label_style',
			[
				'label' => esc_html__( 'Label', 'houzez-theme-functionality' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_label_style' );

		$this->start_controls_tab(
			'label_color_normal',
			[
				'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} p.post-navigation__prev--label' => 'color: {{VALUE}};',
					'{{WRAPPER}} p.post-navigation__next--label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_color_hover',
			[
				'label' => esc_html__( 'Hover', 'houzez-theme-functionality' ),
			]
		);

		$this->add_control(
			'label_hover_color',
			[
				'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} p.post-navigation__prev--label:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} p.post-navigation__next--label:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_hover_color_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 'ms',
				],
				'selectors' => [
					'{{WRAPPER}} p.post-navigation__prev--label' => 'transition-duration: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} p.post-navigation__next--label' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} p.post-navigation__prev--label, {{WRAPPER}} p.post-navigation__next--label',
				'exclude' => [ 'line_height' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			[
				'label' => esc_html__( 'Title', 'houzez-theme-functionality' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => esc_html__( 'Normal', 'houzez-theme-functionality' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--title, {{WRAPPER}} span.post-navigation__next--title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
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
					'{{WRAPPER}} span.post-navigation__prev--title:hover, {{WRAPPER}} span.post-navigation__next--title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_color_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 'ms',
				],
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--title' => 'transition-duration: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} span.post-navigation__next--title' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} span.post-navigation__prev--title, {{WRAPPER}} span.post-navigation__next--title',
				'exclude' => [ 'line_height' ],
			]
		);

		$this->end_controls_section();
		
	}

	protected function render() {
        $this->single_post_preview_query(); // Only for preview
        $settings = $this->get_settings_for_display();

        $prev_label = '';
		$next_label = '';
		$prev_arrow = '';
		$next_arrow = '';

		if ( 'yes' === $settings['show_label'] ) {
			$prev_label = '<p class="post-navigation__prev--label">' . $settings['prev_label'] . '</p>';
			$next_label = '<p class="post-navigation__next--label">' . $settings['next_label'] . '</p>';
		}

		$prev_title = '';
		$next_title = '';

		if ( 'yes' === $settings['show_title'] ) {
			$prev_title = '<span class="post-navigation__prev--title">%title</span>';
			$next_title = '<span class="post-navigation__next--title">%title</span>';
		}
        ?>
		<div class="next-prev-block next-prev-blog blog-section clearfix">
		    <div class="prev-box float-start text-start">
		        <?php
		        $prevPost = get_previous_post(true);
		        if ($prevPost) {
		            ?>
		            <div class="next-prev-block-content">
		                <?php previous_post_link( '%link', $prev_arrow . '<span class="elementor-post-navigation__link__prev">' . $prev_label . $prev_title . '</span>' ); ?>
		            </div>
		            <?php
		        }
		        ?>
		    </div>
		    <div class="next-box float-end text-end">
		        <?php
		        $nextPost = get_next_post(true);
		        if ($nextPost) {
		            ?>
		            <div class="next-prev-block-content">
		                <?php next_post_link( '%link', $next_arrow . '<span class="elementor-post-navigation__link__next">' . $next_label . $next_title . '</span>' ); ?>
		            </div>
		            <?php
		        }
		        ?>
		    </div>
		</div>

		<?php
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Post_Navigation );