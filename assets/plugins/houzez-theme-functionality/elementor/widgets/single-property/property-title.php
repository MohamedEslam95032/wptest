<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Title extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
	use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-title';
	}

	public function get_title() {
		return __( 'Property Title', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-post-title';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

		return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'property title', 'title', 'heading', 'property', 'listing' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Listing Title', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'header_size',
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
					'p' => 'p',
				],
				'default' => 'h1',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->Houzez_Widget_Heading_Style();

		$this->add_control(
            'title_eclipse',
            [
                'label' => esc_html__( 'Title Eclipse', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'nowrap' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                    'normal' => esc_html__( 'No', 'houzez-theme-functionality' ),
                ],
                'default' => 'normal',
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title.item-title' => 'white-space: {{VALUE}}',
                ],
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

        $this->single_property_preview_query(); // Only for preview

        $this->add_render_attribute(
        	[
        		'title' => [
        			'class' => [
        				'elementor-heading-title',
				        'property_title',
				        'item-title',
        			]
        		]
        	]
        );

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'title', 'class', 'elementor-size-' . $settings['size'] );
		} else {
			$this->add_render_attribute( 'title', 'class', 'elementor-size-default' );
		}

		$permalink = get_permalink();

		$link = array('url' => $permalink);

		$title = get_the_title();

		if ( $settings['link'] && ! empty( $permalink ) ) {
			$this->add_link_attributes( 'url', $link );

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );
		}

		$title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['header_size'] ), $this->get_render_attribute_string( 'title' ), $title );

		// PHPCS - the variable $title_html holds safe data.
		echo '<div class="page-title">'.$title_html.'</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped


		$this->reset_preview_query(); // Only for preview
	}


	public function render_plain_content() {}
}
Plugin::instance()->widgets_manager->register( new Property_Title );