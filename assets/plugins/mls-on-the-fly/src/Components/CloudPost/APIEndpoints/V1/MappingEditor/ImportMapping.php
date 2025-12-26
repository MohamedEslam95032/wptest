<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor;

use Realtyna\Core\Abstracts\RestApiEndpointAbstract;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;
use WP_REST_Request;
use WP_Error;

class ImportMapping extends RestApiEndpointAbstract
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
            '/mappings/(?P<application>[\w-]+)/(?P<type>[\w-]+)',
            [
                'methods' => 'POST',
                'callback' => [$this, 'handleRequest'],
                'permission_callback' => [$this, 'checkPermissions'],
            ]
        );
    }

    public function handleRequest(WP_REST_Request $request): \WP_Error|\WP_REST_Response
    {
        $application = $request->get_param('application');
        $type = $request->get_param('type');
        $data = $request->get_param('mapping_data');

        if (empty($data)) {
            return $this->sendJsonError('Invalid or empty request data', 400);
        }

        $mappingData = json_decode($data, true);


        $optionName = "realtyna_mapping_{$application}_{$type}";

        update_option($optionName, $mappingData);

        return $this->sendJsonResponse($mappingData);
    }

    public function checkPermissions(): bool
    {
        return current_user_can('administrator');
    }
}
