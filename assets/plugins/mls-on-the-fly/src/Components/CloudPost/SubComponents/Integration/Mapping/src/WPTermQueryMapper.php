<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src;

use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RFQuery;
use WP_Term_Query;

class WPTermQueryMapper
{
    private $mappingConfig;

    public function __construct(MappingConfig $mappingConfig)
    {
        $this->mappingConfig = $mappingConfig;
    }

    public function mapTermQueryToRFQuery(WP_Term_Query $termQuery): RFQuery|array|null
    {
        // Get the mapping configuration for query parameters.

        // Extract taxonomies from the term query request.
        $taxonomies = $this->extractTaxonomiesFromTermQuery($termQuery);

        // If no taxonomies are found, return null.
        if (empty($taxonomies)) {
            return null;
        }

        // Get the first taxonomy from the list.
        $taxonomy = $taxonomies[0];

        // Get the RF field mapping for the taxonomy.
        $RFFields = $this->mappingConfig->getQueryTaxonomyMapping($taxonomy);
        $RFChildField = $this->mappingConfig->getQueryTaxonomyChild($taxonomy);

        if ($RFFields == null) {
            return null;
        }

        if (is_string($RFFields)) {
            $RFFields = [$RFFields];
        }

        // Set groups for the RFQuery based on the RF field.
        $result = [];
        foreach ($RFFields as $RFField) {
            // Create a new RFQuery object for terms.
            $RFQuery = new RFQuery();
            $RFQuery->set_top(10);
            if (isset($termQuery->query_vars['after_key'])) {
                $RFQuery->set_after_key($termQuery->query_vars['after_key']);
            } else {
                $RFQuery->set_top(0);
            }
            if (isset($termQuery->query_vars['parent_name']) && $termQuery->query_vars['parent_name'] != '' && $RFChildField) {

                $RFQuery->add_filter(is_array($termQuery->query_vars['parent_name']) ? 'in' : ' where', $RFField, is_array($termQuery->query_vars['parent_name']) ? 'in' : ' eq', $termQuery->query_vars['parent_name']);
                $RFQuery->set_groups([$RFChildField, 'aggregate($count as ' . $RFChildField . 'Count)']);
            } else {
                $RFQuery->set_groups([$RFField, 'aggregate($count as ' . $RFField . 'Count)']);

            }
            $result[] = $RFQuery;
        }
        return $result;
    }

    /**
     * Extracts taxonomies from the given WP_Term_Query object.
     *
     * @param \WP_Term_Query $WP_Term_Query The WordPress term query object.
     *
     * @return array The list of extracted taxonomies.
     */
    private function extractTaxonomiesFromTermQuery(\WP_Term_Query $WP_Term_Query): array
    {
        $taxonomies = [];

        // Extract taxonomies from the term query request.
        if ($WP_Term_Query->request != null) {
            preg_match_all("/tt.taxonomy IN \((.*?)\)/", $WP_Term_Query->request, $taxonomies);
            $taxonomies = array_map('trim', explode(', ', str_replace('\'', '', $taxonomies[1][0])));
        }

        return $taxonomies;
    }

}