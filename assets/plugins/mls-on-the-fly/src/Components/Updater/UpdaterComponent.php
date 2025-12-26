<?php

namespace Realtyna\MlsOnTheFly\Components\Updater;

use Realtyna\Core\Abstracts\ComponentAbstract;

class UpdaterComponent extends ComponentAbstract
{
    private $api_base_url = 'https://update.realtyna.com/wordpress/wp-json/realtyna/v1'; // Base URL for your version tracker plugin API
    private $plugin_slug = 'mls-on-the-fly'; // Slug for the MLS On The Fly plugin
    private $plugin_file = 'mls-on-the-fly/mls-on-the-fly.php'; // Path to the main plugin file

    public function register(): void
    {
        // Hook into WordPress's update system
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_plugin_update']);
        add_filter('plugins_api', [$this, 'plugin_update_info'], 10, 3);
    }

    /**
     * Check for updates from the version tracker plugin and add it to the transient.
     *
     * @param object $transient
     * @return object
     */
    public function check_for_plugin_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }


        // Get the latest version info from the custom API
        $latest_version = $this->get_latest_version();

        if ($latest_version && version_compare($this->get_current_version(), $latest_version['version'], '<')) {
            $plugin_info = new \stdClass();
            $plugin_info->slug = $this->plugin_slug;
            $plugin_info->new_version = $latest_version['version'];
            $plugin_info->package = $latest_version['download_url'];
            $plugin_info->url = $this->api_base_url;

            // Add the update information to the transient
            $transient->response[$this->plugin_file] = $plugin_info;
        }

        return $transient;
    }

    /**
     * Provide plugin details for the WordPress plugin page.
     *
     * @param false|object|array $result
     * @param string $action
     * @param object $args
     * @return object|false
     */
    public function plugin_update_info($result, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        // Get the latest version info from the custom API
        $latest_version = $this->get_latest_version();

        if ($latest_version) {
            $info = new \stdClass();
            $info->name = 'MLS On The Fly';
            $info->slug = $this->plugin_slug;
            $info->version = $latest_version['version'];
            $info->author = 'Realtyna';
            $info->download_link = $latest_version['download_url'];
            $info->requires = '5.0'; // Minimum WordPress version required
            $info->tested = '6.0'; // WordPress version compatibility
            $info->last_updated = date('Y-m-d');
            $info->sections = [
                'description' => 'This is the MLS On The Fly plugin, which integrates custom version updates.',
            ];

            return $info;
        }

        return false;
    }

    /**
     * Get the current version of the MLS On The Fly plugin.
     *
     * @return string
     */
    private function get_current_version(): string
    {
        return get_file_data(
            WP_PLUGIN_DIR . '/' . $this->plugin_file,
            ['Version' => 'Version'],
            'plugin'
        )['Version'];
    }

    /**
     * Fetch the latest version information from the version tracker plugin.
     *
     * @return array|null
     */
    private function get_latest_version(): ?array
    {
        $response = wp_remote_get("{$this->api_base_url}/latest-version?plugin_slug={$this->plugin_slug}");

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return $data;
    }
}
