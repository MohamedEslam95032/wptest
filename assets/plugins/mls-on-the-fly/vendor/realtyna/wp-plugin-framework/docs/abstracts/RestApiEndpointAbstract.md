# RestApiEndpointAbstract

The `RestApiEndpointAbstract` class provides a standardized structure for creating and handling REST API endpoints in WordPress plugins. By extending this abstract class, developers can easily define RESTful routes and manage API requests and responses.

## Purpose

The primary purpose of `RestApiEndpointAbstract` is to:

- **Simplify REST API Endpoint Creation**: Provide a reusable framework for defining and handling REST API endpoints in WordPress.
- **Encapsulate Endpoint Logic**: Allow developers to encapsulate the logic for handling API requests and responses in a single, reusable class.
- **Ensure Consistent Responses**: Provide methods for returning JSON responses and handling errors in a consistent manner.

## Key Methods

### Abstract Methods

These methods must be implemented by any class that extends `RestApiEndpointAbstract`:

- **`registerRoutes()`**: Registers the REST API routes. This method must be implemented to define the specific routes and associated callbacks for the API.
- **`handleRequest(\WP_REST_Request $request)`**: Handles the API request and returns a response. This method must be implemented to process the incoming request and generate the appropriate response.

### Concrete Methods

These methods are already implemented in the `RestApiEndpointAbstract` class and provide common functionality:

- **`__construct()`**: Registers the `registerRoutes()` method with the `rest_api_init` action hook, ensuring that the API routes are registered when WordPress initializes its REST API.
- **`sendJsonResponse(mixed $data, int $status_code = 200)`**: Sends a JSON response to the client. This method is useful for returning data from the API in a structured format.
- **`sendJsonError(string $message, int $status_code = 400)`**: Sends a JSON error response to the client. This method is useful for returning error messages when something goes wrong with the API request.

## Example Usage

Here’s an example of how you might use the `RestApiEndpointAbstract` class to create a `TestRestApiEndpoint`:

```php
namespace Realtyna\BasePlugin\Components\Test\RestApiEndpoints;

use Realtyna\Core\Abstracts\RestApiEndpointAbstract;

class TestRestApiEndpoint extends RestApiEndpointAbstract
{
    public function registerRoutes(): void
    {
        register_rest_route('test/v1', '/data', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => '__return_true', // Modify as needed
        ]);
    }

    public function handleRequest(\WP_REST_Request $request): \WP_Error|\WP_REST_Response
    {
        // Process the request (example)
        $data = [
            'message' => __('Hello, this is a REST API response!', 'text-domain'),
            'status' => 'success',
        ];

        return $this->sendJsonResponse($data);
    }
}
```

### Explanation

- **`registerRoutes()`**: Registers a REST API route at `test/v1/data` with a GET method. The `handleRequest()` method is specified as the callback function to handle the request.
- **`handleRequest(\WP_REST_Request $request)`**: Processes the API request and returns a JSON response. In this example, it returns a simple success message.

### How It Works

1. **Route Registration**: When the `TestRestApiEndpoint` class is instantiated, the `registerRoutes()` method is automatically called via the `rest_api_init` action hook. This registers the API route with WordPress.
2. **Request Handling**: When a request is made to the registered route, WordPress calls the `handleRequest()` method. This method processes the request, generates a response, and returns it to the client.
3. **Response Management**: The response is returned as a JSON object using the `sendJsonResponse()` method, ensuring that the data is structured consistently.

## Conclusion

The `RestApiEndpointAbstract` class simplifies the process of creating custom REST API endpoints in WordPress plugins by providing a consistent structure and handling much of the boilerplate code for you. By extending this class, developers can focus on the specific logic and behavior of their API endpoints while ensuring that best practices are followed.
