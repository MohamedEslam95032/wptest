<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class Property_Section_Walkscore extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;
    
	public function get_name() {
		return 'houzez-property-section-walkscore';
	}

	public function get_title() {
		return __( 'Section Walkscore', 'houzez-theme-functionality' );
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
		return ['property', 'Walkscore', 'houzez' ];
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
            'energy_titles_note',
            [
                'label' => __( 'Make sure you have added Walkscore API key in Theme Options -> Property Details -> Walkscore', 'houzez-theme-functionality' ),
                'type' => 'houzez-warning-note',
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
                'label' => __( 'Content Style', 'houzez-theme-functionality' ),
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

	}

	protected function render() {
		
		global $post;

		$settings = $this->get_settings_for_display();

		$ele_settings = $settings;

        $this->single_property_preview_query(); // Only for preview
        
		$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : houzez_option('sps_walkscore', "WalkScore");
        
        $houzez_walkscore_api = houzez_option('houzez_walkscore_api');

        if( $houzez_walkscore_api != '' ) {
        ?>
        <div class="property-walkscore-wrap property-section-wrap" id="property-walkscore-wrap">
            <div class="block-wrap">

                <?php if( $settings['section_header'] ) { ?>
                <div class="block-title-wrap d-flex justify-content-between align-items-center">
                    <h2><?php echo $section_title; ?></h2>
                </div><!-- block-title-wrap -->
                <?php } ?>

                <div class="block-content-wrap">

                    <?php houzez_walkscore($post->ID); ?>
                    <?php
                    if ( Plugin::$instance->editor->is_edit_mode() ) { 
                        echo esc_html__('Visit listing detail page for walkscore preview', 'houzez-theme-functionality');
                    }
                    ?>

                </div><!-- block-content-wrap -->
            </div><!-- block-wrap -->
        </div><!-- property-walkscore-wrap -->
        <?php } 
        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_Walkscore );