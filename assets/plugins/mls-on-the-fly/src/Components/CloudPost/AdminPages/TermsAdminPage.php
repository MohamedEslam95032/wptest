<?php


namespace Realtyna\MlsOnTheFly\Components\CloudPost\AdminPages;

use Realtyna\Core\Abstracts\AdminPageAbstract;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities\RFLookup;
use Realtyna\MlsOnTheFly\Settings\Settings;

class TermsAdminPage extends AdminPageAbstract
{
    /**
     * Method to initialize the admin page.
     */
    public function register(): void
    {
        parent::register();

        add_action('admin_post_mls_on_the_fly_update_settings', array($this, 'updateSettings'));

        // Main menu
        add_action('wp_ajax_delete_taxonomy_terms', array($this, 'ajaxDeleteTaxonomyTerms'));
        add_action('wp_ajax_update_taxonomy_last_update_time', array($this, 'ajaxUpdateTaxonomyLastUpdateTime'));

        // Global Filters
        add_action('wp_ajax_mls_on_the_fly_get_rf_field_values', array($this, 'getFieldValuesByAjax'));

    }
    protected function getPageTitle(): string
    {
        return 'Terms';
    }

    protected function getMenuTitle(): string
    {
        return 'Terms';
    }

    protected function getCapability(): string
    {
        return 'manage_options';
    }

    protected function getMenuSlug(): string
    {
        return 'mls-on-the-fly-terms';
    }

    protected function getPageTemplate(): string
    {
        // Specify the path to the template using the plugin's root directory constant
        return REALTYNA_MLS_ON_THE_FLY_DIR . 'templates/admin/terms.php';
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

        wp_localize_script('mls-on-the-fly-script', 'mlsOnTheFlyAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mls_on_the_fly_nonce')
        ]);



        wp_enqueue_style('bootstrap-css');
        wp_enqueue_script('bootstrap-js');
        wp_enqueue_script('popperjs');

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


    /**
     * Delete taxonomy terms by ajax
     *
     * @return void
     * @author Chandler <chandler.p@realtyna.net>
     *
     */
    public function ajaxDeleteTaxonomyTerms(): void
    {
        check_ajax_referer('mls_on_the_fly_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(
                ['message' => __('You do not have permission to perform this action.', 'realtyna-mls-on-the-fly')]
            );
        }

        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
        $batch_size = isset($_POST['batch_size']) ? intval(
            sanitize_text_field($_POST['batch_size'])
        ) : 50; // Adjust the batch size as needed
        if (taxonomy_exists($taxonomy)) {
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'number' => $batch_size,
            ]);

            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    wp_delete_term($term->term_id, $taxonomy);
                }

                $continue = count($terms) === $batch_size;

                wp_send_json_success(['continue' => $continue]);
            } else {
                wp_send_json_success(['continue' => false]);
            }
        } else {
            wp_send_json_error(['message' => __('The specified taxonomy does not exist.', 'realtyna-mls-on-the-fly')]);
        }
    }

    /**
     * Update taxonomy last update time by ajax
     *
     * @return void
     * @author Chandler <chandler.p@realtyna.net>
     *
     */
    public function ajaxUpdateTaxonomyLastUpdateTime()
    {
        check_ajax_referer('mls_on_the_fly_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(
                ['message' => __('You do not have permission to perform this action.', 'realtyna-mls-on-the-fly')]
            );
        }

        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';

        if (taxonomy_exists($taxonomy)) {
            $current_time = current_time('timestamp');
            delete_option('realtyna_mls_on_the_fly_taxonomy_last_update_time_' . $taxonomy);
            delete_option('realtyna_mls_on_the_fly_taxonomy_after_key_' . $taxonomy);
            wp_send_json_success(
                [
                    'message' => __('Taxonomy is scheduled for update.', 'realtyna-mls-on-the-fly'),
                    'taxonomy' => $taxonomy,
                    'last_update_time' => date('Y-m-d H:i:s', $current_time)
                ]
            );
        } else {
            wp_send_json_error(['message' => __('The specified taxonomy does not exist.', 'realtyna-mls-on-the-fly')]);
        }
    }

    /**
     * Return lookup fields
     *
     * @return void
     * @author Cyrus <cyrus.a@realtyna.com>
     *
     */
    public function getFieldValuesByAjax()
    {
        check_ajax_referer('mls_on_the_fly_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(
                ['message' => __('You do not have permission to perform this action.', 'realtyna-mls-on-the-fly')]
            );
        }

        $field_id = sanitize_text_field($_POST['field_id']);
        $filed_ids = array(
            'PropertyType',
            'PropertySubType',
            'StandardStatus',
        );

        $lookups = array();
        if (in_array($field_id, $filed_ids)) {
            $lookups = RFLookup::getLookupItems(array($field_id));
        }

        wp_send_json_success(array(
            'items' => $lookups,
        ));
    }

}

