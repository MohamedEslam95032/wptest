<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor;

use Realtyna\Core\Abstracts\RestApiEndpointAbstract;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;
use WP_REST_Request;
use WP_Error;

class GetMappingField extends RestApiEndpointAbstract
{
    protected Mapping $mapping;

    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
        parent::__construct();
    }

    public function registerRoutes(): void
    {
        register_rest_route(
            'realtyna/mls-on-the-fly/v1',
            '/mappings/(?P<application>[\w-]+)/(?P<type>[\w-]+)/(?P<field>[\w-]+)',
            [
                'methods' => 'GET',
                'callback' => [$this, 'handleRequest'],
                'permission_callback' => [$this, 'checkPermissions'],
            ]
        );
    }

    public function handleRequest(WP_REST_Request $request): \WP_Error|\WP_REST_Response
    {
        $type = $request->get_param('type');
        $field = $request->get_param('field');

        $mappingData = $this->mapping->mappingConfig->getMappings($type);

        if (isset($mappingData[$field])) {
            return $this->sendJsonResponse($mappingData[$field]);
        }

        return $this->sendJsonError('Field not found in the mapping data', 404);
    }

    public function checkPermissions(): bool
    {
        return current_user_can('administrator');
    }
}
