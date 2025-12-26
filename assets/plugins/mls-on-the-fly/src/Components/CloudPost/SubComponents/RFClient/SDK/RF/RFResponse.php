<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF;


/**
 * Class RFResponse
 * Represents a response from the RF (RealtyFeed) API.
 *
 * @package Realtyna\MlsOnTheFly\SDK\RF
 */
class RFResponse
{
    /**
     * @var array|mixed $items An array of items in the response.
     */
    public mixed $items;

    /**
     * @var false|mixed $success A boolean indicating the success of the API request.
     */
    public bool $success;

    /**
     * @var mixed|string $page_size The number of items per page in the response.
     */
    public int $page_size;

    /**
     * @var mixed|string $page_count The total number of pages in the response.
     */
    public int $page_count;

    /**
     * @var mixed|string $count The total count of items in the response.
     */
    public int $count;
    public string $after_key = '';

    /**
     * RFResponse constructor.
     */
    public function __construct($body)
    {
        $this->success = $body['success'] ?? true;
        $this->page_size = $body['page_size'] ?? 0;
        $this->page_count = $body['page_count'] ?? 0;
        $this->count = $body['@odata.count'] ?? 0;
        $this->items = $body['items'] ?? [];

        if(isset($body['hasNextPage']) && $body['hasNextPage'] == true){
           $nextLink = $body['@odata.nextLink'];
            // Parse the URL to get its components
            $urlComponents = parse_url($nextLink);
            // Check if the URL contains a query string
            if (isset($urlComponents['query'])) {
                // Parse the query string to get its parameters
                parse_str($urlComponents['query'], $queryParams);

                // Check if the 'after_key' parameter exists
                if (isset($queryParams['after_key'])) {
                    $this->after_key = $queryParams['after_key'];
                }
            }
        }
    }
}
