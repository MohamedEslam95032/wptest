<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Houzez_Filters_Traits {
    public function listings_cards_filters() {
        
        $page_filters = houzez_option('houzez_page_filters');
        $hide_filters = !empty($page_filters) ? $page_filters : array();

        $listing_types = array();
        $listing_status = array();
        $listing_labels = array();
        
        houzez_get_terms_array( 'property_status', $listing_status );
        houzez_get_terms_array( 'property_type', $listing_types );
        houzez_get_terms_array( 'property_label', $listing_labels );

        if( isset($hide_filters) && ! in_array('property_type', $hide_filters) ) {
            $this->add_control(
                'property_type',
                [
                    'label'    => esc_html__('Type', 'houzez-theme-functionality'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_types,
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_status', $hide_filters) ) {
            $this->add_control(
                'property_status',
                [
                    'label'    => esc_html__('Status', 'houzez-theme-functionality'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_status,
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_label', $hide_filters) ) {
            $this->add_control(
                'property_label',
                [
                    'label'    => esc_html__('Labels', 'houzez-theme-functionality'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_labels,
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_country', $hide_filters) ) {
            $this->add_control(
                'property_country',
                [
                    'label'         => esc_html__('Country', 'houzez-theme-functionality'),
                    'multiple'      => true,
                    'label_block'   => true,
                    'type'          => 'houzez_autocomplete',
                    'make_search'   => 'houzez_get_taxonomies',
                    'render_result' => 'houzez_render_taxonomies',
                    'taxonomy'      => array('property_country'),
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_state', $hide_filters) ) {
            $this->add_control(
                'property_state',
                [
                    'label'         => esc_html__('State', 'houzez-theme-functionality'),
                    'multiple'      => true,
                    'label_block'   => true,
                    'type'          => 'houzez_autocomplete',
                    'make_search'   => 'houzez_get_taxonomies',
                    'render_result' => 'houzez_render_taxonomies',
                    'taxonomy'      => array('property_state'),
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_city', $hide_filters) ) {
            $this->add_control(
                'property_city',
                [
                    'label'         => esc_html__('City', 'houzez-theme-functionality'),
                    'multiple'      => true,
                    'label_block'   => true,
                    'type'          => 'houzez_autocomplete',
                    'make_search'   => 'houzez_get_taxonomies',
                    'render_result' => 'houzez_render_taxonomies',
                    'taxonomy'      => array('property_city'),
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_area', $hide_filters) ) {
            $this->add_control(
                'property_area',
                [
                    'label'         => esc_html__('Area', 'houzez-theme-functionality'),
                    'multiple'      => true,
                    'label_block'   => true,
                    'type'          => 'houzez_autocomplete',
                    'make_search'   => 'houzez_get_taxonomies',
                    'render_result' => 'houzez_render_taxonomies',
                    'taxonomy'      => array('property_area'),
                ]
            );
        }
        

        $this->add_control(
            'properties_by_agents',
            [
                'label'    => esc_html__('Properties by Agents', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'options'  => array_slice( houzez_get_agents_array(), 1, null, true ),
            ]
        );

        $this->add_control(
            'properties_by_agencies',
            [
                'label'    => esc_html__('Properties by Agencies', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'options'  => array_slice( houzez_get_agency_array(), 1, null, true ),
            ]
        );

        $this->add_control(
            'min_price',
            [
                'label'    => esc_html__('Minimum Price', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::NUMBER,
                'label_block' => false,
            ]
        );
        $this->add_control(
            'max_price',
            [
                'label'    => esc_html__('Maximum Price', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::NUMBER,
                'label_block' => false,
            ]
        );

        $this->add_control(
            'min_beds',
            [
                'label'    => esc_html__('Minimum Beds', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::NUMBER,
                'label_block' => false,
            ]
        );
        $this->add_control(
            'max_beds',
            [
                'label'    => esc_html__('Maximum Beds', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::NUMBER,
                'label_block' => false,
            ]
        );

        $this->add_control(
            'min_baths',
            [
                'label'    => esc_html__('Minimum Baths', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::NUMBER,
                'label_block' => false,
            ]
        );
        $this->add_control(
            'max_baths',
            [
                'label'    => esc_html__('Maximum Baths', 'houzez-theme-functionality'),
                'type'     => Controls_Manager::NUMBER,
                'label_block' => false,
            ]
        );
        

        $this->add_control(
            'houzez_user_role',
            [
                'label'     => esc_html__( 'User Role', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    ''  => esc_html__( 'All', 'houzez-theme-functionality'),
                    'houzez_owner'    => 'Owner',
                    'houzez_manager'  => 'Manager',
                    'houzez_agent'  => 'Agent',
                    'author'  => 'Author',
                    'houzez_agency'  => 'Agency',
                ],
                'description' => '',
                'default' => '',
            ]
        );

        $this->add_control(
            'featured_prop',
            [
                'label'     => esc_html__( 'Featured Properties', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    ''  => esc_html__( '- Any -', 'houzez-theme-functionality'),
                    'no'    => esc_html__('Without Featured', 'houzez-theme-functionality'),
                    'yes'  => esc_html__('Only Featured', 'houzez-theme-functionality')
                ],
                "description" => esc_html__("You can make a post featured by clicking featured properties checkbox while add/edit post", "houzez-theme-functionality"),
                'default' => '',
            ]
        );

        $this->add_control(
            'property_ids',
            [
                'label'     => esc_html__( 'Properties IDs', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::TEXT,
                'description'   => esc_html__( 'Enter properties ids comma separated. Ex 12,305,34', 'houzez-theme-functionality' ),
            ]
        );

    }

    public function listings_cards_thumb_size_control() {
        $this->add_control(
            'listing_thumb_size',
            [
                'label' => esc_html__( 'Thumbnail Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => \Houzez_Image_Sizes::get_enabled_image_sizes_for_elementor(),
                'default' => 'global',
            ]
        );
    }

    public function listings_cards_general_filters() {
        
        $this->add_control(
            'sort_by',
            [
                'label'     => esc_html__( 'Sort By', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => houzez_sorting_array(),
                'description' => '',
                'default' => '',
            ]
        );

        $this->add_control(
            'posts_limit',
            [
                'label'     => esc_html__('Number of properties', 'houzez-theme-functionality'),
                'type'      => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 500,
                'step'    => 1,
                'default' => 9,
            ]
        );

        $this->add_control(
            'offset',
            [
                'label'     => 'Offset',
                'type'      => Controls_Manager::TEXT,
                'description' => '',
            ]
        );

        $this->add_control(
            'post_status',
            [
                'label'     => esc_html__( 'Post Status', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => houzez_ele_property_status(),
                'description' => '',
                'default' => 'all',
            ]
        );
    }

    public function listing_taxonomies_controls() {
        
        $page_filters = houzez_option('houzez_page_filters');
        $hide_filters = !empty($page_filters) ? $page_filters : array();

        $listing_types = array();
        $listing_status = array();
        $listing_labels = array();
        
        houzez_get_terms_array( 'property_status', $listing_status );
        houzez_get_terms_array( 'property_type', $listing_types );
        houzez_get_terms_array( 'property_label', $listing_labels );

        if( isset($hide_filters) && ! in_array('property_type', $hide_filters) ) {
            $this->add_control(
                'property_type',
                [
                    'label'    => esc_html__('Type', 'houzez-theme-functionality'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_types,
                    'condition' => [
                        'houzez_cards_from' => 'property_type',
                    ],
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_status', $hide_filters) ) {
            $this->add_control(
                'property_status',
                [
                    'label'    => esc_html__('Status', 'houzez-theme-functionality'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_status,
                    'condition' => [
                        'houzez_cards_from' => 'property_status',
                    ],
                ]
            );
        }

        if( isset($hide_filters) && ! in_array('property_label', $hide_filters) ) {
            $this->add_control(
                'property_label',
                [
                    'label'    => esc_html__('Labels', 'houzez-theme-functionality'),
                    'type'     => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options'  => $listing_labels,
                    'condition' => [
                        'houzez_cards_from' => 'property_label',
                    ],
                ]
            );
        }

        $this->add_control(
            'property_country',
            [
                'label'         => esc_html__('Country', 'houzez-theme-functionality'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_country'),
                'condition' => [
                    'houzez_cards_from' => 'property_country',
                ],
            ]
        );

        $this->add_control(
            'property_state',
            [
                'label'         => esc_html__('State', 'houzez-theme-functionality'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_state'),
                'condition' => [
                    'houzez_cards_from' => 'property_state',
                ],
            ]
        );

        $this->add_control(
            'property_city',
            [
                'label'         => esc_html__('City', 'houzez-theme-functionality'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_city'),
                'condition' => [
                    'houzez_cards_from' => 'property_city',
                ],
            ]
        );

        $this->add_control(
            'property_area',
            [
                'label'         => esc_html__('Area', 'houzez-theme-functionality'),
                'multiple'      => true,
                'label_block'   => true,
                'type'          => 'houzez_autocomplete',
                'make_search'   => 'houzez_get_taxonomies',
                'render_result' => 'houzez_render_taxonomies',
                'taxonomy'      => array('property_area'),
                'condition' => [
                    'houzez_cards_from' => 'property_area',
                ],
            ]
        );

        $this->add_control(
            'houzez_show_child',
            [
                'label'     => esc_html__( 'Show Child', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '0'  => esc_html__( 'No', 'houzez-theme-functionality'),
                    '1'    => esc_html__( 'Yes', 'houzez-theme-functionality')
                ],
                'description' => '',
                'default' => '0',
            ]
        );

        $this->add_control(
            'houzez_hide_empty',
            [
                'label'     => esc_html__( 'Hide Empty', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '0'  => esc_html__( 'No', 'houzez-theme-functionality'),
                    '1'    => esc_html__( 'Yes', 'houzez-theme-functionality')
                ],
                'description' => '',
                'default' => '1',
            ]
        );

        $this->add_control(
            'houzez_hide_count',
            [
                'label'     => esc_html__( 'Hide Count', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    '0'  => esc_html__( 'No', 'houzez-theme-functionality'),
                    '1'    => esc_html__( 'Yes', 'houzez-theme-functionality')
                ],
                'description' => '',
                'default' => '0',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'     => esc_html__( 'Order By', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'name'  => esc_html__( 'Name', 'houzez-theme-functionality'),
                    'count'    => esc_html__( 'Count', 'houzez-theme-functionality'),
                    'id'    => esc_html__( 'ID', 'houzez-theme-functionality')
                ],
                'description' => '',
                'default' => 'name',
            ]
        );

        $this->add_control(
            'order',
            [
                'label'     => esc_html__( 'Order', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'ASC'  => esc_html__( 'ASC', 'houzez-theme-functionality'),
                    'DESC'  => esc_html__( 'DESC', 'houzez-theme-functionality')
                ],
                'default' => 'ASC',
            ]
        );


        $this->add_control(
            'no_of_terms',
            [
                'label'     => esc_html__('Number of Items to Show', 'houzez-theme-functionality'),
                'type'      => Controls_Manager::TEXT,
                'description' => '',
                'default' => '',
            ]
        );

    }

    public function listings_cards_args($settings) {
        $property_type = $property_status = $property_label = $property_country = $property_state = $property_city = $property_area = $properties_by_agents = $properties_by_agencies = '';

        if(!empty($settings['property_type'])) {
            $property_type = implode (",", $settings['property_type']);
        }

        if(!empty($settings['property_status'])) {
            $property_status = implode (",", $settings['property_status']);
        }

        if(!empty($settings['property_label'])) {
            $property_label = implode (",", $settings['property_label']);
        }

        if(!empty($settings['property_country'])) {
            $property_country = implode (",", $settings['property_country']);
        }

        if(!empty($settings['property_state'])) {
            $property_state = implode (",", $settings['property_state']);
        }

        if(!empty($settings['property_city'])) {
            $property_city = implode (",", $settings['property_city']);
        }

        if(!empty($settings['property_area'])) {
            $property_area = implode (",", $settings['property_area']);
        }

        if( !empty($settings['properties_by_agents']) ) {
            $properties_by_agents = implode (",", $settings['properties_by_agents']);
        }

        if( !empty($settings['properties_by_agencies']) ) {
            $properties_by_agencies = implode (",", $settings['properties_by_agencies']);
        }

        $listing_thumb_size = $settings['listing_thumb_size'] ?? '';
        if ($listing_thumb_size === 'houzez-item-image-1' || $listing_thumb_size === 'global' ) {
            $listing_thumb_size = '';
        }

        $args = [
            'module_type'           => $settings['module_type'] ?? '',
            'houzez_user_role'      => $settings['houzez_user_role'] ?? '',
            'featured_prop'         => $settings['featured_prop'] ?? '',
            'posts_limit'           => $settings['posts_limit'] ?? 10,  // Default value if needed
            'sort_by'               => $settings['sort_by'] ?? 'date',  // Default value if needed
            'offset'                => $settings['offset'] ?? 0,  // Default value if needed
            'pagination_type'       => $settings['pagination_type'] ?? 'none',  // Default value if needed
            'hide_button'           => isset($settings['hide_button']) && $settings['hide_button'] === 'none' ? false : true,
            'hide_author_date'      => isset($settings['hide_author_date']) && $settings['hide_author_date'] === 'none' ? false : true,
            'hide_author'           => isset($settings['hide_author']) && $settings['hide_author'] === 'none' ? false : true,
            'hide_date'             => isset($settings['hide_date']) && $settings['hide_date'] === 'none' ? false : true,
            'post_status'           => $settings['post_status'] ?? 'publish',  // Default value if needed
            'property_type'         => $property_type ?? '',
            'property_status'       => $property_status ?? '',
            'property_label'        => $property_label ?? '',
            'property_country'      => $property_country ?? '',
            'property_state'        => $property_state ?? '',
            'property_city'         => $property_city ?? '',
            'property_area'         => $property_area ?? '',
            'thumb_size'            => $listing_thumb_size,
            'properties_by_agents'  => $properties_by_agents ?? '',
            'properties_by_agencies'=> $properties_by_agencies ?? '',
            'min_price'             => $settings['min_price'] ?? '',
            'max_price'             => $settings['max_price'] ?? '',
            'min_beds'              => $settings['min_beds'] ?? '',
            'max_beds'              => $settings['max_beds'] ?? '',
            'min_baths'             => $settings['min_baths'] ?? '',
            'max_baths'             => $settings['max_baths'] ?? '',
            'property_ids'          => $settings['property_ids'] ?? [],
            //carousel settings
            'all_btn'               => $settings['all_btn'] ?? '',
            'all_url'               => $settings['all_url'] ?? '',
            'slides_to_show'        => $settings['slides_to_show'] ?? '',
            'slides_to_scroll'      => $settings['slides_to_scroll'] ?? '',
            'slide_infinite'        => $settings['slide_infinite'] ?? '',
            'slide_auto'            => $settings['slide_auto'] ?? '',
            'auto_speed'            => $settings['auto_speed'] ?? '',
            'navigation'            => $settings['navigation'] ?? '',
            'nav_position'          => $settings['nav_position'] ?? '',
            'slide_dots'            => $settings['slide_dots'] ?? '',
        ];

        return $args;
    }

}
