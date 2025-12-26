<?php

use Realtyna\MlsOnTheFly\Settings\Settings;

if ( !defined('ABSPATH') ) exit;

$settings = Settings::get_settings();
$tabs = array(
    'general' => array(
        'title'  => __( 'General', 'realtyna-mls-on-the-fly' ),
        'priority' => 10,
    ),
    'terms-and-taxonomies' => array(
        'title'  => __( 'Terms and Taxonomies', 'realtyna-mls-on-the-fly' ),
        'priority' => 20,
    ),
    'reality-feed-credentials' => array(
        'title'  => __( 'RealtyFeed Credentials', 'realtyna-mls-on-the-fly' ),
        'priority' => 30,
    ),
     'pictures' => array(
         'title'  => __( 'Pictures', 'realtyna-mls-on-the-fly' ),
         'priority' => 40,
     ),
    'url-patterns' => array(
        'title'  => __( 'URL Patterns', 'realtyna-mls-on-the-fly' ),
        'priority' => 50,
    ),
    'featured' => array(
        'title'  => __( 'Featured Properties', 'realtyna-mls-on-the-fly' ),
        'priority' => 60,
    ),
    'debug' => array(
        'title'  => __( 'Debug', 'realtyna-mls-on-the-fly' ),
        'priority' => 100,
    ),
);
$tabs = apply_filters( 'realtyna/mls-on-the-fly/admin/settings/tabs', $tabs );
$compare = function($a,$b){
    if((int)$a['priority'] == (int)$b['priority'])return 0;
    if((int)$a['priority']  > (int)$b['priority'])return 1;
    if((int)$a['priority']  < (int)$b['priority'])return -1;
};
uasort( $tabs, $compare );
$active_tab_id = key( $tabs );
?>
<form autocomplete="off" action="<?php echo admin_url('admin-post.php'); ?>" method="post">
    <?php wp_nonce_field( 'realtyna-mls-on-the-fly-nonce-settings', 'mls_on_the_fly_settings_nonce' ); ?>
    <input type="hidden" name="action" value="mls_on_the_fly_update_settings" />

    <div class="realtyna-base-plugin-tabs-wrapper">
        <div class="realtyna-base-plugin-tab-headers">
            <?php
            foreach ( $tabs as $tab_id => $tab_data ) {
                echo '<a href="#realtyna-base-plugin-tab-content-' . esc_attr( $tab_id ) . '" class="realtyna-base-plugin-tab-link '. ( $tab_id === $active_tab_id ? 'active' : '' ) .'">'. esc_html( $tab_data['title'] ) .'</a>';
            }
            ?>
        </div>
        <div class="realtyna-base-plugin-tab-contents-wrapper">
            <?php
            foreach ( $tabs as $tab_id => $tab_data ) {
                echo '<div id="realtyna-base-plugin-tab-content-' . esc_attr( $tab_id ) . '" class="realtyna-base-plugin-tab-content '. ( $tab_id === $active_tab_id ? 'active' : '' ) .'">';

                $filename = $tab_data['filename'] ?? REALTYNA_MLS_ON_THE_FLY_TEMPLATES_PATH . "/admin/settings-page/{$tab_id}-tab.php";
                if ( file_exists( $filename ) ) {

                    include $filename;
                }
                /**
                 * @param string $tab_id
                 * @param array $tab_data
                 * @param array $settings
                 */
                do_action( 'realtyna/mls-on-the-fly/admin/settings/tabs/content', $tab_id, $tab_data, $settings );
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <button id="realtyna-base-plugin-update-settings" class="mls-on-the-fly-btn mls-on-the-fly-btn-primary"><?php esc_html_e( 'Save Settings', 'realtyna-mls-on-the-fly' ); ?></button>
</form>