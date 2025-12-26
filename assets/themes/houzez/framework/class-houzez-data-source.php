<?php
if ( ! class_exists( 'Houzez_Data_Source' ) ) {

    class Houzez_Data_Source {

        /** @var int Used to correct pagination when offset is in play */
        protected static $fake_loop_offset = 0;

        /**
         * Initialize hooks.
         */
        public static function init() {
            add_filter('houzez20_property_filter', [__CLASS__, 'property_filter_callback']);
            add_action( 'wp_ajax_nopriv_houzez_loadmore_properties', [__CLASS__, 'loadmore_properties'] );
            add_action( 'wp_ajax_houzez_loadmore_properties', [__CLASS__, 'loadmore_properties'] );
        }

        /**
         * Add one taxonomy clause to a tax_query array.
         */
        protected static function add_tax_clause( array &$tax_query, $taxonomy, $raw_terms, $exclude_if_tax = '' ) {
            if ( $taxonomy === $exclude_if_tax ) {
                return;
            }
            if ( is_string( $raw_terms ) ) {
                $terms = self::houzez20_traverse_comma_string( $raw_terms );
            } elseif ( is_array( $raw_terms ) ) {
                $terms = $raw_terms;
            } else {
                return;
            }
            if ( empty( $terms ) ) {
                return;
            }
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $terms,
            ];
        }


        /**
         * Add a numeric-range meta_query clause (BETWEEN, >= or <=).
         */
        protected static function add_meta_range_clause( array &$meta_query, $meta_key, $min_raw, $max_raw ) {
            $min = ( $min_raw === '' || empty($min_raw) ) ? null : doubleval( houzez_clean( $min_raw ) );
            $max = ( $max_raw === '' || empty($max_raw) ) ? null : doubleval( houzez_clean( $max_raw ) );

            if ( $min !== null && $max !== null ) {
                if ( $min >= 0 && $max >= $min ) {
                    $meta_query[] = [
                        'key'     => $meta_key,
                        'value'   => [ $min, $max ],
                        'type'    => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    ];
                }
            } elseif ( $min !== null ) {
                if ( $min >= 0 ) {
                    $meta_query[] = [
                        'key'     => $meta_key,
                        'value'   => $min,
                        'type'    => 'NUMERIC',
                        'compare' => '>=',
                    ];
                }
            } elseif ( $max !== null ) {
                if ( $max >= 0 ) {
                    $meta_query[] = [
                        'key'     => $meta_key,
                        'value'   => $max,
                        'type'    => 'NUMERIC',
                        'compare' => '<=',
                    ];
                }
            }
        }


        /**
         * Build WP_Query args from shortcode attributes.
         */
        public static function shortcode_to_args( $atts = '', $paged = 1 ) {
            
            extract( shortcode_atts( [
                'property_type'         => '',
                'property_status'       => '',
                'property_city'         => '',
                'property_country'      => '',
                'property_state'        => '',
                'property_area'         => '',
                'property_label'        => '',
                'houzez_user_role'      => '',
                'featured_prop'         => '',
                'property_ids'          => '',
                'property_id'           => '',
                'posts_limit'           => '',
                'sort_by'               => '',
                'post_status'           => '',
                'offset'                => 0,
                'min_price'             => '',
                'max_price'             => '',
                'min_beds'              => '',
                'max_beds'              => '',
                'min_baths'             => '',
                'max_baths'             => '',
                'properties_by_agents'  => '',
                'properties_by_agencies'=> '',
            ], (array) $atts ) );

            $tax_query  = [];
            $meta_query = [];
            $args       = [
                'ignore_sticky_posts' => 1,
                'post_type'           => 'property',
            ];

            // author__in by role
            if ( $houzez_user_role !== '' ) {
                $ids = self::houzez20_author_ids_by_role( $houzez_user_role );
                if ( $ids ) {
                    $args['author__in'] = $ids;
                }
            }

            // taxonomy filters
            $sort_tax = $_GET['tax'] ?? '';
            if ( isset( $_GET['tab'], $_GET['tax'] ) ) {
                self::add_tax_clause( $tax_query, sanitize_text_field( $_GET['tax'] ), sanitize_text_field( $_GET['tab'] ) );
            }
            self::add_tax_clause( $tax_query, 'property_type',   $property_type,   $sort_tax );
            self::add_tax_clause( $tax_query, 'property_status', $property_status, $sort_tax );
            self::add_tax_clause( $tax_query, 'property_state',  $property_state );
            self::add_tax_clause( $tax_query, 'property_country',$property_country );
            self::add_tax_clause( $tax_query, 'property_city',   $property_city,   $sort_tax );
            self::add_tax_clause( $tax_query, 'property_area',   $property_area );
            self::add_tax_clause( $tax_query, 'property_label',  $property_label );

            // agents/agencies meta
            if ( ! empty( $properties_by_agents ) ) {
                $meta_query[] = [
                    'key'     => 'fave_agents',
                    'value'   => self::houzez20_traverse_comma_string( $properties_by_agents ),
                    'compare' => 'IN',
                ];
                $meta_query[] = [
                    'key'     => 'fave_agent_display_option',
                    'value'   => 'agent_info',
                    'compare' => '=',
                ];
            }
            if ( ! empty( $properties_by_agencies ) ) {
                $meta_query[] = [
                    'key'     => 'fave_property_agency',
                    'value'   => self::houzez20_traverse_comma_string( $properties_by_agencies ),
                    'compare' => 'IN',
                ];
                $meta_query[] = [
                    'key'     => 'fave_agent_display_option',
                    'value'   => 'agency_info',
                    'compare' => '=',
                ];
            }

            // numeric ranges: price, beds, baths
            self::add_meta_range_clause( $meta_query, 'fave_property_price',     $min_price, $max_price );
            self::add_meta_range_clause( $meta_query, 'fave_property_bedrooms',  $min_beds,  $max_beds );
            self::add_meta_range_clause( $meta_query, 'fave_property_bathrooms', $min_baths, $max_baths );

            // featured flag
            if ( ! empty( $featured_prop ) ) {
                if ( $featured_prop === 'yes' ) {
                    $meta_query[] = [ 'key' => 'fave_featured', 'value' => '1', 'compare' => '=' ];
                } else {
                    $meta_query[] = [
                        'relation' => 'OR',
                        [ 'key' => 'fave_featured', 'value' => '0', 'compare' => '=' ],
                        [ 'key' => 'fave_featured', 'compare' => 'NOT EXISTS' ],
                    ];
                }
            }

            // single IDs
            if ( ! empty( $property_id ) ) {
                $args['post__in'] = [ intval( $property_id ) ];
            }
            if ( ! empty( $property_ids ) ) {
                $ids = is_array( $property_ids )
                    ? array_map( 'intval', $property_ids )
                    : array_map( 'intval', explode( ',', $property_ids ) );
                $args['post__in'] = $ids;
            }

            // finalize tax_query & meta_query
            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }
            if ( $tax_query ) {
                $args['tax_query'] = $tax_query;
            }
            if ( count( $meta_query ) > 1 ) {
                $meta_query['relation'] = 'AND';
            }
            if ( $meta_query ) {
                $args['meta_query'] = $meta_query;
            }

            $args = houzez_prop_sort ( $args, $sort_by );

            // post_status
            if ( $post_status === 'houzez_sold' ) {
                $args['post_status'] = 'houzez_sold';
            } elseif ( $post_status === 'publish' ) {
                $args['post_status'] = 'publish';
            } else {
                $args['post_status'] = [ 'publish', 'houzez_sold' ];
            }

            // pagination + offset
            $args['posts_per_page'] = $posts_limit !== '' ? intval( $posts_limit ) : 9;
            $args['paged']          = intval( $paged ) ?: 1;
            
            if ( $offset && $paged > 1 ) { 
                $args['offset'] = intval( $offset ) + ( ( $paged - 1 ) * $args['posts_per_page'] );
            } else { 

                if( ! empty( $offset ) ) {
                    $args['offset'] = intval( $offset );
                }
            }
            self::$fake_loop_offset = $args['offset'] ?? 0;

            return $args;
        }


        /**
         * Like shortcode_to_args(), but retains sticky posts & fixes pagination.
         */
        public static function metabox_to_args( $filter, $paged = 1 ) {
            $args = self::shortcode_to_args( $filter, $paged );
            $args['ignore_sticky_posts'] = 0;
            if ( ! empty( $args['offset'] ) ) {
                add_filter( 'found_posts', [ __CLASS__, 'hook_fix_offset_pagination' ], 1, 2 );
            }
            return $args;
        }


        public static function hook_fix_offset_pagination( $found, $query ) {
            remove_filter( 'found_posts', [ __CLASS__, 'hook_fix_offset_pagination' ] );
            return $found - self::$fake_loop_offset;
        }


        public static function &get_wp_query( $atts = '', $paged = 1 ) {
            $q  = new WP_Query( self::shortcode_to_args( $atts, $paged ) );
            return $q;
        }


        public static function houzez20_traverse_comma_string( $s ) {
            if ( is_string( $s ) && trim( $s ) !== '' ) {
                $arr = array_filter( array_map( 'trim', explode( ',', $s ) ) );
                return $arr ?: '';
            }
            return '';
        }


        public static function houzez20_author_ids_by_role( $role ) {
            return get_users( [ 'role' => $role, 'fields' => 'ID' ] );
        }


        /**
         * Hook for template filters.
         */
        public static function property_filter_callback( $args ) {
            global $paged;
            $page_id    = get_the_ID();
            $tax_query  = [];
            $meta_query = [];

            // paged & sold-status
            $paged = max( 1, get_query_var( 'paged' ) ?: get_query_var( 'page' ) );
            $args['paged'] = $paged;
            $args = apply_filters( 'houzez_sold_status_filter', $args );

            // per-page
            $no = get_post_meta( $page_id, 'fave_prop_no', true );
            $args['posts_per_page'] = $no ? intval( $no ) : 9;

            // Handle status tab from URL parameter
            if ( isset( $_GET['tab'] ) ) {
                self::add_tax_clause( $tax_query, 'property_status', sanitize_text_field( $_GET['tab'] ) );
            }

            // Define mapping of meta keys to taxonomy names
            $taxonomy_mapping = [
                'fave_countries'  => 'property_country',
                'fave_states'     => 'property_state',
                'fave_locations'  => 'property_city',
                'fave_types'      => 'property_type',
                'fave_labels'     => 'property_label',
                'fave_area'       => 'property_area',
                'fave_features'   => 'property_feature',
            ];

            // Only include property status if not already set by tab parameter
            if ( !isset( $_GET['tab'] ) ) {
                $taxonomy_mapping['fave_status'] = 'property_status';
            }
            
            // Process each meta key and add corresponding taxonomy query
            foreach ( $taxonomy_mapping as $meta_key => $taxonomy ) {
                $terms = get_post_meta( $page_id, $meta_key, false );
                if ( !empty( $terms ) ) {
                    self::add_tax_clause( $tax_query, $taxonomy, $terms );
                }
            }
            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }
            if ( $tax_query ) {
                $args['tax_query'] = $tax_query;
            }

            // price / beds / baths
            $min_p = get_post_meta( $page_id, 'fave_min_price', true );
            $max_p = get_post_meta( $page_id, 'fave_max_price', true );
            self::add_meta_range_clause( $meta_query, 'fave_property_price',     $min_p, $max_p );

            $min_b = get_post_meta( $page_id, 'fave_properties_min_beds', true );
            $max_b = get_post_meta( $page_id, 'fave_properties_max_beds', true );
            self::add_meta_range_clause( $meta_query, 'fave_property_bedrooms',  $min_b, $max_b );

            $min_t = get_post_meta( $page_id, 'fave_properties_min_baths', true );
            $max_t = get_post_meta( $page_id, 'fave_properties_max_baths', true );
            self::add_meta_range_clause( $meta_query, 'fave_property_bathrooms', $min_t, $max_t );

            // agents / agencies
            $agents   = array_filter( get_post_meta( $page_id, 'fave_properties_by_agents', false ) );
            $agencies = array_filter( get_post_meta( $page_id, 'fave_properties_by_agency', false ) );
            if ( $agents ) {
                $meta_query[] = [ 'key'=>'fave_agents','value'=>$agents,'compare'=>'IN' ];
                $meta_query[] = [ 'key'=>'fave_agent_display_option','value'=>'agent_info','compare'=>'=' ];
            }
            if ( $agencies ) {
                $meta_query[] = [ 'key'=>'fave_property_agency','value'=>$agencies,'compare'=>'IN' ];
                $meta_query[] = [ 'key'=>'fave_agent_display_option','value'=>'agency_info','compare'=>'=' ];
            }

            if ( count( $meta_query ) > 1 ) {
                $meta_query['relation'] = 'AND';
            }
            if ( $meta_query ) {
                $args['meta_query'] = $meta_query;
            }

            return $args;
        }


        /**
         * Handle AJAX load more properties for properties elementor widgets
         */
        public static function loadmore_properties() {
            global $houzez_local;

            // Security: Verify nonce if provided (optional for backward compatibility)
            if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'houzez_loadmore_nonce')) {
                wp_send_json_error('Invalid nonce');
                wp_die();
            }

            $houzez_local = houzez_get_localization();
            // Initialize attributes array
            $atts = array();
            
            // Process all POST data dynamically
            foreach ($_POST as $key => $value) {
                if ($key === 'action') {
                    continue; // Skip the action parameter
                }
                
                // Convert snake_case keys to the format expected by shortcode_to_args
                $clean_key = sanitize_text_field($key);
                $clean_value = sanitize_text_field($value);

                $atts[$clean_key] = $clean_value;
            }
                
            // Security fix: Validate card parameter to prevent Local File Inclusion (LFI)
            // Only allow specific predefined card versions that correspond to existing template files
            $allowed_card_versions = array('v1', 'list-v1', 'v2', 'list-v2', 'v3', 'v4', 'list-v4', 'v5', 'v6', 'v6-skeleton', 'v7', 'list-v7');
            $card_input = isset($atts['card']) ? sanitize_text_field($atts['card']) : '';
            $card_version = in_array($card_input, $allowed_card_versions, true) ? $card_input : 'v1';
            
            $current_page = $atts['paged'];
            
            
            $wp_query_args = self::shortcode_to_args($atts, $current_page);
            
            $the_query = new WP_Query($wp_query_args);
            
            $response = array(
                'html' => '',
                'has_more_posts' => false,
            );

            if($the_query->have_posts()) {
                ob_start();
                while($the_query->have_posts()): $the_query->the_post();
                    global $post;
                    setup_postdata($post);
                    get_template_part('template-parts/listing/item', $card_version);
                endwhile;
                wp_reset_postdata();
                $response['html'] = ob_get_clean();
                $response['has_more_posts'] = ( $the_query->found_posts > ( $current_page * $wp_query_args['posts_per_page'] ) );
                
            } else {
                $response['html'] = 'no_result';
            }

            wp_send_json($response);
            wp_die();
        }
    }
}

// Add backward compatibility
if ( ! class_exists( 'houzez_data_source' ) ) {
    class houzez_data_source extends Houzez_Data_Source {}
}

// Initialize the class and add the necessary hooks.
Houzez_Data_Source::init();