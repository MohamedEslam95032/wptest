<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration;

use Exception;
use Realtyna\Core\Abstracts\ComponentAbstract;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\CrocoBlockIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\EPLIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\HouzezIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\InspiryThemesIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\PropertyDriveIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\ToolsetIntegration;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets\WPLIntegration;
use Realtyna\MlsOnTheFly\Settings\Settings;

class IntegrationComponent extends ComponentAbstract
{
    /**
     * @var IntegrationInterface|null The current active integration instance.
     */
    protected ?IntegrationInterface $integration = null;
    protected bool $forceLoaded = false;

    /**
     * Registers the component and detects the active integration.
     * @throws Exception
     */
    public function register(): void
    {
        $this->detectIntegration();
        if ($this->integration) {
            App::set(IntegrationInterface::class, $this->integration);
        }
    }

    /**
     * Detects the active integration based on available plugins/themes or settings.
     *
     * @return void
     * @throws \ReflectionException
     */
    protected function detectIntegration(): void
    {
        // Retrieve the default integration from settings if it exists
        $defaultIntegrationClass = Settings::get_setting('default_integration');
        if ($defaultIntegrationClass) {
            $defaultIntegrationClass = str_replace('\\\\', '\\', $defaultIntegrationClass);
        }

        // Attempt to detect integration based on active plugins/themes
        if (class_exists('Easy_Property_Listings')) {
            $this->integration = App::get(EPLIntegration::class);
        } elseif (class_exists('Houzez') || defined('HOUZEZ_PLUGIN_URL')) {
            $this->integration = App::get(HouzezIntegration::class);
        } elseif (defined('WPL_VERSION')) {
            $this->integration = App::get(WPLIntegration::class);
        } elseif (in_array('toolset-blocks/wp-views.php', get_option('active_plugins'))) {
            $this->integration = App::get(ToolsetIntegration::class);
        } elseif (function_exists('jet_engine')) {
            $this->integration = App::get(CrocoBlockIntegration::class);
        } elseif ((function_exists('is_plugin_active') && is_plugin_active('wp-property-drive/wp-property-drive.php')) || defined('WPPD_VERSION')) {
            $this->integration = App::get(PropertyDriveIntegration::class);
        } elseif ((function_exists('is_plugin_active') && is_plugin_active('easy-real-estate/easy-real-estate.php')) || class_exists('Easy_Real_Estate')) {
            $this->integration = App::get(InspiryThemesIntegration::class);
        }

        // If no integration detected and a default is set in settings, use the default
        if ($defaultIntegrationClass != null && $defaultIntegrationClass != 'auto') {
            $this->integration = App::get($defaultIntegrationClass);
            Log::info('Integration set from settings: ' . $this->integration->name);
        }

        // If still no integration detected, default to WPLIntegration
        if (!$this->integration) {
            $this->integration = App::get(WPLIntegration::class);
            $this->forceLoaded = true;

            Log::info('No integration detected. Defaulting to: ' . $this->integration->name);
        }

        // Apply a filter to the final integration
        $this->integration = apply_filters('mls_on_the_fly_final_integration', $this->integration);

        // Add a closable success notice if an integration is detected or set from settings
        if ($this->integration) {
            $this->addAdminNotice(
                '<h3>MLS On The Fly ®</h3>
            <strong>Integration detected or set from settings:</strong> ' . $this->integration->name .
                '<p>If you want to override this option, you can use the settings page to change the default integration.</p>',
                'success',
                true, // Making the notice closable
                'mls-on-the-fly-integration-detected'
            );
        } else {
            $this->addAdminNotice(
                '<h3>MLS On The Fly ®</h3>
            <strong>There is no target application.</strong>
            <p>You need to install one of the following plugins or themes:</p>
            <ul>
                <li><a href="https://wordpress.org/plugins/easy-property-listings/" target="_blank">Easy Property Listing</a></li>
                <li><a href="https://themeforest.net/item/houzez-real-estate-wordpress-theme/15752549" target="_blank">Houzez</a></li>
                <li><a href="https://realtyna.com/wpl-platform/" target="_blank">WPL</a></li>
                <li><a href="https://discover-wp.com/site-types/cb-properties-real-estate-site/" target="_blank">Toolset (RealState Demo)</a></li>
                <li><a href="https://crocoblock.com/dynamic-templates/findero/" target="_blank">CrocoBlock (RealState Demo)</a></li>
            </ul>',
                'error'
            );
        }
    }


    /**
     * Gets the active integration instance.
     *
     * @return IntegrationInterface|null The detected integration, or null if no integration was found.
     */
    public function getIntegration(): ?IntegrationInterface
    {
        return $this->integration;
    }

    public function getIntegrationName(): string
    {
        return $this->integration->name ?? '';
    }

    /**
     * Gets the custom post types provided by the active integration.
     *
     * @return array The list of custom post types supported by the active integration.
     */
    public function getCustomPostTypes(): array
    {
        if ($this->integration) {
            return $this->integration->getCustomPostTypes();
        }
        return [];
    }

    /**
     * Gets the custom taxonomies provided by the active integration.
     *
     * @return array The list of custom taxonomies supported by the active integration.
     */
    public function getCustomTaxonomies(): array
    {
        if ($this->integration) {
            return $this->integration->getCustomTaxonomies();
        }
        return [];
    }

    /**
     * Checks if the integration was successfully detected.
     *
     * @return bool True if an integration is detected; false otherwise.
     */
    public function hasIntegration(): bool
    {
        return $this->integration !== null;
    }

    public function isForceLoaded(): bool
    {
        return $this->forceLoaded;
    }
}
