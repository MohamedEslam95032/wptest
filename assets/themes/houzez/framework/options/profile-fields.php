<?php
global $houzez_opt_name, $allowed_html_array;

Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__( 'Profile Fields', 'houzez' ),
    'id'     => 'houzez-profile-fields',
    'desc'   => esc_html__( 'Manage the visibility of profile fields in the frontend dashboard', 'houzez' ),
    'icon'   => 'el-icon-user el-icon-small',
    'fields' => array(
        array(
            'id'       => 'profile_fields_section_info',
            'type'     => 'info',
            'title'    => esc_html__( 'Profile Fields Visibility', 'houzez' ),
            'subtitle' => esc_html__( 'Control which profile fields are visible in the frontend dashboard profile section. Disabled fields will be hidden from users.', 'houzez' ),
            'style'    => 'info',
        ),
        
        // Basic Information Fields
        array(
            'id'       => 'profile_fields_basic_section',
            'type'     => 'section',
            'title'    => esc_html__( 'Basic Information Fields', 'houzez' ),
            'subtitle' => esc_html__( 'Show or hide basic profile information fields', 'houzez' ),
            'indent'   => true,
        ),
        
        array(
            'id'       => 'profile_field_username',
            'type'     => 'switch',
            'title'    => esc_html__( 'Username', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide username field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_email',
            'type'     => 'switch',
            'title'    => esc_html__( 'Email', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide email field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_first_name',
            'type'     => 'switch',
            'title'    => esc_html__( 'First Name', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide first name field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_last_name',
            'type'     => 'switch',
            'title'    => esc_html__( 'Last Name', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide last name field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_display_name',
            'type'     => 'switch',
            'title'    => esc_html__( 'Public Display Name', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide public display name field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        // Professional Information Fields
        array(
            'id'       => 'profile_fields_professional_section',
            'type'     => 'section',
            'title'    => esc_html__( 'Professional Information Fields', 'houzez' ),
            'subtitle' => esc_html__( 'Show or hide professional profile fields', 'houzez' ),
            'indent'   => true,
        ),
        
        array(
            'id'       => 'profile_field_title',
            'type'     => 'switch',
            'title'    => esc_html__( 'Title/Position', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide title/position field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_license',
            'type'     => 'switch',
            'title'    => esc_html__( 'License', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide license field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_company',
            'type'     => 'switch',
            'title'    => esc_html__( 'Company Name', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide company name field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_tax_number',
            'type'     => 'switch',
            'title'    => esc_html__( 'Tax Number', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide tax number field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_language',
            'type'     => 'switch',
            'title'    => esc_html__( 'Language', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide language field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        // Contact Information Fields
        array(
            'id'       => 'profile_fields_contact_section',
            'type'     => 'section',
            'title'    => esc_html__( 'Contact Information Fields', 'houzez' ),
            'subtitle' => esc_html__( 'Show or hide contact information fields', 'houzez' ),
            'indent'   => true,
        ),
        
        array(
            'id'       => 'profile_field_mobile',
            'type'     => 'switch',
            'title'    => esc_html__( 'Mobile Number', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide mobile number field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_phone',
            'type'     => 'switch',
            'title'    => esc_html__( 'Office Number', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide office phone field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_whatsapp',
            'type'     => 'switch',
            'title'    => esc_html__( 'WhatsApp', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide WhatsApp field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_telegram',
            'type'     => 'switch',
            'title'    => esc_html__( 'Telegram', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Telegram field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_line_id',
            'type'     => 'switch',
            'title'    => esc_html__( 'LINE ID', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide LINE ID field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_fax',
            'type'     => 'switch',
            'title'    => esc_html__( 'Fax Number', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide fax number field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        // Additional Information Fields
        array(
            'id'       => 'profile_fields_additional_section',
            'type'     => 'section',
            'title'    => esc_html__( 'Additional Information Fields', 'houzez' ),
            'subtitle' => esc_html__( 'Show or hide additional profile fields', 'houzez' ),
            'indent'   => true,
        ),
        
        array(
            'id'       => 'profile_field_address',
            'type'     => 'switch',
            'title'    => esc_html__( 'Address', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide address field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_service_areas',
            'type'     => 'switch',
            'title'    => esc_html__( 'Service Areas', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide service areas field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_specialties',
            'type'     => 'switch',
            'title'    => esc_html__( 'Specialties', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide specialties field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_about',
            'type'     => 'switch',
            'title'    => esc_html__( 'About Me/Agency', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide about me/agency field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
    )
));

// Social Media Fields Section
Redux::setSection( $houzez_opt_name, array(
    'title'      => esc_html__( 'Social Media Fields', 'houzez' ),
    'id'         => 'houzez-profile-social-fields',
    'desc'       => esc_html__( 'Manage the visibility of social media fields in the frontend dashboard', 'houzez' ),
    'subsection' => true,
    'fields'     => array(
        array(
            'id'       => 'profile_social_fields_info',
            'type'     => 'info',
            'title'    => esc_html__( 'Social Media Fields Visibility', 'houzez' ),
            'subtitle' => esc_html__( 'Control which social media fields are visible in the frontend dashboard profile section.', 'houzez' ),
            'style'    => 'info',
        ),
        
        array(
            'id'       => 'profile_field_facebook',
            'type'     => 'switch',
            'title'    => esc_html__( 'Facebook', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Facebook field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_twitter',
            'type'     => 'switch',
            'title'    => esc_html__( 'X (Twitter)', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide X (Twitter) field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_linkedin',
            'type'     => 'switch',
            'title'    => esc_html__( 'LinkedIn', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide LinkedIn field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_instagram',
            'type'     => 'switch',
            'title'    => esc_html__( 'Instagram', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Instagram field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_youtube',
            'type'     => 'switch',
            'title'    => esc_html__( 'YouTube', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide YouTube field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_pinterest',
            'type'     => 'switch',
            'title'    => esc_html__( 'Pinterest', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Pinterest field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_googleplus',
            'type'     => 'switch',
            'title'    => esc_html__( 'Google', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Google field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_vimeo',
            'type'     => 'switch',
            'title'    => esc_html__( 'Vimeo', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Vimeo field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_skype',
            'type'     => 'switch',
            'title'    => esc_html__( 'Skype', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Skype field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_tiktok',
            'type'     => 'switch',
            'title'    => esc_html__( 'TikTok', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide TikTok field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_zillow',
            'type'     => 'switch',
            'title'    => esc_html__( 'Zillow Profile', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Zillow profile field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_realtor_com',
            'type'     => 'switch',
            'title'    => esc_html__( 'Realtor.com Profile', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide Realtor.com profile field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
        
        array(
            'id'       => 'profile_field_website',
            'type'     => 'switch',
            'title'    => esc_html__( 'Website', 'houzez' ),
            'subtitle' => esc_html__( 'Show/hide website field', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Show', 'houzez' ),
            'off'      => esc_html__( 'Hide', 'houzez' ),
        ),
    )
)); 