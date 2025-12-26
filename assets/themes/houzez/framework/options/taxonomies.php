<?php
global $houzez_opt_name;

Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__( 'Taxonomies Layout', 'houzez' ),
    'id'     => 'taxonomies-pages',
    'desc'   => esc_html__( 'Select taxonomies (type, status, country, city, state, features, areas, labels) pages layout', 'houzez' ),
    'icon'   => 'el-icon-th-list el-icon-small',
    'subsection' => false,
    'fields' => array(
        array(
            'id'       => 'taxonomy_layout',
            'type'     => 'image_select',
            'title'    => __('Page Layout', 'houzez'),
            'subtitle' => '',
            'options'  => array(
                'no-sidebar' => array(
                    'alt'   => '',
                    'img'   => HOUZEZ_IMAGE. '1c.png'
                ),
                'left-sidebar' => array(
                    'alt'   => '',
                    'img'   => HOUZEZ_IMAGE. '2cl.png'
                ),
                'right-sidebar' => array(
                    'alt'   => '',
                    'img'  => HOUZEZ_IMAGE. '2cr.png'
                )
            ),
            'default' => 'right-sidebar'
        ),
        array(
            'id'       => 'taxonomy_content_position',
            'type'     => 'select',
            'title'    => __('Content Position', 'houzez'),
            'desc' => __('Select content position for taxonomies pages. Content can be added in desciption field for each taxonomy', 'houzez'),
            'options'  => array(
                'above' => esc_html__('Above listings', 'houzez'),
                'bottom' => esc_html__('Below listings', 'houzez'),
            ),
            'default' => 'above'
        ),

        array(
            'id'       => 'taxonomy_posts_layout',
            'type'     => 'select',
            'title'    => __('Listings Layout', 'houzez'),
            'desc' => __('Select the listings layout for the taxonomy page. View switcher will be automatically enabled for v1, v2, and v5 layouts.', 'houzez'),
            'options'  => array(
                'Listings Version 1' => array(
                    'list-view-v1' => 'List View',
                    'grid-view-v1' => 'Grid View',
                ),
                'Listings Version 2' => array(
                    'list-view-v2' => 'List View',
                    'grid-view-v2' => 'Grid View',
                ),

                'Listings Version 3' => array(
                    'grid-view-v3' => 'Grid View',
                ),

                'Listings Version 4' => array(
                    'grid-view-v4' => 'Grid View',
                    'list-view-v4' => 'List View',
                ),

                'Listings Version 5' => array(
                    'grid-view-v5' => 'Grid View',
                ),

                'Listings Version 6' => array(
                    'grid-view-v6' => 'Grid View',
                ),

                'Listings Version 7' => array(
                    'list-view-v7' => 'List View',
                    'grid-view-v7' => 'Grid View',
                ),
            ),
            'default' => 'list-view-v1'
        ),

        array(
            'id'       => 'taxonomy_grid_columns',
            'type'     => 'select',
            'title'    => esc_html__('Grid Columns', 'houzez'),
            'desc'     => esc_html__('Select the number of columns to display in grid view when no sidebar is present. Note: 4-column grid is only applicable for Version 1 and Version 2 templates.', 'houzez'),
            'options'  => array(
                '2' => esc_html__('2 Columns', 'houzez'),
                '3' => esc_html__('3 Columns', 'houzez'),
                '4' => esc_html__('4 Columns (Only for Version 1 & 2)', 'houzez'),
            ),
            'default'  => '3',
            'required' => array('taxonomy_layout', '=', 'no-sidebar'),
        ),

        array(
            'id'       => 'taxonomy_default_order',
            'type'     => 'select',
            'title'    => __('Default Order', 'houzez'),
            'desc' => esc_html__('Select the taxonomy page listings order.', 'houzez'),
            'options'  => array(
                'a_title' => esc_html__( 'Title - ASC', 'houzez' ),
                'd_title' => esc_html__( 'Title - DESC', 'houzez' ),
                'd_date' => esc_html__( 'Date New to Old', 'houzez' ),
                'a_date' => esc_html__( 'Date Old to New', 'houzez' ),
                'd_price' => esc_html__( 'Price (High to Low)', 'houzez' ),
                'a_price' => esc_html__( 'Price (Low to High)', 'houzez' ),
                'featured_first' => esc_html__( 'Show Featured Listings on Top', 'houzez' ),
                'featured_first_random' => esc_html__( 'Show Featured Listings on Top - Randomly', 'houzez' ),
                'random' => esc_html__( 'Random Listings', 'houzez' ),
            ),
            'default' => 'd_date'
        ),

        array(
            'id'       => 'taxonomy_num_posts',
            'type'     => 'text',
            'title'    => esc_html__('Number of Listings to Show', 'houzez'),
            'subtitle' => '',
            'desc' => esc_html__('Enter the number of listings to display.', 'houzez'),
            'default'  => '9',
            'validate' => 'numeric',
        ),
    )
));