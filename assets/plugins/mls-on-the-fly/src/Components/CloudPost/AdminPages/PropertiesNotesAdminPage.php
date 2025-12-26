<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages;

use Realtyna\Core\Abstracts\AdminPageAbstract;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RF;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RFQuery;
use Realtyna\MlsOnTheFly\Settings\Settings;

class PropertiesNotesAdminPage extends AdminPageAbstract
{
    public function register(): void
    {
        parent::register();

        add_action('admin_post_mls_on_the_fly_save_properties_notes', [$this, 'handleForm']);
        add_action('wp_ajax_save_property_notes', [$this, 'ajaxSavePropertyNotes']);

        add_action('wp_ajax_delete_property_notes', [$this, 'ajaxDeletePropertyNotes']);

        // Todo This need to be refactored and use Ajax
        add_filter(
            "realtyna_get_property_note",
            [$this, "realtyna_get_property_note"],
            10,
            1
        );
    }

    function realtyna_get_property_note($listingKey)
    {
        //Sanitize input:
        $listingKey = sanitize_text_field($listingKey);

        if (empty($listingKey)) {
            return null; // Or throw an exception, depending on your error handling preference.
        }

        $RF = App::get(RF::class); // Ensure App::get is accessible here!
        $query = new RFQuery();
        $query->set_entity("PropertyAdditionalInfo");
        $query->add_filter("eq", "ListingKey", "eq", $listingKey);
        $query->set_top(1);
        $query->set_select(["ALL"]);

        try {
            $RFResponse = $RF->get($query, [
                "ignore_global_filter" => true,
                "ignore_featured_properties" => true,
                "ignore_cache" => true,
                "expand" => false,
                "count" => false,
            ]);

            if ($RFResponse->items && count($RFResponse->items) > 0) {
                return $RFResponse->items[0];
            } else {
                return null;
            }
        } catch (\Exception $e) {
            error_log(
                "Error fetching property note in realtyna_get_property_note: " .
                $e->getMessage()
            );
            return null;
        }
    }


    public function ajaxDeletePropertyNotes(): void
    {
        $propertyAdditionalInfoKey = sanitize_text_field($_POST['property_additional_info_key'] ?? '');
        $listingKey = sanitize_text_field($_POST['listing_key'] ?? '');
        $originSystem = sanitize_text_field($_POST['originating_system_name'] ?? '');

        if (
            !$listingKey ||
            !$propertyAdditionalInfoKey ||
            !isset($_POST['security']) ||
            !wp_verify_nonce($_POST['security'], 'delete_property_notes_' . $listingKey)
        ) {
            wp_send_json_error(['message' => 'Invalid nonce or parameters.']);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        // Compose the API endpoint
        $endpoint = "https://api.realtyfeed.com/reso/odata/PropertyAdditionalInfo('{$propertyAdditionalInfoKey}')";
        $RF = App::get(RF::class);
        $accessToken = $RF->getAuthenticator()->accessToken;

        // Make the DELETE request
        $response = wp_remote_request($endpoint, [
            'method'  => 'DELETE',
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Connection error: ' . $response->get_error_message()]);
        }

        $code = wp_remote_retrieve_response_code($response);

        if ($code >= 200 && $code < 300) {
            wp_send_json_success();
        } else {
            $body = wp_remote_retrieve_body($response);
            wp_send_json_error([
                'message' => 'API error',
                'status'  => $code,
                'body'    => $body
            ]);
        }
    }



    public function ajaxSavePropertyNotes(): void
    {
        check_ajax_referer('save_property_notes_nonce', 'security');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $listingKey = sanitize_text_field($_POST['listing_key'] ?? '');
        $originSystem = sanitize_text_field($_POST['originating_system_name'] ?? '');
        if($originSystem == 'CCAR'){
            $originSystem = 'Demo';
        }
        $propertyAdditionalInfoKey = sanitize_text_field($_POST['property_additional_info_key'] ?? '');
        $notes = $_POST['notes'] ?? [];
        if(!$listingKey && !$originSystem){
            wp_send_json_error(['message' => 'Missing or invalid data']);
            die();
        }

        $RF = App::get(RF::class);
        $accessToken = $RF->getAuthenticator()->accessToken;

        if($propertyAdditionalInfoKey != ''){
            // Update
            $response = wp_remote_post('https://api.realtyfeed.com/reso/odata/PropertyAdditionalInfo(\''.$propertyAdditionalInfoKey.'\')', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer $accessToken",
                ],
                'body' => json_encode([
                    'mls_name' => $originSystem,
                    'listing_key' => $listingKey,
                    'additional_data' => $notes,
                ]),
                'method' => 'POST',
                'data_format' => 'body',
            ]);
            // Optional: Handle the response
            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Missing or invalid data']);
                die();
            } else {
                $httpStatusCode = wp_remote_retrieve_response_code($response);
                if($httpStatusCode == 201){
                    wp_send_json_success();
                    die();
                }
            }



        }else{
            // Create
            $response = wp_remote_post('https://api.realtyfeed.com/reso/odata/PropertyAdditionalInfo', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer $accessToken",
                ],
                'body' => json_encode([
                    'mls_name' => $originSystem,
                    'listing_key' => $listingKey,
                    'additional_data' => $notes,
                ]),
                'method' => 'POST',
                'data_format' => 'body',
            ]);
            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Missing or invalid data']);
                die();
            } else {
                $httpStatusCode = wp_remote_retrieve_response_code($response);
                if($httpStatusCode == 201){
                    wp_send_json_success();
                    die();
                }
            }

        }

        wp_send_json_error(['message' => 'Missing or invalid data']);
    }


    protected function getPageTitle(): string
    {
        return 'Properties Notes';
    }

    protected function getMenuTitle(): string
    {
        return 'Properties Notes';
    }

    protected function getCapability(): string
    {
        return 'manage_options';
    }

    protected function getMenuSlug(): string
    {
        return 'mls-on-the-fly-properties-notes';
    }

    protected function getPageTemplate(): string
    {
        return REALTYNA_MLS_ON_THE_FLY_DIR . 'templates/admin/properties-notes.php';
    }

    protected function isSubmenu(): bool
    {
        return true;
    }

    protected function getParentSlug(): string
    {
        return 'mls-on-the-fly';
    }

    protected function getPosition(): int
    {
        return 43;
    }

    protected function enqueuePageAssets(): void
    {
        // You can still enqueue Bootstrap if needed
        wp_enqueue_style('bootstrap-css');
        wp_enqueue_script('bootstrap-js');
        wp_enqueue_script('popperjs');
    }

    /**
     * Handle form submission to save filter settings
     */
    public function handleForm(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'realtyna-mls-on-the-fly'));
        }

        check_admin_referer('mls_on_the_fly_properties_notes');

        $field = sanitize_text_field($_POST['filter_field'] ?? '');
        $value = sanitize_text_field($_POST['filter_value'] ?? '');

        if ($field && $value) {
            Settings::update_setting('properties_notes_filter', compact('field', 'value'));
        }

        wp_redirect(admin_url('admin.php?page=' . $this->getMenuSlug()));
        exit;
    }

    public function renderPage(): void
    {
        $page_slug = str_replace(['realtyna-realtyna-base-plugin-', 'realtyna-realtyna-base-plugin'], '', $_GET['page'] ?? '');
        $page_id = $page_slug ?: 'main';

        $filter = Settings::get_setting('properties_notes_filter', ['field' => '', 'value' => '']);
        $field = $filter['field'] ?? '';
        $value = $filter['value'] ?? '';

        $properties = [];
        $totalProperties = 0;
        $RFPropertyAdditionalInfos = [];

        $per_page = 10;
        $paged = isset($_GET['paged']) ? max(1, (int)$_GET['paged']) : 1;

        if ($field && $value) {
            try {
                $query = new RFQuery();
                $query->set_top($per_page);
                $query->set_skip(($paged - 1) * $per_page);
                $query->add_filter('where', $field, 'eq', $value);
                $query->set_select(['ALL']);

                $RF = App::get(RF::class);
                $RFResponse = $RF->get($query, ['ignore_global_filter' => true, 'ignore_featured_properties' => true]);

                $properties = $RFResponse->items;
                $totalProperties = $RFResponse->count;

                $listingKeys = array_column($properties, 'ListingKey');
                $RF = App::get(RF::class);
                $query = new RFQuery();
                $query->set_entity('PropertyAdditionalInfo');
                $query->set_top($per_page);
                $query->set_skip(($paged - 1) * $per_page);
                $query->add_filter('in', 'ListingKey', 'in', $listingKeys);
                $query->set_select(['ALL']);

                $RFResponse = $RF->get($query, [
                    'ignore_global_filter' => true,
                    'ignore_featured_properties' => true,
                    'ignore_cache' => true,
                    'expand' => false,
                    'count' => false,
                ]);

                foreach ($RFResponse->items as $item) {
                    $RFPropertyAdditionalInfos[$item->ListingKey] = $item;
                }
            } catch (\ReflectionException|\Exception $e) {
                // You can set a flash message or logging here
            }
        }

        $template = $this->getPageTemplate();

        echo '<div id="realtyna-base-plugin-' . esc_attr($page_id) . '" class="realtyna-base-plugin-dashboard-wrapper">';
        echo '<div class="realtyna-base-plugin-dashboard-header-wrapper">';
        echo '<h2>' . esc_html($this->getPageTitle()) . '</h2>';
        echo '<div class="realtyna-base-plugin-dashboard-header-sub-title"></div>';
        echo '</div>'; // Close header wrapper

        echo '<div class="realtyna-base-plugin-dashboard-content-wrapper">';

        if (file_exists($template)) {
            include $template;
        } else {
            echo '<p>' . esc_html__('Template file not found.', 'realtyna-core') . '</p>';
        }

        echo '</div>'; // Close content wrapper
        echo '</div>'; // Close dashboard wrapper
    }



    public function enqueueGlobalAssets(): void
    {
        // TODO: Implement enqueueGlobalAssets() method.
    }
}
