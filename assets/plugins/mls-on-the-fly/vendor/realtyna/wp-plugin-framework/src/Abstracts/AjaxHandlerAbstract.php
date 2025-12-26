<?php

namespace Realtyna\Core\Abstracts;

/**
 * Class AjaxHandlerAbstract
 *
 * An abstract class that defines the structure for handling AJAX requests in WordPress.
 *
 * @package Realtyna\Core\Abstracts
 */
abstract class AjaxHandlerAbstract
{
    /**
     * AjaxHandlerAbstract constructor.
     *
     * Registers the AJAX action hooks for logged-in and logged-out users.
     */
    public function __construct()
    {
        add_action('wp_ajax_' . $this->getAction(), [$this, 'handle']);
        add_action('wp_ajax_nopriv_' . $this->getAction(), [$this, 'handle']);
    }

    /**
     * Get the action name for the AJAX handler.
     *
     * @return string
     */
    abstract protected function getAction(): string;

    /**
     * Handle the AJAX request.
     *
     * This method should be implemented by child classes to process the AJAX request.
     *
     * @return void
     */
    abstract public function handle(): void;

    /**
     * Send a JSON response and terminate the script.
     *
     * @param mixed $data The data to send in the JSON response.
     * @param int $status_code The HTTP status code (default is 200).
     * @return void
     */
    protected function sendJsonResponse($data, int $status_code = 200): void
    {
        wp_send_json($data, $status_code);
    }

    /**
     * Send a JSON error response and terminate the script.
     *
     * @param string $message The error message to send in the JSON response.
     * @param int $status_code The HTTP status code (default is 400).
     * @return void
     */
    protected function sendJsonError(string $message, int $status_code = 400): void
    {
        wp_send_json_error(['message' => $message], $status_code);
    }

    /**
     * Verify the AJAX request nonce.
     *
     * @param string $nonce_name The name of the nonce to check.
     * @param string $action The action name associated with the nonce.
     * @return bool
     */
    protected function verifyNonce(string $nonce_name, string $action): bool
    {
        if (!isset($_POST[$nonce_name]) || !wp_verify_nonce($_POST[$nonce_name], $action)) {
            $this->sendJsonError(__('Invalid nonce', 'text-domain'), 403);
            return false;
        }

        return true;
    }
}
