<?php
global $houzez_opt_name, $allowed_html_array, $Option_Countries;

// Define map choices for reuse
$map_choices = array(
    'osm' => 'Open Street Map',
    'mapbox' => 'Mapbox',
    'google' => 'Google Maps',
);

Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__( 'Map Settings', 'houzez' ),
    'id'     => 'houzez-map-settings', // Changed ID slightly for clarity
    'desc'   => '',
    'icon'   => 'el-icon-globe el-icon-small',
    'fields' => array(
        array(
            'id'       => 'map_selection_mode',
            'type'     => 'button_set',
            'title'    => esc_html__('Map Selection Mode', 'houzez'),
            'subtitle' => esc_html__('Choose how to select the map system.', 'houzez'),
            'options'  => array(
                'global' => esc_html__('Global', 'houzez'),
                'specific' => esc_html__('Specific Areas', 'houzez'),
            ),
            'default'  => 'global'
        ),

        // --- Global Settings (Visible when map_selection_mode is 'global') ---
        array(
            'id'       => 'houzez_map_system',
            'type'     => 'button_set',
            'title'    => esc_html__('Global Map System', 'houzez'),
            'subtitle' => esc_html__('Select the map system to use everywhere.', 'houzez'),
            'desc'     => '',
            'required' => array('map_selection_mode', '=', 'global'),
            'options'  => $map_choices,
            'default'  => 'osm'
        ),

        // --- Specific Area Settings (Visible when map_selection_mode is 'specific') ---
        array(
            'id'     => 'specific_map_settings_info',
            'type'   => 'info',
            'title'  => esc_html__('Specific Map Settings', 'houzez'),
            'desc'   => esc_html__('Select the map system for each specific area of the website. If a specific map system requires an API key (Mapbox, Google Maps), ensure the key is entered below.', 'houzez'),
            'required' => array('map_selection_mode', '=', 'specific'),
            'style'  => 'info',
        ),
        array(
            'id'       => 'map_system_header',
            'type'     => 'button_set',
            'title'    => esc_html__('Header Map', 'houzez'),
            'subtitle' => esc_html__('Map used in page headers (e.g., "Property Map" header type).', 'houzez'),
            'required' => array('map_selection_mode', '=', 'specific'),
            'options'  => $map_choices,
            'default'  => 'osm'
        ),
        array(
            'id'       => 'map_system_listing', // Renamed for clarity (was implicit before)
            'type'     => 'button_set',
            'title'    => esc_html__('Listing Taxonomy Maps', 'houzez'),
            'subtitle' => esc_html__('Map used on property taxonomy pages with map headers.', 'houzez'),
            'required' => array('map_selection_mode', '=', 'specific'),
            'options'  => $map_choices,
            'default'  => 'osm'
        ),
        array(
            'id'       => 'map_system_halfmap',
            'type'     => 'button_set',
            'title'    => esc_html__('Half Map Template', 'houzez'),
            'subtitle' => esc_html__('Map used on the Half Map listing/search results template.', 'houzez'),
            'required' => array('map_selection_mode', '=', 'specific'),
            'options'  => $map_choices,
            'default'  => 'osm'
        ),
        array(
            'id'       => 'map_system_detail',
            'type'     => 'button_set',
            'title'    => esc_html__('Property Detail Page', 'houzez'),
            'subtitle' => esc_html__('Map displayed on the single property detail page.', 'houzez'),
            'required' => array('map_selection_mode', '=', 'specific'),
            'options'  => $map_choices,
            'default'  => 'osm'
        ),
         array(
            'id'       => 'map_system_submit',
            'type'     => 'button_set',
            'title'    => esc_html__('Submit Property Form', 'houzez'),
            'subtitle' => esc_html__('Map used when adding or editing a property in frontend.', 'houzez'),
            'required' => array('map_selection_mode', '=', 'specific'),
            'options'  => $map_choices,
            'default'  => 'osm'
        ),
        array(
            'id'       => 'map_system_agent',
            'type'     => 'button_set',
            'title'    => esc_html__('Agent/Agency Detail Page', 'houzez'),
            'subtitle' => esc_html__('Map used on single agent and agency pages.', 'houzez'),
            'required' => array('map_selection_mode', '=', 'specific'),
            'options'  => $map_choices,
            'default'  => 'osm'
        ),

        array(
            'id'       => 'map_system_backend',
            'type'     => 'button_set',
            'title'    => esc_html__('Submit Property Form - WP Admin', 'houzez'),
            'subtitle' => esc_html__('Map used when adding or editing a property in backend.', 'houzez'),
            'options'  => array(
                'google' => 'Google Maps',
                'osm' => 'Open Street Map',
            ),
            'default'  => 'google'
        ),


        // --- API Keys & Common Settings (Always visible, but apply based on selection) ---
         array(
            'id'     => 'api_keys_info',
            'type'   => 'info',
            'title'  => esc_html__('API Keys & Common Settings', 'houzez'),
            'desc'   => esc_html__('Enter API keys required by the selected map systems. Other settings below apply based on the map system chosen (globally or specifically).', 'houzez'),
            'style'  => 'info',
        ),
        array(
            'id'       => 'googlemap_api_key',
            'type'     => 'text',
            'title'    => esc_html__( 'Google Maps API KEY', 'houzez' ),
            'desc'     => wp_kses(__( 'Required if using Google Maps globally or specifically. Get key from <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key">here</a>.', 'houzez' ), $allowed_html_array),
            'subtitle' => esc_html__( 'Enter your google maps api key', 'houzez' ),
            // Removed 'required' as it depends on actual usage now
        ),
        array(
            'id'       => 'mapbox_api_key',
            'type'     => 'text',
            'title'    => esc_html__( 'Mapbox API KEY', 'houzez' ),
            'desc'     => wp_kses(__( 'Required if using Mapbox globally or specifically. Get key from <a target="_blank" href="https://account.mapbox.com/">here</a>.', 'houzez' ), $allowed_html_array),
             // Removed 'required'
        ),
        array(
            'id'       => 'houzez_map_type', // Google Specific Map Type
            'type'     => 'button_set',
            'title'    => esc_html__('Google Map Type', 'houzez'),
            'subtitle' => esc_html__( 'Select the default map type for Google Maps.', 'houzez' ),
            'desc'     => '',
            //'required'  => array('houzez_map_system', '=', 'google'), // Keep this linked to global for simplicity? Or remove requirement? Let's remove for now.
            'options' => array(
                'roadmap' => 'Road Map',
                'satellite' => 'Satellite',
                'hybrid' => 'Hybrid',
                'terrain' => 'Terrain',
             ),
            'default' => 'roadmap'
        ),


        array(
            'id'       => 'markerPricePins',
            'type'     => 'select',
            'title'    => esc_html__( 'Marker Type', 'houzez' ),
            'desc' => esc_html__( 'Select marker type for maps (affects Google Maps and Mapbox).', 'houzez' ),
            //'desc'     => '',
            'options'  => array(
                'no'   => esc_html__( 'Marker Icon', 'houzez' ),
                'yes'   => esc_html__( 'Price Pins', 'houzez' )
            ),
            'default'  => 'no'
        ),
        array(
            'id'       => 'short_prices_pins',
            'type'     => 'switch',
            'title'    => esc_html__( 'Short Price on Pins', 'houzez' ),
            'subtitle'     => esc_html__( 'Note: Currency switcher might not work with short prices.', 'houzez' ),
            'desc' => esc_html__( 'Enable short prices like 12K, 10M, 10B on price pins.', 'houzez' ),
            'default'  => 1,
            'on'       => esc_html__( 'Enabled', 'houzez' ),
            'off'      => esc_html__( 'Disabled', 'houzez' ),
            'required'  => array('markerPricePins', '=', 'yes'),
        ),

        array(
            'id'       => 'map_default_zoom',
            'type'     => 'text',
            'title'    => esc_html__( 'Default Zoom', 'houzez' ),
            'subtitle' => esc_html__( 'Default zoom level for Maps.', 'houzez' ),
            'default'  => '12',
            'validate' => 'numeric'
        ),

        array(
            'id'       => 'map_max_zoom',
            'type'     => 'text',
            'title'    => esc_html__( 'Maximum Zoom Level', 'houzez' ),
            'subtitle' => esc_html__( 'Maximum zoom level for Maps.', 'houzez' ),
            'default'  => '18',
            'validate' => 'numeric'
        ),

        array(
            'id'       => 'map_default_lat',
            'type'     => 'text',
            'title'    => esc_html__( 'Default Fallback Latitude', 'houzez' ),
            'subtitle' => esc_html__( 'Used if a property/agent has no coordinates.', 'houzez' ),
            'default'  => '25.686540',
            'validate' => 'numeric'
        ),

        array(
            'id'       => 'map_default_long',
            'type'     => 'text',
            'title'    => esc_html__( 'Default Fallback Longitude', 'houzez' ),
            'subtitle' => esc_html__( 'Used if a property/agent has no coordinates.', 'houzez' ),
            'default'  => '-80.431345',
            'validate' => 'numeric'
        ),

        array(
            'id'       => 'geo_country_limit',
            'type'     => 'switch',
            'title'    => esc_html__( 'Limit Geocoding/Autocomplete to Country', 'houzez' ),
            'desc'     => '',
            'subtitle' => esc_html__( 'Limit address search suggestions to a specific country.', 'houzez' ),
            'default'  => 0,
            'on'       => 'Enabled',
            'off'      => 'Disabled',
        ),
        array(
            'id'		=> 'geocomplete_country',
            'type'		=> 'select',
            'required'  => array('geo_country_limit', '=', '1'),
            'title'		=> esc_html__( 'Select Country for Geocoding Limit', 'houzez' ),
            'subtitle'	=> '',
            'options'	=> $Option_Countries,
            'default' => ''
        ),

    ),
));

// Cluster subsection remains mostly the same, but might need notes about compatibility
Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__( 'Cluster Settings', 'houzez' ),
    'id'     => 'map-cluster',
    'desc'   => esc_html__('Marker clustering settings. Applies to Google Maps and OpenStreetMap/Mapbox.', 'houzez'),
    'icon'   => '',
    'subsection' => true,
    'fields'    => array(
        array(
            'id'       => 'map_cluster_enable',
            'type'     => 'switch',
            'title'    => esc_html__( 'Enable Markers Clustering', 'houzez' ),
            'subtitle' => '',
            'desc'     => '',
            'on'       => esc_html__('Enabled', 'houzez'),
            'off'      => esc_html__('Disabled', 'houzez'),
            'default'  => 0
        ),
        array(
            'id'        => 'map_cluster', // Used for Google Maps & OSM
            'type'      => 'media',
            'title'     => esc_html__( 'Map Cluster Icon', 'houzez' ),
            'read-only' => false,
            'default'   => array( 'url' => HOUZEZ_IMAGE . 'map/cluster-icon.png' ),
            'desc'  => esc_html__( 'Upload the map cluster icon (used for Google Maps and OSM/Mapbox).', 'houzez' ),
        ),
        array(
            'id'       => 'googlemap_zoom_cluster', // Google Specific
            'type'     => 'text',
            'title'    => esc_html__( 'Google Maps - Cluster Max Zoom Level', 'houzez' ),
            'desc' => esc_html__( 'Max zoom level for clustering to appear on Google Maps (1-20, default 12).', 'houzez' ),
            'default'  => '12',
            // Required only if Google Maps is used somewhere and clustering is enabled
        ),
    )

));

// Single Listing Map subsection remains mostly the same
Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__( 'Single Listing Map', 'houzez' ),
    'id'     => 'map-single-listing',
    'desc'   => '',
    'icon'   => '',
    'subsection' => true,
    'fields'    => array(
        array(
            'id'       => 'detail_map_pin_type',
            'type'     => 'select',
            'title'    => esc_html__('Pin or Circle', 'houzez'),
            'desc' => esc_html__('Show a marker pin or a radius circle on the single property map.', 'houzez'),
            'options'  => array(
                'marker'   => esc_html__( 'Marker Pin', 'houzez' ),
                'circle'   => esc_html__( 'Circle', 'houzez' ),
            ),
            'default'  => 'marker',
        ),
        array(
            'id'       => 'single_mapzoom',
            'type'     => 'text',
            'title'    => esc_html__( 'Single Listing Map Zoom', 'houzez' ),
            'desc'     => esc_html__( 'Default zoom level (1-20, default 14).', 'houzez' ),
            'default'  => '14',
            'validate' => 'numeric'
        )
    )
));

// Map Style subsection remains the same (primarily for Google Maps)
Redux::setSection( $houzez_opt_name, array(
    'title'  => esc_html__( 'Map Style', 'houzez' ),
    'id'     => 'map-style',
    'desc'   => esc_html__('Configure map styling options for your preferred map provider.', 'houzez'),
    'icon'   => '',
    'subsection' => true,
    'fields'    => array(
        array(
            'id'       => 'googlemap_map_id',
            'type'     => 'text',
            'title'    => esc_html__('Google Cloud Map ID', 'houzez'),
            'subtitle' => wp_kses(__('Create custom map styles in the <a href="https://console.cloud.google.com/google/maps-apis/studio" target="_blank">Google Cloud Console</a>.', 'houzez'), $allowed_html_array),
            'desc'     => esc_html__('Enter your Map ID from Google Maps Cloud Customization (e.g., "12345ab67c890de1f").', 'houzez'),
            'default'  => '',
        ),
        array(
            'id'       => 'mapbox_style',
            'type'     => 'select',
            'title'    => esc_html__('Mapbox Style', 'houzez'),
            'subtitle' => esc_html__('Select a pre-defined Mapbox style or enter your own custom style URL.', 'houzez'),
            'options'  => array(
                'mapbox://styles/mapbox/streets-v11' => 'Streets',
                'mapbox://styles/mapbox/outdoors-v11' => 'Outdoors',
                'mapbox://styles/mapbox/light-v10' => 'Light',
                'mapbox://styles/mapbox/dark-v10' => 'Dark',
                'mapbox://styles/mapbox/satellite-v9' => 'Satellite',
                'mapbox://styles/mapbox/satellite-streets-v11' => 'Satellite Streets',
                'custom' => 'Custom Style URL',
            ),
            'default'  => 'mapbox://styles/mapbox/streets-v11',
        ),
        array(
            'id'       => 'mapbox_custom_style_url',
            'type'     => 'text',
            'title'    => esc_html__('Custom Mapbox Style URL', 'houzez'),
            'subtitle' => esc_html__('Enter your custom Mapbox style URL.', 'houzez'),
            'required' => array('mapbox_style', '=', 'custom'),
            'desc'     => 'Example: mapbox://styles/mapbox/streets-v11',
            'default'  => '',
        ),
        array(
            'id'     => 'mapbox_custom_style_info',
            'type'   => 'info',
            'title'  => esc_html__('Instructions for Custom Mapbox Style URL', 'houzez'),
            'desc'   => esc_html__('To create a custom Mapbox style URL, follow these steps:
1. Select "Custom Style URL" from the Mapbox Style dropdown above.
2. Go to the Mapbox Studio (https://studio.mapbox.com/).
3. Create a new style or edit an existing one.
4. Once you have your style, click on "Share" and copy the style URL.
5. Paste the URL in the "Custom Mapbox Style URL" field above.', 'houzez'),
            'style'  => 'info',
        ),
    )
));