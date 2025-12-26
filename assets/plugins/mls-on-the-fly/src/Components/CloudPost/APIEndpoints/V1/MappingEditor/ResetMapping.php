<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor;

use Realtyna\Core\Abstracts\RestApiEndpointAbstract;
use WP_REST_Request;
use WP_Error;

class ResetMapping extends RestApiEndpointAbstract
{
    public function registerRoutes(): void
    {
        register_rest_route(
            "realtyna/mls-on-the-fly/v1",
            "/mappings/(?P<application>[\w-]+)/(?P<type>[\w-]+)",
            [
                "methods" => "DELETE",
                "callback" => [$this, "handleRequest"],
                "permission_callback" => [$this, "checkPermissions"],
            ]
        );
    }

    public function handleRequest(
        WP_REST_Request $request
    ): \WP_Error|\WP_REST_Response {
        $application = $request->get_param("application");
        $type = $request->get_param("type");
        $optionName = "realtyna_mapping_{$application}_{$type}";
        //delete option
        delete_option($optionName);
        return $this->sendJsonResponse([
            "message" => "Reset mapping successfully",
        ]);
    }

    public function checkPermissions(): bool
    {
        return current_user_can("administrator");
    }
}
