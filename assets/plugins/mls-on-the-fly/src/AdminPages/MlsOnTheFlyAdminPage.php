<?php

namespace Realtyna\MlsOnTheFly\AdminPages;

use Realtyna\Core\Abstracts\AdminPageAbstract;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RF;
use Realtyna\MlsOnTheFly\Settings\Settings;

class MlsOnTheFlyAdminPage extends AdminPageAbstract
{

    public function register(): void
    {
        parent::register();

        // Add action for AJAX request
        add_action('wp_ajax_verify_api_keys', [$this, 'verify_api_keys_callback']);
        add_action('wp_ajax_nopriv_verify_api_keys', [$this, 'verify_api_keys_callback']);
        add_action('wp_ajax_log_user_action', [$this, 'log_user_action']);
        // Add action for AJAX request to log batched user actions
        add_action('wp_ajax_log_user_action_batch', [$this, 'log_user_action_batch']);
    }


    function log_user_action_batch()
    {
        // Verify nonce for security
        check_ajax_referer('mls_on_the_fly_nonce', 'nonce');

        // Get the logs from the AJAX request
        $logs = isset($_POST['logs']) ? $_POST['logs'] : array();

        // Make sure logs are an array
        if (!is_array($logs)) {
            wp_send_json_error('Invalid log data format.');
            return;
        }

        // Get the existing action logs from the wp_options table
        $existing_logs = get_option('realtyna_otf_user_action_logs', array());
        if ($existing_logs == '') {
            $existing_logs = [];
        }
        // Append new logs to the existing ones
        foreach ($logs as $log) {
            $new_log = array(
                'user_action' => sanitize_text_field($log['user_action']),
                'step' => intval($log['step']),
                'timestamp' => sanitize_text_field($log['timestamp']),
                'user_id' => get_current_user_id(), // Get the current user ID if logged in
                'user_email' => is_user_logged_in() ? wp_get_current_user()->user_email : 'Guest',
                'site_url' => site_url(), // Get the current website URL
            );

            $existing_logs[] = $new_log;
        }

        // Update the option in the database with the new logs
        update_option('realtyna_otf_user_action_logs', $existing_logs);

        // Send success response back to the client
        wp_send_json_success('Batched actions logged successfully.');
    }


    function log_user_action()
    {
        // Verify nonce
        check_ajax_referer('mls_on_the_fly_nonce', 'nonce');

        // Get the data from the request
        $user_action = sanitize_text_field($_POST['user_action']);
        $step = intval($_POST['step']);
        $user_id = get_current_user_id(); // Get current user ID

        // Get user data to retrieve the email
        $user_info = get_userdata($user_id);
        $user_email = $user_info ? $user_info->user_email : 'Guest';

        // Get the current site URL
        $site_url = site_url();

        // Prepare the new action data to store
        $new_action = array(
            'user_id' => $user_id,
            'user_email' => $user_email,
            'action' => $user_action,
            'step' => $step,
            'timestamp' => current_time('mysql'),
            'site_url' => $site_url,
        );

        // Get the existing actions from the wp_options table
        $existing_actions = get_option('realtyna_otf_user_action_logs');
        if ($existing_actions == '') {
            $existing_logs = [];
        }
        // If no actions are saved yet, initialize an empty array
        if (!$existing_actions) {
            $existing_actions = array();
        }

        // Append the new action to the existing actions
        $existing_actions[] = $new_action;

        // Update the option with the new action log
        update_option('realtyna_otf_user_action_logs', $existing_actions);

        // Return success response
        wp_send_json_success('Action logged successfully');
    }


    function verify_api_keys_callback()
    {
        // Check if the request contains the necessary fields
        if (!isset($_POST['api_key'], $_POST['client_id'], $_POST['client_secret'])) {
            wp_send_json_error(
                array('message' => 'Missing required fields. Please provide API Key, Client ID, and Client Secret.')
            );
        }
        delete_transient('realtyna_mls_on_the_fly_rf_access_token');
        // Sanitize the input fields to avoid security risks
        $api_key = sanitize_text_field($_POST['api_key']);
        $client_id = sanitize_text_field($_POST['client_id']);
        $client_secret = sanitize_text_field($_POST['client_secret']);

        // Check if any of the fields are empty
        if (empty($api_key) || empty($client_id) || empty($client_secret)) {
            wp_send_json_error(
                array('message' => 'All fields are required. Please ensure you have filled in the API Key, Client ID, and Client Secret.')
            );
        }

        // Here, replace this with your actual API key validation logic
        $is_valid = $this->validate_api_keys(
            $api_key,
            $client_id,
            $client_secret
        ); // Simulate a real validation process

        // If the keys are valid, send success response
        if ($is_valid) {
            Settings::update_setting('api_key', $api_key);
            Settings::update_setting('client_id', $client_id);
            Settings::update_setting('client_secret', $client_secret);

            wp_send_json_success();
        } else {
            // If validation fails, send an error message
            wp_send_json_error(
                array('message' => 'Invalid API keys. Please check your API Key, Client ID, and Client Secret and try again.')
            );
        }
    }

    /**
     * Simulated function to validate API keys.
     * In a real-world scenario, this would check against a database or third-party API.
     * @throws \Exception
     */
    function validate_api_keys($api_key, $client_id, $client_secret): bool
    {
        $RFClient = new RF($client_id, $client_secret, $api_key);

        if ($RFClient->getAuthenticator()->accessToken) {
            return true;
        }
        return false;
    }


    protected function getPageTitle(): string
    {
        return 'MLS On The Fly &reg;';
    }

    protected function getMenuTitle(): string
    {
        return 'MLS On The Fly &reg;';
    }

    protected function getCapability(): string
    {
        return 'manage_options';
    }

    protected function getMenuSlug(): string
    {
        return 'mls-on-the-fly';
    }

    protected function getPageTemplate(): string
    {
        // Specify the path to the template using the plugin's root directory constant
        return REALTYNA_MLS_ON_THE_FLY_DIR . 'templates/admin/mls-on-the-fly.php';
    }


    public function addMenuPage(): void
    {
        // Add a separator before MLS On The Fly<sup>&reg;</sup>
        global $menu;
        $menu[40] = [
            '',
            'read',
            'separator-1-' . $this->getMenuSlug(),
            '',
            'wp-menu-separator realtyna-base-plugin ' . $this->getMenuSlug()
        ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited// Add MLS On The Fly<sup>&reg;</sup> menu
        parent::addMenuPage();
        $menu[43] = [
            '',
            'read',
            'separator-2-' . $this->getMenuSlug(),
            '',
            'wp-menu-separator realtyna-base-plugin ' . $this->getMenuSlug()
        ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited// Add MLS On The Fly<sup>&reg;</sup> menu
    }

    public function enqueueGlobalAssets(): void
    {
        wp_register_style(
            'mls-on-the-fly-menu',
            REALTYNA_MLS_ON_THE_FLY_URL . 'assets/css/mls-on-the-fly-menu.css',
            array(),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );
        wp_enqueue_style('mls-on-the-fly-menu');
    }

    protected function enqueuePageAssets(): void
    {
        // Enqueue any styles or scripts specific to this admin page
        wp_register_style(
            'mls-on-the-fly-style',
            REALTYNA_MLS_ON_THE_FLY_URL . 'assets/css/mls-on-the-fly-style.css',
            array(),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );
        wp_register_script(
            'mls-on-the-fly-script',
            REALTYNA_MLS_ON_THE_FLY_URL . 'assets/js/mls-on-the-fly-script.js',
            array('jquery'),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );

        wp_register_style(
            'mls-on-the-fly-main-page-style',
            REALTYNA_MLS_ON_THE_FLY_URL . 'assets/css/main-page-style.css',
            array(),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );

        wp_register_script(
            'mls-on-the-fly-main-page-script',
            REALTYNA_MLS_ON_THE_FLY_URL . 'assets/js/main-page-script.js',
            array('jquery'),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );

        wp_register_style(
            'select2',
            REALTYNA_MLS_ON_THE_FLY_URL . 'assets/css/select2.min.css',
            array(),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );
        wp_register_script(
            'select2',
            REALTYNA_MLS_ON_THE_FLY_URL . 'assets/js/select2.min.js',
            array('jquery'),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );


        wp_localize_script('mls-on-the-fly-main-page-script', 'mlsOnTheFlyAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mls_on_the_fly_nonce')
        ]);


        wp_enqueue_script('select2');
        wp_enqueue_style('select2');
        wp_enqueue_script('mls-on-the-fly-script');
        wp_enqueue_style('mls-on-the-fly-style');
        wp_enqueue_style('mls-on-the-fly-main-page-style');
        wp_enqueue_script('mls-on-the-fly-main-page-script');
    }

    protected function getIconUrl(): string
    {
        return "data:image/svg+xml;base64," . base64_encode(
                file_get_contents(REALTYNA_MLS_ON_THE_FLY_DIR . 'assets/image/mls-on-the-fly-icon.svg')
            );
    }

    protected function getPosition(): int
    {
        return 41;
    }

}

