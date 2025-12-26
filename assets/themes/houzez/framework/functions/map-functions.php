<?php
if( !function_exists('houzez_get_map_system') ) {
    /**
     * Get the map system based on context or global settings.
     *
     * @param string|null $context Optional context (e.g., 'submit', 'header', 'detail', 'halfmap', 'agent', 'listing'). Defaults to 'global'.
     * @return string The determined map system ('osm', 'mapbox', 'google').
     */
    function houzez_get_map_system() {
        global $post;

        $context = 'global';
        $page_header_type = '';
        $selection_mode = houzez_option('map_selection_mode', 'global');
        $global_map_system = houzez_option('houzez_map_system', 'osm'); // Default global to osm
        $map_system = $global_map_system; // Start with the global default

        
        if ($selection_mode === 'specific') {
            $specific_map_option = '';
           
            $page_id = isset($post->ID) ? $post->ID : '';

            if(!empty($page_id)) {
                $page_header_type = get_post_meta($page_id, 'fave_header_type', true); 
            }

            if( is_page_template('template/user_dashboard_submit.php') ) {
                $context = 'submit';
            } elseif( is_singular('property') || is_singular('fts_builder') ) {
                $context = 'property_detail';
            } elseif( is_page_template('template/property-listings-map.php')
                || ( is_page_template('template/template-search.php') && houzez_option('search_result_page') == 'half_map' ) ) {
                $context = 'halfmap';
            } elseif( houzez_is_taxonomy_map() ) {
                $context = 'taxonomy';
            } elseif( is_singular('houzez_agent') || is_singular('houzez_agency') ) {
                $context = 'agent';
            } elseif( $page_header_type == 'property_map' ) {
                $context = 'header';
            } 
            
            switch ($context) {
                case 'submit':
                    $specific_map_option = houzez_option('map_system_submit');
                    break;
                case 'header':
                    $specific_map_option = houzez_option('map_system_header');
                    break;
                case 'property_detail':
                    $specific_map_option = houzez_option('map_system_detail');
                    break;
                case 'halfmap':
                    $specific_map_option = houzez_option('map_system_halfmap');
                    break;
                case 'agent': // Covers both agent and agency pages
                case 'agency':
                    $specific_map_option = houzez_option('map_system_agent');
                    break;
                case 'listing': // Covers archives and taxonomies
                case 'archive':
                case 'taxonomy':
                     $specific_map_option = houzez_option('map_system_listing');
                     break;
                case 'global': // Explicitly requested global
                     $map_system = $global_map_system;
                     break;
                default:
                    // If context is unknown or not specifically handled, fall back to global
                    $map_system = $global_map_system;
                    break;
            }

            // Use the specific setting only if it's set and not empty, otherwise keep the global fallback
            if (!empty($specific_map_option)) {
                $map_system = $specific_map_option;
            } else {
                 // If a specific context was requested but no option was found, use the global setting
                $map_system = $global_map_system;
            }

        } else {
            // Global mode selected
            $map_system = $global_map_system;
        }

        $google_api_key = houzez_option('googlemap_api_key');
        $mapbox_api_key = houzez_option('mapbox_api_key');


        // Final check: Ensure API keys are present if Google or Mapbox is selected, otherwise default to OSM
        if ( $map_system == 'google' && empty($google_api_key) ) {
            $map_system = 'osm'; // Fallback if Google key is missing
        } elseif ( $map_system == 'mapbox' && empty($mapbox_api_key) ) {
            $map_system = 'osm'; // Fallback if Mapbox key is missing
        } elseif ( $map_system != 'osm' && $map_system != 'mapbox' && $map_system != 'google' ) {
             $map_system = 'osm'; // Fallback for any invalid value
        }

        // Allow filtering the final map system choice
        return apply_filters('houzez_selected_map_system', $map_system, $context);
    }
}

if(!function_exists('houzez_enqueue_maps_api_geolocation_field')) {
    function houzez_enqueue_maps_api_geolocation_field() {
        if(houzez_get_map_system() == 'google') {
            wp_enqueue_script('houzez-google-map-api');
            wp_enqueue_script('houzez-google-maps');

        } elseif(houzez_get_map_system() == 'mapbox') {
            wp_enqueue_script('mapbox-gl');
            wp_enqueue_style('mapbox-gl');
            wp_enqueue_script('mapbox-geocoder');
            wp_enqueue_style('mapbox-geocoder');
            wp_enqueue_script('houzez-mapbox'); 

        } else {
            wp_enqueue_script('leaflet');

            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_style('jquery-ui');
            wp_enqueue_script('houzez-openstreetmap');
        }
    }
}

if( !function_exists('houzez_metabox_map_type') ) {
    function houzez_metabox_map_type() {
        $houzez_map_system = houzez_option('map_system_backend', 'google');
        $googlemap_api_key = houzez_option('googlemap_api_key'); 

        if( $houzez_map_system == 'google' && $googlemap_api_key != "" ) {
            $map_system = 'map';
        } elseif($houzez_map_system == 'osm') {
            $map_system = 'osm';
        } else {
            $map_system = 'osm';
        }
        return $map_system;
    }
}

if( !function_exists('houzez_metabox_map_region') ) {
    function houzez_metabox_map_region() {
        $geo_country_limit = houzez_option('geo_country_limit', 0);
        $region = houzez_option('geocomplete_country', 'us');
        
        if( $geo_country_limit != 0 ) {
            return $region;
        } else {
            return '';
        }
    }
}

if( !function_exists('houzez_map_api_key') ) {

    function houzez_map_api_key() {

        $houzez_map_system = houzez_option('map_system_backend', 'google');   
        $googlemap_api_key = houzez_option('googlemap_api_key'); 

        if($houzez_map_system == 'google' && $googlemap_api_key != "") {
            $googlemap_api_key = urlencode( $googlemap_api_key );
            return $googlemap_api_key;
        } 

        return '';
    }
}

if(!function_exists('houzez_is_taxonomy_map')) {
    function houzez_is_taxonomy_map() {
        $tax_show_map = get_term_meta(get_queried_object_id(), 'fave_taxonomy_show_header_map', true);

        if(houzez_is_tax() && $tax_show_map == 1) {
            return true;
        }
        return false;
    }
}

if( ! function_exists('houzez_get_single_listing_map_data_json') ) {
    /**
     * Get map data JSON for a single listing.
     *
     * @param int $post_id The ID of the listing post.
     * @return string JSON encoded map data, escaped for use in an HTML attribute.
     */
    function houzez_get_single_listing_map_data_json( $post_id ) {
        $google_map_address = houzez_get_listing_data('property_map_address', $post_id);
        $location = houzez_get_listing_data('property_location', $post_id);
        $lat_lng = explode(',', $location);
        $latitude = $lat_lng[0] ?? '';
        $longitude = $lat_lng[1] ?? '';

        $pricePin = houzez_listing_price_map_pins($post_id);
        $marker = '';
        $retinaMarker = '';
        $marker_color = '';
        $term_id = null;

        $property_type = get_the_terms( $post_id, 'property_type' );
        if ( $property_type && ! is_wp_error( $property_type ) ) {
            foreach ( $property_type as $p_type ) {
                $marker_id = get_term_meta( $p_type->term_id, 'fave_marker_icon', true );
                $retina_marker_id = get_term_meta( $p_type->term_id, 'fave_marker_retina_icon', true );
                $meta = get_option( '_houzez_property_type_'.$p_type->term_id );
                
                $term_id = $p_type->term_id;
                
                // Check for custom color regardless of marker
                if ( !empty($meta) && is_array($meta) && isset($meta['color_type']) && $meta['color_type'] === 'custom' && !empty($meta['color']) ) {
                    $marker_color = sanitize_hex_color($meta['color']);
                }

                if ( ! empty ( $marker_id ) ) {
                    $marker_url = wp_get_attachment_url( $marker_id );
                    if ( $marker_url ) {
                        $marker = esc_url( $marker_url );
                        if ( ! empty ( $retina_marker_id ) ) {
                            $retina_marker_url = wp_get_attachment_url( $retina_marker_id );
                            if ( $retina_marker_url ) {
                                $retinaMarker = esc_url( $retina_marker_url );
                            }
                        }
                        break; // Found marker, exit loop
                    }
                }
            }
        }

        // Set default markers if property type has no marker uploaded or term doesn't exist
        if ( empty( $marker ) ) {
            // Attempt to get default marker from theme options or fallback
            $default_marker = houzez_option('houzez_default_map_marker');
            if ( !empty($default_marker['url']) ) {
                $marker = esc_url($default_marker['url']);
            } else {
                $marker = HOUZEZ_IMAGE . 'map/pin-single-family.png';
            }

            $default_retina_marker = houzez_option('houzez_default_map_marker_retina');
            if ( !empty($default_retina_marker['url']) ) {
                $retinaMarker = esc_url($default_retina_marker['url']);
            } else {
                // Use the same as non-retina if retina is not set
                $retinaMarker = $marker; 
            }
        }

        $map_data = array(
            'latitude'     => $latitude,
            'longitude'    => $longitude,
            'address'      => $google_map_address,
            'marker'       => $marker,
            'retinaMarker' => $retinaMarker,
            'pricePin'     => $pricePin,
            'term_id'      => $term_id,
            'post_id'      => $post_id,
            'property_id'  => $post_id,
            'marker_color' => $marker_color,
        );

        return esc_attr( wp_json_encode( $map_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ) );
    }
}

if ( ! function_exists( 'houzez_get_listings_map_data' ) ) {
    /**
     * Get map data JSON for all listings based on current context (search, taxonomy, etc.).
     *
     * @param array $args WP_Query arguments for property selection.
     * @return string JSON encoded map data for multiple properties, escaped for use in an HTML attribute.
     */
    function houzez_get_listings_map_data( $args = [] ) {
        $properties_data = [];

        // ——— OPTIMIZE: only pull IDs, no extra cache or found-rows ———
        $args['fields']                 = 'ids';
        $args['no_found_rows']          = true;
        $args['update_post_meta_cache'] = false;
        $args['update_post_term_cache'] = false;

        // Run the property query
        $prop_map_query = new WP_Query( $args );

        // Loop over plain IDs
        if ( ! empty( $prop_map_query->posts ) ) { 
            foreach ( $prop_map_query->posts as $post_id ) { 
                if ( $data = houzez_get_property_map_data( $post_id ) ) {
                    $properties_data[] = $data;
                }
            }
        }
        wp_reset_postdata();

        // wrap & return
        $map_data = [ 'properties' => $properties_data ];
        $map_data_json = wp_json_encode( $map_data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
        return esc_attr( $map_data_json );
    }
}

/**
 * Get property map data for a single property
 *
 * @param int $post_id The property post ID
 * @return array|null
 */
if( ! function_exists('houzez_get_property_map_data') ) {
    function houzez_get_property_map_data( int $post_id ): ?array {
        $location = get_post_meta( $post_id, 'fave_property_location', true );
        if ( empty( $location ) ) {
            return null;
        }

        list( $latitude, $longitude ) = array_map( 'trim', explode( ',', $location ) );
        if ( empty( $latitude ) || empty( $longitude ) ) {
            return null;
        }

        $pricePin = houzez_listing_price_map_pins( $post_id );

        // Marker icons
        $marker = '';
        $retina = '';
        $marker_color = '';
        $term_id = null;
        $terms   = get_the_terms( $post_id, 'property_type' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $t ) {
                $mid = get_term_meta( $t->term_id, 'fave_marker_icon', true );
                $rid = get_term_meta( $t->term_id, 'fave_marker_retina_icon', true );
                $meta = get_option( '_houzez_property_type_'.$t->term_id );
                
                $term_id = $t->term_id;
                
                // Check for custom color regardless of marker
                if ( !empty($meta) && is_array($meta) && isset($meta['color_type']) && $meta['color_type'] === 'custom' && !empty($meta['color']) ) {
                    $marker_color = sanitize_hex_color($meta['color']);
                }
                
                if ( $mid ) {
                    $marker = wp_get_attachment_url( $mid );
                    $retina = $rid ? wp_get_attachment_url( $rid ) : $marker;
                    break;
                }
            }
        }

        if ( ! $marker ) {
            $default = houzez_option( 'houzez_default_map_marker' );
            $marker = $default['url'] ?? HOUZEZ_IMAGE . 'map/pin-single-family.png';
            $retina = houzez_option( 'houzez_default_map_marker_retina' )['url'] ?? $marker;
        }

        // Thumbnail
        $thumbnail = '';
        if ( has_post_thumbnail( $post_id ) ) {
            $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'houzez-item-image-6' );
            $thumbnail = $img[0] ?? '';
        }

        $featured = get_post_meta( $post_id, 'fave_featured', true );
        $featured_label = '';
        if ( $featured ) {
            $featured_label = '<div class="info-window-label label-featured">'.houzez_option('cl_featured_label', esc_html__( 'Featured', 'houzez' )).'</div>';
        }

        return [
            'title'        => get_the_title( $post_id ),
            'url'          => get_permalink( $post_id ),
            'link_target'  => houzez_option( 'listing_link_target', '_self' ),
            'price'        => houzez_listing_price_v5( $post_id ),
            'property_id'  => $post_id,
            'latitude'     => $latitude,
            'longitude'    => $longitude,
            'address'      => houzez_get_infowindow_address( $post_id ),
            'property_type'=> houzez_taxonomy_simple( 'property_type', $post_id ),
            'marker'       => esc_url( $marker ),
            'retinaMarker' => esc_url( $retina ),
            'pricePin'     => $pricePin,
            'term_id'      => $term_id,
            'marker_color' => $marker_color,
            'thumbnail'    => $thumbnail,
            'featured_label' => $featured_label,
            'meta' => houzez_map_listing_meta( $post_id )
        ];
    }
}


if( ! function_exists('houzez_get_header_map_data') ) {
    /**
     * Get header map data JSON.
     *
     * @return string JSON encoded header map data, escaped for use in an HTML attribute.
     */ 
    function houzez_get_header_map_data() {
        $tax_query = array();
        $properties_limit = 1000;
        $properties_data = array();

        $header_type = houzez_get_listing_data('header_type');

        // Set up query arguments based on context
        $wp_query_args = array(
            'post_type' => 'property',
            'posts_per_page' => apply_filters('houzez_header_map_properties', $properties_limit),
            'post_status' => 'publish'
        );

        $wp_query_args = apply_filters('houzez_sold_status_filter', $wp_query_args);

        if ( houzez_is_listings_template() && $header_type == 'property_map' ) {
            $wp_query_args = apply_filters('houzez20_property_filter', $wp_query_args);

            if( houzez_option('listing_show_all_listings_on_map', 0) ) {
                $wp_query_args['posts_per_page'] = -1;
            }
            $wp_query_args = houzez_prop_sort($wp_query_args);

        } elseif ( is_page_template(array('template/template-search.php')) && $header_type == 'property_map' ) {
            global $paged;

            $wp_query_args = apply_filters('houzez20_search_filters', $wp_query_args);
            $wp_query_args = houzez_prop_sort($wp_query_args);

            $properties_limit = intval(houzez_option('search_num_posts', 12));
            if ($properties_limit <= 0) {
                $properties_limit = 12;
            }
            $wp_query_args['posts_per_page'] = $properties_limit;
            if( houzez_option('search_show_all_listings_on_map', 0) ) {
                $wp_query_args['posts_per_page'] = -1;
            }
            $wp_query_args['paged'] = $paged;

        } elseif ( $header_type == 'property_map' ) {

            $cities = houzez_get_listing_data('map_city', false);
            if (!empty($cities)) {
                $tax_query[] = array(
                    'taxonomy' => 'property_city',
                    'field' => 'slug',
                    'terms' => $cities
                );
            }

            $tax_count = count($tax_query);
            if ($tax_count > 1) {
                $tax_query['relation'] = 'AND';
            }
            if ($tax_count > 0) {
                $wp_query_args['tax_query'] = $tax_query;
            }

            $wp_query_args = houzez_prop_sort($wp_query_args);

        } elseif ( houzez_is_taxonomy_map() ) {

            global $wp_query, $paged;
            $tax_query[] = array(
                'taxonomy' => $wp_query->query_vars['taxonomy'],
                'field' => 'slug',
                'terms' => $wp_query->query_vars['term']
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $wp_query_args['tax_query'] = $tax_query;
            }

            $tax_show_map = get_term_meta(get_queried_object_id(), 'fave_taxonomy_show_header_map', true);
            $map_listings = get_term_meta(get_queried_object_id(), 'fave_taxonomy_map_listings', true);

            if($tax_show_map == 1 && $map_listings == 'all_listings') {
                $wp_query_args['posts_per_page'] = -1;
            } else {
                $properties_limit = intval(houzez_option('taxonomy_num_posts', 12));
                if ($properties_limit <= 0) {
                    $properties_limit = 12;
                }
                $wp_query_args['posts_per_page'] = $properties_limit;
            }

            $wp_query_args['paged'] = $paged;
            $wp_query_args = houzez_prop_sort($wp_query_args);
        }

        $properties_data = houzez_get_listings_map_data($wp_query_args);

        return $properties_data;
    }   
}


if( ! function_exists('houzez_get_half_map_data') ) {
    /**
     * Get half map data JSON.
     *
     * @return string JSON encoded half map data, escaped for use in an HTML attribute.
     */ 
    function houzez_get_half_map_data() {
        global $paged;
        $properties_data = array();

        // Set up query arguments based on context
        $wp_query_args = array(
            'post_type' => 'property',
            'post_status' => 'publish'
        );

        $wp_query_args = apply_filters('houzez_sold_status_filter', $wp_query_args);

        if( is_page_template(array('template/property-listings-map.php')) ) { 

            $wp_query_args = apply_filters( 'houzez20_property_filter', $wp_query_args );
            $wp_query_args = houzez_prop_sort ( $wp_query_args );

        } else {
            
            $wp_query_args = apply_filters( 'houzez20_search_filters', $wp_query_args );
            $properties_limit = intval( houzez_option('search_num_posts', 12) );
            if ( $properties_limit <= 0  ) {
                $properties_limit = 12;
            }
            $wp_query_args['posts_per_page'] = $properties_limit;
            $wp_query_args['paged'] = $paged;
            
        }

        if( houzez_option('auto_load_map_listings', 1) ) {
            $wp_query_args['posts_per_page'] = houzez_option('search_num_map_posts', 100);
            $wp_query_args['paged'] = 1;
        }

        $wp_query_args = houzez_prop_sort ( $wp_query_args );

        $properties_data = houzez_get_listings_map_data($wp_query_args);

        return $properties_data;
    }
}

if( ! function_exists('houzez_get_map_options') ) {
    /**
     * Get general map options JSON.
     *
     * Retrieves map-related theme options and prepares them as a JSON string
     * suitable for use in data attributes.
     *
     * @return string JSON encoded map options, escaped for use in an HTML attribute.
     */
    function houzez_get_map_options() {
        $map_options = array();

        $mapbox_style = houzez_option('mapbox_style', 'mapbox://styles/mapbox/streets-v12');
        $mapbox_custom_style_url = houzez_option('mapbox_custom_style_url');

        if ( $mapbox_style == 'custom' && !empty($mapbox_custom_style_url) ) {
            $mapbox_style = $mapbox_custom_style_url;
        }

        // Default map center coordinates
        $map_options['default_lat'] = houzez_option('map_default_lat', 25.686540);
        $map_options['default_lng'] = houzez_option('map_default_long', -80.431345);
        
        // Check if we're on a taxonomy page that has coordinates
        if (houzez_is_tax()) {
            $tax_id = get_queried_object_id();
            $taxonomy_lat = get_term_meta($tax_id, 'fave_taxonomy_latitude', true);
            $taxonomy_lng = get_term_meta($tax_id, 'fave_taxonomy_longitude', true);
            
            // If both coordinates exist, use them instead of defaults
            if (!empty($taxonomy_lat) && !empty($taxonomy_lng)) {
                $map_options['center_lat'] = $taxonomy_lat;
                $map_options['center_lng'] = $taxonomy_lng;
            }
        }

        $map_options['markerPricePins'] = houzez_option('markerPricePins');
        $map_options['single_map_zoom'] = houzez_option('single_mapzoom', 15);
        $map_options['default_zoom'] = houzez_option('map_default_zoom', 12);
        $map_options['max_zoom'] = houzez_option('map_max_zoom', 18);
        $map_options['clusterer_zoom'] = houzez_option('googlemap_zoom_cluster');
        $map_options['map_cluster_enable'] = houzez_option('map_cluster_enable');
        $map_options['map_type'] = houzez_option('houzez_map_type');
        $map_options['mapbox_style'] = $mapbox_style;
        $map_options['mapbox_access_token'] = houzez_option('mapbox_api_key');
        $map_options['map_pin_type'] = houzez_option('detail_map_pin_type', 'marker');
        $map_options['closeIcon'] = HOUZEZ_IMAGE . 'map/close.png';
        $map_options['infoWindowPlac'] = HOUZEZ_IMAGE . 'pixel.gif';
        $map_options['mapId'] = houzez_option('googlemap_map_id', 'HOUZEZ_MAP_ID');



        // Add cluster icon if available
        $map_cluster = houzez_option('map_cluster', false, 'url');
        if ($map_cluster != '') {
            $map_options['clusterIcon'] = $map_cluster;
        } else {
            $map_options['clusterIcon'] = HOUZEZ_IMAGE . 'map/cluster-icon.png';
        }

        return $map_options;
    }
}

if( ! function_exists('houzez_get_infowindow_address') ) {
    function houzez_get_infowindow_address( $post_id ) {

        $address_composer = houzez_option('listing_address_composer');
        $enabled_data = isset($address_composer['enabled']) ? $address_composer['enabled'] : [];
        $temp_array = array();

        $icon = '<i class="houzez-icon icon-pin" aria-hidden="true"></i>';
        $result = '';
        if ($enabled_data) {
            unset($enabled_data['placebo']);
            foreach ($enabled_data as $key=>$value) {

                
                if( $key == 'address' ) {
                    $map_address = get_post_meta( $post_id, 'fave_property_map_address', true );

                    if( $map_address != '' ) {
                        $temp_array[] = $map_address;
                    }

                } else if( $key == 'streat-address' ) {
                    $property_address = get_post_meta( $post_id, 'fave_property_address', true );

                    if( $property_address != '' ) {
                        $temp_array[] = $property_address;
                    }

                } else if( $key == 'country' ) {
                    $country = houzez_taxonomy_simple('property_country', $post_id);

                    if( $country != '' ) {
                        $temp_array[] = $country;
                    }

                } else if( $key == 'state' ) {
                    $state = houzez_taxonomy_simple('property_state', $post_id);

                    if( $state != '' ) {
                        $temp_array[] = $state;
                    }

                } else if( $key == 'city' ) {
                    $city = houzez_taxonomy_simple('property_city', $post_id);

                    if( $city != '' ) {
                        $temp_array[] = $city;
                    }

                } else if( $key == 'area' ) {
                    $area = houzez_taxonomy_simple('property_area', $post_id);

                    if( $area != '' ) {
                        $temp_array[] = $area;
                    }

                }
            }

            $result = join( ", ", $temp_array );
            return '<div class="info-window-address-info text-truncate">' . $icon . ' ' . $result . '</div>';
        }
        return '';
    }
}


if( ! function_exists('houzez_map_listing_meta') ) {
    /**
     * Get map listing meta.
     *
     * @param int $post_id The post ID.
     * @return string HTML output for map listing meta.
     */
    function houzez_map_listing_meta( $post_id ) {
        $output = '';
        $metadata = '';

        $output .= '<div class="info-window-info-details">';
            $listing_data_composer = houzez_option('listing_data_composer');
            $data_composer = isset($listing_data_composer['enabled']) ? $listing_data_composer['enabled'] : array();
            
            if(empty($data_composer)) {
                $data_composer = array();
            }
            
            unset($data_composer['placebo']);
            $i = 0;
            
            if (!empty($data_composer)) {
                foreach ($data_composer as $key => $value) { 
                    $i++;

                    $field_info = houzez_map_info_window_fields($key);
                    
                    $custom_field_value = get_post_meta($post_id, 'fave_'.$field_info['field'], true);
                    
                    if (!empty($custom_field_value)) { 
                        $icon_html = '';
                        
                        if (houzez_option('icons_type') == 'font-awesome') {
                            $fa_class = houzez_option('fa_'.$key);
                            if (!empty($fa_class)) {
                                $icon_html = '<i class="' . esc_attr($fa_class) . ' me-1"></i>';
                            }
                        } elseif (houzez_option('icons_type') == 'custom') {
                            $cus_icon = houzez_option($key);
                            if (!empty($cus_icon['url'])) {
                                $alt = isset($cus_icon['title']) ? esc_attr($cus_icon['title']) : '';
                                $icon_html = '<img class="img-fluid me-1" src="' . esc_url($cus_icon['url']) . '" width="16" height="16" alt="' . $alt . '">';
                            }
                        } else {
                            $icon_html = '<i class="' . esc_attr($field_info['icon']) . ' me-1"></i>';
                        }
                        
                        $css_class = ($i > 1) ? 'info-window-meta-item ms-2' : 'info-window-meta-item';
                        
                        // Sanitize the field value to prevent JSON issues
                        $sanitized_value = esc_html($custom_field_value);
                        
                        // Add size unit for area-size and land-area
                        if ($key == 'area-size') {
                            $listing_area_size = houzez_get_listing_area_size( $post_id );
                            $listing_size_unit = houzez_get_listing_size_unit($post_id);
                            $sanitized_unit = esc_html($listing_size_unit);
                            $output .= '<span class="' . esc_attr($css_class) . '">' . $icon_html . ' ' . $listing_area_size . ' ' . $sanitized_unit . '</span>';
                        } elseif ($key == 'land-area') {
                            $listing_land_size = houzez_get_land_area_size( $post_id );
                            $listing_size_unit = houzez_get_land_size_unit($post_id);
                            $sanitized_unit = esc_html($listing_size_unit);
                            $output .= '<span class="' . esc_attr($css_class) . '">' . $icon_html . ' ' . $listing_land_size . ' ' . $sanitized_unit . '</span>';
                        } else {
                            $output .= '<span class="' . esc_attr($css_class) . '">' . $icon_html . ' ' . $sanitized_value . '</span>';
                        }
                    }

                    if ($i == 4) {
                        break;
                    }
                }
            }

        $output .= '</div>';

        return $output;
    }
}

if(!function_exists('houzez_map_info_window_fields')) {
    /**
     * Get field information for map info window.
     *
     * @param string $key The field key.
     * @return array Field information including field name and icon.
     */
    function houzez_map_info_window_fields($key) {
        $result = array();
        
        switch($key) {
            case 'bed':
                $result = array(
                    'field' => 'property_bedrooms',
                    'icon' => 'houzez-icon icon-hotel-double-bed-1',
                );
                break;
            case 'room':
                $result = array(
                    'field' => 'property_rooms',
                    'icon' => 'houzez-icon icon-real-estate-dimensions-plan-1',
                );
                break;
            case 'bath':
                $result = array(
                    'field' => 'property_bathrooms',
                    'icon' => 'houzez-icon icon-bathroom-shower-1',
                );
                break;
            case 'garage':
                $result = array(
                    'field' => 'property_garage',
                    'icon' => 'houzez-icon icon-car-1',
                );
                break;
            case 'area-size':
                $result = array(
                    'field' => 'property_size',
                    'icon' => 'houzez-icon icon-ruler-triangle',
                );
                break;
            case 'land-area':
                $result = array(
                    'field' => 'property_land',
                    'icon' => 'houzez-icon icon-real-estate-dimensions-map',
                );
                break;
            case 'property-id':
                    $result = array(
                        'field' => 'property_id',
                        'icon' => 'houzez-icon icon-tags',
                    );
                    break;
            default:
                $result = array(
                    'field' => $key,
                    'icon' => '',
                );
                break;
        }
        
        return $result;
    }
}


if(!function_exists( 'houzez_enqueue_marker_clusterer' )) {
    /**
     * Enqueues the marker clusterer script for Google Maps
     * 
     * @deprecated since 4.0.0 This function is deprecated and will be removed in a future version
     * @return void
     */
    function houzez_enqueue_marker_clusterer() {}
}

if(!function_exists( 'houzez_enqueue_richmarker' )) {
    /**
     * @deprecated since 4.0.0 This function is deprecated and will be removed in a future version
     * @return void
     */
    function houzez_enqueue_richmarker() {}
}

if(!function_exists( 'houzez_enqueue_marker_spiderfier' )) {
    /**
     * @deprecated since 4.0.0 This function is deprecated and will be removed in a future version
     * @return void
     */
    function houzez_enqueue_marker_spiderfier() {}
}


if(!function_exists('houzez_enqueue_geo_location_js')) {
    /**
     * @deprecated since 4.0.0 This function is deprecated and will be removed in a future version
     * @return void
     */
    function houzez_enqueue_geo_location_js() {}
}

if(!function_exists('houzez_google_maps_scripts')) {
    /**
     * @deprecated since 4.0.0 This function is deprecated and will be removed in a future version
     * @return void
     */
    function houzez_google_maps_scripts() {}
}

if( !function_exists( 'houzez_get_google_map_properties' ) ) {
    
    /**
     * @deprecated since 4.0.0 This function is deprecated and will be removed in a future version
     * @return void
     */
    function houzez_get_google_map_properties() {}
}


/*-----------------------------------------------------------------------
* Single Property Map
*----------------------------------------------------------------------*/
if( !function_exists( 'houzez_get_single_property_map' ) ) {
    
    /**
     * @deprecated since 4.0.0 This function is deprecated and will be removed in a future version
     * @return void
     */
    function houzez_get_single_property_map() {}
}

if( ! function_exists('houzez_get_address_coordinates') ) {
    function houzez_get_address_coordinates($address) {
        if(houzez_get_map_system() == 'google') {
            return houzez_getLatLongFromAddress($address);
        } elseif(houzez_get_map_system() == 'mapbox') {
            return houzezMapbox_getLatLngFromAddress($address);
        } else {
            return houzezOSM_getLatLngFromAddress($address);
        }
    }
}


if( ! function_exists('houzez_getLatLongFromAddress') ) {
    function houzez_getLatLongFromAddress($address) {       
        

        if ( false === ( $agent_address = get_transient( 'agent-'.$address ) ) ) {

            $googlemap_api_key = houzez_option('googlemap_api_key');
            // geocoding api url
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$googlemap_api_key";
            // send api request
            $response = wp_safe_remote_get($url);
            
            if ( is_wp_error( $response ) ) {
                return false;
            }

            if ( ! empty( $response['body'] ) && is_ssl() ) {
                $response['body'] = str_replace( 'http:', 'https:', $response['body'] );
            } elseif ( is_ssl() ) {
                $response = str_replace( 'http:', 'https:', $response );
            }

            $json = json_decode($response['body']);

            if( $json->status == "OK" ) {
                $data['lat'] = $json->results[0]->geometry->location->lat;
                $data['lng'] = $json->results[0]->geometry->location->lng;

                set_transient( 'agent-'.$address, $data );

                return $data;
            }

        } else {
            return get_transient( 'agent-'.$address );
        }
    }
}


if (!function_exists('houzezOSM_getLatLngFromAddress')) {
    function houzezOSM_getLatLngFromAddress($address) {

        // Sanitize the address input
        $safe_address = sanitize_text_field($address);
        $transient_name = 'agent-' . md5($safe_address); // Use MD5 to ensure the transient name is unique and valid

        delete_transient($transient_name);

        // Check if the coordinates are already cached
        $cached_coordinates = get_transient($transient_name);
        if (false !== $cached_coordinates) { 
            // Return the cached coordinates
            return $cached_coordinates;
        } else { 
            // Prepare the request URL, making sure the address is URL-encoded
            $url = 'https://nominatim.openstreetmap.org/search?q=' . urlencode($safe_address) . '&format=json';


            // Send the API request
            $response = wp_safe_remote_get($url);

           

            // Check for errors in the response
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
                // Return false if there was an error with the request
                return false;
            }

            // Decode the response body
            $body = wp_remote_retrieve_body($response);
            $json = json_decode($body);

            // Check if the response is valid and contains coordinates
            if (empty($json) || empty($json[0]->lat) || empty($json[0]->lon)) {
                // Return false if the response is invalid
                return false;
            }

            // Extract latitude and longitude
            $coordinates = array(
                'lat' => $json[0]->lat,
                'lng' => $json[0]->lon,
            );

            // Cache the coordinates for future use to reduce API calls
            set_transient($transient_name, $coordinates, 12 * HOUR_IN_SECONDS);

            // Return the coordinates
            return $coordinates;
        }
    }
}

if (!function_exists('houzezMapbox_getLatLngFromAddress')) {
    /**
     * Get Latitude and Longitude from an address using Mapbox Geocoding API.
     *
     * @param string $address The address string to geocode.
     * @return array|false An array with 'lat' and 'lng' keys on success, false on failure.
     */
    function houzezMapbox_getLatLngFromAddress($address) {
        
        // Sanitize the address input
        $safe_address = sanitize_text_field($address);
        if(empty($safe_address)) {
            return false;
        }

        // Create a unique transient key based on the sanitized address
        $transient_name = 'houzez_mapbox_' . md5($safe_address);

        // Check if the coordinates are already cached
        $cached_coordinates = get_transient($transient_name);
        if (false !== $cached_coordinates) {
            // Return the cached coordinates
            return $cached_coordinates;
        } else {
            // Get Mapbox API Key from theme options
            $mapbox_api_key = houzez_option('mapbox_api_key');
            if(empty($mapbox_api_key)) {
                error_log('Mapbox API Key is not set in Houzez Options.');
                return false; // API key is required
            }

            // Prepare the request URL for Mapbox Geocoding API v6 (Forward Geocoding)
            $url = 'https://api.mapbox.com/search/geocode/v6/forward?q=' . urlencode($safe_address) . '&access_token=' . $mapbox_api_key . '&limit=1'; // Limit to 1 result

            // Send the API request using WordPress HTTP API
            $response = wp_safe_remote_get($url, array('timeout' => 10)); // Added timeout

            // Check for errors in the response
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
                error_log('Mapbox API request failed: ' . (is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_response_message($response)));
                return false; // Return false if there was an error with the request
            }

            // Decode the response body
            $body = wp_remote_retrieve_body($response);
            $json = json_decode($body);

            // Check if the response is valid and contains features with coordinates
            if (empty($json) || empty($json->features) || empty($json->features[0]->geometry->coordinates)) {
                error_log('Mapbox API response invalid or missing coordinates for address: ' . $safe_address);
                // Optionally cache 'false' to prevent repeated failed lookups for a short time
                // set_transient($transient_name, false, 1 * HOUR_IN_SECONDS); 
                return false; // Return false if the response is invalid
            }

            // Extract longitude and latitude (Mapbox returns [lng, lat])
            $coordinates = array(
                'lng' => $json->features[0]->geometry->coordinates[0],
                'lat' => $json->features[0]->geometry->coordinates[1],
            );

            // Cache the coordinates for future use (e.g., 12 hours)
            set_transient($transient_name, $coordinates, 12 * HOUR_IN_SECONDS);

            // Return the coordinates
            return $coordinates;
        }
    }
}