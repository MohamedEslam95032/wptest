<?php


namespace Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages;

use Realtyna\Core\Abstracts\AdminPageAbstract;
use Realtyna\MlsOnTheFly\Settings\Settings;

class SettingAdminPage extends AdminPageAbstract
{
    /**
     * Method to initialize the admin page.
     */
    public function register(): void
    {
        parent::register();

        add_action('admin_post_mls_on_the_fly_update_settings', array($this, 'updateSettings'));

    }
    protected function getPageTitle(): string
    {
        return 'Settings';
    }

    protected function getMenuTitle(): string
    {
        return 'Settings';
    }

    protected function getCapability(): string
    {
        return 'manage_options';
    }

    protected function getMenuSlug(): string
    {
        return 'mls-on-the-fly-settings';
    }

    protected function getPageTemplate(): string
    {
        // Specify the path to the template using the plugin's root directory constant
        return REALTYNA_MLS_ON_THE_FLY_DIR . 'templates/admin/settings.php';
    }

    protected function isSubmenu(): bool
    {
        return true;
    }

    protected function getParentSlug(): string
    {
        return 'mls-on-the-fly';
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


        wp_enqueue_script('select2');
        wp_enqueue_style('select2');

        wp_enqueue_script('mls-on-the-fly-script');
        wp_enqueue_style('mls-on-the-fly-style');
    }


    protected function getPosition(): int
    {
        return 42;
    }

    public function updateSettings(): void
    {
        $nonce = sanitize_text_field($_POST['mls_on_the_fly_settings_nonce'] ?? '');
        $action = 'realtyna-mls-on-the-fly-nonce-settings';

        if ($nonce && wp_verify_nonce($nonce, $action)) {
            $settings = $_POST['mls-on-the-fly-settings'] ?? array();
            Settings::update_settings($settings);
        }

        $location = wp_get_referer();
        wp_redirect($location);
        exit;
    }

}

