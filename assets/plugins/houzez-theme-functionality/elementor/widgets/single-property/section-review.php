<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Property_Section_Review extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Button_Traits;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-section-review';
	}

	public function get_title() {
		return __( 'Section Reviews', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-review';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['property', 'reviews', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();


		$this->houzez_review_styling_traits();

        $this->houzez_review_form_styling_traits();

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
            'submit_button_style',
            [
                'label' => esc_html__( 'Submit Button', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_houzez_forms_button_style_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'leave_review_button_style',
            [
                'label' => esc_html__( 'Leave a Review Button', 'houzez-theme-functionality' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->register_houzez_leave_review_button_style_controls();

        $this->end_controls_section();

	}

	protected function render() {
		
		global $ele_settings, $post;

		$settings = $this->get_settings_for_display();
        $ele_settings = $settings;

        $this->single_property_preview_query(); // Only for preview

		get_template_part('template-parts/reviews/main');

        if ( Plugin::$instance->editor->is_edit_mode() ) :  ?>
            <script>
                jQuery('.selectpicker').selectpicker('refresh');
            </script>
        <?php
        endif;
        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_Review );