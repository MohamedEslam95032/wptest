# AjaxHandlerAbstract

The `AjaxHandlerAbstract` class provides a standardized structure for handling AJAX requests in WordPress plugins. By extending this abstract class, developers can easily manage AJAX actions with built-in support for nonce verification, JSON responses, and error handling.

## Purpose

The primary purpose of `AjaxHandlerAbstract` is to:

- **Simplify AJAX Handling**: Provide a reusable structure for handling AJAX requests in WordPress.
- **Ensure Security**: Include nonce verification to protect against unauthorized requests.
- **Standardize Responses**: Provide methods for sending consistent JSON responses and errors.

## Key Methods

### Abstract Methods

These methods must be implemented by any class that extends `AjaxHandlerAbstract`:

- **`getAction()`**: Returns the action name for the AJAX handler. This action name is used to register the AJAX hooks.
- **`handle()`**: Handles the AJAX request. This method must be implemented to define the logic for processing the request.

### Concrete Methods

These methods are already implemented in the `AjaxHandlerAbstract` class and provide common functionality:

- **`__construct()`**: Registers the AJAX action hooks for both logged-in and logged-out users. The hooks are based on the action name returned by `getAction()`.
- **`sendJsonResponse($data, $status_code = 200)`**: Sends a JSON response and terminates the script. This method is useful for returning data to the client after processing the request.
- **`sendJsonError($message, $status_code = 400)`**: Sends a JSON error response and terminates the script. This method is useful for returning an error message if something goes wrong.
- **`verifyNonce($nonce_name, $action)`**: Verifies the AJAX request nonce to ensure the request is valid. This helps protect against CSRF attacks.

## Example Usage

Here’s an example of how you might use the `AjaxHandlerAbstract` class to create a `TestAjaxHandler`:

```php
namespace Realtyna\BasePlugin\Components\Test\AjaxHandlers;

use Realtyna\Core\Abstracts\AjaxHandlerAbstract;

class TestAjaxHandler extends AjaxHandlerAbstract
{
    protected function getAction(): string
    {
        return 'test_action';
    }

    public function handle(): void
    {
        // Verify nonce
        $nonce_verified = $this->verifyNonce('test_nonce', 'test_action');

        if (!$nonce_verified) {
            return; // Exit if nonce verification fails
        }

        // Process the AJAX request (example: retrieve data from $_POST)
        $test_data = isset($_POST['test_data']) ? sanitize_text_field($_POST['test_data']) : '';

        // Example response
        $response = [
            'success' => true,
            'data'    => $test_data,
            'message' => __('AJAX request processed successfully!', 'text-domain'),
        ];

        // Send the JSON response
        $this->sendJsonResponse($response);
    }
}
```

### Explanation

- **`getAction()`**: Defines the action name `test_action` that will be used to register the AJAX hooks.
- **`handle()`**: Processes the AJAX request by first verifying the nonce to ensure security. It then retrieves the data sent via `$_POST`, processes it, and sends a JSON response back to the client.

### How It Works

1. **Action Registration**: When the `TestAjaxHandler` class is instantiated, the `__construct()` method registers the action hooks `wp_ajax_test_action` and `wp_ajax_nopriv_test_action`.
2. **Nonce Verification**: The `handle()` method verifies the nonce to protect against CSRF attacks.
3. **Request Handling**: The data sent via the AJAX request is processed, and a response is sent back to the client using `sendJsonResponse()`.

## Conclusion

The `AjaxHandlerAbstract` class simplifies the process of handling AJAX requests in WordPress plugins by providing a consistent structure and built-in methods for security and response management. By extending this class, developers can focus on the specific logic of their AJAX requests while ensuring that best practices for security and response handling are followed.
