<?php
if( !function_exists('houzez_get_realtor_tax_stats') ) {
    function houzez_get_realtor_tax_stats( $taxonomy_name, $meta_key, $listings_ids ) {

        $taxonomies = [];
        $total_count = [];
        $total_listings = 0;
        $others = 0;
        $other_percent = 0;

        // Collect property city taxonomies and calculate counts in a single loop
        foreach ($listings_ids as $listing_id) {
            $terms = get_the_terms($listing_id, $taxonomy_name);
            if ($terms && !is_wp_error($terms)) {
                $term = $terms[0];
                $slug = $term->slug;
                $name = $term->name;
                
                if (!isset($taxonomies[$slug])) {
                    $taxonomies[$slug] = $name;

                    // Get default language ID for WPML compatibility
                    $current_id = get_the_ID();
                    $post_type = get_post_type($current_id);
                    $default_lang = function_exists('wpml_get_default_language') ? wpml_get_default_language() : null;
                    $default_lang_id = apply_filters('wpml_object_id', $current_id, $post_type, false, $default_lang);

                    $count = houzez_realtor_stats($taxonomy_name, $meta_key, $default_lang_id, $slug);
                    if ($count > 0) {
                        $total_count[$slug] = $count;
                        $total_listings += $count;
                    }
                }
            }
        }

        // Calculate percentages and sort taxonomies by count
        $stats_array = [];
        foreach ($total_count as $slug => $count) {
            $name = $taxonomies[$slug];
            $stats_array[$name] = ($count / $total_listings) * 100;
        }
        arsort($stats_array);

        // Prepare chart data
        $tax_chart_data = array_slice(array_values($stats_array), 0, 3);
        $taxs_list_data = array_slice(array_keys($stats_array), 0, 3);

        // Calculate others percentage if applicable
        $top_counts = array_slice($total_count, 0, 3);
        $total_top_count = array_sum($top_counts);

        if ($total_top_count < $total_listings) {
            $others = $total_listings - $total_top_count;
            $other_percent = ($others / $total_listings) * 100;
            if ($other_percent > 0) {
                $tax_chart_data[] = $other_percent;
            }
        }

        $return = array(
            'taxonomies' => $taxonomies,
            'taxs_list_data' => $taxs_list_data,
            'tax_chart_data' => $tax_chart_data,
            'total_count' => $total_count,
            'total_top_count' => $total_top_count,
            'others' => $others,
            'other_percent' => $other_percent
        );

        return $return;

    }
}

if(!function_exists('houzez_get_term_slugs_for_stats')) {
	function houzez_get_term_slugs_for_stats($taxonomy) {
		$terms = get_terms(array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
			'orderby'    => 'count',
			'order'    => 'DESC',
		));

		$term_data = $slug = $name = array();

		$i = 0;
		foreach ($terms as $terms): 
			$i++;
		    $slug[] = $terms->slug; 
		    $name[] = $terms->name; 

		    if($i == 3) {
		    	//break;
		    }
		endforeach;

		$term_data['name'] = $name;
		$term_data['slug'] = $slug;
		return $term_data;
	}
}

if (!function_exists('houzez_realtor_stats_new')) {
    function houzez_realtor_stats_new($taxonomy, $meta_key, $meta_value, $term_slug) {
        global $wpdb, $author_id;

        // Get default language ID for WPML compatibility
        $current_id = get_the_ID();
        $post_type = get_post_type($current_id);
        $default_lang = function_exists('wpml_get_default_language') ? wpml_get_default_language() : null;
        $default_lang_id = apply_filters('wpml_object_id', $current_id, $post_type, false, $default_lang);

        // Create a unique cache key
        $cache_key = 'houzez_realtor_stats_' . md5($taxonomy . $meta_key . $meta_value . $term_slug . $default_lang_id);
        $cached_result = get_transient($cache_key);

        if ($cached_result !== false) {
            return $cached_result;
        }

        // Base SQL query
        $sql = "
            SELECT COUNT(DISTINCT p.ID) 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)
            INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
            INNER JOIN {$wpdb->terms} t ON (tt.term_id = t.term_id)
            LEFT JOIN {$wpdb->postmeta} pm1 ON (p.ID = pm1.post_id AND pm1.meta_key = %s)
            LEFT JOIN {$wpdb->postmeta} pm2 ON (p.ID = pm2.post_id AND pm2.meta_key = 'fave_agent_display_option')
            WHERE p.post_type = 'property' 
              AND p.post_status = 'publish'
              AND t.slug = %s
              AND tt.taxonomy = %s";

        // Additional conditions based on context
        $conditions = array($meta_key, $term_slug, $taxonomy);

        if (is_singular('houzez_agency')) {
            $agency_agents_ids = Houzez_Query::loop_agency_agents_ids($default_lang_id);
            if (!empty($agency_agents_ids)) {
                $sql .= " AND (pm1.meta_value IN (" . implode(',', array_fill(0, count($agency_agents_ids), '%d')) . ") OR (pm1.meta_value = %s AND pm2.meta_value = 'agency_info'))";
                $conditions = array_merge($conditions, $agency_agents_ids, array($meta_value));
            } else {
                $sql .= " AND pm1.meta_value = %s AND pm2.meta_value = 'agency_info'";
                $conditions[] = $meta_value;
            }
        } elseif (is_singular('houzez_agent')) {
            $sql .= " AND pm1.meta_value = %s AND pm2.meta_value = 'agent_info'";
            $conditions[] = $meta_value;
        } elseif (is_author()) {
            $sql .= " AND p.post_author = %d";
            $conditions[] = $author_id;
        }

        // Prepare and execute the query
        $query = $wpdb->prepare($sql, $conditions);
        $count = $wpdb->get_var($query);

        // Cache the result
        set_transient($cache_key, $count, 12 * HOUR_IN_SECONDS);

        return $count;
    }
}


if (!function_exists('houzez_realtor_stats')) {
    /**
     * Calculate realtor statistics based on property taxonomy and meta data using a custom database query.
     * Results are cached to improve performance.
     *
     * @param string $taxonomy   The taxonomy name.
     * @param string $meta_key   The meta key to query.
     * @param string $meta_value The meta value to match.
     * @param string $term_slug  The taxonomy term slug.
     * @return int               The count of matching properties.
     */
    function houzez_realtor_stats($taxonomy, $meta_key, $meta_value, $term_slug) {
        global $wpdb;

        // Generate a unique cache key based on function arguments
        $cache_key = 'houzez_realtor_stats_' . md5(serialize(func_get_args()));
        $cached_result = get_transient($cache_key);

        // If cached result exists, return it
        if ($cached_result !== false) {
            return (int) $cached_result;
        }

        $post_type   = 'property';
        $post_status = 'publish';
        $query_args  = [$post_type, $post_status];

        // Start building the SQL query
        $sql = "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p";

        // Initialize JOIN and WHERE clauses
        $joins  = '';
        $wheres = "WHERE p.post_type = %s AND p.post_status = %s";

        // Taxonomy Query
        if (!empty($taxonomy) && !empty($term_slug)) {
            $joins .= " INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id";
            $joins .= " INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
            $joins .= " INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id";

            $wheres      .= " AND tt.taxonomy = %s AND t.slug = %s";
            $query_args[] = $taxonomy;
            $query_args[] = $term_slug;
        }

        // Meta Query
        $meta_query_clauses = [];

        if (is_singular('houzez_agency')) {
            $meta_query_clauses = get_agency_meta_query($meta_key, $meta_value);
        } elseif (is_singular('houzez_agent')) {
            $meta_query_clauses = get_agent_meta_query($meta_key, $meta_value);
        } elseif (is_author()) {
            $author_id    = get_queried_object_id();
            $wheres      .= " AND p.post_author = %d";
            $query_args[] = $author_id;
        }

        // Process Meta Query
        if (!empty($meta_query_clauses)) {
            $meta_query = new WP_Meta_Query($meta_query_clauses);
            $mq_sql     = $meta_query->get_sql('post', 'p', 'ID');

            $joins  .= " " . $mq_sql['join'];
            $wheres .= " " . $mq_sql['where'];
        }

        // Apply Custom Filters (if any)
        $wheres = apply_filters('houzez_sold_status_filter_sql', $wheres, 'p');

        // Complete SQL Query
        $sql .= " $joins $wheres";

        // Prepare and Execute the Query
        $prepared_sql = $wpdb->prepare($sql, $query_args);
        $count        = $wpdb->get_var($prepared_sql);

        // Cache the result for 12 hours (adjust as needed)
        set_transient($cache_key, $count, 12 * HOUR_IN_SECONDS);

        return (int) $count;
    }

    /**
     * Get meta query for agency.
     *
     * @param string $meta_key   The meta key to query.
     * @param string $meta_value The meta value to match.
     * @return array             The meta query for agency.
     */
    function get_agency_meta_query($meta_key, $meta_value) {
        // Get default language ID for WPML compatibility
        $current_id = get_the_ID();
        $default_lang = function_exists('wpml_get_default_language') ? wpml_get_default_language() : null;
        $default_lang_agency_id = apply_filters('wpml_object_id', $current_id, 'houzez_agency', false, $default_lang);

        $agency_agents_ids = Houzez_Query::loop_agency_agents_ids($default_lang_agency_id);

        $meta_query = ['relation' => 'OR'];

        if (!empty($agency_agents_ids)) {
            $meta_query[] = [
                'key'     => 'fave_agents',
                'value'   => $agency_agents_ids,
                'compare' => 'IN',
            ];
        }

        $meta_query[] = [
            'relation' => 'AND',
            [
                'key'     => $meta_key,
                'value'   => $meta_value,
                'compare' => '=',
            ],
            [
                'key'     => 'fave_agent_display_option',
                'value'   => 'agency_info',
                'compare' => '=',
            ],
        ];

        return $meta_query;
    }

    /**
     * Get meta query for agent.
     *
     * @param string $meta_key   The meta key to query.
     * @param string $meta_value The meta value to match.
     * @return array             The meta query for agent.
     */
    function get_agent_meta_query($meta_key, $meta_value) {
        return [
            'relation' => 'AND',
            [
                'key'     => $meta_key,
                'value'   => $meta_value,
                'compare' => '=',
            ],
            [
                'key'     => 'fave_agent_display_option',
                'value'   => 'agent_info',
                'compare' => '=',
            ],
        ];
    }
}


if( ! function_exists('houzez_clear_realtor_stats_cache') ) {
    function houzez_clear_realtor_stats_cache($post_id) {
        if (get_post_type($post_id) !== 'property') {
            return;
        }
        // Clear all related transients
        // If you have multiple transients, consider using a pattern to delete them
        global $wpdb;
        $transient_name = '%houzez_realtor_stats_%';
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_' . $transient_name,
                '_transient_timeout_' . $transient_name
            )
        );
    }
    add_action('save_post_property', 'houzez_clear_realtor_stats_cache');
    add_action('delete_post', 'houzez_clear_realtor_stats_cache');
}



/*if(!function_exists('houzez_realtor_stats')) {
	function houzez_realtor_stats($taxonomy, $meta_key, $meta_value, $term_slug) {
		global $author_id;

		$args = array(
			'post_type' => 'property',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'tax_query' => array(
		        array(
		            'taxonomy' => $taxonomy,
		            'field'    => 'slug',
		            'terms'    => $term_slug,
		            'include_children' => false
		        )
		    )
		);

		$args = apply_filters( 'houzez_sold_status_filter', $args );

		$meta_query = array();

        if(is_singular('houzez_agency')) {

        	$agents_array = array();
        	$agenyc_agents_ids = Houzez_Query::loop_agency_agents_ids(get_the_ID());

        	if( !empty($agenyc_agents_ids) ) {
	        	$agents_array = array(
		            'key' => 'fave_agents',
		            'value' => $agenyc_agents_ids,
		            'compare' => 'IN',
		        );
	        }

        	$args['meta_query'] = array(
                'relation' => 'OR',
                $agents_array,
                array(
                    'relation' => 'AND',
                    array(
			            'key'     => $meta_key,
			            'value'   => $meta_value,
			            'compare' => '='
			        ),
			        array(
			            'key'     => 'fave_agent_display_option',
			            'value'   => 'agency_info',
			            'compare' => '='
			        )
                ),
            );


        } elseif(is_singular('houzez_agent')) {

        	$args['meta_query'] = array(
                'relation' => 'AND',
                array(
		            'key'     => $meta_key,
		            'value'   => $meta_value,
		            'compare' => '='
		        ),
		        array(
		            'key'     => 'fave_agent_display_option',
		            'value'   => 'agent_info',
		            'compare' => '='
		        )
            );


        } elseif(is_author()) {
        	$args['author'] = $author_id;
        }

		$posts = get_posts($args); 

		return count($posts);
	}	
}*/