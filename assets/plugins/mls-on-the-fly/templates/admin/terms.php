<?php

use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;

if (!defined('ABSPATH')) {
    exit;
}

$settings = array();
// Get the active integration
try {
    $integration = App::get(IntegrationInterface::class);
} catch (ReflectionException $e) {
    return ;
}

// Get the custom taxonomies
$customTaxonomies = $integration->customTaxonomies;
?>

<div id="mls-on-the-fly-message" style="display: none; margin-top: 20px;"></div>

<p><strong><?php esc_html_e('READ Before Taking Any Action:', 'realtyna-mls-on-the-fly'); ?></strong></p>
<ol>
    <li>
        <strong><?php esc_html_e('If this is your first time installing the plugin:', 'realtyna-mls-on-the-fly'); ?></strong>
        <ul>
            <li><?php esc_html_e('We recommend deleting all terms first and then clicking on "Update Now" to ensure you have a complete and updated list of terms.', 'realtyna-mls-on-the-fly'); ?></li>
            <li><strong><?php esc_html_e('Important:', 'realtyna-mls-on-the-fly'); ?></strong> <?php esc_html_e('If you have edited your current terms and added various details in their edit pages, do not use the Delete button as it will erase all that data. In this case, simply click on "Update Now" and refresh the page once. Be aware that this approach may result in some extra terms in each category that could return no results when viewed. The default solution for this issue is to delete all terms and update them again. Please choose your actions carefully.', 'realtyna-mls-on-the-fly'); ?></li>
        </ul>
    </li>
    <li><strong><?php esc_html_e('When you see a spinning loader, do not refresh the page.', 'realtyna-mls-on-the-fly'); ?></strong></li>
</ol>
<p style="font-weight: bold; color: #000;"><?php esc_html_e('Select a taxonomy to delete all its terms or update its last update time:', 'realtyna-mls-on-the-fly'); ?></p>
<p style="font-weight: bold; color: #000;"><?php esc_html_e('Do not refresh the page when you see the spinner', 'realtyna-mls-on-the-fly'); ?></p>

<div id="global-spinner" class="spinner"></div>
<div class="clearfix"></div>
<div class="taxonomy-grid">
    <?php
    foreach ( $customTaxonomies as $taxonomy ) {
        if ( taxonomy_exists( $taxonomy ))  {

            $tax_data = get_taxonomy( $taxonomy );
            $lastUpdateTime = get_option('realtyna_mls_on_the_fly_taxonomy_last_update_time_' . $taxonomy);
            $lastUpdateTimeFormatted = $lastUpdateTime ? date('M d, H:i', $lastUpdateTime) : __('Never', 'realtyna-mls-on-the-fly');
            $termCount = wp_count_terms( $taxonomy );
            ?>
            <div class="taxonomy-box">
                <div class="box-1">
                    <strong><?php echo esc_html( $tax_data->label ); ?></strong>
                    <form method="post" action="" class="delete-taxonomy-terms">
                        <?php wp_nonce_field('delete_taxonomy_terms_nonce', 'delete_taxonomy_terms_nonce_field'); ?>
                        <input type="hidden" name="taxonomy_to_delete" value="<?php echo esc_attr($taxonomy); ?>">
                        <button type="submit" class="mls-on-the-fly-btn mls-on-the-fly-btn-transparent btn-clear-cache"><?php esc_html_e('Delete All terms', 'realtyna-mls-on-the-fly'); ?></button>
                        <button class="mls-on-the-fly-btn mls-on-the-fly-btn-primary update-last-update-time" data-taxonomy="<?php echo esc_attr($taxonomy); ?>"><?php esc_html_e('Update Now', 'realtyna-mls-on-the-fly'); ?></button>
                    </form>
                </div>
                <div class="box-2">
                    <div class="total-cache"><?php echo esc_html($termCount); ?></div>
                    <div class="last-updated"><label><?php esc_html_e('Updated:', 'realtyna-mls-on-the-fly'); ?></label><span><?php echo esc_html($lastUpdateTimeFormatted); ?></span></div>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>
<div id="mls-on-the-fly-message"></div>

