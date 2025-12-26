<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Section Title Widget.
 * @since 4.0.0
 */
class Houzez_Elementor_Properties_Mapbox extends \Elementor\Widget_Base {

    public function get_script_depends() {
        return [ 'mapbox-gl', 'houzez-mapbox' ];
    }

    public function get_style_depends() {
        return [ 'mapbox-gl' ];
    }
    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 4.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_properties_mapbox';
    }

    /**
     * Get widget title.
     * @since 4.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Properties Mapbox', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 4.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-map-pin';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 4.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'houzez-elements' ];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 4.0.0
     * @access protected
     */
    protected function register_controls() {

        $allowed_html = array(
            'a'      => array(
                'href'  => array(),
                'title' => array()
            ),
            'br'     => array(),
            'em'     => array(),
            'strong' => array(),
        );

        $this->start_controls_section(
            'map_options_section',
            [
                'label'     => esc_html__( 'Map Options', 'houzez-theme-functionality' ),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'mapbox_style',
            [
                'label'   => esc_html__( 'Map Style', 'houzez-theme-functionality' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'global' => esc_html__( 'Global (Use Theme Options)', 'houzez-theme-functionality' ),
                    'mapbox://styles/mapbox/streets-v12'   => esc_html__( 'Streets', 'houzez-theme-functionality' ),
                    'mapbox://styles/mapbox/outdoors-v12'  => esc_html__( 'Outdoors', 'houzez-theme-functionality' ),
                    'mapbox://styles/mapbox/light-v11'     => esc_html__( 'Light', 'houzez-theme-functionality' ),
                    'mapbox://styles/mapbox/dark-v11'      => esc_html__( 'Dark', 'houzez-theme-functionality' ),
                    'mapbox://styles/mapbox/satellite-v9'  => esc_html__( 'Satellite', 'houzez-theme-functionality' ),
                    'mapbox://styles/mapbox/satellite-streets-v12' => esc_html__( 'Satellite Streets', 'houzez-theme-functionality' ),
                    'custom' => esc_html__( 'Custom Style URL', 'houzez-theme-functionality' ),
                ],
                'default' => 'global',
            ]
        );

        $this->add_control(
            'mapbox_custom_style_url',
            [
                'label' => esc_html__( 'Custom Style URL', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'mapbox_style' => 'custom',
                ],
            ]
        );
        $this->add_control(
            'mapbox_style_instructions',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => sprintf(
                    esc_html__('To use a custom Mapbox style URL, select "Custom Style URL" above and enter your Mapbox style URL. You can create custom styles in the %1$sMapbox Studio%2$s.', 'houzez-theme-functionality'),
                    '<a href="https://studio.mapbox.com/" target="_blank">',
                    '</a>'
                ),
                'condition' => [
                    'mapbox_style' => 'custom',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'next_previous_control',
            [
                'label'     => esc_html__( 'Hide Next/Previous Control', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                "description" => '',
                'label_on'     => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off'    => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'selectors'    => [
                    '{{WRAPPER}} .map-next-prev-actions' => 'display: none;',
                ],
            ]
        );

        $this->add_control(
            'zoom_control',
            [
                'label'     => esc_html__( 'Hide Zoom Control', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                "description" => '',
                'label_on'     => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off'    => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'selectors'    => [
                    '{{WRAPPER}} .map-arrows-actions' => 'display: none;',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Properties Filters', 'houzez-theme-functionality' ),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_limit',
            [
                'label'     => esc_html__('Number of properties', 'houzez-theme-functionality'),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 5000,
                'step'    => 1,
                'default' => 9,
            ]
        );

        $this->add_control(
            'offset',
            [
                'label'     => 'Offset',
                'type'      => \Elementor\Controls_Manager::TEXT,
                'description' => '',
            ]
        );

        // Property taxonomies controls
        $prop_taxonomies = get_object_taxonomies( 'property', 'objects' );

        unset( $prop_taxonomies['property_country'] );
        unset( $prop_taxonomies['property_state'] );
        unset( $prop_taxonomies['property_city'] );
        unset( $prop_taxonomies['property_area'] );

        if ( ! empty( $prop_taxonomies ) && ! is_wp_error( $prop_taxonomies ) ) {
            foreach ( $prop_taxonomies as $single_tax ) {

                $options_array = array();
                $terms   = get_terms( $single_tax->name );

                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $options_array[ $term->slug ] = $term->name;
                    }
                }

                $this->add_control(
                    $single_tax->name,
                    [
                        'label'    => $single_tax->label,
                        'type'     => \Elementor\Controls_Manager::SELECT2,
                        'multiple' => true,
                        'label_block' => true,
                        'options'  => $options_array,
                    ]
                );
            }
        }

        $this->add_control(
            'property_country',
            [
                'label'         => esc_html__('Country', 'houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_country'),
            ]
        );

        $this->add_control(
            'property_state',
            [
                'label'         => esc_html__('State', 'houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_state'),
            ]
        );

        $this->add_control(
            'property_city',
            [
                'label'         => esc_html__('City', 'houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_city'),
            ]
        );

        $this->add_control(
            'property_area',
            [
                'label'         => esc_html__('Area', 'houzez'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_area'),
            ]
        );

        $this->add_control(
            'featured_prop',
            [
                'label'     => esc_html__( 'Featured Properties', 'houzez-theme-functionality' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                "description" => esc_html__("You can make a property featured by clicking featured properties checkbox while add/edit property", "houzez-theme-functionality"),
                'label_on'     => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off'    => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );

        $this->add_responsive_control(
            'map_height',
            [
                'label'           => esc_html__( 'Map Height (px)', 'houzez-theme-functionality' ),
                'type'            => \Elementor\Controls_Manager::SLIDER,
                'range'           => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'devices'         => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 600,
                    'unit' => 'px',
                ],
                'tablet_default'  => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default'  => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'default' => [
                    'size' => 600,
                    'unit' => 'px',
                ],
                'selectors'       => [
                    '{{WRAPPER}} #houzez-properties-map' => 'height: {{SIZE}}{{UNIT}};',

                ],
            ]
        );

        
        $this->end_controls_section();
    }

    public function render_map_buttons() {
       ?>
       <div class="map-arrows-actions">
            <button id="listing-mapzoomin" class="map-btn"><i class="houzez-icon icon-add"></i></button>
            <button id="listing-mapzoomout" class="map-btn"><i class="houzez-icon icon-subtract"></i></button>
        </div><!-- map-arrows-actions -->
        <div class="map-next-prev-actions">
            
            <button id="houzez-gmap-prev" class="map-btn"><i class="houzez-icon icon-arrow-left-1 me-1"></i> <span><?php esc_html_e('Prev', 'houzez'); ?></span></button>
            <button id="houzez-gmap-next" class="map-btn"><span><?php esc_html_e('Next', 'houzez'); ?></span> <i class="houzez-icon icon-arrow-right-1 ms-1"></i></button>
        </div><!-- map-next-prev-actions -->
       <?php
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 4.0.0
     * @access protected
     */
    protected function render() {       
        $settings = $this->get_settings_for_display();

        if ( get_query_var( 'paged' ) ) {
            $paged = get_query_var( 'paged' );
        } elseif ( get_query_var( 'page' ) ) { // if is static front page
            $paged = get_query_var( 'page' );
        } else {
            $paged = 1;
        }

        if ( $settings['offset'] ) {
            $offset = $settings['offset'] + ( $paged - 1 ) * $settings['posts_limit'];
        } else {
            $offset = '';
        }
        $wp_query_args = array(
            'post_type'      => 'property',
            'posts_per_page' => $settings['posts_limit'],
            'offset'         => $offset,
            'post_status'    => 'publish'
        );

        $taxonomies = get_object_taxonomies( 'property', 'objects' );
        if ( ! empty( $taxonomies ) && ! is_wp_error( $taxonomies ) ) {
            foreach ( $taxonomies as $single_tax ) {
                $setting_key = $single_tax->name;
                if ( ! empty( $settings[ $setting_key ] ) ) {
                    $wp_query_args['tax_query'][] = [
                        'taxonomy' => $setting_key,
                        'field'    => 'slug',
                        'terms'    => $settings[ $setting_key ],
                    ];
                }
            }

            if ( isset( $wp_query_args['tax_query'] ) && count( $wp_query_args['tax_query'] ) > 1 ) {
                $wp_query_args['tax_query']['relation'] = 'AND';
            }
        }

        if (!empty($settings['featured_prop'])) {
                
            if( $settings['featured_prop'] == "yes" ) {
                $wp_query_args['meta_key'] = 'fave_featured';
                $wp_query_args['meta_value'] = '1';
            }
        }

        $wp_query_args['paged']                    = 1;
        $wp_query_args['fields']                   = 'ids';
        $wp_query_args['no_found_rows']            = true;
        $wp_query_args['update_post_meta_cache']   = false;
        $wp_query_args['update_post_term_cache']   = false;

        $map_options = array();
        $properties_data = array();
        $prop_map_query = new WP_Query( $wp_query_args );
        
        foreach ( $prop_map_query->posts as $post_id ) {
            if ( $data = houzez_get_property_map_data( $post_id ) ) {
                $properties_data[] = $data;
            }
        }
        wp_reset_postdata();

        $map_data = [ 'properties' => $properties_data ];
        $map_data = esc_attr( wp_json_encode( $map_data ) );


        $map_options = houzez_get_map_options();

        $map_options['markerPricePins'] = houzez_option('markerPricePins');

        if($settings['mapbox_style'] != 'global') { 
            $mapbox_style = $settings['mapbox_style'];
            $make_custom_style = $settings['mapbox_custom_style_url'];
            if($mapbox_style == 'custom' && !empty($make_custom_style)) {
                $map_options['mapbox_style'] = $make_custom_style;
            } else if($mapbox_style != 'custom' && !empty($mapbox_style)) {
                $map_options['mapbox_style'] = $mapbox_style;
            }

        }

        $map_options_json = esc_attr( wp_json_encode( $map_options ) );
        ?>

        <div class="houzez-elementor-map-wrap">
        
            <?php self::render_map_buttons(); ?>

            <div id="houzez-properties-map" class="h-properties-map-for-elementor" data-map='<?php echo $map_data; ?>' data-options='<?php echo $map_options_json; ?>'></div>
        </div>
        <?php
        
    } // End render

}
\Elementor\Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Properties_Mapbox() );