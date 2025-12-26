<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_Map extends \Elementor\Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-section-map';
	}

	public function get_title() {
		return __( 'Section Map', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-map-pin';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['property', 'mapbox', 'google map', 'Open Street Map', 'section map', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();


		$this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        
        $this->add_control(
            'marker_type',
            [
                'label' => esc_html__( 'Pin or Circle', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'marker',
                'options' => array(
                    'marker' => esc_html__('Marker Pin', 'houzez-theme-functionality'),
                    'circle' => esc_html__('Circle', 'houzez-theme-functionality'),
                ),
            ]
        );

        $this->add_control(
            'zoom_level',
            [
                'label' => esc_html__( 'Zoom', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => '15',
            ]
        );

        $this->add_responsive_control(
            'map_height',
            [
                'label'           => esc_html__( 'Map Height (px)', 'houzez-theme-functionality' ),
                'type'            => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 500,
                ],
                'selectors'       => [
                    '{{WRAPPER}} .h-properties-map-for-elementor' => 'height: {{SIZE}}{{UNIT}};',

                ],
            ]
        );

        $this->end_controls_section();


	}

	protected function render() {
		$settings = $this->get_settings_for_display();

        $map_options = array();
        $property_data = array();

        $this->single_property_preview_query(); // Only for preview

        $map_data_json = houzez_get_single_listing_map_data_json( get_the_ID() );
        $map_options = houzez_get_map_options();

        if($settings['zoom_level']) {
            $map_options['single_map_zoom'] = $settings['zoom_level'];
        }

        if($settings['marker_type']) {
            $map_options['map_pin_type'] = $settings['marker_type'];
        }
        $map_options['markerPricePins'] = 'no';

        
        $map_options_json = esc_attr( wp_json_encode( $map_options ) );

        if ( Plugin::$instance->editor->is_edit_mode() ) {?>
            <style>
                .houzez-elementor-map-wrap {
                    border: 1px solid #ccc;
                }
                #houzez-single-listing-map, #houzez-single-listing-map-address, #houzez-single-listing-map-elementor {
                    display: flex;
                    justify-content: center; /* Horizontal centering */
                    align-items: center; /* Vertical centering */
                    text-align: center; /* Center the text inside the div */
                }
            </style>
            <div class="houzez-elementor-map-wrap h-properties-map-for-elementor">
                <div id="houzez-single-listing-map-elementor" data-map='<?php echo $map_data_json; ?>' data-options='<?php echo $map_options_json; ?>'>
                <?php esc_html_e( 'Map will show here on frontend', 'houzez-theme-functionality' );?>
                </div>
            </div>
        <?php
        } else { ?>
            <div class="houzez-elementor-map-wrap h-properties-map-for-elementor">
                <div id="houzez-single-listing-map-elementor" data-map='<?php echo $map_data_json; ?>' data-options='<?php echo $map_options_json; ?>'></div>
            </div>
        <?php }

        $this->reset_preview_query(); // Only for preview
	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new Property_Section_Map() );