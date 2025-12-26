<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Section Title Widget.
 * @since 2.0
 */
class Houzez_Elementor_Grid_Builder extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 2.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Houzez_elementor_grid_builder';
    }

    /**
     * Get widget title.
     * @since 2.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Grids Builder', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 2.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-inner-section';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 2.0
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
     * @since 2.0
     * @access protected
     */
    protected function register_controls() {

    
        $this->start_controls_section(
            'houzez_grid_builder_content',
            [
                'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'grid_data',
            [
                'label' => __( 'Data', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                
                'options' => [
                    'custom'  => __( 'Custom', 'houzez-theme-functionality' ),
                    'dynamic' => __( 'Dynamic', 'houzez-theme-functionality' ),
                ],
                'default' => 'custom',
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => __( 'Choose Image', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'grid_data' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'grid_title',
            [
                'label' => esc_html__( 'Title', 'houzez-theme-functionality' ),
                'type'  => Controls_Manager::TEXT,
                'condition' => [
                    'grid_data' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'grid_subtitle',
            [
                'label' => esc_html__( 'Subtitle', 'houzez-theme-functionality' ),
                'type'  => Controls_Manager::TEXT,
                'condition' => [
                    'grid_data' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'more_text',
            [
                'label' => esc_html__( 'More Details Text', 'houzez-theme-functionality' ),
                'type'  => Controls_Manager::TEXT,
                'condition' => [
                    'grid_data' => 'custom',
                ],
                'default' => 'More Details'
            ]
        );

        $this->add_control(
            'more_link',
            [
                'label' => esc_html__( 'Link', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::URL,
                'placeholder' => 'https://your-link.com',
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'grid_data' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'grid_taxonomy',
            [
                'label'     => esc_html__( 'Choose Taxonomy', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'property_type' => 'Type',
                    'property_country' => 'Country',
                    'property_state' => 'State',
                    'property_city' => 'City',
                    'property_area' => 'Area',
                    'property_label' => 'Label',
                ],
                'description' => '',
                'default' => 'property_type',
                'condition' => [
                    'grid_data' => 'dynamic',
                ],
            ]
        );

        // Property taxonomies controls
        $prop_taxonomies = get_object_taxonomies( 'property', 'objects' );
        $page_filters = houzez_option('houzez_page_filters');

        if( isset($page_filters) && !empty($page_filters) ) {
            foreach ($page_filters as $filter) {
                unset( $prop_taxonomies[$filter] );
            }
        }

        unset( $prop_taxonomies['property_status'] );
        unset( $prop_taxonomies['property_feature'] );
        unset( $prop_taxonomies['property_country'] );
        unset( $prop_taxonomies['property_state'] );
        unset( $prop_taxonomies['property_city'] );
        unset( $prop_taxonomies['property_area'] );

        if ( ! empty( $prop_taxonomies ) && ! is_wp_error( $prop_taxonomies ) ) {
            foreach ( $prop_taxonomies as $single_tax ) {

                $options_array = array();
                
                $terms = get_terms( 
                    array(
                        'taxonomy' => $single_tax->name,
                        'hide_empty' => true
                )   );

                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $options_array[ $term->slug ] = $term->name;
                    }
                }

                $tax_field_name = '';
                $single_taxonomy = $single_tax->name;

                if( $single_taxonomy == 'property_type' ) {
                    $tax_field_name = 'tax_type';
                } else if( $single_taxonomy == 'property_city' ) {
                    $tax_field_name = 'tax_city';
                } else if( $single_taxonomy == 'property_country' ) {
                    $tax_field_name = 'tax_country';
                } else if( $single_taxonomy == 'property_area' ) {
                    $tax_field_name = 'tax_area';
                } else if( $single_taxonomy == 'property_state' ) {
                    $tax_field_name = 'tax_state';
                } else if( $single_taxonomy == 'property_label' ) {
                    $tax_field_name = 'tax_label';
                }

                $this->add_control(
                    $tax_field_name,
                    [
                        'label'    => $single_tax->label,
                        'type'     => Controls_Manager::SELECT2,
                        'multiple' => false,
                        'label_block' => false,
                        'options'  => $options_array,
                        'condition' => [
                            'grid_taxonomy' => $single_tax->name,
                            'grid_data' => 'dynamic',
                        ],
                    ]
                );
            }
        }

        $this->add_control(
            'tax_country',
            [
                'label'         => esc_html__('Country', 'houzez'),
                'multiple'      => false,
                'label_block'   => false,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_country'),
                'condition' => [
                    'grid_taxonomy' => 'property_country',
                    'grid_data' => 'dynamic',
                ],
            ]
        );

        $this->add_control(
            'tax_state',
            [
                'label'         => esc_html__('State', 'houzez'),
                'multiple'      => false,
                'label_block'   => false,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_state'),
                'condition' => [
                    'grid_taxonomy' => 'property_state',
                    'grid_data' => 'dynamic',
                ],
            ]
        );

        $this->add_control(
            'tax_city',
            [
                'label'         => esc_html__('City', 'houzez'),
                'multiple'      => false,
                'label_block'   => false,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_city'),
                'condition' => [
                    'grid_taxonomy' => 'property_city',
                    'grid_data' => 'dynamic',
                ],
            ]
        );

        $this->add_control(
            'tax_area',
            [
                'label'         => esc_html__('Area', 'houzez'),
                'multiple'      => false,
                'label_block'   => false,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_area'),
                'condition' => [
                    'grid_taxonomy' => 'property_area',
                    'grid_data' => 'dynamic',
                ],
            ]
        );


        $this->add_control(
            'more_text_d',
            [
                'label' => esc_html__( 'More Details Text', 'houzez-theme-functionality' ),
                'type'  => Controls_Manager::TEXT,
                'condition' => [
                    'grid_data' => 'dynamic',
                ],
                'default' => 'More Details'
            ]
        );

        $this->add_control(
            'listing_count',
            [
                'label' => esc_html__( 'Listing Count', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'grid_data' => 'dynamic',
                ],
            ]
        );

        $this->add_control(
            'properties_text',
            [
                'label' => esc_html__( 'Properties Text', 'houzez-theme-functionality' ),
                'type'  => Controls_Manager::TEXT,
                'condition' => [
                    'listing_count' => 'yes',
                ],
                'default' => 'Properties'
            ]
        );
        $this->add_control(
            'property_text',
            [
                'label' => esc_html__( 'Property Text', 'houzez-theme-functionality' ),
                'type'  => Controls_Manager::TEXT,
                'condition' => [
                    'listing_count' => 'yes',
                ],
                'default' => 'Property'
            ]
        );

        $this->add_control(
            'tax_image_type',
            [
                'label' => esc_html__( 'Image', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Dynamic', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'Custom', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'grid_data' => 'dynamic',
                ],
            ]
        );

        $this->add_control(
            'grid_image_size',
            [
                'label' => esc_html__( 'Image Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => \Houzez_Image_Sizes::get_enabled_image_sizes_for_elementor(),
                'default' => 'full',
                'condition' => [
                    'grid_data' => 'dynamic',
                    'tax_image_type' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'tax_custom_image',
            [
                'label' => __( 'Choose Image', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'tax_image_type' => '',
                    'grid_data' => 'dynamic',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'homey_section_titles_spacings',
            [
                'label' => esc_html__('Grid Styling', 'houzez-theme-functionality'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'grid_type',
            [
                'label' => esc_html__( 'Grid Type', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'grid-item-v1' => esc_html__('Landscape', 'houzez-theme-functionality'),
                    'grid-item-v2' => esc_html__('Square', 'houzez-theme-functionality'),
                    'grid-item-v3' => esc_html__('Portrait', 'houzez-theme-functionality'),
                    'grid-item-v4' => esc_html__('Custom', 'houzez-theme-functionality'),
                ],
                'default' => 'grid-item-v1'
            ]
        );

        $this->add_responsive_control(
            'grid_height',
            [
                'label' => esc_html__( 'Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'condition' => [
                    'grid_type' => 'grid-item-v4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .grid-item:not([data-ratio])' => 'padding-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .grid-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding_bottom',
            [
                'label' => esc_html__( 'Padding Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .grid-item' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_padding',
            [
                'label' => esc_html__( 'Text Padding(px)', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'selectors' => [
                    '{{WRAPPER}} .grid-item-text-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'radius',
            [
                'label' => __( 'Radius', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .grid-item, {{WRAPPER}} .hover-effect-flat:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'grid_title_typography',
                'label'    => esc_html__( 'Title Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .grid-item-title',
                'fields_options' => [
                    // Inner control name
                    'font_weight' => [
                        // Inner control settings
                        'default' => '300',
                    ],
                    'font_family' => [
                        'default' => 'Roboto',
                    ],
                    'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'grid_subtitle_typography',
                'label'    => esc_html__( 'Subtitle Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .grid-item-subtitle',
                'fields_options' => [
                    // Inner control name
                    'font_weight' => [
                        // Inner control settings
                        'default' => '300',
                    ],
                    'font_family' => [
                        'default' => 'Roboto',
                    ],
                    'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 12 ] ],
                ],
            ]
        );

        $this->add_control(
            'grid_title_color',
            [
                'label'     => esc_html__( 'Title Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .grid-item-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'grid_subtitle_color',
            [
                'label'     => esc_html__( 'Subtitle Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .grid-item-subtitle' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();


    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 2.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();
        $grid_data = $settings['grid_data'];
        $bg_image = '';
        $target = '';

        if($grid_data == 'dynamic') {
            $grid_taxonomy = $settings['grid_taxonomy'];
            $tax_image_type = $settings['tax_image_type'];
            $tax_custom_image = $settings['tax_custom_image'];
            $listing_count = $settings['listing_count'];
            
            $slug = $subtitle = $image = $more_link = '';

            if( $grid_taxonomy == 'property_city' ) {
                $slug = $settings['tax_city'];

            } else if ( $grid_taxonomy == 'property_area' ) {
                $slug = $settings['tax_area'];

            } else if ( $grid_taxonomy == 'property_country' ) {
                $slug = $settings['tax_country'];

            } else if ( $grid_taxonomy == 'property_state' ) {
                $slug = $settings['tax_state'];

            } else if ( $grid_taxonomy == 'property_label' ) {
                $slug = $settings['tax_label'];

            } else {
                $slug = $settings['tax_type'];
            }

            $term = get_term_by('slug', $slug, $grid_taxonomy);

            if( $term ) {
                $title = $term->name;

                if( $listing_count == 'yes') {
                    $count = $term->count;

                    $properties_text = $settings['properties_text'];
                    $property_text = $settings['property_text'];

                    if($count > 1) {
                        $subtitle = $count.' '.$properties_text;
                    } elseif( $count <= 1 ) {
                        $subtitle = $count.' '.$property_text;
                    } 
                }
                $more_link = get_term_link($term->term_id);
                $more_text = $settings['more_text_d'];

                if($tax_image_type == 'yes') {
                    $term_img_id = get_term_meta($term->term_id, 'fave_taxonomy_img', true);
                    $image_size = $settings['grid_image_size'] === 'global' ? 'full' : $settings['grid_image_size'];
                    $thumb_url_array = wp_get_attachment_image_src( $term_img_id, $image_size, true );
                    $image = $thumb_url_array[0];

                } else {
                    $image = $tax_custom_image['url'];
                }
            }

        } else {

            $title = $settings['grid_title'];
            $subtitle = $settings['grid_subtitle'];
            $image = $settings['image']['url'];
            $more_text = $settings['more_text'];
            $target = $settings['more_link']['is_external'] ? ' target="_blank"' : '';
            $more_link = $settings['more_link']['url'];
        }

        $lazyloadbg = '';
        if ( ! Plugin::$instance->editor->is_edit_mode() ) {
            $lazyloadbg = houzez_get_lazyload_for_bg();
        }

        $data_ratio = '';
        $grid_type = $settings['grid_type'];
        if($grid_type == 'grid-item-v1') {
            $data_ratio = 'data-ratio="landscape"';
        } else if($grid_type == 'grid-item-v2') {
            $data_ratio = 'data-ratio="square"';
        } else if($grid_type == 'grid-item-v3') {
            $data_ratio = 'data-ratio="vertical"';
        }

        ?>

        <div class="grid-item <?php echo $lazyloadbg; ?> <?php echo esc_attr($grid_type); ?>" <?php echo $data_ratio; ?> style="background-image: url(<?php echo esc_url($image); ?>)">
            <a class="grid-item-link hover-effect-flat" <?php echo $target; ?> href="<?php echo esc_url($more_link); ?>">
                <div class="grid-item-text-wrap d-flex flex-column justify-content-between w-100 h-100">
                    <div class="d-flex flex-column">
                        <?php if(!empty($subtitle)) { ?>
                        <div class="grid-item-subtitle mb-1"><?php echo esc_attr($subtitle); ?></div>
                        <?php } ?>

                        <?php if(!empty($title)) { ?>
                        <div class="grid-item-title"><?php echo esc_attr($title); ?></div>
                        <?php } ?>
                    </div>
                    
                    <?php if(!empty($more_text)) { ?>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="grid-item-text-link"><?php echo esc_attr($more_text); ?></div>
                        <div class="grid-item-icon">
                            <i class="houzez-icon icon-arrow-button-right-2"></i>
                        </div>
                    </div>
                    <?php } ?>
                </div><!-- taxonomy-text-wrap -->
            </a>
        </div>
        
        <?php
    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Grid_Builder );