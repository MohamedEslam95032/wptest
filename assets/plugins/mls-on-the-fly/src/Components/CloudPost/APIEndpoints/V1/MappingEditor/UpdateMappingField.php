<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor;

use Realtyna\Core\Abstracts\RestApiEndpointAbstract;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;
use WP_REST_Request;
use WP_Error;

class UpdateMappingField extends RestApiEndpointAbstract
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
                'methods' => 'PUT',
                'callback' => [$this, 'handleRequest'],
                'permission_callback' => [$this, 'checkPermissions'],
            ]
        );
    }

    public function handleRequest(WP_REST_Request $request): \WP_Error|\WP_REST_Response
    {
        $application = $request->get_param('application');
        $type = $request->get_param('type');
        $field = $request->get_param('field');
        $data = $request->get_params();

        if (empty($data)) {
            return $this->sendJsonError('Invalid or empty request data', 400);
        }

        $mappingData = $this->mapping->mappingConfig->getMappings($type);
        $mappingData[$field] = [
            'mapping' => $data['mapping'] ?? '',
            'default' => $data['default'] ?? '',
            'replaces' => $this->buildReplacesArray($data)
        ];

        $optionName = "realtyna_mapping_{$application}_{$type}";
        update_option($optionName, $mappingData);

        return $this->sendJsonResponse($mappingData[$field]);
    }

    protected function buildReplacesArray(array $data): array
    {
        $replaces = [];
        if (!empty($data['search']) && !empty($data['replace'])) {
            foreach ($data['search'] as $index => $searchValue) {
                $replaces[] = [
                    'search' => $searchValue,
                    'replace' => $data['replace'][$index] ?? ''
                ];
            }
        }
        return $replaces;
    }

    public function checkPermissions(): bool
    {
        return current_user_can('administrator');
    }
}
