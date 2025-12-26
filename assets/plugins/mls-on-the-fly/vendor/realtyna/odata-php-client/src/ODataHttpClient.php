<?php

namespace Realtyna\OData;

use Realtyna\OData\Exceptions\ODataHttpClientException;
use Realtyna\OData\Interfaces\AuthenticatorInterface;

class ODataHttpClient
{
    private string $baseUri;
    private ODataResponseParser $responseParser;
    private AuthenticatorInterface $authenticator;

    public function __construct($baseUri, AuthenticatorInterface $authenticator)
    {
        $this->baseUri = $baseUri;
        $this->responseParser = new ODataResponseParser();
        $this->authenticator = $authenticator;
    }

    /**
     * Send an authenticated HTTP GET request.
     *
     * @param string $endpoint The API endpoint to request.
     *
     * @return array|null The parsed response data, or null on failure.
     * @throws ODataHttpClientException
     */
    public function get(string $endpoint): ?array
    {
        // Base URL and endpoint
        $baseUri = $this->baseUri;

        // Separate the path from the query string
        $parsed_url = parse_url($endpoint);
        $path = $parsed_url['path'];
        $query_string = isset($parsed_url['query']) ? $parsed_url['query'] : '';

        // Encode the query string
        parse_str($query_string, $query_params);
        $encoded_query_string = http_build_query($query_params, '', '&', PHP_QUERY_RFC3986);

        // Reconstruct the full URL
        $url = $baseUri . $path . '?' . $encoded_query_string;

        $response = wp_remote_get($url, [
            'headers' => $this->authenticator->getHeaders(),
            'timeout' => '30'
        ]);

        if (is_wp_error($response)) {
            throw new ODataHttpClientException($response->get_error_message());
        }

        // Check the HTTP status code
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($status_code !== 200) {
            throw new ODataHttpClientException("Request to $url failed with status code: $status_code. Response body: $body");
        }

        return $this->responseParser->parseResponse($body);
    }


    /**
     * Send an authenticated HTTP POST request.
     *
     * @param string $endpoint The API endpoint to request.
     * @param array $data The data to send in the request body.
     *
     * @return array|null The parsed response data, or null on failure.
     * @throws ODataHttpClientException
     */
    public function post(string $endpoint, array $data): ?array
    {
        $url = $this->baseUri . $endpoint;
        $response = wp_remote_post($url, [
            'headers' => $this->authenticator->getHeaders(),
            'body'    => wp_json_encode($data),
        ]);

        if (is_wp_error($response)) {
            throw new ODataHttpClientException($response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        return $this->responseParser->parseResponse($body);
    }

    /**
     * @return ODataResponseParser
     */
    public function getResponseParser(): ODataResponseParser
    {
        return $this->responseParser;
    }
}
