<?php

namespace Realtyna\Core\Abstracts;

/**
 * Class RestApiEndpointAbstract
 *
 * An abstract class that defines the structure for handling REST API endpoints in WordPress.
 *
 * @package Realtyna\Core\Abstracts
 */
abstract class RestApiEndpointAbstract
{
    /**
     * RestApiEndpointAbstract constructor.
     *
     * Registers the REST API route with WordPress.
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    /**
     * Registers the routes for the REST API endpoint.
     *
     * This method should be implemented by child classes to define the REST API routes.
     *
     * @return void
     */
    abstract public function registerRoutes(): void;

    /**
     * Handle the request and return a response.
     *
     * This method should be implemented by child classes to process the request and return a response.
     *
     * @param \WP_REST_Request $request The request object.
     * @return \WP_REST_Response|\WP_Error The response object or WP_Error on failure.
     */
    abstract public function handleRequest(\WP_REST_Request $request): \WP_Error|\WP_REST_Response;

    /**
     * Send a JSON response.
     *
     * @param mixed $data The data to send in the JSON response.
     * @param int $status_code The HTTP status code (default is 200).
     * @return \WP_REST_Response
     */
    protected function sendJsonResponse(mixed $data, int $status_code = 200): \WP_REST_Response
    {
        return new \WP_REST_Response($data, $status_code);
    }

    /**
     * Send a JSON error response.
     *
     * @param string $message The error message to send in the JSON response.
     * @param int $status_code The HTTP status code (default is 400).
     * @return \WP_Error
     */
    protected function sendJsonError(string $message, int $status_code = 400): \WP_Error
    {
        return new \WP_Error('rest_error', $message, ['status' => $status_code]);
    }
}