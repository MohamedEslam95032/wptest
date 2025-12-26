<?php


namespace Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages;

use Realtyna\Core\Abstracts\AdminPageAbstract;

class MappingEditorAdminPage extends AdminPageAbstract
{
    /**
     * Method to initialize the admin page.
     */
    public function register(): void
    {
        parent::register();
        add_action(
            'admin_post_mls_on_the_fly_update_global_filters_settings',
            array($this, 'updateGlobalFiltersSettings')
        );
    }

    protected function getPageTitle(): string
    {
        return 'Mapping';
    }

    protected function getMenuTitle(): string
    {
        return 'Mapping';
    }

    protected function getCapability(): string
    {
        return 'manage_options';
    }

    protected function getMenuSlug(): string
    {
        return 'mls-on-the-fly-mapping';
    }

    protected function getPageTemplate(): string
    {
        // Specify the path to the template using the plugin's root directory constant
        return REALTYNA_MLS_ON_THE_FLY_DIR . 'templates/admin/mapping-edit.php';
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
        $js_base_url = plugins_url('/assets/js/', REALTYNA_MLS_ON_THE_FLY_DIR . 'realtyna-mls-on-the-fly.php');
        $css_base_url = plugins_url('/assets/css/', REALTYNA_MLS_ON_THE_FLY_DIR . 'realtyna-mls-on-the-fly.php');

        // Enqueue any styles or scripts specific to this admin page
        wp_register_style(
            'font-montserrat',
            'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap',
            array(''),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );
        
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
            'realtyna-mls-on-the-fly-mapping-editor-js',
            $js_base_url . '/mapping-editor.js',
            array('jquery'),
            REALTYNA_MLS_ON_THE_FLY_VERSION,
            true
        );
        wp_register_style(
            'realtyna-mls-on-the-fly-mapping-editor-css',
            $css_base_url . '/mapping-editor.css',
            array(),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );
        wp_localize_script(
            'realtyna-mls-on-the-fly-mapping-editor-js',
            'mlsOnTheFlyMappingAjax',
            array(
                'nonce' => wp_create_nonce('wp_rest'),
                'siteUrl' => site_url(),
            )
        );
        wp_register_script(
            'popperjs',
            "https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js",
            array('jquery'),
            REALTYNA_MLS_ON_THE_FLY_VERSION
        );
        wp_register_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
        wp_register_script(
            'bootstrap-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js',
            array('jquery'),
            REALTYNA_MLS_ON_THE_FLY_VERSION,
            true
        );


        wp_enqueue_style('font-montserrat');
        wp_enqueue_style('bootstrap-css');
        wp_enqueue_script('bootstrap-js');
        wp_enqueue_script('popperjs');
        wp_enqueue_script('realtyna-mls-on-the-fly-mapping-editor-js');
        wp_enqueue_style('realtyna-mls-on-the-fly-mapping-editor-css');

        wp_enqueue_script('mls-on-the-fly-script');
        wp_enqueue_style('mls-on-the-fly-style');
    }


    protected function getPosition(): int
    {
        return 42;
    }

    public function updateSettings(): void
    {
        $nonce = sanitize_text_field($_POST['mls_on_the_fly_global_filters_settings_nonce'] ?? '');
        $action = 'realtyna-mls-on-the-fly-nonce-global-filters-settings';

        if ($nonce && wp_verify_nonce($nonce, $action)) {
            // Process and save the filter to the database
            $current_filter = str_replace('\\', '', $_POST['mls-on-the-fly-global-filters-input']);
            $current_filter = sanitize_text_field($current_filter);

            $json = str_replace('\\', '', $_POST['mls-on-the-fly-global-filters-input-json']);
            $json = sanitize_textarea_field($json);

            $global_filters = array($current_filter); // Replace the old filters with the new one

            // Save the updated filters to the database
            update_option('realtyna_mls_on_the_fly_global_filters', $global_filters);
            update_option('realtyna_mls_on_the_fly_global_filters_json', $json);
        }

        $location = wp_get_referer();
        wp_redirect($location);
        exit;
    }

}

