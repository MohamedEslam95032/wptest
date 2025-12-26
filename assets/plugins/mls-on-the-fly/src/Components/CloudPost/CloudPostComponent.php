<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost;


use Realtyna\Core\Abstracts\ComponentAbstract;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages\GlobalFiltersAdminPage;
use Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages\MappingEditorAdminPage;
use Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages\PropertiesNotesAdminPage;
use Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages\SettingAdminPage;
use Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages\TermsAdminPage;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\DeleteMappingField;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\GetMappingField;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\DeleteQueryMappingField;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\GetQueryMappingField;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\ExportMapping;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\ImportMapping;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\ResetMapping;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\UpdateQueryMappingField;
use Realtyna\MlsOnTheFly\Components\CloudPost\APIEndpoints\V1\MappingEditor\UpdateMappingField;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\CacheManager\CacheManagerComponent;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Compatibilities\CompatibilitiesComponent;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\IntegrationComponent;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\PostInjectionComponent;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\RFClientComponent;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RF;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\TermImporter\TermImporterComponent;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\AdminBar\AddAdminBar;
use Realtyna\MlsOnTheFly\Settings\Settings;

class CloudPostComponent extends ComponentAbstract
{
    private $last_run_option_name = 'mls_on_the_fly_last_data_track_time'; // Option name for last execution time

    public function register(): void
    {
    }

    /**
     * @throws \Exception
     */
    public function subComponents(): void
    {
        $this->addSubComponent(RFClientComponent::class);
        $this->addSubComponent(IntegrationComponent::class);
        if (App::has(RF::class)) {
            $this->addSubComponent(PostInjectionComponent::class);
            $this->addSubComponent(TermImporterComponent::class);
            $this->addSubComponent(CompatibilitiesComponent::class);
            $this->addSubComponent(AddAdminBar::class);
            $this->addSubComponent(CacheManagerComponent::class);
        }

        // Track integration data if it hasn't been tracked in the last 24 hours
        $this->track_integration_data_if_needed();
    }

    public function adminPages(): void
    {
        $this->addAdminPage(SettingAdminPage::class);
        $this->addAdminPage(TermsAdminPage::class);
        $this->addAdminPage(MappingEditorAdminPage::class);
        $this->addAdminPage(GlobalFiltersAdminPage::class);
        $this->addAdminPage(PropertiesNotesAdminPage::class);
    }

    public function restApiEndpoints(): void
    {
        $this->addRestApiEndpoint(GetMappingField::class, App::class);
        $this->addRestApiEndpoint(UpdateMappingField::class, App::class);
        $this->addRestApiEndpoint(DeleteMappingField::class, App::class);
	    $this->addRestApiEndpoint(GetQueryMappingField::class, App::class);
	    $this->addRestApiEndpoint(UpdateQueryMappingField::class, App::class);
        $this->addRestApiEndpoint(DeleteQueryMappingField::class, App::class);
        $this->addRestApiEndpoint(ExportMapping::class, App::class);
        $this->addRestApiEndpoint(ImportMapping::class, App::class);
        $this->addRestApiEndpoint(ResetMapping::class, App::class);
    }

    /**
     * Check if data tracking is needed and track integration data using the data tracker API.
     */
    private function track_integration_data_if_needed(): void
    {
        $last_run_time = get_option($this->last_run_option_name, false);
        $current_time = current_time('timestamp');

        // Check if 24 hours have passed since the last run
        if (!$last_run_time || ($current_time - $last_run_time) > DAY_IN_SECONDS) {
            $this->track_integration_data();
            update_option($this->last_run_option_name, $current_time);
        }
    }

    /**
     * Track integration data using the data tracker API
     */
    private function track_integration_data(): void
    {
        $data = $this->prepare_integration_data();
        $this->send_data_to_tracker($data);
    }

    /**
     * Prepare the data required for the integration tracker
     *
     * @return array
     */
    private function prepare_integration_data(): array
    {
        // Retrieve credentials from settings
        $clientId = Settings::get_setting('client_id', false);
        $clientSecret = Settings::get_setting('client_secret', false);
        $apiKey = Settings::get_setting('api_key', false);

        // Get active integration status
        /** @var IntegrationComponent $integration */
        $integration = App::get(IntegrationComponent::class);
        $activeIntegration = $integration->isForceLoaded() ? 'Force Loaded' : $integration->getIntegrationName();

        // Website URL and admin email
        $websiteURL = get_site_url();
        $adminEmail = get_option('admin_email');

        // Retrieve user action logs from the options table
        $userActionLogs = get_option('realtyna_otf_user_action_logs', []);

        // Prepare data to send, including the user action logs
        return [
            'plugin_id' => 1, // Replace with your actual plugin ID
            'data_type' => 'integration_status',
            'data_value' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'api_key' => $apiKey,
                'integration_type' => '',
                'active_integration' => $activeIntegration,
                'website_url' => $websiteURL,
                'admin_email' => $adminEmail,
                'user_action_logs' => $userActionLogs // Add the logs here
            ]
        ];
    }

    /**
     * Send data to the data tracker API
     *
     * @param array $data
     */
    private function send_data_to_tracker(array $data): void
    {
        $api_url = 'https://update.realtyna.com/wordpress/wp-json/realtyna/v1/track-data';
        $response = wp_remote_post($api_url, [
            'body' => json_encode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        if (is_wp_error($response)) {
            // Log error or handle it as needed
            error_log('Failed to send data to tracker: ' . $response->get_error_message());
        } else {
            // Handle successful response if needed
            $response_body = wp_remote_retrieve_body($response);
            error_log('Data sent to tracker: ' . $response_body);

            // Clear the user action logs after successfully sending
            update_option('realtyna_otf_user_action_logs', ''); // Clear the logs after sending
        }
    }

}