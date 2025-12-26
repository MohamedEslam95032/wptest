<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agency_Contact_Form extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Button_Traits;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-agency-contact-form';
	}

	public function get_title() {
		return __( 'Agency Contact Form', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-single-agency eicon-featured-image';
	}

	public function get_categories() {
        
        if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agency')  {
            return ['houzez-single-agency-builder']; 
        }

		return [ 'houzez-single-agency' ];
	}

	public function get_keywords() {
		return ['agency', 'agency contact form', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
            'box_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'agent_detail',
            [
                'label' => esc_html__( 'Agency Detail', 'elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
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
                    '{{WRAPPER}} .block-wrap' => 'margin-top: {{SIZE}}{{UNIT}};'
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
                    '{{WRAPPER}} .agent-details' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'agent_detail' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Agent Detail', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'agent_detail' => 'yes'
                ]
            ]
        );

        $this->houzez_agent_detail_style_controls();
      
        $this->end_controls_section();

        $this->start_controls_section(
            'content_style',
            [
                'label' => __( 'Box', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_backgourd_box_style_controls();

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
		
		global $settings;

        $settings = $this->get_settings_for_display();

        $this->single_agency_preview_query(); // Only for preview

        echo '<div class="block-wrap">';
        htf_get_template_part('elementor/template-part/single-agency/contact-form');
        echo '</div>';

        if ( Plugin::$instance->editor->is_edit_mode() ) :  ?>
            <script>
                jQuery('.selectpicker').selectpicker('refresh');
            </script> 
        <?php
        endif;

        $this->reset_preview_query(); // Only for preview

	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agency_Contact_Form );