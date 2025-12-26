<?php
if( !function_exists('houzez_agency_metaboxes') ) {

    function houzez_agency_metaboxes( $meta_boxes ) {
        $houzez_prefix = 'fave_';
        
        $meta_boxes[] = array(
            'id'        => 'fave_agencies_template',
            'title'     => esc_html__('Agencies Options', 'houzez'),
            'post_types'     => array( 'page' ),
            'priority'   => 'high',
            'context' => 'normal',
            'show'       => array(
                'template' => array(
                    'template/template-agencies.php'
                ),
            ),

            'fields'    => array(
                array(
                    'name'      => esc_html__('Order By', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_orderby',
                    'type'      => 'select',
                    'options'   => array('none' => 'None', 'ID' => 'ID', 'title' => 'Title', 'date' => 'Date', 'rand' => 'Random', 'menu_order' => 'Menu Order' ),
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => false
                ),
                array(
                    'name'      => esc_html__('Order', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_order',
                    'type'      => 'select',
                    'options'   => array('ASC' => 'ASC', 'DESC' => 'DESC' ),
                    'desc'      => '',
                    'columns' => 6,
                    'multiple' => false
                ),
            )
        );

        $meta_boxes[] = array(
            'id'        => 'houzez_agencies',
            'title'     => esc_html__('Agency Information', 'houzez'),
            'post_types'     => array( 'houzez_agency' ),
            'context' => 'normal',
            'priority'   => 'high',

            'fields'    => array(
                array(
                    'name'      => esc_html__('Email', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_email',
                    'type'      => 'email',
                    'placeholder'      => esc_html__('Enter the email address','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_visible",
                    'name' => esc_html__( 'Visibility Hidden', 'houzez' ),
                    'desc' => esc_html__('Hide agency to show on front-end', 'houzez'),
                    'type' => 'checkbox',
                    'std' => "",
                    'columns'   => 3
                ),
                array(
                    'id' => "{$houzez_prefix}agency_verified",
                    'name' => esc_html__( 'Mark Agency as Verified', 'houzez' ),
                    'type' => 'switch',
                    'style' => 'rounded',
                    'on_label'  => 'Yes',
                    'off_label' => 'No',
                    'std' => "",
                    'columns'   => 3
                ),
                array(
                    'name'      => esc_html__('Service Areas', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_service_area',
                    'placeholder'      => esc_html__('Enter your service area', 'houzez'),
                    'type'      => 'text',
                    'desc'      => '',
                    'columns'   => 6
                ),
                array(
                    'name'      => esc_html__('Specialties', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_specialties',
                    'placeholder'      => esc_html__('Enter your speciaties', 'houzez'),
                    'type'      => 'text',
                    'desc'      => '',
                    'columns'   => 6
                ),
                array(
                    'name'      => esc_html__('Mobile Number', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_mobile',
                    'type'      => 'text',
                    'desc'      => '',
                    'placeholder'      => esc_html__('Enter the mobile number','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_whatsapp",
                    'name' => esc_html__("WhatsApp", 'houzez'),
                    'placeholder'      => esc_html__('Enter the WhatsApp number with country code', 'houzez'),
                    'type' => 'text',
                    'std' => "",
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_line_id",
                    'name' => esc_html__("LINE ID", 'houzez'),
                    'placeholder'      => esc_html__('Enter the line id', 'houzez'),
                    'type' => 'text',
                    'std' => "",
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_telegram",
                    'name' => esc_html__("Telegram Username", 'houzez'),
                    'placeholder'      => esc_html__('Enter your telegram username','houzez'),
                    'type' => 'text',
                    'std' => "",
                    'columns'   => 6
                ),
                array(
                    'name'      => esc_html__('Phone Number', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_phone',
                    'type'      => 'text',
                    'desc'      => '',
                    'placeholder'      => esc_html__('Enter the phone number','houzez'),
                    'columns'   => 6
                ),
                array(
                    'name'      => esc_html__('Fax Number', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_fax',
                    'type'      => 'text',
                    'desc'      => '',
                    'placeholder'      => esc_html__('Enter the fax number','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_language",
                    'name' => esc_html__( 'Language', 'houzez' ),
                    'placeholder'      => esc_html__('Enter the language you speak','houzez'),
                    'type' => 'text',
                    'std' => "",
                    'columns'   => 6
                ),
                array(
                    'name'      => esc_html__('License', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_licenses',
                    'type'      => 'text',
                    'desc'      => '',
                    'placeholder'      => esc_html__('Enter the license','houzez'),
                    'columns'   => 6
                ),
                array(
                    'name'      => esc_html__('Tax Number', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_tax_no',
                    'type'      => 'text',
                    'desc'      => '',
                    'placeholder'      => esc_html__('Enter the tax number','houzez'),
                    'columns'   => 6
                ),
                array(
                    'name'      => esc_html__('Website Url', 'houzez'),
                    'id'        => $houzez_prefix . 'agency_web',
                    'type'      => 'text',
                    'placeholder'      => esc_html__('Enter the website URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_address",
                    'name' => esc_html__('Address', 'houzez'),
                    'placeholder'      => esc_html__('Enter the full address','houzez'),
                    'type' => 'text',
                    'std' => "",
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_facebook",
                    'name' => "Facebook URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your Facebook profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_twitter",
                    'name' => "X URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your X profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_linkedin",
                    'name' => "Linkedin URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your Linkedin profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_googleplus",
                    'name' => "Google URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your Google profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_youtube",
                    'name' => "Youtube URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your Youtube profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_tiktok",
                    'name' => "Tiktok URL",
                    'placeholder'      => esc_html__('Enter your Tiktok profile URL','houzez'),
                    'type' => 'text',
                    'std' => "",
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_instagram",
                    'name' => "Instagram URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your instagram profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_pinterest",
                    'name' => "Pinterest URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your Pinterest profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_vimeo",
                    'name' => "Vimeo URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your Vimeo profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_zillow",
                    'name' => "Zillow URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your zillow profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_realtor_com",
                    'name' => "Realtor.com URL",
                    'type' => 'text',
                    'std' => "",
                    'placeholder'      => esc_html__('Enter your realtor.com profile URL','houzez'),
                    'columns'   => 6
                ),
                array(
                    'id' => "{$houzez_prefix}agency_shortcode",
                    'name' => "Shortcode",
                    'placeholder'      => esc_html__('Enter shortcode','houzez'),
                    'type' => 'text',
                    'std' => "",
                    'columns'   => 6
                ),

            )
        );

        return apply_filters('houzez_agency_meta', $meta_boxes);

    }

    add_filter( 'rwmb_meta_boxes', 'houzez_agency_metaboxes' );
}