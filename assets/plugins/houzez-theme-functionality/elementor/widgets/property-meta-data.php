<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
class Houzez_Property_Meta_Data extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-meta';
	}

	public function get_title() {
		return __( 'Property Meta Data', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-listing eicon-post-excerpt';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

		return [ 'houzez-elements', 'houzez-single-property' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'listing meta', 'property meta', 'property' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$meta_fields = array();

        $meta_fields = [
            'property_rooms' => esc_html__( 'Rooms', 'houzez-theme-functionality' ),
            'property_bedrooms' => esc_html__( 'Bedrooms', 'houzez-theme-functionality' ),
            'property_bathrooms' => esc_html__( 'Bathrooms', 'houzez-theme-functionality' ),
            'property_size' => esc_html__( 'Area Size', 'houzez-theme-functionality' ),
            'property_land' => esc_html__( 'Land Area', 'houzez-theme-functionality' ),
            'property_garage' => esc_html__( 'Garages', 'houzez-theme-functionality' ),
            'property_year' => esc_html__( 'Built Year', 'houzez-theme-functionality' ),
            'property_price' => esc_html__( 'Price', 'houzez-theme-functionality' ),
            'property_sec_price' => esc_html__( 'Second Price', 'houzez-theme-functionality' ),
            'property_status' => esc_html__( 'Property Status', 'houzez-theme-functionality' ),
            'property_type' => esc_html__( 'Property Type', 'houzez-theme-functionality' ),
            'property_label' => esc_html__( 'Property Label', 'houzez-theme-functionality' ),
            'property_country' => esc_html__( 'Property Country', 'houzez-theme-functionality' ),
            'property_city' => esc_html__( 'Property City', 'houzez-theme-functionality' ),
            'property_state' => esc_html__( 'Property State', 'houzez-theme-functionality' ),
            'property_area' => esc_html__( 'Property Area', 'houzez-theme-functionality' ),
            'property_id' => esc_html__( 'Property ID', 'houzez-theme-functionality' ),
            
        ];

        $meta_fields = array_merge($meta_fields, houzez_get_custom_field_for_elementor());

        /**
         * field types.
         */
        $meta_fields = apply_filters( 'houzez/property_meta_data', $meta_fields );

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Property Meta', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'meta_layout',
            [
                'label' => esc_html__( 'Meta Layout', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'module-v1' => esc_html__( 'Layout v1', 'houzez-theme-functionality' ),
                    'module-v2' => esc_html__( 'Layout v2', 'houzez-theme-functionality' ),
                    'module-v3' => esc_html__( 'Layout v3', 'houzez-theme-functionality' ),
                    'module-v4' => esc_html__( 'Layout v4', 'houzez-theme-functionality' ),
                ],
                'default' => 'module-v1',
            ]
        );

        $this->add_control(
            'field_type',
            [
                'label' => esc_html__( 'Select Field', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => $meta_fields,
                'default' => 'property_bedrooms',
            ]
        );

        $this->add_control(
            'label_singular',
            [
                'label' => esc_html__( 'Label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => '!in',
                            'value' => [
                                'property_price',
                                'property_sec_price',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'label_plural',
            [
                'label' => esc_html__( 'Label Plural', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => '!in',
                            'value' => [
                                'property_size',
                                'property_year',
                                'property_land',
                                'property_id',
                                'property_status',
                                'property_type',
                                'property_price',
                                'property_sec_price',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'price_label',
            [
                'label' => esc_html__( 'Price Label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'field_type' => ['property_price', 'property_sec_price'],
                ],
            ]
        );

        $this->add_control(
            'show_price_prefix',
            [
                'label' => esc_html__( 'Show Prefix', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'field_type' => 'property_price',
                ],
            ]
        );

        $this->add_control(
            'show_price_postfix',
            [
                'label' => esc_html__( 'Show Postfix', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'field_type' => ['property_price', 'property_sec_price'],
                ],
            ]
        );

        $this->add_control(
            'icon_type',
            [
                'label' => esc_html__( 'Icons From', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'theme_options' => esc_html__( 'Theme Options ', 'houzez-theme-functionality' ),
                    'custom' => esc_html__('Custom Icon', 'houzez-theme-functionality'),
                    'none' => esc_html__('No Icon', 'houzez-theme-functionality'),
                ],
                'default' => 'theme_options',
                'condition' => [
                    'meta_layout!' => 'module-v3'
                ]
            ]
        );

        $this->add_control(
            'meta_icon',
            [
                'label' => esc_html__( 'upload Icon', 'text-domain' ),
                'type' => Controls_Manager::ICONS,
                'condition' => [
                    'icon_type' => 'custom',
                    'meta_layout!' => 'module-v3'
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'property_meta_style',
			[
				'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'meta_layout!' => 'module-v3'
                ]
			]
		);

        $this->add_responsive_control(
            'space_between',
            [
                'label' => esc_html__( 'Space Between', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .hs-meta-widget-module' => 'column-gap: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'meta_layout!' => 'module-v3'
                ]
            ]
        );

		$this->end_controls_section();

        $this->start_controls_section(
            'property_meta_icon_style',
            [
                'label' => esc_html__( 'Icon', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'meta_layout!' => 'module-v3'
                ]
            ]
        );
        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__( 'Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .hs-meta-widget-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .hs-meta-widget-icon svg' => 'fill: {{VALUE}};color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__( 'Size', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'default' => [
                    'size' => 14,
                ],
                'range' => [
                    'px' => [
                        'min' => 6,
                    ],
                    '%' => [
                        'min' => 6,
                    ],
                    'vw' => [
                        'min' => 6,
                    ],
                ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .hs-meta-widget-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .hs-meta-widget-icon svg' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'text_indent',
            [
                'label' => esc_html__( 'Gap', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .hs-meta-widget-icon' => is_rtl() ? 'padding-left: {{SIZE}}{{UNIT}};' : 'padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_mval_style',
            [
                'label' => esc_html__( 'Meta Value', 'elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'mval_padding',
            [
                'label' => esc_html__( 'Gap', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .hs-meta-widget-data' => 'padding-top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'meta_layout' => 'module-v3'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'mval_typography',
                'selector' => '{{WRAPPER}} .hs-meta-widget-data',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'mval_shadow',
                'selector' => '{{WRAPPER}} .hs-meta-widget-data',
            ]
        );

        
        $this->add_control(
            'mval_color',
            [
                'label' => esc_html__( 'Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .hs-meta-widget-data' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_label_style',
            [
                'label' => esc_html__( 'Label', 'elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_padding',
            [
                'label' => esc_html__( 'Gap', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .hs-meta-widget-title' => 'padding-top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'meta_layout' => 'module-v2'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .hs-meta-widget-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'label_shadow',
                'selector' => '{{WRAPPER}} .hs-meta-widget-title',
            ]
        );

        
        $this->add_control(
            'label_color',
            [
                'label' => esc_html__( 'Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .hs-meta-widget-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
		
	}

	protected function houzez_taxonomy_field($settings)
    {
        global $post;
        $output = "";

        $icon_type = $settings['icon_type'];
        $meta_field = $settings['field_type'];
        $label_plural = $settings['label_plural'];
        $label_singular = $settings['label_singular'];

        // Retrieve taxonomy data
        $field_data = houzez_taxonomy_simple_2($meta_field, $post->ID);
        $field_label = $label_singular;

        if (!empty($field_data)) {
            // Render icon if available
            $icon = houzez_render_icon($settings['meta_icon'], ['aria-hidden' => 'true']);

            if ($icon_type === 'custom' && !empty($icon)) {
                $output .= '<span class="hs-meta-widget-icon">'. $icon . '</span>';
            }

            if( !empty( $field_label ) ) {
	            $output .= '<span class="hs-meta-widget-title">' . esc_attr($field_label) . '</span>';
	        }

            if( !empty( $field_data ) ) {
	            $output .= '<span class="hs-meta-widget-data">' . esc_attr($field_data) . '</span>';
	        }
        }

        return $output;
    }

	protected function houzez_meta_field($settings)
	{
	    global $post;
	    $output = "";

	    $icon_type = $settings['icon_type'];
	    $meta_field = $settings['field_type'];
	    $label_plural = $settings['label_plural'];
	    $label_singular = $settings['label_singular'];

	    // Change true to false to get an array of values
	    $field_data = get_post_meta($post->ID, 'fave_' . $meta_field, false);

	    // Check if $field_data is an array and join the values with a comma
	    if (is_array($field_data)) {
	        $field_data = implode(', ', $field_data);
	    }

        $field_label = ($field_data > 1 && !empty($label_plural)) ? $label_plural : $label_singular;

	    // Specific field data handling
	    switch ($meta_field) {
	        case 'property_size':
	            $field_data = houzez_get_listing_area_size($post->ID);
	            $field_label = $field_label . ' ' . houzez_get_listing_size_unit($post->ID);
	            break;
	        case 'property_land':
	            $field_data = houzez_get_land_area_size($post->ID);
	            $field_label = $field_label . ' ' . houzez_get_land_size_unit($post->ID);
	            break;
            case 'property_price':
                $field_data = $this->houzez_meta_price($settings);
                $field_label = !empty($settings['price_label']) ? $settings['price_label'] : $label_singular;
                break;

            case 'property_sec_price':
                $field_data = $this->houzez_meta_price_second($settings);
                $field_label = !empty($settings['price_label']) ? $settings['price_label'] : $label_singular;
                break;
	    }

	    if (!empty($field_data)) {
	        $icon = houzez_render_icon($settings['meta_icon'], ['aria-hidden' => 'true']);

	        if ($icon_type == 'custom' && !empty($icon)) {
	            $output .= '<span class="hs-meta-widget-icon">'. $icon . '</span>';
	        } elseif ($icon_type == 'theme_options') {
	            $output .= $this->get_meta_field_icon($meta_field);
	        }

	        if (!empty($field_label)) {
	            $output .= '<span class="hs-meta-widget-title">' . esc_attr($field_label) . '</span>';
	        }

	        if (!empty($field_data)) {
	            $output .= '<span class="hs-meta-widget-data">' . esc_attr($field_data) . '</span>';
	        }
	    }

	    return $output;
	}


    private function get_meta_field_icon($meta_field)
    {

        $icon_output = '';
        $icons = [
            'property_bedrooms' => ['fa_bed', 'icon-hotel-double-bed-1', 'bed'],
            'property_rooms' => ['fa_room', 'icon-real-estate-dimensions-plan-1', 'room'],
            'property_bathrooms' => ['fa_bath', 'icon-bathroom-shower-1', 'bath'],
            'property_garage' => ['fa_garage', 'icon-car-1', 'garage'],
            'property_size' => ['fa_area-size', 'icon-real-estate-dimensions-plan-1', 'area-size'],
            'property_land' => ['fa_land-area', 'icon-real-estate-dimensions-map', 'land-area'],
            'property_year' => ['fa_year-built', 'icon-calendar-3', 'year-built'],
            'property_id' => ['fa_property-id', 'icon-tags', 'property-id']
        ];

        if ( empty($meta_field) ) {
            return '';
        }

        $icon_type = houzez_option('icons_type');

        switch ($icon_type) {
            case 'font-awesome':
                $icon_field = $icons[$meta_field][0] ?? 'fa_'.$meta_field;
                $icon_output .= $this->append_icon_output($icon_field, houzez_option($icon_field));
                break;

            case 'custom':
                $icon_field = $icons[$meta_field][2] ?? $meta_field;
                $custom_icon = houzez_option($icon_field);
                if (!empty($custom_icon['url'])) {
                    $alt = $custom_icon['title'] ?? '';
                    $icon_output .= '<span class="hs-meta-widget-icon"><img class="img-fluid" src="' . esc_url($custom_icon['url']) . '" width="16" height="16" alt="' . esc_attr($alt) . '"></span>';
                }
                break;

            default:
                $icon_field = $icons[$meta_field][1] ?? '';
                $icon_output .= $this->append_icon_output($icon_field, 'houzez-icon');
                break;
        }

        return $icon_output;
    }

    private function append_icon_output($icon_field, $icon_class) {
        return !empty($icon_class) ? '<span class="hs-meta-widget-icon"><i class="' . $icon_class . ' ' . $icon_field . '"></i></span>' : '';
    }

    protected function render() {
	    global $settings;
	    $this->single_property_preview_query(); // Only for preview

	    $settings = $this->get_settings_for_display();

	    $this->add_render_attribute(
	        'property-meta-wrap', 'class', [
	            'hs-meta-widget-module',
	            'hs-meta-widget-' . $settings['meta_layout']
	        ]
	    );

        $field_name = $settings['field_type'];
        $output = $this->get_field_output($field_name, $settings);
        
        if (!empty($output)) {
            echo '<div ' . $this->get_render_attribute_string('property-meta-wrap') . '>';
            echo $output;
            echo '</div>';
        }

	    $this->reset_preview_query(); // Only for preview
	}

	private function get_field_output($field_name, $settings) {
	    $taxonomy_fields = [
	        'property_type', 'property_status', 'property_label',
	        'property_city', 'property_country', 'property_state', 
	        'property_area'
	    ];

	    if (in_array($field_name, $taxonomy_fields)) {
	        return $this->houzez_taxonomy_field($settings);
	    } else {
	        return $this->houzez_meta_field($settings);
	    }
	}

    private function get_price_placeholder_value($listing_id) {
        $price_placeholder = get_post_meta($listing_id, 'fave_property_price_placeholder', true);

        if (!empty($price_placeholder)) {
            return $price_placeholder;
        }

        return '';
    }

    private function houzez_meta_price($settings = array()) {
        global $post;

        $listing_id = $post->ID;

        $output = '';
        $sale_price     = get_post_meta( $listing_id, 'fave_property_price', true );
        $second_price     = get_post_meta( $listing_id, 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( $listing_id, 'fave_property_price_postfix', true );
        $price_prefix  = get_post_meta( $listing_id, 'fave_property_price_prefix', true );
        $price_separator = houzez_option('currency_separator');

        $show_prefix = isset($settings['show_price_prefix']) && $settings['show_price_prefix'] === 'yes';
        $show_postfix = isset($settings['show_price_postfix']) && $settings['show_price_postfix'] === 'yes';

        if( houzez_is_price_placeholder($listing_id) ) {
            return $this->get_price_placeholder_value($listing_id);
        }

        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            return $sale_price;
        }

        if( !empty( $price_prefix ) && $show_prefix ) {
            $output .= $price_prefix . ' ';
        }

        $output .= houzez_get_property_price($sale_price, $listing_id);

        if (!empty( $price_postfix ) && $show_postfix && empty($second_price)) {
            $output .= $price_separator . $price_postfix;
        }

        return $output;
    }

    private function houzez_meta_price_second($settings = array()) {
        global $post;

        $listing_id = $post->ID;

        $output = '';
        $second_price     = get_post_meta( $listing_id, 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( $listing_id, 'fave_property_price_postfix', true );
        $price_separator = houzez_option('currency_separator');

        $show_postfix = isset($settings['show_price_postfix']) && $settings['show_price_postfix'] === 'yes';

        // Second price doesn't show placeholder - return empty if no second price
        if( empty($second_price) ) {
            return '';
        }

        $price_as_text = doubleval( $second_price );
        if( !$price_as_text ) {
            return $second_price;
        }

        // Second price only shows the price value and optional postfix (no prefix)
        $output .= houzez_get_property_price($second_price, $listing_id);

        if (!empty( $price_postfix ) && $show_postfix) {
            $output .= $price_separator . $price_postfix;
        }

        return $output;
    }

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Meta_Data );