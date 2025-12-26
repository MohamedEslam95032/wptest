<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Overview extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-overview';
	}

	public function get_title() {
		return __( 'Section Overview', 'houzez-theme-functionality' );
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
		return ['property', 'overview', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();


		$repeater = new Repeater();
		$field_types = array();

        $field_types = [
            'property_type' => esc_html__( 'Property Type', 'houzez-theme-functionality' ),
            'property_bedrooms' => esc_html__( 'Bedrooms', 'houzez-theme-functionality' ),
            'property_rooms' => esc_html__( 'Rooms', 'houzez-theme-functionality' ),
            'property_bathrooms' => esc_html__( 'Bathrooms', 'houzez-theme-functionality' ),
            'property_size' => esc_html__( 'Area Size', 'houzez-theme-functionality' ),
            'property_land' => esc_html__( 'Land Area', 'houzez-theme-functionality' ),
            'property_garage' => esc_html__( 'Garages', 'houzez-theme-functionality' ),
            'property_year' => esc_html__( 'Built Year', 'houzez-theme-functionality' ),
            'property_status' => esc_html__( 'Property Status', 'houzez-theme-functionality' ),
            'property_id' => esc_html__( 'Property ID', 'houzez-theme-functionality' ),
            
        ];

        $field_types = array_merge($field_types, houzez_get_custom_field_for_elementor());
        
        /**
         * field types.
         */
        $field_types = apply_filters( 'houzez/overview_data', $field_types );


        $repeater->add_control(
            'field_type',
            [
                'label' => esc_html__( 'Field', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => $field_types,
                'default' => 'text',
            ]
        );

        $repeater->add_control(
            'label_singular',
            [
                'label' => esc_html__( 'Label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
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
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
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
            ]
        );

        $repeater->add_control(
			'meta_icon',
			[
				'label' => esc_html__( 'upload Icon', 'text-domain' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
                	'icon_type' => 'custom'
                ],
			]
		);
        

		$this->start_controls_section(
            'overview_content',
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
            'data_type',
            [
                'label' => esc_html__( 'Data Type', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__( 'Pre Defined', 'houzez-theme-functionality' ),
                    'custom' => esc_html__('Custom', 'houzez-theme-functionality'),
                ],
                'default' => '',
            ]
        );

        $this->add_control(
            'overview_fields',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        '_id' => 'property_type',
                        'field_type' => 'property_type',
                        'label_singular' => esc_html__( 'Property Type', 'houzez-theme-functionality' ),
                        'label_plural' => '',
                        'icon_type' => 'none',
                    ],
                    [
                        '_id' => 'property_bedrooms',
                        'field_type' => 'property_bedrooms',
                        'label_singular' => esc_html__( 'Bedroom', 'houzez-theme-functionality' ),
                        'label_plural' => esc_html__( 'Bedrooms', 'houzez-theme-functionality' ),
                        'icon_type' => 'theme_options',
                    ],
                    [
                        '_id' => 'property_bathrooms',
                        'field_type' => 'property_bathrooms',
                        'label_singular' => esc_html__( 'Bathroom', 'houzez-theme-functionality' ),
                        'label_plural' => esc_html__( 'Bathrooms', 'houzez-theme-functionality' ),
                        'icon_type' => 'theme_options',
                    ],
                    [
                        '_id' => 'property_garage',
                        'field_type' => 'property_garage',
                        'label_singular' => esc_html__( 'Garage', 'houzez-theme-functionality' ),
                        'label_plural' => esc_html__( 'Garages', 'houzez-theme-functionality' ),
                        'icon_type' => 'theme_options',
                    ],
                    [
                        '_id' => 'property_size',
                        'field_type' => 'property_size',
                        'label_singular' => esc_html__( 'Area Size', 'houzez-theme-functionality' ),
                        'label_plural' => '',
                        'icon_type' => 'theme_options',
                    ],
                    [
                        '_id' => 'property_year',
                        'field_type' => 'property_year',
                        'label_singular' => esc_html__( 'Year Built', 'houzez-theme-functionality' ),
                        'label_plural' => '',
                        'icon_type' => 'theme_options',
                    ],
                    
                ],
                'title_field' => '{{{ label_singular }}}',
                'condition' => [
                	'data_type' => 'custom'
                ],
            ]
        );

        $this->add_control(
            'overview_cols',
            [
                'label' => esc_html__( 'Columns', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '6' => esc_html__( '6', 'houzez-theme-functionality' ),
                    '5' => esc_html__( '5', 'houzez-theme-functionality' ),
                    '4' => esc_html__( '4', 'houzez-theme-functionality' ),
                    '3' => esc_html__( '3', 'houzez-theme-functionality' ),
                    '2' => esc_html__( '2', 'houzez-theme-functionality' ),
                ],
                'default' => 6,
                'condition' => [
                	'data_type' => 'custom'
                ],
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
            'overview_style',
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
            'show_separator',
            [
                'label' => esc_html__( 'Separator', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'border_type',
            [
                'label' => esc_html__( 'Border Type', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__( 'Default', 'houzez-theme-functionality' ),
                    'none' => esc_html__( 'None', 'houzez-theme-functionality' ),
                    'solid' => esc_html__( 'Solid', 'houzez-theme-functionality' ),
                    'double' => esc_html__('Double', 'houzez-theme-functionality'),
                    'dotted' => esc_html__('Dotted', 'houzez-theme-functionality'),
                    'dashed' => esc_html__('Dashed', 'houzez-theme-functionality'),
                    'groove' => esc_html__('Groove', 'houzez-theme-functionality'),
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-section-wrap.with-separator .block-wrap ul' => 'border-right-style: {{VALUE}}',
                    '{{WRAPPER}} .property-section-wrap.with-separator .block-wrap ul:first-of-type' => 'border-left-style: {{VALUE}}',
                ],
                'condition' => [
                    'show_separator' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'border_width',
            [
                'label' => esc_html__( 'Border Width', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-section-wrap.with-separator .block-wrap ul' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'show_separator' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'text_align',
            [
                'label' => esc_html__( 'Text Align', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__( 'Default', 'houzez-theme-functionality' ),
                    'center' => esc_html__('Center', 'houzez-theme-functionality'),
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .property-overview-data' => 'text-align: {{VALUE}}',
                ],
                'condition' => [
                    'show_separator' => 'yes'
                ]
            ]
        );

		$this->add_control(
			'heading_section_title',
			[
				'label' => esc_html__( 'Section Title', 'houzez-theme-functionality' ),
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

        $this->add_control(
			'heading_labels',
			[
				'label' => esc_html__( 'Meta Labels', 'houzez-theme-functionality' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
            'meta_label_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-overview-data li.hz-meta-label' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_label_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-overview-data li.hz-meta-label',
            ]
        );

        $this->add_control(
			'heading_values',
			[
				'label' => esc_html__( 'Meta Data', 'houzez-theme-functionality' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
            'meta_data_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-overview-item strong' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_data_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .property-overview-item strong',
            ]
        );

        $this->add_control(
			'heading_icons',
			[
				'label' => esc_html__( 'Meta Icons', 'houzez-theme-functionality' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
            'meta_icon_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .property-overview-item i, .property-overview-item svg' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_icon_size',
            [
                'label' => esc_html__( 'Icons Size(px)', 'houzez-theme-functionality' ),
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
                    '{{WRAPPER}} .property-overview-item i, .property-overview-item svg' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
      

		$this->end_controls_section();

	}

	protected function render() {
		
		$settings = $this->get_settings_for_display();

        $overview_cols = $settings['overview_cols'] ?? 6;

        $this->single_property_preview_query(); // Only for preview

		$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : houzez_option('sps_overview', 'Overview');

        $this->add_render_attribute(
            [
                'overview-wrap' => [
                    'class' => [
                        'property-section-wrap',
                    ],
                    'id' => [
                        'property-overview-wrap'
                    ]
                ],
            ]
        );

        if( $settings['show_separator'] != 'yes' ) {
            $this->add_render_attribute( 'overview-wrap', 'class', 'property-overview-wrap' );
        } else {
            $this->add_render_attribute( 'overview-wrap', 'class', 'with-separator' );
        }

		if( $settings['data_type'] != 'custom' ) {
			
			$prop_id 	= houzez_get_listing_data('property_id');
			?>
			<div <?php echo $this->get_render_attribute_string( 'overview-wrap' ); ?>>
				<div class="block-wrap">
					
					<?php if( $settings['section_header'] ) { ?>
					<div class="block-title-wrap d-flex justify-content-between align-items-center">
						<h2><?php echo esc_attr($section_title); ?></h2>

						<?php if( !empty( $prop_id ) ) { ?>
						<div><strong><?php echo houzez_option('spl_prop_id', 'Property ID'); ?>:</strong> <?php echo houzez_propperty_id_prefix($prop_id); ?></div>
						<?php } ?>
					</div><!-- block-title-wrap -->
					<?php } ?>

					<div class="property-overview-data" role="list">
						<?php get_template_part('property-details/partials/overview-data'); ?>
					</div><!-- d-flex -->
				</div><!-- block-wrap -->
			</div><!-- property-overview-wrap -->

		<?php
		} else { ?>

			<div class="property-overview-wrap property-section-wrap" id="property-overview-wrap">
				<div class="block-wrap">

					<?php if( $settings['section_header'] ) { ?>
					<div class="block-title-wrap d-flex justify-content-between align-items-center">
						<h2><?php echo esc_attr($section_title); ?></h2>
					</div><!-- block-title-wrap -->
					<?php } ?>

					<?php
                     $actual_items = 0;
                     $html_output = '';
					foreach ( $settings['overview_fields'] as $item_index => $item ) :
                        $data = '';
		                switch ( $item['field_type'] ) :
		                    
		                    case 'property_bedrooms':
		                    case 'property_bathrooms':
                            case 'property_rooms':
		                    case 'property_garage':
		                    case 'property_size':
		                    case 'property_land':
		                    case 'property_year':
		                    case 'property_id':
		                        $data = $this->houzez_meta_field( $item, $item_index );
                                $html_output .= $data;
                                if (!empty($data)) {
                                    $actual_items++;
                                }

		                    case 'property_type':
		                    case 'property_status':
		                        $data = $this->houzez_taxonomy_field( $item, $item_index );
                                    if (!empty($data)) {
                                        $actual_items++;    
                                    }
                                    $html_output .= $data;
                                break;
		                       
		                    default:
		                    	$data = $this->houzez_meta_field( $item, $item_index );
                                    if (!empty($data)) {
                                        $actual_items++;
                                    }
                                    $html_output .= $data;
                                    break;
		                         
		                endswitch;
		       
		            endforeach;

                    // Determine the actual number of columns to use
                    $cols_to_use = ($actual_items > 0 && $actual_items < $overview_cols) ? $actual_items : $overview_cols;
		            ?>
                    <div class="property-overview-data" role="list">
                        <div class="row row-cols-sm-2 row-cols-md-<?php echo esc_attr($cols_to_use); ?> g-4" role="list">
                            <?php echo $html_output; ?>
                        </div>
                    </div>

	            </div><!-- block-wrap -->
			</div><!-- property-overview-wrap -->
		<?php
		}
        
        $this->reset_preview_query(); // Only for preview
	}


	protected function houzez_meta_field($item, $i)
    {
        global $post;
        $output = "";

        $icon_type = $item['icon_type'];
        $meta_field = $item['field_type'];
        $label_plural = $item['label_plural'];
        $label_singular = $item['label_singular'];

        // Change true to false to get an array of values
        $field_data = get_post_meta($post->ID, 'fave_' . $meta_field, false);

        if (empty($field_data)) {
            return '';
        }

        // Check if $field_data is an array and join the values with a comma
        if (is_array($field_data)) {
            $field_data = implode(', ', $field_data);
        }

        $field_label = ($field_data > 1 && !empty($label_plural)) ? $label_plural : $label_singular;

        // Specific field data handling
        switch ($meta_field) {
            case 'property_size':
                $field_data = houzez_get_listing_area_size($post->ID);
                $field_label = houzez_get_listing_size_unit($post->ID);
                break;
            case 'property_land':
                $field_data = houzez_get_land_area_size($post->ID);
                $field_label = houzez_option('spl_lot', 'Lot') . ' ' . houzez_get_land_size_unit($post->ID);
                break;
        }

        if (!empty($field_data)) {
            $icon = self::houzez_render_icon($item['meta_icon'], ['aria-hidden' => 'true', 'class' => 'me-2']);

            $output .= '<div class="col" role="listitem">';
                $output .= '<ul class="list-unstyled mb-0">';
                    $output .= '<li class="property-overview-item d-flex align-items-center">';

                        if ($icon_type == 'custom' && !empty($icon)) {
                            $output .= $icon . ' ';
                        } elseif ($icon_type == 'theme_options') {
                            $output .= $this->get_meta_field_icon($meta_field);
                        }

                        $output .= '<strong>' . esc_attr($field_data) . '</strong>';
                    $output .= '</li>';
                    $output .= '<li class="hz-meta-label">' . esc_attr($field_label) . '</li>';
                $output .= '</ul>';
            $output .= '</div>';
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
                    $icon_output .= '<img class="img-fluid me-2" src="' . esc_url($custom_icon['url']) . '" width="16" height="16" alt="' . esc_attr($alt) . '"> ';
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
        return !empty($icon_field) ? '<i class="' . $icon_class . ' ' . $icon_field . ' me-2"></i>' : '';
    }


    protected function houzez_taxonomy_field($item, $i)
    {
        global $post;
        $output = "";

        $icon_type = $item['icon_type'];
        $meta_field = $item['field_type'];
        $label_plural = $item['label_plural'];
        $label_singular = $item['label_singular'];

        // Retrieve taxonomy data
        $field_data = houzez_taxonomy_simple_2($meta_field, $post->ID);
        if (empty($field_data)) {
            return '';
        }
        $field_label = $label_singular;

        if (!empty($field_data)) {
            // Render icon if available
            $icon = self::houzez_render_icon($item['meta_icon'], ['aria-hidden' => 'true', 'class' => 'me-1']);

            $output .= '<div class="col" role="listitem">';
                $output .= '<ul class="list-unstyled mb-0">';
                    $output .= '<li class="property-overview-item">';

                        if ($icon_type == 'custom' && !empty($icon)) {
                            $output .= $icon . ' ';
                        } 

                        $output .= '<strong>' . esc_attr($field_data) . '</strong>';
                    $output .= '</li>';
                    $output .= '<li class="hz-meta-label">' . esc_attr($field_label) . '</li>';
                $output .= '</ul>';
            $output .= '</div>';
        }

        return $output;
    }


	public static function houzez_render_icon( $icon, $attributes = [], $tag = 'i' ) {

		if ( empty( $icon['library'] ) ) {
			return false;
		}

		$output = '';
		// handler SVG Icon
		if ( 'svg' === $icon['library'] ) {
			$output = self::houzez_render_svg_icon( $icon['value'] );
		} else {
			$output = self::houzez_render_icon_html( $icon, $attributes, $tag );
		}

		return $output;
	}

	private static function houzez_render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
		$icon_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
		if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
			return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
		}

		if ( empty( $attributes['class'] ) ) {
			$attributes['class'] = $icon['value'];
		} else {
			if ( is_array( $attributes['class'] ) ) {
				$attributes['class'][] = $icon['value'];
			} else {
				$attributes['class'] .= ' ' . $icon['value'];
			}
		}
		return '<' . $tag . ' ' . Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
	}

	private static function houzez_render_svg_icon( $value ) {
		if ( ! isset( $value['id'] ) ) {
			return '';
		}

		return Svg_Handler::get_inline_svg( $value['id'] );
	}

}
Plugin::instance()->widgets_manager->register( new Property_Overview );