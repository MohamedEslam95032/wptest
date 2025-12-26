<?php
global $houzez_opt_name;

Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__( 'Projects Layout', 'houzez' ),
    'id'     => 'projects-pages',
    'desc'   => esc_html__( 'Select projects pages layout', 'houzez' ),
    'icon'   => 'el-icon-th-list el-icon-small',
    'subsection' => false,
    'fields' => array(
        array(
            'id'       => 'projects_layout',
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
            'id'       => 'projects_content_position',
            'type'     => 'select',
            'title'    => __('Content Position', 'houzez'),
            'desc' => __('Select content position for projects pages. Content can be added in desciption field for each project', 'houzez'),
            'options'  => array(
                'above' => esc_html__('Above listings', 'houzez'),
                'bottom' => esc_html__('Below listings', 'houzez'),
            ),
            'default' => 'above'
        ),

        array(
            'id'       => 'projects_posts_layout',
            'type'     => 'select',
            'title'    => __('Listings Layout', 'houzez'),
            'desc' => __('Select the listings layout for the projects page.', 'houzez'),
            'options'  => array(
                'Listings Version 1' => array(
                    'list-v1' => 'List View',
                    'grid-view-v1' => 'Grid View',
                ),
                'Listings Version 2' => array(
                    'list-view-v2' => 'List View',
                    'grid-view-v2' => 'Grid View',
                ),
            ),
            'default' => 'grid-view-v1'
        ),

        array(
            'id'       => 'projects_default_order',
            'type'     => 'select',
            'title'    => __('Default Order', 'houzez'),
            'desc' => esc_html__('Select the projects page listings order.', 'houzez'),
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
            'id'       => 'projects_num_posts',
            'type'     => 'text',
            'title'    => esc_html__('Number of Listings to Show', 'houzez'),
            'subtitle' => '',
            'desc' => esc_html__('Enter the number of listings to display.', 'houzez'),
            'default'  => '9',
            'validate' => 'numeric',
        ),
    )
));