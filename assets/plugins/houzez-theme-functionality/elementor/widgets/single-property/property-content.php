<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Content extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-content';
	}

	public function get_title() {
		return __( 'Property Content', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-post-content';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return [ 'content', 'post', 'property', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
            'prop_content_typo',
            [
                'label' => __( 'Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
            'prop_content_align',
            [
                'label' => __( 'Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'prop_text_color',
            [
                'label' => __( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
            'read_more_style',
            [
                'label' => __( 'Read More Link', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'read_more_color',
            [
                'label' => __( 'Link Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#00aeef',
                'selectors' => [
                    '{{WRAPPER}} .houzez-read-more-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_hover_color',
            [
                'label' => __( 'Link Hover Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#0080b3',
                'selectors' => [
                    '{{WRAPPER}} .houzez-read-more-link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'read_more_typography',
                'selector' => '{{WRAPPER}} .houzez-read-more-link',
            ]
        );

        $this->end_controls_section();

	}

	protected function render() {

        $this->single_property_preview_query(); // Only for preview

		// Get the raw post content without triggering Elementor's rendering
		// This prevents capturing all page widgets and exponential DB growth
		global $post;
		$content = $post->post_content;

		// Process content with auto excerpt if enabled
		$processed_content = houzez_auto_excerpt_content($content, 'property');

		if( $processed_content['has_more'] ) {
			// Apply only safe WordPress filters, NOT the_content which triggers Elementor
			$content_before_more = wpautop( do_shortcode( $processed_content['content_before'] ) );
			$content_after_more = wpautop( do_shortcode( $processed_content['content_after'] ) );

			// Output the content with read more functionality
			echo wp_kses_post( $content_before_more );

			// Output read more link (safe - generated by our code, not user input)
			$more_link_text = houzez_option('read_more_text', __( 'Read More', 'houzez-theme-functionality' ));
			?>
			<p><a href="#" class="houzez-read-more-link" onclick="this.style.display='none'; this.parentNode.nextElementSibling.style.display='block'; return false;"><?php echo esc_html($more_link_text); ?></a></p>
			<div class="houzez-more-content" style="display: none;"><?php echo wp_kses_post( $content_after_more ); ?></div>
			<?php
		} else {
			// No more tag needed, apply safe filters and display the content
			$content = wpautop( do_shortcode( $processed_content['content'] ) );
			echo wp_kses_post( $content );
		}

        $this->reset_preview_query(); // Only for preview
	}

	/**
	 * Render Plain Content.
	 *
	 * Override the default behavior to prevent Elementor from saving rendered content.
	 * This is crucial because the content comes from the post itself, not the widget settings.
	 * Outputting nothing here prevents database bloat on each save.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_plain_content() {}

}
Plugin::instance()->widgets_manager->register( new Property_Content );