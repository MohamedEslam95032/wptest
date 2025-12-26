<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src;

use Realtyna\MLSOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RFQuery;
use WP_Query;

class WPQueryMapper
{
    private MappingConfig $mappingConfig;
    private RFQuery $RFQuery;
    private WP_Query $wp_query;

    /**
     * Constructor for WPQueryMapper.
     * Initializes the mapper with a specific configuration for mapping WordPress query parameters to RFQuery parameters.
     *
     * @param MappingConfig $mappingConfig The configuration object that provides mappings and settings for the query transformation.
     * @author Chandler P <chandler.p@realtyna.net>
     * @version 1.4.6.4
     */
    public function __construct(MappingConfig $mappingConfig)
    {
        $this->mappingConfig = $mappingConfig;
    }

    /**
     * Maps a WP_Query object to an RFQuery object.
     * This method sets up the RFQuery entity, configures selections, handles pagination,
     * ordering, and adds various filters based on the provided WP_Query settings.
     *
     * @param WP_Query $wp_query The WordPress query object to map.
     * @return RFQuery The fully configured RFQuery object ready for execution.
     * @author Chandler P <chandler.p@realtyna.net>
     * @version 1.4.6.4
     */
    public function mapWPQueryRFQuery(WP_Query $wp_query): RFQuery
    {
        // Store the WP_Query object internally for use in other methods.
        $this->wp_query = $wp_query;

        // Initialize the RFQuery object and set the main entity to query.
        $this->RFQuery = new RFQuery();
        $this->RFQuery->set_entity('Property');

        // Configure the fields to select based on the WP_Query settings.
        $this->setSelect();

        // Configure query to handle single post retrieval if applicable.
        if ($this->wp_query->is_single()) {
            $this->configureSinglePostQuery();
        }

        // Set pagination parameters from WP_Query.
        $this->setPagination();

        // Set sorting parameters from WP_Query.
        $this->setOrder();

        // Add filters based on metadata, taxonomy, and post types from WP_Query.
        $this->handlePostIds();
        $this->addMetaFilters();
        $this->addTaxonomyFilters();
        $this->addPostTypeFilters();
        $this->addRFParams();
        // If no specific order is set, default to ordering by modification timestamp in descending order.
        if (empty($this->RFQuery->get_orders())) {
            $this->RFQuery->set_order(['ModificationTimestamp', 'desc']);
        }
        if ($this->wp_query->is_single()) {
            add_filter('mls_on_the_fly_is_single', function () {
                return true;
            });
            add_filter('mls_on_the_fly_raw_data', function () {
                return $this->wp_query->post->meta_data['realty_feed_raw_data'];
            });
        }
        // Return the fully configured RFQuery object.
        return $this->RFQuery;
    }


    private function handlePostIds(): void
    {
        $postIds = $this->wp_query->query['post__in'] ?? [];
        if (!empty($postIds)) {
            if (count($postIds) > 1) {
                $this->RFQuery->open_filter('OR');
            }
            foreach ($postIds as $postId) {
                global $wpdb;
                $listingKey = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT listing_key FROM {$wpdb->prefix}realtyna_rf_mappings WHERE post_id = %d LIMIT 1",
                        $postId
                    )
                );
                if ($listingKey) {
                    $this->RFQuery->add_filter('where', 'ListingKey', 'eq', $listingKey);
                }
            }
            if (count($postIds) > 1) {
                $this->RFQuery->close_filter();
            }
        }
    }

    /**
     * Sets the SELECT clause of the RFQuery based on the WordPress query parameters.
     * Ensures that essential fields such as 'ListingKey' and 'ListingId' are always included
     * in the query when specific fields are requested. Defaults to selecting all fields if none are specified.
     *
     * @return void
     * @author Chandler P <chandler.p@realtyna.net>
     * @version 1.4.6.4
     */
    private function setSelect(): void
    {
        // Check if specific fields are requested in the WordPress query
        if (isset($this->wp_query->query['rf_select_fields']) && !empty($this->wp_query->query['rf_select_fields'])) {
            // Ensure essential fields are included in the selection
            $this->wp_query->query['rf_select_fields'][] = 'ListingKey';
            $this->wp_query->query['rf_select_fields'][] = 'ListingId';
            // Remove duplicate field requests to optimize the query
            $this->wp_query->query['rf_select_fields'] = array_unique($this->wp_query->query['rf_select_fields']);
            // Set the select clause in RFQuery with the specified fields
            $this->RFQuery->set_select($this->wp_query->query['rf_select_fields']);
        } else {
            // Default to selecting all fields if no specific fields are specified
            $this->RFQuery->set_select(['ALL']);
        }
    }


    /**
     * Configures the query when querying a single post.
     * Sets the query to retrieve a specific post based on a 'listingKey' derived from the WordPress query.
     * This key should correlate directly with an identifiable field in the data source.
     *
     * @return void
     * @author Chandler P <chandler.p@realtyna.net>
     * @version 1.4.6.4
     */
    private function configureSinglePostQuery(): void
    {
        // Log the action of querying a single post for better traceability
        Log::info('Querying a single post');

        // Retrieve the post name and mapping pattern
        $postName = $this->wp_query->query['name'] ?? null;
        $postNamePattern = $this->mappingConfig->getMappingMapping('post_name');

        // Validate the presence of 'ListingKey' in the pattern
        if (!str_contains($postNamePattern, 'ListingKey')) {
            $postNamePattern = "{ListingKey}-$postNamePattern";
        }

        // Extract the listing key from the post name
        $listingKey = $this->extractListingKey($postName, $postNamePattern);

        if (!$listingKey) {
            $this->RFQuery->set_find('random-key-to-trigger-404');
        } else {
            // Configure the RFQuery to find the specific entry
            $this->RFQuery->set_find($listingKey);
        }
    }

    /**
     * Extracts the ListingKey from the post name based on the dynamic pattern.
     *
     * @param string|null $postName
     * @param string $pattern
     * @return string|null
     */
    private function extractListingKey(?string $postName, string $pattern): ?string
    {
        if (!$postName) {
            Log::error('Post name is null or empty.');
            return null;
        }

        // Split the pattern and post name into components
        $patternComponents = explode('-', $pattern);
        $postNameComponents = explode('-', $postName);


        // Get the count of both arrays
        $patternCount = count($patternComponents);
        $postNameCount = count($postNameComponents);

        // Determine the difference
        $diff = $patternCount - $postNameCount;

        // If $postNameComponents has fewer items, pad it with empty strings
        if ($diff > 0) {
            $postNameComponents = array_pad($postNameComponents, $patternCount, "");
        }

        // If $patternComponents has fewer items (unlikely in your case), pad it with empty strings
        if ($diff < 0) {
            $patternComponents = array_pad($patternComponents, $postNameCount, "");
        }

        // Map the pattern components to the post name components
        $data = array_combine($patternComponents, $postNameComponents);

        // Debugging: Log the mapped data
        Log::debug('Mapped data: ' . json_encode($data));

        // Extract the ListingKey
        if (!isset($data['{ListingKey}'])) {
            Log::error('ListingKey is missing in the post name.');
            return null;
        }

        return $data['{ListingKey}'];
    }


    /**
     * Configures the pagination for the RFQuery based on WordPress query parameters.
     * This method sets up pagination by computing skips and limits (top) according to page number and offset settings.
     *
     * @return void
     * @author Chandler P <chandler.p@realtyna.net>
     * @version 1.4.6.4
     */
    private function setPagination(): void
    {
        // Retrieve pagination parameters from the WordPress query
        $paged = $this->wp_query->query['paged'] ?? false;
        $offset = $this->wp_query->query['offset'] ?? 0;
        $rfSelectFields = $this->wp_query->query['rf_select_fields'] ?? false;

        // Determine the number of posts per page
        $postsPerPage = 20; // Default fallback

        if (isset($this->wp_query->query['posts_per_page']) || isset($this->wp_query->query_vars['posts_per_page'])) {
            $postsPerPageQuery = $this->wp_query->query['posts_per_page'] ?? $this->wp_query->query_vars['posts_per_page'];

            if ($postsPerPageQuery == '-1') {
                $postsPerPage = 20; // Override if 'posts_per_page' is set to unlimited (-1)
            } elseif ($postsPerPageQuery >= 1) {
                $postsPerPage = $postsPerPageQuery;
            }
        }

        if ($rfSelectFields) {
            $postsPerPage = $this->wp_query->query['posts_per_page'] ?? 20; // Enforce maximum limit for performance
        }

        // Calculate the offset to skip to the correct set of posts
        if ($paged) {
            $rfSkip = $postsPerPage * ($paged - 1);
            $rfSkip = apply_filters('realtyna_mls_on_the_fly_rf_skip', $rfSkip, $paged, $postsPerPage);

            if ($rfSkip) {
                $this->RFQuery->set_skip($rfSkip); // Compute skip for paginated results
            }
        }

        if ($offset && is_int($offset)) {
            $this->RFQuery->set_skip($offset); // Override skip if an explicit offset is provided
        }

        // Set the number of posts to fetch
        $this->RFQuery->set_top($postsPerPage);
    }


    /**
     * Sets the ORDER BY clause of the RFQuery based on WordPress query parameters.
     * Handles sorting by meta values or other specified fields, with a default fallback.
     *
     * @return void
     * @author Chandler P <chandler.p@realtyna.net>
     * @version 1.4.6.4
     */
    private function setOrder(): void
    {
        // Retrieve order direction and the field to order by from the WordPress query
        $order = $this->wp_query->query['order'] ?? 'desc'; // Default order direction is descending
        $orderBy = $this->wp_query->query['orderby'] ?? false; // Field to order by

        // Special handling when ordering by meta value (numerically)
        if ($orderBy == 'meta_value_num') {
            $orderBy = $this->wp_query->query['meta_key'] ?? ""; // Use the meta key specified in the query for ordering
        }

        // Check if an order by field is set and it is not an array
        if ($orderBy && $order && !is_array($orderBy)) {
            // Map the WordPress field to an RFQuery field using configuration mappings
            $OrderRFField = $this->mappingConfig->getQueryKeyMapping($orderBy);
            if ($OrderRFField) {
                // Set the order with the mapped field and direction
                $this->RFQuery->set_order([$OrderRFField, $order]);
            }
        } else {
            // Default ordering by modification timestamp in descending order if no specific ordering is provided
            $this->RFQuery->set_order(['ModificationTimestamp', 'desc']);
        }
    }

    /**
     * Adds meta filters to the RFQuery based on meta query parameters from the WordPress query.
     * This includes checking for specific meta keys and values and integrating them into the RFQuery.
     *
     * @return void
     * @version 1.4.6.4
     * @author Chandler P <chandler.p@realtyna.net>
     */
    private function addMetaFilters(): void
    {
        // Retrieve any existing meta query parameters from the WordPress query
        $wpMetaQueries = $this->wp_query->query_vars['meta_query'] ?? [];

        // Check if both meta key and value are set and not empty; if so, add to the meta queries array
        if (
            isset($this->wp_query->query_vars['meta_key'], $this->wp_query->query_vars['meta_value']) &&
            $this->wp_query->query_vars['meta_key'] !== '' && $this->wp_query->query_vars['meta_value'] !== ''
        ) {
            $wpMetaQueries[] = [
                'key' => $this->wp_query->query_vars['meta_key'],
                'value' => $this->wp_query->query_vars['meta_value'],
            ];
        }


        // Clean empty meta queries before processing
        $wpMetaQueries = $this->cleanMetaQueries($wpMetaQueries);
        // Process any accumulated meta queries by passing them to a function that handles nested queries
        if ($wpMetaQueries) {
            $this->processNestedMetaQueries($wpMetaQueries);
        }
    }

    private function cleanMetaQueries(array $metaQueries): array
    {
        $cleanedQueries = [];

        foreach ($metaQueries as $key => $query) {
            if (is_array($query)) {
                // Recursively clean nested meta queries
                $query = $this->cleanMetaQueries($query);

                // Remove any empty arrays or arrays with only "relation" key
                if (!empty($query) && !(count($query) === 1 && isset($query['relation']))) {
                    $cleanedQueries[$key] = $query;
                }
            } else {
                // Preserve non-array elements (e.g., 'relation' at the top level)
                $cleanedQueries[$key] = $query;
            }
        }

        // Reset numeric indexes for cleaner arrays
        if (array_keys($cleanedQueries) === range(0, count($cleanedQueries) - 1)) {
            $cleanedQueries = array_values($cleanedQueries);
        }

        return $cleanedQueries;
    }


    /**
     * Recursively processes nested meta queries to handle complex query relationships.
     * This function handles different types of meta queries, including nested conditions with specific relations.
     *
     * @param array $metaQueries Array of meta queries to process, potentially including nested structures.
     * @return void
     * @version 1.4.6.4
     * @author Chandler P <chandler.p@realtyna.net>
     */
    private function processNestedMetaQueries(array $metaQueries): void
    {
        // Open a new filter context if a relation is specified (e.g., 'AND', 'OR')
        if (isset($metaQueries['relation'])) {
            $relation = strtoupper($metaQueries['relation']);
            $this->RFQuery->open_filter($relation);
        }
        // Iterate over each meta query in the array
        foreach ($metaQueries as $wpMetaQuery) {
            if (is_array($wpMetaQuery) && isset($wpMetaQuery['key'])) {
                // Extract key and value from the meta query
                $metaKey = $wpMetaQuery['key'];
                $metaValue = $wpMetaQuery['value'] ?? '';  // Default to empty string if no value is specified
                $metaRFMapping = $this->mappingConfig->getQueryPostMapping(
                    $metaKey
                );  // Map the meta key to a query field


                // Process each meta query based on its key and mapping
                if ($metaKey && ($metaRFMapping != null || $metaKey == 'raw')) {
                    $metaCompare = isset($wpMetaQuery['compare']) ? $this->sqlOperatorToODataOperator(
                        $wpMetaQuery['compare']
                    )
                        ?? $wpMetaQuery['compare'] : 'eq';  // Convert SQL operators to OData operators, default to 'eq'

                    if ($metaKey == 'fave_property_agency' || $metaKey == 'fave_agencies') {
                        if (is_array($metaValue)) {
                            $agencyID = array_pop($metaValue);
                        }
                        $agencyMLSOfficeID = get_post_meta($agencyID, 'mls_office_id', true);

                        if ($agencyMLSOfficeID) {
                            $metaValue = [$agencyMLSOfficeID];
                        } else {
                            continue;
                        }
                    }
                    if ($metaKey == 'fave_property_agent' || $metaKey == 'fave_agents') {
                        if (is_array($metaValue)) {
                            $MLSAgentIDs = [];
                            foreach ($metaValue as $item) {
                                $tmp = get_post_meta($item, 'list_agent_mls_id', true);
                                if ($tmp == '') {
                                    continue;
                                }
                                $MLSAgentIDs[] = get_post_meta($item, 'list_agent_mls_id', true);
                            }
                            $MLSAgentIDs = array_unique($MLSAgentIDs);
                            $metaValue = $MLSAgentIDs;
                            $metaCompare = 'in';
                        } else {
                            $agentID = $metaValue;
                            $MLSAgentID = get_post_meta($agentID, 'list_agent_mls_id', true);
                            if ($MLSAgentID) {
                                $metaValue = [$MLSAgentID];
                            } else {
                                continue;
                            }
                        }
                    }

                    if ($metaRFMapping == 'UnparsedAddress') {
                        // Normalize the input address
                        // Normalize spaces and commas
                        $metaValue = trim($metaValue);
                        $metaValue = preg_replace('/\s*,\s*/', ',', $metaValue); // Normalize comma spacing
                        $metaValue = preg_replace('/\s+/', ' ', $metaValue);     // Remove excess spaces

                        // Break the address into its components (this example assumes a simple pattern of street, city, state, zip)
                        // 28665 Windbreak , Saugus, CA 91350 => [ "28665 Windbreak", "Saugus", "CA", "91350" ]
                        $addressParts = preg_split('/,/', $metaValue);

                        // Handle each part
                        $street = $addressParts[0] ?? '';
                        $city = $addressParts[1] ?? '';
                        $zipCode = $addressParts[3] ?? '';
                        // Add filters
                        if ($street) {
                            $this->addRegularFilter($this->compileQuery($metaKey, 'contains', trim($street)));
                        }
                    } else {
                        // Compile the conditions into a format suitable for RFQuery
                        $condition = $this->compileQuery($metaKey, $metaCompare, $metaValue);

                        // Add specific filters based on the comparison type
                        if ($metaCompare === 'BETWEEN') {
                            $this->addBetweenFilter($condition);  // Handle range queries
                        } else {
                            $this->addRegularFilter($condition);  // Handle standard queries
                        }
                    }
                }
            } elseif (is_array($wpMetaQuery)) {
                // Recursively handle nested queries
                $this->processNestedMetaQueries($wpMetaQuery);
            }
        }

        // Close the current filter context if a relation was originally opened
        if (isset($metaQueries['relation'])) {
            $this->RFQuery->close_filter();
        }
    }


    /**
     * Compiles a query based on meta key, comparison operator, and value into a structured array for filtering.
     *
     * This function transforms SQL-style operators into OData operators and maps meta keys to their
     * respective query configurations. Handles default values and special cases like 'LIKE' operator.
     *
     * @param string $metaKey The meta key from the query.
     * @param string $metaCompare The SQL comparison operator (e.g., '=', 'LIKE').
     * @param mixed $metaValue The value associated with the meta key.
     * @return array Compiled query conditions with RFQuery formatting.
     * @author Chandler P
     * @version 1.4.6.4
     */
    private function compileQuery(string $metaKey, string $metaCompare, mixed $metaValue): array
    {
        $data = [];

        // Get method and key configuration based on meta key
        $data['rfMethod'] = $this->mappingConfig->getQueryPostMappingMethod($metaKey) ?? 'Where';
        $data['rfKey'] = $this->mappingConfig->getQueryPostMapping($metaKey) ?? '';

        // Apply default value from configuration if available
        $data['defaultValue'] = $this->mappingConfig->getQueryPostMappingValue($metaKey);
        $data['rfOperator'] = $metaCompare;
        $data['rfValue'] = $metaValue;

        // Special handling for LIKE operator, changing method to 'contains'
        if ($data['rfOperator'] == 'LIKE') {
            $data['rfMethod'] = 'contains';
        }

        if ($data['rfOperator'] == 'IN' || $data['rfOperator'] == 'in') {
            $data['rfMethod'] = 'in';
        }

        if ($data['rfMethod'] == 'whereIn' && !is_array($data['rfValue'])) {
            $data['rfValue'] = [$data['rfValue']];
        }
        
        
        // Use the default value if one exists
        if ($data['defaultValue']) {
            $data['rfValue'] = $data['defaultValue'];
        }

        /**
         * Filter the compiled query arguments.
         *
         * @param array  $data        The compiled query data.
         * @param string $metaKey     The meta key used in the query.
         * @param string $metaCompare The comparison operator.
         * @param mixed  $metaValue   The value for comparison.
         */
        return apply_filters('mls_on_the_fly_compile_query', $data, $metaKey, $metaCompare, $metaValue);
    }



    /**
     * Adds a regular filter to the query based on the given condition.
     *
     * @param array $condition The condition array to process.
     * @return void
     * @version 1.4.6.4
     * @author Chandler P
     */
    private function addRegularFilter(array $condition): void
    {
        // Adjust method and operator for single 'in' conditions
        if (
            ($condition['rfMethod'] == 'in' || $condition['rfMethod'] == 'IN') && is_array(
                $condition['rfValue']
            ) && count($condition['rfValue']) == 1
        ) {
            $condition['rfMethod'] = 'where';
            $condition['rfOperator'] = 'eq';
            $condition['rfValue'] = $condition['rfValue'][0];
        }

        // Adjust method and operator for single 'in' conditions
        if (($condition['rfMethod'] == 'in' || $condition['rfMethod'] == 'IN') && !is_array($condition['rfValue'])) {
            $condition['rfMethod'] = 'where';
            $condition['rfOperator'] = 'eq';
        }

        $this->RFQuery->add_filter(
            method: $condition['rfMethod'],
            key: trim($condition['rfKey']),
            operator: $condition['rfOperator'],
            value: $condition['rfValue'],
        );
    }

    /**
     * Adds a filter for BETWEEN comparisons specifically.
     *
     * @param array $condition The condition array to process.
     * @return void
     * @version 1.4.6.4
     * @author Chandler P
     */
    private function addBetweenFilter(array $condition): void
    {
        // Ensure value is an array and contains exactly two elements
        if (is_array($condition['rfValue']) && count($condition['rfValue']) == 2) {
            // Swap values if necessary to maintain order
            if ($condition['rfValue'][0] > $condition['rfValue'][1]) {
                // Handle filtering for inverted ranges
                $this->RFQuery->add_filter(
                    method: $condition['rfMethod'],
                    key: trim($condition['rfKey']),
                    operator: 'le',
                    value: $condition['rfValue'][0],
                );

                $this->RFQuery->add_filter(
                    method: $condition['rfMethod'],
                    key: trim($condition['rfKey']),
                    operator: 'ge',
                    value: $condition['rfValue'][1],
                );
            } else {
                $this->RFQuery->add_filter(
                    method: $condition['rfMethod'],
                    key: trim($condition['rfKey']),
                    operator: 'ge',
                    value: $condition['rfValue'][0],
                );

                $this->RFQuery->add_filter(
                    method: $condition['rfMethod'],
                    key: trim($condition['rfKey']),
                    operator: 'le',
                    value: $condition['rfValue'][1],
                );
            }
        }
    }

    /**
     * Converts SQL comparison operators to OData comparison operators.
     * This helps align SQL query semantics with OData service expectations.
     *
     * @param string $sqlOperator The SQL operator to convert.
     * @return string|null The corresponding OData operator, or null if no match is found.
     */
    private function sqlOperatorToODataOperator(string $sqlOperator): ?string
    {
        // Mapping of SQL operators to OData operators
        $operatorMap = [
            '=' => 'eq',   // Equal
            '<>' => 'ne',  // Not equal
            '!=' => 'ne',  // Not equal
            '<' => 'lt',   // Less than
            '<=' => 'le',  // Less than or equal to
            '>' => 'gt',   // Greater than
            '>=' => 'ge',  // Greater than or equal to
            // Additional mappings can be added here as needed
        ];

        // Return the OData equivalent of the provided SQL operator
        return $operatorMap[$sqlOperator] ?? null;
    }

    /**
     * Adds taxonomy-based filters to the RFQuery based on taxonomy queries from WordPress.
     * This method processes complex taxonomy queries including hierarchical relationships.
     *
     * @return void
     */
    private function addTaxonomyFilters(): void
    {
        $WPTaxQueries = $this->extractTaxQueryInfo($this->wp_query->tax_query->queries ?? []);
        $queries = [];

        foreach ($WPTaxQueries as $WPTaxQuery) {
            $taxonomy = $this->normalizeTaxonomy($WPTaxQuery['taxonomy']);
            $terms = $WPTaxQuery['terms'] ?? [];

            foreach ($terms as $term) {
                $WPTerm = get_term_by($WPTaxQuery['field'] ?? '', $term, $taxonomy);

                if ($WPTerm) {
                    $this->mapTermToRFQuery($WPTerm, $taxonomy, $queries);
                } elseif (str_contains($taxonomy, 'advance_search')) {
                    $this->mapRawTermToRFQuery($term, $taxonomy, $queries);
                }
            }
        }

        $this->applyRFQueryFilters($queries);
    }

    private function normalizeTaxonomy(string $taxonomy): string
    {
        return str_contains($taxonomy, '|') ? explode('|', $taxonomy)[0] : $taxonomy;
    }

    private function getReplacementGroups(array $replaces): array
    {
        $groups = [];
        foreach ($replaces as $replace) {
            $groups[$replace['replace']][] = $replace['search'];
        }
        return $groups;
    }

    private function getAllEquivalentTerms(string $term, array $groups): array
    {
        foreach ($groups as $replacement => $terms) {
            if (in_array($term, $terms, true)) {
                return $terms; // All terms that map to this group
            }
        }
        return [$term]; // No group match; use original term
    }


    private function mapTermToRFQuery($WPTerm, string $taxonomy, array &$queries): void
    {
        $replaces = $this->mappingConfig->getQueryTaxonomyReplaces($taxonomy);
        $replacementGroups = $this->getReplacementGroups($replaces);
        $equivalentTerms = $this->getAllEquivalentTerms($WPTerm->name, $replacementGroups);

        $RFField = $this->mappingConfig->getQueryTaxonomyMapping($taxonomy);
        $RFChildField = $this->mappingConfig->getQueryTaxonomyChild($taxonomy);

        if ($RFChildField && $WPTerm->parent) {
            $ParentWPTerm = get_term_by('id', $WPTerm->parent, $taxonomy);
            if ($ParentWPTerm) {
                $parentEquivalents = $this->getAllEquivalentTerms($ParentWPTerm->name, $replacementGroups);
                if (is_array($RFField)) {
                    $RFField = $RFField[0];
                }
                $queries[$RFField] = array_merge($queries[$RFField] ?? [], $parentEquivalents);
                $queries[$RFChildField] = array_merge($queries[$RFChildField] ?? [], $equivalentTerms);
            }
        } else {
            $this->addTermToQueries($queries, $RFField, $equivalentTerms);
        }
    }


    private function mapRawTermToRFQuery(string $term, string $taxonomy, array &$queries): void
    {
        $replaces = $this->mappingConfig->getQueryTaxonomyReplaces($taxonomy);
        $term = $this->applyReplacements($term, $replaces);
        $RFField = $this->mappingConfig->getQueryTaxonomyMapping($taxonomy);
        $this->addTermToQueries($queries, $RFField, $term);
    }

    private function addTermToQueries(array &$queries, $RFField, array $terms): void
    {
        if (is_array($RFField)) {
            $queries['open'] = 'OR';
            foreach ($RFField as $SingleRFField) {
                $queries[$SingleRFField] = array_merge($queries[$SingleRFField] ?? [], $terms);
            }
            $queries['close'] = 'OR';
        } else {
            $queries[$RFField] = array_merge($queries[$RFField] ?? [], $terms);
        }
    }

    private function applyRFQueryFilters(array $queries): void
    {
        foreach ($queries as $RFField => $RFValues) {
            switch ($RFField) {
                case 'open':
                    $this->RFQuery->open_filter($RFValues);
                    break;
                case 'close':
                    $this->RFQuery->close_filter();
                    break;
                default:
                    $RFValues = array_unique($RFValues);
                    $count = count($RFValues);
                    if ($count > 0) {
                        if ($count > 1) {
                            $this->RFQuery->add_filter(
                                method: 'in',
                                key: $RFField,
                                operator: 'eq',
                                value: $RFValues
                            );
                        } else {
                            $this->RFQuery->add_filter(
                                method: 'where',
                                key: $RFField,
                                operator: 'eq',
                                value: reset($RFValues)
                            );
                        }
                    }
                    break;
            }
        }
    }


    /**
     * Extracts and simplifies taxonomy query information from WordPress.
     * This method organizes taxonomy queries into a more uniform format, focusing on the essential elements used in filtering operations.
     *
     * @param array $taxQueries Array of raw taxonomy queries as obtained from WordPress.
     * @return array An array of simplified taxonomy query information, making it easier to process further.
     */
    private function extractTaxQueryInfo(array $taxQueries): array
    {
        $extracted = []; // Initialize the array to hold simplified taxonomy queries.

        // Iterate through each original taxonomy query
        foreach ($taxQueries as $taxQuery) {
            // Ensure that the essential parts of the taxonomy query are present
            if (isset($taxQuery['taxonomy'], $taxQuery['terms'], $taxQuery['field'])) {
                // Simplify and structure the taxonomy query into a more manageable format
                $extracted[] = [
                    'taxonomy' => $taxQuery['taxonomy'],
                    // The type of taxonomy (e.g., category, tag)
                    'terms' => (array) $taxQuery['terms'],
                    // The terms within the taxonomy, cast to array for uniformity
                    'field' => $taxQuery['field']
                    // The field by which terms are identified (e.g., slug, term_id)
                ];
            }
        }

        // Return the array of simplified taxonomy queries
        return $extracted;
    }


    /**
     * Adds filters to the RFQuery based on the post types specified in the WordPress query.
     * This method configures filters to select only those entries that match the specified post types,
     * supporting multiple post types with logical OR conditions if necessary.
     *
     * @return void
     * @author Chandler P <chandler.p@realtyna.net>
     * @version 1.4.6.4
     */
    private function addPostTypeFilters(): void
    {
        // Retrieve the post types from the WordPress query
        $postTypes = $this->wp_query->query['post_type'];

        // Check if any post types are specified for filtering
        if ($postTypes) {
            // Open an OR filter if there are multiple post types to allow any of them to match
            if (count($postTypes) > 1) {
                $this->RFQuery->open_filter('OR');
            }

            // Apply a filter for each post type
            foreach ($postTypes as $postType) {
                $this->configurePostTypeFilter($postType);
            }

            // Close the OR filter if it was opened
            if (count($postTypes) > 1) {
                $this->RFQuery->close_filter();
            }
        }
    }

    /**
     * Configures a filter within the RFQuery for a specific post type.
     * This helper function is called for each post type needing to be filtered.
     *
     * @param string $postType The post type to configure within the query.
     */
    private function configurePostTypeFilter(string $postType): void
    {
        // Retrieve the mapping for the post type to ensure correct field and value association in RFQuery
        $postTypeMapping = $this->mappingConfig->getQueryPostTypeMapping($postType);
        if ($postTypeMapping) {
            // Set the filter based on the mapping provided
            $rfMethod = $this->mappingConfig->getQueryPostTypeMethod($postType);
            $rfKey = $this->mappingConfig->getQueryPostTypeMapping($postType);
            $rfOperator = $this->mappingConfig->getQueryPostTypeOperator($postType) ?? 'eq';
            $rfValue = $this->mappingConfig->getQueryPostTypeValue($postType);

            // Add the filter to the RFQuery
            $this->RFQuery->add_filter(
                method: $rfMethod,
                key: trim($rfKey),
                operator: $rfOperator,
                value: $rfValue,
                boolean: 'or' // Specify the boolean condition as 'or' if multiple post types are involved
            );
        }
    }

    private function addRFParams(): void
    {
        if (isset($this->wp_query->query['rf_params']) && !empty($this->wp_query->query['rf_params'])) {
            $this->RFQuery->set_params($this->wp_query->query['rf_params']);
        }
    }
}
