<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Post_Comments extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-post-comments';
	}

	public function get_title() {
		return esc_html__( 'Post Comments', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'eicon-comments';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-post')  {
            return ['houzez-single-post-builder']; 
        }

		return [ 'houzez-single-post' ];
	}

	public function get_keywords() {
		return [ 'comments', 'post', 'response', 'form' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Comments', 'houzez-theme-functionality' ),
			]
		);

		$this->add_control(
			'_skin',
			[
				'type' => Controls_Manager::HIDDEN,
			]
		);

		$this->add_control(
			'skin_temp',
			[
				'label' => esc_html__( 'Skin', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Theme Comments', 'houzez-theme-functionality' ),
				],
				'description' => esc_html__( 'The Theme Comments skin uses the currently active theme comments design and layout to display the comment form and comments.', 'houzez-theme-functionality' ),
			]
		);


		$this->end_controls_section();
	}

	public function render() {
		global $settings, $houzez_local;

        $houzez_local = houzez_get_localization();

		$settings = $this->get_settings();

		$this->single_post_preview_query(); // Only for preview

		if ( ! comments_open() && ( Plugin::instance()->preview->is_preview_mode() || Plugin::instance()->editor->is_edit_mode() ) ) :
			?>
			<div class="elementor-alert elementor-alert-danger" role="alert">
				<span class="elementor-alert-title">
					<?php esc_html_e( 'Comments are closed.', 'houzez-theme-functionality' ); ?>
				</span>
				<span class="elementor-alert-description">
					<?php esc_html_e( 'Switch on comments from either the discussion box on the WordPress post edit screen or from the WordPress discussion settings.', 'houzez-theme-functionality' ); ?>
				</span>
			</div>
			<?php
		else :
			comments_template();
		endif;

		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Post_Comments );
