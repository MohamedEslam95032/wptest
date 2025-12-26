<?php


namespace Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages;

use Realtyna\Core\Abstracts\AdminPageAbstract;
use Realtyna\MlsOnTheFly\Settings\Settings;

class GlobalFiltersAdminPage extends AdminPageAbstract
{
    /**
     * Method to initialize the admin page.
     */
    public function register(): void
    {
        parent::register();
    }
    protected function getPageTitle(): string
    {
        return 'Global Filters';
    }

    protected function getMenuTitle(): string
    {
        return 'Global Filters';
    }

    protected function getCapability(): string
    {
        return 'manage_options';
    }

    protected function getMenuSlug(): string
    {
        return 'mls-on-the-fly-global-filters';
    }

    protected function getPageTemplate(): string
    {
        // Specify the path to the template using the plugin's root directory constant
        return REALTYNA_MLS_ON_THE_FLY_DIR . 'templates/admin/global-filters.php';
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

    }

    protected function enqueuePageAssets(): void
    {
        $js_base_url = plugins_url('/assets/js/', REALTYNA_MLS_ON_THE_FLY_DIR . 'realtyna-mls-on-the-fly.php');
        $css_base_url = plugins_url('/assets/css/', REALTYNA_MLS_ON_THE_FLY_DIR . 'realtyna-mls-on-the-fly.php');
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

        wp_register_script(
            'realtyna-mls-on-the-fly-global-filters-js',
            $js_base_url . 'global-filters.js',
            array('jquery'),
            REALTYNA_MLS_ON_THE_FLY_VERSION,
            true
        );
        wp_localize_script(
            'realtyna-mls-on-the-fly-global-filters-js',
            'mls_on_the_fly_global_filters_data',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mls_on_the_fly_nonce'),
                'i18n' => array(
                    'add_group' => esc_html__('Add Group', 'realtyna-mls-on-the-fly'),
                    'add_filter' => esc_html__('Add Filter', 'realtyna-mls-on-the-fly'),
                    'and' => esc_html__('And', 'realtyna-mls-on-the-fly'),
                    'or' => esc_html__('Or', 'realtyna-mls-on-the-fly'),
                    'edit' => esc_html__('Edit', 'realtyna-mls-on-the-fly'),
                    'duplicate' => esc_html__('Duplicate', 'realtyna-mls-on-the-fly'),
                    'ungroup' => esc_html__('Ungroup', 'realtyna-mls-on-the-fly'),
                    'delete' => esc_html__('Delete', 'realtyna-mls-on-the-fly'),
                ),
                'icons' => array(
                    'folder' => mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg('folder')),
                    'filter' => mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg('filter')),
                    'edit' => mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg('edit')),
                    'duplicate' => mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg('duplicate')),
                    'ungroup' => mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg('ungroup')),
                    'delete' => mls_on_the_fly_kses_svg(mls_on_the_fly_get_svg('trash')),
                ),
            ),
        );

        wp_register_script(
            'realtyna-mls-on-the-fly-global-filters-old-js',
            $js_base_url . 'global-filters-old.js',
            array('jquery'),
            REALTYNA_MLS_ON_THE_FLY_VERSION,
            true
        );


        wp_enqueue_style('bootstrap-css');
        wp_enqueue_script('bootstrap-js');
        wp_enqueue_script('popperjs');
        wp_enqueue_script('realtyna-mls-on-the-fly-global-filters-js');

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

