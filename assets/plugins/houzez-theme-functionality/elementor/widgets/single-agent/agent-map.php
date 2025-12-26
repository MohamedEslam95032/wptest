<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agent_Location_Map extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agent-location-map';
	}

	public function get_title() {
		return __( 'Agent Location Map', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-agent eicon-google-maps';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agent')  {
            return ['houzez-single-agent-builder']; 
        }

		return [ 'houzez-single-agent' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'agent location map' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->end_controls_section();
		
	}

	protected function render() {
        $this->single_agent_preview_query(); // Only for preview

        $settings = $this->get_settings_for_display(); 
        ?>

		<div class="agent-ele-image-wrap">
            <?php
			$agent_address = get_post_meta( get_the_ID(), 'fave_agent_address', true );

			if( ! empty( $agent_address ) ) {
				$address_coordinates = houzez_get_address_coordinates($agent_address);
			}

			if( ! empty( $address_coordinates ) ) { 
				if ( Plugin::$instance->editor->is_edit_mode() ) {?>
                    <style>
                        #houzez-agent-sidebar-map {
                            border: 1px solid #ccc;
                            display: flex;
                            justify-content: center; /* Horizontal centering */
                            align-items: center; /* Vertical centering */
                            height: 300px; /* Set a height for the container */
                            text-align: center; /* Center the text inside the div */
                        }
                    </style>
                    <div id="houzez-agent-sidebar-map"><?php esc_html_e( 'Map will show here on frontend', 'houzez-theme-functionality' );?></div>
                <?php
                } else {?>
                	<div id="houzez-agent-sidebar-map" data-lat="<?php echo esc_attr($address_coordinates['lat']); ?>" data-lng="<?php echo esc_attr($address_coordinates['lng']); ?>">
                <?php
                }
            } ?>
        </div><!-- agent-image -->
       <?php 
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agent_Location_Map );