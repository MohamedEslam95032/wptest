<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF;

use Exception;
use Realtyna\MlsOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\CacheManager\Cache;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities\RFLookup;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities\RFProperty;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities\RFPropertyAdditionalInfo;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Exceptions\EntityNotDefinedException;
use Realtyna\MlsOnTheFly\Settings\Settings;
use Realtyna\OData\Exceptions\ODataHttpClientException;
use Realtyna\OData\Exceptions\ODataResponseException;
use Realtyna\OData\Interfaces\AuthenticatorInterface;
use Realtyna\OData\ODataHttpClient;
use Realtyna\OData\ODataQueryBuilder;

/**
 * Class RF
 * @package Realtyna\MlsOnTheFly\SDK\RF
 */
class RF
{
    private string $baseURL = 'https://api.realtyfeed.com/reso/odata';
    private ODataHttpClient $client;
    private bool $cache = true;
    private RFQuery $query;
    private AuthenticatorInterface $authenticator;

    /**
     * RF constructor.
     *
     * @param $clientId
     * @param $clientSecret
     * @param $apiKey
     *
     * @throws Exception
     */
    public function __construct($clientId, $clientSecret, $apiKey)
    {
        $this->authenticator = new OAuth2Authenticator($clientId, $clientSecret, $apiKey);
        $this->client = new ODataHttpClient($this->baseURL, $this->authenticator);
    }

    public function metadata(): ?array
    {
        try {
            return $this->client->get('/v1/$metadata');
        } catch (ODataHttpClientException|ODataResponseException $e) {
            Log::error($e->getMessage());
        }
        return null;
    }

    /**
     * Get data from the API using the provided query.
     *
     * @param RFQuery $query
     * @param array $args
     * @return RFResponse|null
     *
     * @throws EntityNotDefinedException
     */
    public function get(RFQuery $query, array $args = []): ?RFResponse
    {
        if ($query->get_entity() == '') {
            throw new EntityNotDefinedException('Entity is not defined');
        }
        if (!isset($args['ignore_global_filter'])) {
            $args['ignore_global_filter'] = false;
        }
        if (!isset($args['ignore_featured_properties'])) {
            $args['ignore_featured_properties'] = false;
        }

        if (!isset($args['expand'])) {
            $args['expand'] = true;
        }

        if (!isset($args['count'])) {
            $args['count'] = true;
        }

        if (!isset($args['ignore_cache'])) {
            $args['ignore_cache'] = false;
        }
        $this->query = $query;
        $cacheKey = $query->getCacheKey();

        // Check if cache is available
        if ($this->cache && !$args['ignore_cache']) {
            $cachedResponse = Cache::get("RF-Query-Cache-$cacheKey");
            if ($cachedResponse) {
                add_filter('mls_on_the_fly_requests', function ($requests) use ($cacheKey, $cachedResponse) {
                    $data = [];
                    $data['RF Cached Response'] = $cachedResponse;
                    $requests["RF Cache Key: $cacheKey"] = $data;
                    return $requests;
                });
                return $cachedResponse;
            }
        }

        $client = $this->client;

        // Extract relevant properties from the provided object
        $entity = $query->get_entity();
        $find = $query->get_find();
        $top = $query->get_top();
        $skip = $query->get_skip();
        $select = $query->get_select();
        $filters = $query->get_filters();
        $orders = $query->get_orders();
        $groups = $query->get_groups();
        $after_key = $query->get_after_key();
        $custom_params = $query->get_custom_params();

        if (empty($select)) {
            $select = ['ALL'];
        }

        if ($entity == 'v1/Lookup') {
            $returnEntityType = RFLookup::class;
        } elseif ($entity == 'PropertyAdditionalInfo') {
            $returnEntityType = RFPropertyAdditionalInfo::class;
        } else {
            $returnEntityType = RFProperty::class;
        }
        $queryBuilder = new ODataQueryBuilder('/' . $entity);
        $queryBuilder->select($select);

        if ($find != '') {
            $queryBuilder->addFilter('ListingKey', 'eq', $find);
        } else {
            // Set the $top and $skip options
            if ($top >= 0) {
                $queryBuilder->top($top);
            }
            if ($skip >= 0) {
                $queryBuilder->skip($skip);
            }
            foreach ($groups as $group) {
                $queryBuilder->groupBy($group);
                $returnEntityType = 'array';
            }

            // Loop through and set filters
            foreach ($filters as $filter) {
                if (isset($filter['type']) && $filter['type'] === 'open') {
                    $queryBuilder->openFilter($filter['relation']);
                } elseif (isset($filter['type']) && $filter['type'] === 'close') {
                    $queryBuilder->closeFilter();
                } else {
                    $queryBuilder->addFilter(
                        $filter['key'],
                        $filter['operator'],
                        $filter['value'],
                        $filter['boolean'],
                        $filter['method']
                    );
                }
            }

            // Set the $orderby options
            foreach ($orders as $order) {
                $queryBuilder->orderBy($order);
            }
        }

        // Get the constructed OData query
        $odataQuery = $queryBuilder->buildQueryUrl();


        if (!$args['ignore_global_filter']) {
            // Check if global filters exist
            $globalFilters = get_option('realtyna_mls_on_the_fly_global_filters', []);
            // Join the global filters using "and"
            $globalFilter = implode(' and ', $globalFilters);
            if ($globalFilter && $globalFilter != '( )' && $globalFilter != '()' && $returnEntityType != RFLookup::class) {
                // Check if there's an existing filter in the query
                if (str_contains($odataQuery, '$filter=')) {
                    // The default query contains a filter, so merge the global filter with the existing filter
                    $odataQuery = str_replace('$filter=', '$filter=(' . $globalFilter . ') and ', $odataQuery);
                } else {
                    // The default query does not contain a filter, so add the global filter as the filter
                    if (str_contains($odataQuery, '?')) {
                        // The query contains "?", so use "&" to append the filter
                        $odataQuery .= '&$filter=(' . $globalFilter . ')';
                    } else {
                        // The query doesn't contain "?", so use "?"
                        $odataQuery .= '?$filter=(' . $globalFilter . ')';
                    }
                }
            }
        }

        if (!$args['ignore_featured_properties']) {
            if (!str_contains($odataQuery, 'groupby')) {
                $featuredListingsKey = Settings::get_setting('featured_key', 'none');
                $featuredListingsValues = Settings::get_setting('featured_values', []);
                if ($featuredListingsKey != 'none' && count($featuredListingsValues) > 0) {
                    foreach ($featuredListingsValues as $key => $value) {
                        $featuredListingsValues[$key] = "'$value'";
                    }
                    if (count($featuredListingsValues) == 1) {
                        $odataQuery .= '&$feature=' . $featuredListingsKey . ' eq ' . $featuredListingsValues[0];
                    } else {
                        $odataQuery .= '&$feature=' . $featuredListingsKey . ' in (' . implode(
                                ',',
                                $featuredListingsValues
                            ) . ')';
                    }
                }
            }
        }
        if (!empty($custom_params)) {
            foreach ($custom_params as $custom_param_key => $custom_param_value) {
                $odataQuery .= "&$custom_param_key=$custom_param_value";
            }
        }

        $odataQuery = str_replace("\t", '', $odataQuery);
        $odataQuery = str_replace(' AND ()', '', $odataQuery);
        $odataQuery = str_replace(' OR ()', '', $odataQuery);
        $odataQuery = str_replace(' and ()', '', $odataQuery);
        $odataQuery = str_replace(' or ()', '', $odataQuery);
        if ($args['expand']) {
            $odataQuery .= $odataQuery . '&$expand=Media';
        }
        if ($args['count']) {
            $odataQuery .= $odataQuery . '&$count=true';
        }

        if ($after_key) {
            $odataQuery = $odataQuery . '&$after_key=' . $after_key;
        }

        try {
            Log::info("HTTP Client Request Started", [$odataQuery]);
            //start timing to get response time
            $responseTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $data = $client->get($odataQuery);
            //end timing to get response time
            $responseTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"] - $responseTime;
            //make response time in seconds and round it to 2 decimal places and make it half of the original value
            $responseTime = round($responseTime, 2) . " seconds";
            Log::info("HTTP Client Request Finished", [$odataQuery]);

            $items = $client->getResponseParser()->extractEntities($data, $returnEntityType);
            // Check if there are more pages to fetch
            if (isset($data['@odata.nextLink']) && $data['@odata.nextLink'] !== '') {
                $data['hasNextPage'] = true;
            }

            $data['items'] = $items;
            $response = new RFResponse($data);

            add_filter('mls_on_the_fly_requests', function ($requests) use ($odataQuery, $response, $responseTime) {
                $data = [];
                $data['RF Response'] = $response;
                $data['RF Response Time'] = $responseTime;
                $requests["RF Query: $odataQuery"] = $data;
                return $requests;
            });

            if ($this->cache) {
                Cache::set("RF-Query-Cache-$cacheKey", $response, 300); // Cache the response
            }

            return $response;
        } catch (ODataHttpClientException|ODataResponseException $e) {
            if ($this->cache) {
                $cachedResponse = Cache::get("RF-Query-Cache-$cacheKey", true);
                if ($cachedResponse) {
                    return $cachedResponse;
                }
            }
            Log::error($e->getMessage());
            return new RFResponse([]);
        }
    }


    /**
     * Convert query filters to OData conditions.
     *
     * @return array
     */
    public function turnToOdataConditions(): array
    {
        $groupedFilters = $this->query->get_filters()->groupBy('key');
        $wheres = [];
        if (is_array($groupedFilters) || is_object($groupedFilters)) {
            foreach ($groupedFilters as $key => $filters) {
                $wheres[$key] = $wheres[$key] ?? [];
                foreach ($filters as $filter) {
                    $method = $filter['method'];
                    switch ($method) {
                        case 'orWhere':
                        case 'where':
                            $wheres[$key][$method][] = [
                                $filter['key'],
                                $filter['operator'],
                                $filter['value'],
                                $filter['boolean']
                            ];
                            break;
                        case 'whereIn':
                        case 'orWhereIn':
                        case 'whereNotIn':
                        case 'orWhereNotIn':
                            $wheres[$key][$method][] = [$filter['key'], $filter['operator'], $filter['value']];
                            break;
                        case 'whereNotNull':
                        case 'orWhereNull':
                        case 'orWhereNotNull':
                        case 'whereNull':
                            $wheres[$key][$method][] = [$filter['key']];
                            break;
                        default:
                            echo '';
                    }
                }
            }
        }

        return $wheres;
    }


    /**
     * @return ODataHttpClient
     */
    public function getClient(): ODataHttpClient
    {
        return $this->client;
    }

    /**
     * @param RFQuery $query
     */
    public function setQuery(RFQuery $query): void
    {
        $this->query = $query;
    }

    /**
     * @return bool
     */
    public function isCache(): bool
    {
        return $this->cache;
    }

    /**
     * @param bool $cache
     */
    public function setCache(bool $cache): void
    {
        $this->cache = $cache;
    }


    /**
     * @param ODataHttpClient $client
     */
    public function setClient(ODataHttpClient $client): void
    {
        $this->client = $client;
    }

    public function getAuthenticator(): AuthenticatorInterface
    {
        return $this->authenticator;
    }

}
