<?php
/**
 * Houzez Property Search Class
 *
 * Handles property search functionality with various filters
 */
class Houzez_Property_Search {

    /**
     * Initialize hooks.
     *
     * @return void
     */
    public static function init() {
        add_filter( 'houzez20_search_filters', [ __CLASS__, 'properties_search' ], 10, 1 );
        
        add_filter( 'houzez20_search_filters', [ __CLASS__, 'search_by_id' ], 10, 1 );
        add_filter( 'houzez20_search_filters', [ __CLASS__, 'search_by_user' ], 10, 1 );
        add_filter( 'houzez20_search_filters', [ __CLASS__, 'search_by_post_status' ], 10, 1 );
        add_filter( 'houzez20_search_filters', [ __CLASS__, 'search_by_user_role' ], 10, 1 );
        
        // Taxonomy search filters
        add_filter( 'houzez_taxonomy_search_filter', [ __CLASS__, 'search_status' ], 10, 1 );
        add_filter( 'houzez_taxonomy_search_filter', [ __CLASS__, 'search_type' ], 10, 1 );
        add_filter( 'houzez_taxonomy_search_filter', [ __CLASS__, 'search_country' ], 10, 1 );
        add_filter( 'houzez_taxonomy_search_filter', [ __CLASS__, 'search_states' ], 10, 1 );
        add_filter( 'houzez_taxonomy_search_filter', [ __CLASS__, 'search_cities' ], 10, 1 );
        add_filter( 'houzez_taxonomy_search_filter', [ __CLASS__, 'search_areas' ], 10, 1 );
        add_filter( 'houzez_taxonomy_search_filter', [ __CLASS__, 'search_features' ], 10, 1 );
        add_filter( 'houzez_taxonomy_search_filter', [ __CLASS__, 'search_label' ], 10, 1 );
        
        // Meta search filters
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_min_max_price' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_bedrooms' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_bathrooms' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_min_max_area' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_land_min_max_area' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_rooms' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_property_id' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_year_built' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_garage' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_custom_fields' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_currency' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_by_agents' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_by_agency' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_by_featured' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_radius' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_radius_api' ], 10, 1 );
        add_filter( 'houzez_meta_search_filter', [ __CLASS__, 'search_map_coordinates' ], 10, 1 );
        
        // AJAX handlers
        add_action( 'wp_ajax_nopriv_houzez_half_map_listings', [ __CLASS__, 'half_map_listings' ] );
        add_action( 'wp_ajax_houzez_half_map_listings', [ __CLASS__, 'half_map_listings' ] );

        // Register search form UI helpers
        add_action( 'houzez_search_hidden_fields', [ __CLASS__, 'half_map_search_hidden_fields' ] );
    }

    /**
     * Process property search filters
     *
     * @param array $filters Initial search filters
     * @return array Modified search query arguments
     */
    public static function properties_search( $filters ) {
        
        $tax_query = array();
        $meta_query = array();
        $search_qry = isset($filters) ? $filters : array();
        $keyword_array = '';
        $keyword_field = houzez_option('keyword_field');

        // Define allowed HTML tags - empty means strip all HTML
        $allowed_html = array();

        // Process keyword search
        if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
            $keyword = sanitize_text_field($_GET['keyword']);
            
            if ($keyword_field == 'prop_address') {
                $keyword_array = self::get_address_keyword_filter($keyword);
            } else if ($keyword_field == 'prop_city_state_county') {
                $tax_query[] = self::get_location_keyword_filter($keyword, $allowed_html);
            } else {
                $search_qry = self::keyword_search($filters);
            }
        }

        // Apply taxonomy filters
        $tax_query = apply_filters('houzez_taxonomy_search_filter', $tax_query);
        $tax_count = count($tax_query);

        if ($tax_count > 1) {
            $tax_query['relation'] = 'AND';
        }
        
        if ($tax_count > 0) {
            $search_qry['tax_query'] = $tax_query;
        }

        // Apply meta filters
        $meta_query = apply_filters('houzez_meta_search_filter', $meta_query);
        $meta_count = count($meta_query);
        
        if ($meta_count > 0 || !empty($keyword_array)) {
            $search_qry['meta_query'] = array(
                'relation' => 'AND',
                $keyword_array,
                array(
                    'relation' => 'AND',
                    $meta_query
                ),
            );
        }
        
        return $search_qry;
    }

    /**
     * Add taxonomy filter to query arguments
     *
     * @param array $query_arg Current taxonomy query arguments
     * @param string $taxonomy Taxonomy name
     * @param string $param_name GET parameter name
     * @param bool $check_array_item Check if first array item exists
     * @return array Modified query arguments
     */
    protected static function add_taxonomy_filter($query_arg, $taxonomy, $param_name, $check_array_item = true) {
        if (isset($_GET[$param_name]) && !empty($_GET[$param_name])) {
            $param_value = $_GET[$param_name];
            
            // Sanitize input based on type while preserving UTF-8 characters
            if (is_array($param_value)) {
                $param_value = array_map(function($value) {
                    // Use wp_unslash to remove slashes and preserve UTF-8 characters
                    $value = wp_unslash($value);
                    // Remove any HTML tags but preserve UTF-8
                    $value = wp_strip_all_tags($value);
                    // Trim whitespace
                    return trim($value);
                }, $param_value);
            } else {
                // Use wp_unslash to remove slashes and preserve UTF-8 characters
                $param_value = wp_unslash($param_value);
                // Remove any HTML tags but preserve UTF-8
                $param_value = wp_strip_all_tags($param_value);
                // Trim whitespace
                $param_value = trim($param_value);
            }
            
            // Additional validations
            if (($check_array_item && is_array($param_value) && empty($param_value[0])) || 
                $param_value == 'all' || 
                $param_value == '-1' || 
                ($check_array_item && is_array($param_value) && $param_value[0] == '-1')) {
                return $query_arg;
            }
            
            $query_arg[] = array(
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => $param_value
            );
        }
        return $query_arg;
    }

    /**
     * Process keyword search
     *
     * @param array $search_qry Current search query
     * @return array Modified search query
     */
    private static function keyword_search($search_qry) {

		if (isset($_GET['keyword'])) {
			$keyword = trim($_GET['keyword']);

			if (!empty($keyword)) {
				$search_qry['s'] = $keyword;
				return $search_qry;
			}
		}
		return $search_qry;
	}

    /**
     * Get keyword filter for address searches
     * 
     * @param string $keyword The search keyword
     * @return array Address meta query
     */
    private static function get_address_keyword_filter($keyword) {
        $allowed_html = array();
		$property_id_prefix = houzez_option('property_id_prefix');

		$meta_keywork = wp_kses(stripcslashes($keyword), $allowed_html);
        $address_array = array(
            'key' => 'fave_property_map_address',
            'value' => $meta_keywork,
            'type' => 'CHAR',
            'compare' => 'LIKE',
        );

        $street_array = array(
            'key' => 'fave_property_address',
            'value' => $meta_keywork,
            'type' => 'CHAR',
            'compare' => 'LIKE',
        );

        $zip_array = array(
            'key' => 'fave_property_zip',
            'value' => $meta_keywork,
            'type' => 'CHAR',
            'compare' => '=',
        );

        $propid_array = array(
            'key' => 'fave_property_id',
            'value' => str_replace($property_id_prefix, "", $meta_keywork),
            'type' => 'CHAR',
            'compare' => '=',
        );

        $keyword_array = array(
            'relation' => 'OR',
            $address_array,
            $street_array,
            $propid_array,
            $zip_array
        );

        return $keyword_array;
    }
    
    /**
     * Get keyword filter for location-based searches
     * 
     * @param string $keyword The search keyword
     * @param array $allowed_html Allowed HTML tags for sanitization
     * @return array Location taxonomy query
     */
    private static function get_location_keyword_filter($keyword, $allowed_html) {
        // Use wp_strip_all_tags instead of sanitize_title to preserve UTF-8 characters
        $cleaned_keyword = wp_strip_all_tags(wp_kses($keyword, $allowed_html));
        $cleaned_keyword = trim($cleaned_keyword);
        
        // Keep original case for accurate matching and also add lowercase version
        $taxlocation[] = $cleaned_keyword;
        $taxlocation[] = mb_strtolower($cleaned_keyword, 'UTF-8');
        
        $_tax_query = array();
		        $_tax_query['relation'] = 'OR';

		        $_tax_query[] = array(
		            'taxonomy' => 'property_area',
		            'field' => 'name',
		            'terms' => $taxlocation
		        );

		        $_tax_query[] = array(
		            'taxonomy' => 'property_city',
		            'field' => 'name',
		            'terms' => $taxlocation
		        );

		        $_tax_query[] = array(
		            'taxonomy' => 'property_state',
		            'field' => 'name',
		            'terms' => $taxlocation
		        );
        
        return $_tax_query;
    }

    /**
     * Filter search results by property status
     * 
     * @param array $query_arg Current taxonomy query arguments
     * @return array Modified taxonomy query arguments
     */
    public static function search_status($query_arg) {
        $query_arg = self::add_taxonomy_filter($query_arg, 'property_status', 'status', true);

        // Exclude property with exclude statuses
        $all_statuses = houzez_option('search_exclude_status');

        if (!empty($all_statuses)) {
            $query_arg[] = array(
                array(
                    'taxonomy' => 'property_status',
                    'field'    => 'id',
                    'terms'    => $all_statuses,
                    'operator' => 'NOT IN'
                )
            );
        }

        return $query_arg;
    }

    /**
     * Filter search results by property type
     * 
     * @param array $query_arg Current taxonomy query arguments
     * @return array Modified taxonomy query arguments
     */
    public static function search_type($query_arg) {
        return self::add_taxonomy_filter($query_arg, 'property_type', 'type', true);
    }

    /**
     * Filter search results by property country
     * 
     * @param array $query_arg Current taxonomy query arguments
     * @return array Modified taxonomy query arguments
     */
    public static function search_country($query_arg) {
        return self::add_taxonomy_filter($query_arg, 'property_country', 'country', true);
    }

    /**
     * Filter search results by property state - handles both 'state' and 'states' parameters
     * 
     * @param array $query_arg Current taxonomy query arguments
     * @return array Modified taxonomy query arguments
     */
    public static function search_states($query_arg) {
        $query_arg = self::add_taxonomy_filter($query_arg, 'property_state', 'states', true);
        $query_arg = self::add_taxonomy_filter($query_arg, 'property_state', 'state', true);
        return $query_arg;
    }

    /**
     * Filter search results by property city - handles both 'city' and 'location' parameters
     * 
     * @param array $query_arg Current taxonomy query arguments
     * @return array Modified taxonomy query arguments
     */
    public static function search_cities($query_arg) {
        $query_arg = self::add_taxonomy_filter($query_arg, 'property_city', 'location', true);
        $query_arg = self::add_taxonomy_filter($query_arg, 'property_city', 'city', true);
        return $query_arg;
    }

    /**
     * Filter search results by property area - handles both 'area' and 'areas' parameters
     * 
     * @param array $query_arg Current taxonomy query arguments
     * @return array Modified taxonomy query arguments
     */
    public static function search_areas($query_arg) {
        $query_arg = self::add_taxonomy_filter($query_arg, 'property_area', 'areas', true);
        $query_arg = self::add_taxonomy_filter($query_arg, 'property_area', 'area', true);
        return $query_arg;
    }

    /**
     * Filter search results by property features
     * 
     * @param array $query_arg Current taxonomy query arguments
     * @return array Modified taxonomy query arguments
     */
    public static function search_features($query_arg) {
        if (isset($_GET['feature']) && !empty($_GET['feature'][0])) {
            if (is_array($_GET['feature'])) {
                $features = array_map('sanitize_text_field', $_GET['feature']);

                // Iterate over each feature and add it to the $query_arg array
                foreach ($features as $feature) {
                    $query_arg[] = array(
                        'taxonomy' => 'property_feature',
                        'field' => 'slug',
                        'terms' => $feature,
                    );
                }
            }
        }
        return $query_arg;
    }

    /**
     * Filter search results by property label
     * 
     * @param array $query_arg Current taxonomy query arguments
     * @return array Modified taxonomy query arguments
     */
    public static function search_label($query_arg) {
        return self::add_taxonomy_filter($query_arg, 'property_label', 'label', true);
    }

    /**
     * Add a numeric-range meta_query clause (BETWEEN, >= or <=)
     *
     * @param array $meta_query The meta query to add the clause to
     * @param string $meta_key The meta key to query
     * @param mixed $min_raw The minimum value (raw)
     * @param mixed $max_raw The maximum value (raw)
     * @return array Modified meta query
     */
    protected static function add_meta_range_clause( array &$meta_query, $meta_key, $min_raw, $max_raw ) {
        $min = ( $min_raw === '' || $min_raw === 'any' ) ? null : doubleval( houzez_clean( $min_raw ) );
        $max = ( $max_raw === '' || $max_raw === 'any' ) ? null : doubleval( houzez_clean( $max_raw ) );

        if ( $min !== null && $max !== null ) {
            if ( $min >= 0 && $max >= $min ) {
                $meta_query[] = array(
                    'key'     => $meta_key,
                    'value'   => array( $min, $max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            }
        } elseif ( $min !== null ) {
            if ( $min >= 0 ) {
                $meta_query[] = array(
                    'key'     => $meta_key,
                    'value'   => $min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            }
        } elseif ( $max !== null ) {
            if ( $max >= 0 ) {
                $meta_query[] = array(
                    'key'     => $meta_key,
                    'value'   => $max,
                    'type'    => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }
        
        return $meta_query;
    }

    /**
     * Filter properties by price range
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_min_max_price($meta_query) {
        $min_price = isset($_GET['min-price']) ? sanitize_text_field($_GET['min-price']) : '';
        $max_price = isset($_GET['max-price']) ? sanitize_text_field($_GET['max-price']) : '';
        
        return self::add_meta_range_clause($meta_query, 'fave_property_price', $min_price, $max_price);
    }

    /**
     * Filter properties by number of bedrooms
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_bedrooms($meta_query) {
        // Check for the single 'bedrooms' parameter
        if (isset($_GET['bedrooms']) && $_GET['bedrooms'] !== "" && $_GET['bedrooms'] !== 'any') {
            $search_criteria = '=';
            $type = 'NUMERIC';

            // Allowed operators
            $allowed_operators = ['=', '>', '<', '>=', '<='];

            if (isset($_GET['bedrooms-operator']) && in_array($_GET['bedrooms-operator'], $allowed_operators, true)) {
                $search_criteria = $_GET['bedrooms-operator'];
            } else {
                // fallback to theme option
                $beds_baths_search = houzez_option('beds_baths_search', 'equal');

                if ($beds_baths_search === 'greater') {
                    $search_criteria = '>=';
                } elseif ($beds_baths_search === 'like') {
                    $search_criteria = 'LIKE';
                    $type = 'CHAR';
                }
            }

            $bedrooms = sanitize_text_field($_GET['bedrooms']);
            $meta_query[] = array(
                'key' => 'fave_property_bedrooms',
                'value' => $bedrooms,
                'type' => $type,
                'compare' => $search_criteria,
            );

            return $meta_query;
        }

        // If no single 'bedrooms' parameter, check for min/max range
        $min_beds = isset($_GET['min-beds']) ? sanitize_text_field($_GET['min-beds']) : '';
        $max_beds = isset($_GET['max-beds']) ? sanitize_text_field($_GET['max-beds']) : '';

        if (!empty($min_beds) || !empty($max_beds)) {
            return self::add_meta_range_clause($meta_query, 'fave_property_bedrooms', $min_beds, $max_beds);
        }

        return $meta_query;
    }

    
    /**
     * Filter properties by number of bathrooms
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_bathrooms($meta_query) {
        // Check for the single 'bathrooms' parameter
        if (isset($_GET['bathrooms']) && $_GET['bathrooms'] !== "" && $_GET['bathrooms'] !== 'any') {
            $search_criteria = '=';
            $type = 'NUMERIC';

            // Allowed operators
            $allowed_operators = ['=', '>', '<', '>=', '<='];

            if (isset($_GET['bathrooms-operator']) && in_array($_GET['bathrooms-operator'], $allowed_operators, true)) {
                $search_criteria = $_GET['bathrooms-operator'];
            } else {
                // fallback to theme option
                $beds_baths_search = houzez_option('beds_baths_search', 'equal');

                if ($beds_baths_search === 'greater') {
                    $search_criteria = '>=';
                } elseif ($beds_baths_search === 'like') {
                    $search_criteria = 'LIKE';
                    $type = 'CHAR';
                }
            }

            $bathrooms = sanitize_text_field($_GET['bathrooms']);
            $meta_query[] = array(
                'key' => 'fave_property_bathrooms',
                'value' => $bathrooms,
                'type' => $type,
                'compare' => $search_criteria,
            );

            return $meta_query;
        }

        // If no single 'bathrooms' parameter, check for min/max range
        $min_baths = isset($_GET['min-baths']) ? sanitize_text_field($_GET['min-baths']) : '';
        $max_baths = isset($_GET['max-baths']) ? sanitize_text_field($_GET['max-baths']) : '';

        if (!empty($min_baths) || !empty($max_baths)) {
            return self::add_meta_range_clause($meta_query, 'fave_property_bathrooms', $min_baths, $max_baths);
        }

        return $meta_query;
    }


    /**
     * Filter properties by area size
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_min_max_area($meta_query) {
        $min_area = isset($_GET['min-area']) ? sanitize_text_field($_GET['min-area']) : '';
        $max_area = isset($_GET['max-area']) ? sanitize_text_field($_GET['max-area']) : '';
        
        return self::add_meta_range_clause($meta_query, 'fave_property_size', $min_area, $max_area);
    }

    /**
     * Filter properties by land area size
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_land_min_max_area($meta_query) {
        $min_land_area = isset($_GET['min-land-area']) ? sanitize_text_field($_GET['min-land-area']) : '';
        $max_land_area = isset($_GET['max-land-area']) ? sanitize_text_field($_GET['max-land-area']) : '';
        
        return self::add_meta_range_clause($meta_query, 'fave_property_land', $min_land_area, $max_land_area);
    }

    /**
     * Filter properties by number of rooms
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_rooms($meta_query) {
        if (isset($_GET['rooms']) && $_GET['rooms'] !== "" && $_GET['rooms'] !== 'any') {
            $search_criteria = '=';
            $type = 'NUMERIC';

            // Allowed operators
            $allowed_operators = ['=', '>', '<', '>=', '<='];

            if (isset($_GET['rooms-operator']) && in_array($_GET['rooms-operator'], $allowed_operators, true)) {
                $search_criteria = $_GET['rooms-operator'];
            } else {
                // fallback to theme option
                $beds_baths_search = houzez_option('beds_baths_search', 'equal');

                if ($beds_baths_search === 'greater') {
                    $search_criteria = '>=';
                } elseif ($beds_baths_search === 'like') {
                    $search_criteria = 'LIKE';
                    $type = 'CHAR';
                }
            }

            $rooms = sanitize_text_field($_GET['rooms']);
            $meta_query[] = array(
                'key' => 'fave_property_rooms',
                'value' => $rooms,
                'type' => $type,
                'compare' => $search_criteria,
            );
        }

        return $meta_query;
    }


    /**
     * Filter properties by property ID
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_property_id($meta_query) {
        if (isset($_GET['property_id']) && !empty($_GET['property_id'])) {
            $property_id_prefix = houzez_option('property_id_prefix');
            $propid = trim(sanitize_text_field($_GET['property_id']));
            $propid = str_replace($property_id_prefix, "", $propid);
            
            $meta_query[] = array(
                'key' => 'fave_property_id',
                'value' => $propid,
                'compare' => 'IN',
            );
        }
        return $meta_query;
    }


    /**
     * Filter properties by year built
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_year_built($meta_query) {
        if (isset($_GET['year-built']) && $_GET['year-built'] !== "" && $_GET['year-built'] !== 'any') {
            $search_criteria = 'LIKE';
            $type = 'CHAR';

            // Allowed operators
            $allowed_operators = ['=', '>', '<', '>=', '<='];

            if (isset($_GET['year-built-operator']) && in_array($_GET['year-built-operator'], $allowed_operators, true)) {
                $search_criteria = $_GET['year-built-operator'];
            }

            $year_built = sanitize_text_field($_GET['year-built']);
            $meta_query[] = array(
                'key'     => 'fave_property_year',
                'value'   => $year_built,
                'type'    => $type,
                'compare' => $search_criteria,
            );
        }

        return $meta_query;
    }


    /**
     * Filter properties by number of garages
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_garage($meta_query) {
        if (isset($_GET['garage']) && $_GET['garage'] !== "" && $_GET['garage'] !== 'any') {
            $search_criteria = '=';
            $type = 'NUMERIC';

            // Allowed operators
            $allowed_operators = ['=', '>', '<', '>=', '<='];

            if (isset($_GET['garage-operator']) && in_array($_GET['garage-operator'], $allowed_operators, true)) {
                $search_criteria = $_GET['garage-operator'];
            }

            $garage = sanitize_text_field($_GET['garage']);
            $meta_query[] = array(
                'key'     => 'fave_property_garage',
                'value'   => $garage,
                'type'    => $type,
                'compare' => $search_criteria,
            );
        }

        return $meta_query;
    }


    /**
     * Filter properties by custom fields created in the Fields Builder
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_custom_fields($meta_query) {
        if (class_exists('Houzez_Fields_Builder')) {
            $fields_array = Houzez_Fields_Builder::get_form_fields();
            
            if (!empty($fields_array)) {
                $builtInFields = Houzez_Fields_Builder::builtInFields();
                
                foreach ($fields_array as $value) {
                    $field_title = $value->label;
                    $field_name = $value->field_id;
                    $is_search = $value->is_search;
                    $field_type = $value->type;

                    if ($is_search == 'yes' && !in_array($field_name, $builtInFields)) {
                        if (isset($_GET[$field_name]) && !empty($_GET[$field_name])) {
                            // Check if the input is an array
                            if (is_array($_GET[$field_name])) {
                                $field_value = array_map(function($item) {
                                    // Sanitize each item in the array
                                    return sanitize_text_field($item);
                                }, $_GET[$field_name]);
                            } else {
                                // If it's not an array, sanitize the string directly
                                $field_value = sanitize_text_field($_GET[$field_name]);
                            }

                            $compare = 'LIKE';
                            if ($field_type == 'checkbox_list' || $field_type == 'multiselect') {
                                $compare = 'IN';
                            }

                            $meta_query[] = array(
                                'key' => 'fave_' . $field_name,
                                'value' => $field_value,
                                'type' => 'CHAR',
                                'compare' => $compare,
                            );
                        }
                    }
                }
            }
        }
        return $meta_query;
    }

    /**
     * Filter properties by currency when multi-currency is enabled
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_currency($meta_query) {
        $multi_currency = houzez_option('multi_currency');
        
        if ($multi_currency == 1) {
            if (isset($_GET['currency']) && !empty($_GET['currency'])) {
                $currency = sanitize_text_field($_GET['currency']);
                
                $meta_query[] = array(
                    'key' => 'fave_currency',
                    'value' => $currency,
                    'type' => 'CHAR',
                    'compare' => '=',
                );
            }
        }
        
        return $meta_query;
    }

    /**
     * Filter properties by specific post IDs
     * 
     * @param array $search_query Current search query arguments
     * @return array Modified search query arguments
     */
    public static function search_by_id($search_query) {
        if (isset($_GET['ids']) && !empty($_GET['ids'])) {
            $ids = is_array($_GET['ids']) ? $_GET['ids'] : explode(',', $_GET['ids']);
            
            // Sanitize and ensure all values are integers
            $ids = array_map(function($id) {
                return intval(sanitize_text_field($id));
            }, $ids);
            
            $search_query['post__in'] = $ids;
        }
        return $search_query;
    }

    /**
     * Filter properties by author (user ID)
     * 
     * @param array $search_query Current search query arguments
     * @return array Modified search query arguments
     */
    public static function search_by_user($search_query) {
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $search_query['author'] = intval(sanitize_text_field($_GET['user_id']));
        }
        return $search_query;
    }
    
    /**
     * Filter properties by post status
     * 
     * @param array $search_query Current search query arguments
     * @return array Modified search query arguments
     */
    public static function search_by_post_status($search_query) {
        if (isset($_GET['post_status'])) {
            if (is_array($_GET['post_status'])) {
                $search_query['post_status'] = array_map('sanitize_text_field', $_GET['post_status']);
            } else {
                $search_query['post_status'] = sanitize_text_field($_GET['post_status']);
            }
        }
        return $search_query;
    }
    
    /**
     * Filter properties by agent
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_by_agents($meta_query) {
        if (isset($_GET['agent_id']) && !empty($_GET['agent_id'])) {
            $agent_ids = is_array($_GET['agent_id']) ? $_GET['agent_id'] : explode(',', $_GET['agent_id']);
            
            // Sanitize and ensure all values are integers
            $agent_ids = array_map(function($id) {
                return intval(sanitize_text_field($id));
            }, $agent_ids);
            
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key' => 'fave_agents',
                    'value' => $agent_ids,
                    'compare' => 'IN'
                ),
                array(
                    'key' => 'fave_agent_display_option',
                    'value' => 'agent_info',
                    'compare' => '='
                )
            );
        }
        return $meta_query;
    }
    
    /**
     * Filter properties by agency
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_by_agency($meta_query) {
        if (isset($_GET['agency_id']) && !empty($_GET['agency_id'])) {
            $agency_ids = is_array($_GET['agency_id']) ? $_GET['agency_id'] : explode(',', $_GET['agency_id']);
            
            // Sanitize and ensure all values are integers
            $agency_ids = array_map(function($id) {
                return intval(sanitize_text_field($id));
            }, $agency_ids);
            
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key' => 'fave_property_agency',
                    'value' => $agency_ids,
                    'compare' => 'IN'
                ),
                array(
                    'key' => 'fave_agent_display_option',
                    'value' => 'agency_info',
                    'compare' => '='
                )
            );
        }
        return $meta_query;
    }

    /**
     * Filter properties by featured status
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_by_featured($meta_query) {
        if (isset($_GET['featured'])) {
            $featured = sanitize_text_field($_GET['featured']);
            
            if ($featured === '1') {
                // Get featured properties
                $meta_query[] = array(
                    'key' => 'fave_featured',
                    'value' => '1',
                    'compare' => '='
                );
            } elseif ($featured === '0') {
                // Get non-featured properties
                $meta_query[] = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'fave_featured',
                        'value' => '0',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'fave_featured',
                        'compare' => 'NOT EXISTS'
                    )
                );
            }
        }
        return $meta_query;
    }
    
    /**
     * Filter properties by user role
     * 
     * @param array $search_query Current search query arguments
     * @return array Modified search query arguments
     */
    public static function search_by_user_role($search_query) {
        if (isset($_GET['user_role']) && !empty($_GET['user_role'])) {
            $role = sanitize_text_field($_GET['user_role']);
            
            // Get all users with the specified role
            $users = get_users(array('role' => $role));
            
            if (!empty($users)) {
                // Get array of user IDs
                $user_ids = array_map(function($user) {
                    return $user->ID;
                }, $users);
                
                // Add author filter to query
                if (!empty($user_ids)) {
                    $search_query['author__in'] = $user_ids;
                }
            } else {
                // If no users found with this role, return no results
                $search_query['author__in'] = array(0); // This will return no results
            }
        }
        return $search_query;
    }

    /**
     * Filter properties by viewport search
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_map_coordinates($meta_query) {
        
        $ne_lat = isset($_GET['ne_lat']) ? (float)$_GET['ne_lat'] : false;
        $ne_lng = isset($_GET['ne_lng']) ? (float)$_GET['ne_lng'] : false;
        $sw_lat = isset($_GET['sw_lat']) ? (float)$_GET['sw_lat'] : false;
        $sw_lng = isset($_GET['sw_lng']) ? (float)$_GET['sw_lng'] : false;
        $zoom = isset($_GET['zoom']) ? (int)$_GET['zoom'] : false;

        if (!$ne_lat || !$ne_lng || !$sw_lat || !$sw_lng || !$zoom) {
            return $meta_query;
        }

        $meta_query[] = array(
            'key' => 'houzez_geolocation_lat',
            'value' => array($sw_lat, $ne_lat),
            'type' => 'DECIMAL(10,7)',
            'compare' => 'BETWEEN',
        );

        $meta_query[] = array(
            'key' => 'houzez_geolocation_long',
            'value' => array($sw_lng, $ne_lng),
            'type' => 'DECIMAL(10,7)',
            'compare' => 'BETWEEN',
        );

        return $meta_query;
    }
    

    /**
     * Filter properties by radius search
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_radius($meta_query) {

        // Skip radius search if viewport coordinates are available and not empty
        if (isset($_GET['ne_lat']) && !empty($_GET['ne_lat']) && 
            isset($_GET['ne_lng']) && !empty($_GET['ne_lng']) && 
            isset($_GET['sw_lat']) && !empty($_GET['sw_lat']) && 
            isset($_GET['sw_lng']) && !empty($_GET['sw_lng'])) {
            return $meta_query;
        }

        $search_location = isset($_GET['search_location']) ? sanitize_text_field($_GET['search_location']) : false;
        $latitude = isset($_GET['lat']) ? (float)$_GET['lat'] : false;
        $longitude = isset($_GET['lng']) ? (float)$_GET['lng'] : false;
        $radius = isset($_GET['radius']) ? (int)$_GET['radius'] : false;
        $use_radius = isset($_GET['use_radius']) ? sanitize_text_field($_GET['use_radius']) : '';

        // If required parameters are missing, return the unmodified query
        if (!($use_radius && $latitude && $longitude && $radius) || !$search_location) {
            return $meta_query;
        }

        // Determine earth radius unit (km or miles)
        $radius_unit = houzez_option('radius_unit');
        if ($radius_unit == 'km') {
            $earth_radius_num = 111;
        } elseif ($radius_unit == 'mi') {
            $earth_radius_num = 69;
        } else {
            $earth_radius_num = 111; // default to km
        }

        // Add latitude bounds
        $meta_query[] = array(
            'key' => 'houzez_geolocation_lat',
            'value' => array($latitude - $radius / $earth_radius_num, $latitude + $radius / $earth_radius_num),
            'type' => 'DECIMAL(10,7)',
            'compare' => 'BETWEEN',
        );

        // Add longitude bounds (adjusted for latitude)
        $meta_query[] = array(
            'key' => 'houzez_geolocation_long',
            'value' => array(
                $longitude - $radius / (cos(deg2rad($latitude)) * $earth_radius_num), 
                $longitude + $radius / (cos(deg2rad($latitude)) * $earth_radius_num)
            ),
            'type' => 'DECIMAL(10,7)',
            'compare' => 'BETWEEN',
        );

        return $meta_query;
    }

    /**
     * Filter properties by radius search for API requests
     * Uses different parameter names than the frontend search
     * 
     * @param array $meta_query Current meta query arguments
     * @return array Modified meta query arguments
     */
    public static function search_radius_api($meta_query) {
        $latitude = isset($_GET['latitude']) ? (float)$_GET['latitude'] : false;
        $longitude = isset($_GET['longitude']) ? (float)$_GET['longitude'] : false;
        $radius = isset($_GET['radius']) ? (int)$_GET['radius'] : false;

        // If required parameters are missing, return the unmodified query
        if (!($latitude && $longitude && $radius)) {
            return $meta_query;
        }

        // Determine earth radius unit (km or miles)
        $radius_unit = houzez_option('radius_unit');
        if ($radius_unit == 'km') {
            $earth_radius_num = 111;
        } elseif ($radius_unit == 'mi') {
            $earth_radius_num = 69;
        } else {
            $earth_radius_num = 111; // default to km
        }

        // Add latitude bounds
        $meta_query[] = array(
            'key' => 'houzez_geolocation_lat',
            'value' => array($latitude - $radius / $earth_radius_num, $latitude + $radius / $earth_radius_num),
            'type' => 'DECIMAL(10,7)',
            'compare' => 'BETWEEN',
        );

        // Add longitude bounds (adjusted for latitude)
        $meta_query[] = array(
            'key' => 'houzez_geolocation_long',
            'value' => array(
                $longitude - $radius / (cos(deg2rad($latitude)) * $earth_radius_num), 
                $longitude + $radius / (cos(deg2rad($latitude)) * $earth_radius_num)
            ),
            'type' => 'DECIMAL(10,7)',
            'compare' => 'BETWEEN',
        );

        return $meta_query;
    }

    /**
     * Handle AJAX requests for half map listings
     */
    public static function half_map_listings() {
        $tax_query = array();
        $meta_query = array();
        $keyword_array = '';

        $keyword_field = houzez_option('keyword_field');
        $number_of_prop = absint( houzez_option( 'search_num_posts' ) ) ?: 9;
        $paged = absint( $_GET['paged'] ?? 1 );

        $search_qry = array(
            'post_type' => 'property',
            'posts_per_page' => $number_of_prop,
            'paged' => $paged,
            'post_status' => 'publish'
        );

        $search_qry = apply_filters('houzez_sold_status_filter', $search_qry);

        // Layout parameters
        // Define valid item layouts to prevent directory traversal
        $valid_item_layouts = array('v1', 'v2', 'v3', 'v4', 'v5', 'v6', 'v6-skeleton', 'v7', 'none',
                                   'list-v1', 'list-v2', 'list-v4', 'list-v7',
                                   'list-v1-half-map', 'list-v2-half-map',
                                   'list-v4-half-map', 'list-v7-half-map');

        $item_layout = sanitize_text_field( $_GET['item_layout'] ?? 'v1' );
        // Validate against whitelist to prevent LFI
        if (!in_array($item_layout, $valid_item_layouts, true)) {
            $item_layout = 'v1'; // Default fallback
        }

        $layout_css    = sanitize_text_field( $_GET['layout_css']    ?? 'listing-view grid-view row row-cols-1 row-cols-md-2 gy-4 gx-4 mx-0' );
        $layout_view   = sanitize_text_field( $_GET['layout_view']   ?? 'grid' );
        $is_pagination = filter_var( $_GET['is_pagination_request'] ?? false, FILTER_VALIDATE_BOOLEAN );
        $auto_load     = (bool) houzez_option( 'auto_load_map_listings', 1 );

        // Process keyword search - using same methods as properties_search
        if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
            $keyword = sanitize_text_field($_GET['keyword']);
            
            if ($keyword_field == 'prop_address') {
                $keyword_array = self::get_address_keyword_filter($keyword);
            } else if ($keyword_field == 'prop_city_state_county') {
                $tax_query[] = self::get_location_keyword_filter($keyword, array());
            } else {
                $search_qry = self::keyword_search($search_qry);
            }
        }

        // Apply taxonomy and meta filters
        $tax_query = apply_filters('houzez_taxonomy_search_filter', $tax_query);
        $tax_count = count($tax_query);
        
        if ($tax_count > 1) {
            $tax_query['relation'] = 'AND';
        }

        if ($tax_count > 0) {
            $search_qry['tax_query'] = $tax_query;
        }

        $meta_query = apply_filters('houzez_meta_search_filter', $meta_query);
        $meta_count = count($meta_query);
        
        if ($meta_count > 0 || !empty($keyword_array)) {
            $search_qry['meta_query'] = array(
                'relation' => 'AND',
                $keyword_array,
                array(
                    'relation' => 'AND',
                    $meta_query
                ),
            );
        }

        $search_qry = houzez_prop_sort($search_qry);
        
        // Map data
        $properties_data = [];
        if ( $auto_load && ! $is_pagination ) {
            $map_qry = $search_qry;
            $map_qry['posts_per_page']           = absint( houzez_option( 'search_num_map_posts', 200 ) );
            $map_qry['paged']                    = 1;
            $map_qry['fields']                   = 'ids';
            $map_qry['no_found_rows']            = true;
            $map_qry['update_post_meta_cache']   = false;
            $map_qry['update_post_term_cache']   = false;

            $map_qry = houzez_prop_sort( $map_qry );
            $map_query = new WP_Query( $map_qry );

            foreach ( $map_query->posts as $post_id ) {
                if ( $data = houzez_get_property_map_data( $post_id ) ) {
                    $properties_data[] = $data;
                }
            }
            wp_reset_postdata();
        } // End map listings data

        // Main listings query
        $query = new WP_Query( $search_qry );
        
        ob_start();
        printf(
            '<div class="%s" role="list" data-view="%s" data-layout="%s" data-css="%s">',
            esc_attr( $layout_css ),
            esc_attr( $layout_view ),
            esc_attr( $item_layout ),
            esc_attr( $layout_css )
        );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                if ( ! $auto_load ) {
                    $post_id = get_the_ID();
                    if ( $data = houzez_get_property_map_data( $post_id ) ) {
                        $properties_data[] = $data;
                    }
                }

                get_template_part( 'template-parts/listing/item', $item_layout );
            }
        } else {
            echo '<div class="search-no-results-found flex-grow-1 text-center mx-4">' . esc_html__("We didn't find any results", 'houzez') . '</div>';
        }
        wp_reset_postdata();
        echo '</div>';

        // Pagination
        houzez_ajax_pagination( $query->max_num_pages );

        $html         = ob_get_clean();
        $total        = $query->found_posts;
        $encoded_qry  = base64_encode( json_encode( $query->query ) );

        // Preserve referer URI
        $search_uri = '';
        if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
            $parts = explode( '/?', wp_unslash( $_SERVER['HTTP_REFERER'] ), 2 );
            if ( ! empty( $parts[1] ) ) {
                $search_uri = $parts[1];
            }
        }

        $response = [
            'properties'             => $properties_data,
            'total_results'          => $total,
            'propHtml'               => $html,
            'query'                  => $encoded_qry,
            'search_uri'             => $search_uri,
        ];

        if ( $auto_load ) {
            $response['getProperties'] = ! $is_pagination;
        } else {
            $response['getProperties']          = true;
        }
    
        wp_send_json( $response );
    }


    /**
     * Render a custom search field based on its type
     * This method outputs HTML directly and is meant to be used in search form templates
     * 
     * @param string $key The field key/slug to render
     * @return void
     */
    public static function get_custom_search_field($key) {
        if(!class_exists('Houzez_Fields_Builder')) {
            return;
        }
        
        $field_array = Houzez_Fields_Builder::get_field_by_slug($key);
        
        // Validate field_array
        if(empty($field_array) || !is_array($field_array) || !isset($field_array['label'], $field_array['field_id'], $field_array['type'])) {
            return;
        }
        
        $field_title = houzez_wpml_translate_single_string($field_array['label']);
        $field_name = $field_array['field_id'];
        $field_type = $field_array['type'];
        $get_field_name = '';
    
        if($field_type == 'select') { ?>

            <div class="form-group">
                <select data-size="5" name="<?php echo esc_attr($field_name);?>" class="selectpicker <?php houzez_ajax_search(); ?> form-control bs-select-hidden" title="<?php echo esc_attr($field_title); ?>" data-live-search="true">
                    
                    <option value=""><?php echo esc_attr($field_title); ?></option>
                    <?php
                    if(isset($_GET[$field_name])) {
                        $get_field_name = $_GET[$field_name];
                    }
                    $options = unserialize($field_array['fvalues']);
                    
                    // Validate unserialized data
                    if(!is_array($options)) {
                        $options = array();
                    }
                    
                    foreach ($options as $key => $val) {

                        if(!empty($key)) {
                            $val = houzez_wpml_translate_single_string($val);
                            $key = houzez_wpml_translate_single_string($key);
                            echo '<option class="'.sanitize_title_with_dashes($key).'" '.selected( $key, $get_field_name, false).' value="'.esc_attr($key).'">'.esc_attr($val).'</option>';
                        }
                    }
                    ?>

                </select><!-- selectpicker -->
            </div>

        <?php
        } else if($field_type == 'multiselect') { ?>

            <div class="form-group">
                <select data-size="5" name="<?php echo esc_attr($field_name).'[]';?>" data-size="5" class="selectpicker <?php houzez_ajax_search(); ?> form-control bs-select-hidden" title="<?php echo esc_attr($field_title); ?>" data-live-search="true" data-selected-text-format="count > 1" data-actions-box="true" data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-count-selected-text="{0} <?php echo houzez_option('srh_item_selected', 'items selected'); ?>" multiple>
                
                    <?php
                    $options = unserialize($field_array['fvalues']);
                    
                    // Validate unserialized data
                    if(!is_array($options)) {
                        $options = array();
                    }
                    
                    if(isset($_GET[$field_name])) {
                        $get_field_name = $_GET[$field_name];
                        // Ensure multiselect values are always an array
                        if(!is_array($get_field_name)) {
                            $get_field_name = !empty($get_field_name) ? array($get_field_name) : array();
                        }
                    }

                    foreach ($options as $key => $val) {

                        $selected = ( ! empty( $get_field_name ) && is_array($get_field_name) && in_array( $key, $get_field_name ) ) ? 'selected' : '';

                        if(!empty($key)) {
                            $val = houzez_wpml_translate_single_string($val);
                            $key = houzez_wpml_translate_single_string($key);
                            echo '<option '.esc_attr($selected).' value="'.esc_attr($key).'">'.esc_attr($val).'</option>';
                        }
                    }
                    ?>

                </select><!-- selectpicker -->
            </div>

        <?php
        } else if($field_type == 'radio') { ?>

            <div class="form-group">
                <select data-size="5" name="<?php echo esc_attr($field_name);?>" class="selectpicker <?php houzez_ajax_search(); ?> form-control bs-select-hidden" title="<?php echo esc_attr($field_title); ?>" data-live-search="true">
                    
                    <option value=""><?php echo esc_attr($field_title); ?></option>
                    <?php
                    if(isset($_GET[$field_name])) {
                        $get_field_name = $_GET[$field_name];
                    }
                    $options    = unserialize($field_array['fvalues']);
                    
                    // Handle both string and array formats
                    if(is_string($options)) {
                        $options    = explode( ',', $options );
                        $options    = array_filter( array_map( 'trim', $options ) );
                        $radios     = array_combine( $options, $options );
                    } else if(is_array($options)) {
                        $radios = $options;
                    } else {
                        $radios = array();
                    }
                    
                    foreach ($radios as $radio) {

                        if(!empty($radio)) {
                            $radio_title = houzez_wpml_translate_single_string($radio);
                            $radio_val = houzez_wpml_translate_single_string($radio);
                            echo '<option '.selected( $radio_val, $get_field_name, false).' value="'.esc_attr($radio_val).'">'.esc_attr($radio_title).'</option>';
                        }
                    }
                    ?>

                </select><!-- selectpicker -->
            </div>

        <?php
        } else if($field_type == 'checkbox_list') { ?>

            <div class="form-group">
                <select data-size="5" name="<?php echo esc_attr($field_name).'[]';?>" data-size="5" class="selectpicker <?php houzez_ajax_search(); ?> form-control bs-select-hidden" title="<?php echo esc_attr($field_title); ?>" data-live-search="true" data-selected-text-format="count > 1" data-actions-box="true" data-select-all-text="<?php echo houzez_option('cl_select_all', 'Select All'); ?>" data-deselect-all-text="<?php echo houzez_option('cl_deselect_all', 'Deselect All'); ?>" data-count-selected-text="{0} <?php echo houzez_option('srh_item_selected', 'items selected'); ?>" multiple>
                    
                    <?php
                    if(isset($_GET[$field_name])) {
                        $get_field_name = $_GET[$field_name];
                        // Ensure checkbox_list values are always an array
                        if(!is_array($get_field_name)) {
                            $get_field_name = !empty($get_field_name) ? array($get_field_name) : array();
                        }
                    }
                    $options    = unserialize($field_array['fvalues']);
                    
                    // Handle both string and array formats
                    if(is_string($options)) {
                        $options    = explode( ',', $options );
                        $options    = array_filter( array_map( 'trim', $options ) );
                        $radios     = array_combine( $options, $options );
                    } else if(is_array($options)) {
                        $radios = $options;
                    } else {
                        $radios = array();
                    }
                    
                    foreach ($radios as $radio) {

                        if(!empty($radio)) {
                            $radio_title = houzez_wpml_translate_single_string($radio);
                            $radio_val = houzez_wpml_translate_single_string($radio);

                            $selected = ( ! empty( $get_field_name ) && is_array($get_field_name) && in_array( $radio_val, $get_field_name ) ) ? 'selected' : '';

                            echo '<option '.esc_attr($selected).' value="'.esc_attr($radio_val).'">'.esc_attr($radio_title).'</option>';
                        }
                    }
                    ?>

                </select><!-- selectpicker -->
            </div>

        <?php
        } else if( $field_type == 'number' ) { ?>
            <?php
            if(isset($_GET[$field_name])) {
                $get_field_name = $_GET[$field_name];
            }
            ?>
            <div class="form-group">
                <input name="<?php echo esc_attr($field_name);?>" type="number" class="<?php houzez_ajax_search(); ?> form-control" value="<?php echo esc_attr($get_field_name); ?>" placeholder="<?php echo esc_attr($field_title);?>">
            </div>

        <?php
        } else { ?>
            <?php
            if(isset($_GET[$field_name])) {
                $get_field_name = $_GET[$field_name];
            }
            ?>
            <div class="form-group">
                <input name="<?php echo esc_attr($field_name);?>" type="text" class="<?php houzez_ajax_search(); ?> form-control" value="<?php echo esc_attr($get_field_name); ?>" placeholder="<?php echo esc_attr($field_title);?>">
            </div>

        <?php
        }
    }

    /**
     * Adds hidden fields to the search form for half map search
     * 
     * This method adds hidden input fields to the search form for the half map search.
     * It checks if the current page is using the 'template/property-listings-map.php' template
     * and if so, it adds the necessary hidden fields to the form.
     * 
     * @return void
     */
    public static function half_map_search_hidden_fields() {
        global $post;

        if( (is_page_template(array('template/template-search.php')) && houzez_option('search_result_page') == 'half_map' ) || is_page_template(array('template/property-listings-map.php') ) ) {
            echo '<input type="hidden" class="hz-halfmap-paged" name="paged" value=""/>';
            echo '<input type="hidden" name="listing_page_id" value="'.intval($post->ID).'"/>';
            echo '<input type="hidden" name="ne_lat" value=""/>';
            echo '<input type="hidden" name="ne_lng" value=""/>';
            echo '<input type="hidden" name="sw_lat" value=""/>';
            echo '<input type="hidden" name="sw_lng" value=""/>';
            echo '<input type="hidden" name="zoom" value=""/>';
        }
        
        if( is_page_template(array('template/property-listings-map.php')) ) {
            
            $search_builder = houzez_search_builder();

            if( ! array_key_exists( 'status', $search_builder['enabled'] ) ) {
                $fave_status = get_post_meta($post->ID, 'fave_status', false);

                if( !empty($fave_status) && is_array($fave_status) ) {
                    foreach ($fave_status as $status) {
                        echo '<input type="hidden" name="status[]" value="'.esc_attr($status).'">';
                    }
                    
                }
            }

            if( ! array_key_exists( 'type', $search_builder['enabled'] ) ) {
                $fave_types = get_post_meta($post->ID, 'fave_types', false);
                
                if( !empty($fave_types) && is_array($fave_types) ) {
                    foreach ($fave_types as $type) {
                        echo '<input type="hidden" name="type[]" value="'.esc_attr($type).'">';
                    }
                    
                }
            }

            if( ! array_key_exists( 'label', $search_builder['enabled'] ) ) {
                $fave_labels = get_post_meta($post->ID, 'fave_labels', false);
                
                if( !empty($fave_labels) && is_array($fave_labels) ) {
                    foreach ($fave_labels as $label) {
                        echo '<input type="hidden" name="label[]" value="'.esc_attr($label).'">';
                    }
                    
                }
            }

            if( ! array_key_exists( 'state', $search_builder['enabled'] ) ) {
                $fave_states = get_post_meta($post->ID, 'fave_states', false);
                
                if( !empty($fave_states) && is_array($fave_states) ) {
                    foreach ($fave_states as $state) {
                        echo '<input type="hidden" name="states[]" value="'.esc_attr($state).'">';
                    }
                    
                }
            }

            if( ! array_key_exists( 'areas', $search_builder['enabled'] ) ) {
                $fave_areas = get_post_meta($post->ID, 'fave_area', false);
                
                if( !empty($fave_areas) && is_array($fave_areas) ) {
                    foreach ($fave_areas as $area) {
                        echo '<input type="hidden" name="areas[]" value="'.esc_attr($area).'">';
                    }
                    
                }
            }

            if( ! array_key_exists( 'city', $search_builder['enabled'] ) ) {
                $fave_cities = get_post_meta($post->ID, 'fave_locations', false);
                
                if( !empty($fave_cities) && is_array($fave_cities) ) {
                    foreach ($fave_cities as $city) {
                        echo '<input type="hidden" name="location[]" value="'.esc_attr($city).'">';
                    }
                    
                }
            }
        } 
    }
    
}

// Initialize the class and add the necessary hooks.
Houzez_Property_Search::init();