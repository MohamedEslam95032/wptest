<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping;

use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src\MappingConfig;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src\RFPropertyMapper;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src\RFTermMapper;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src\WPQueryMapper;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src\WPTermQueryMapper;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities\RFProperty;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RFQuery;
use WP_Term_Query;

class Mapping
{
    public MappingConfig $mappingConfig;
    /**
     * @var mixed|Mapping
     */
    private WPTermQueryMapper $termQueryMapper;
    private RFTermMapper $RFTermMapper;
    private RFPropertyMapper $RFPropertyMapper;
    private WPQueryMapper $WPQueryMapper;
    private IntegrationInterface $integration;

    public function __construct(IntegrationInterface $integration)
    {
        $this->integration = $integration;
        $this->mappingConfig = new MappingConfig($integration);
        $this->termQueryMapper = new WPTermQueryMapper($this->mappingConfig);
        $this->RFTermMapper = new RFTermMapper($this->mappingConfig);
        $this->RFPropertyMapper = new RFPropertyMapper($this->mappingConfig);
        $this->WPQueryMapper = new WPQueryMapper($this->mappingConfig);
    }

    public function mapTermQueryToRFQuery(WP_Term_Query $termQuery): RFQuery|array|null
    {
        return $this->termQueryMapper->mapTermQueryToRFQuery($termQuery);
    }

    public function mapRFTermsToWPTerms(array $RFTerms, string $taxonomy, $parent = ''): array
    {
        return $this->RFTermMapper->mapToWPTerm($RFTerms, $taxonomy, $parent);
    }

    public function mapRFTermToWPTerm(RFTermEntity $RFTerm, $taxonomy): \WP_Term
    {
        return $this->RFTermMapper->mapSingleToWPTerm($RFTerm, $taxonomy);
    }


    public function mapRFPropertyToWPTerm($RFProperty, $taxonomy, $term_query): array
    {
        return $this->RFPropertyMapper->mapRFPropertyTPWPTerm($RFProperty, $taxonomy, $term_query);
    }

    public function mapWPQueryRFQuery(\WP_Query $wp_query): RFQuery
    {
        return $this->WPQueryMapper->mapWPQueryRFQuery($wp_query);
    }

    public function mapRFPropertiesToWPPost(array $RFProperties, \WP_Query $wp_query): array
    {
        $results = array();
        foreach ($RFProperties as $RFProperty){
            $results[] = $this->RFPropertyMapper->mapRFPropertyToWPPost($RFProperty,$wp_query);
        }

        return $results;
    }

    public function mapRFPropertyToWPPost(RFProperty $RFProperty, ?\WP_Query $wp_query = null): \WP_Post
    {
        return $this->RFPropertyMapper->mapRFPropertyToWPPost($RFProperty, $wp_query);
    }

    public function getIntegration(): IntegrationInterface
    {
        return $this->integration;
    }


}