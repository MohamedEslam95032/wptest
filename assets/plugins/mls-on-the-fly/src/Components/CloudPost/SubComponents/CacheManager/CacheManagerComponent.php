<?php
namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\CacheManager;

use Realtyna\Core\Abstracts\ComponentAbstract;

class CacheManagerComponent extends ComponentAbstract
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mls_on_the_fly_cache';
    }

    public function register(): void
    {
        add_action("wp_before_admin_bar_render", [
            $this,
            "addCustomMenuItemToAdminBar",
        ]);
        add_action("admin_init", [$this, "clearRfQueryCachePage"]);
        add_action("init", [$this, "deleteExpiredCache"]);
        add_action("admin_notices", [$this, "displayCacheClearedNotice"]);
    }

    public function deleteExpiredCache()
    {
        global $wpdb;

        // Delete expired cache entries
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->table_name}
                WHERE expires_at IS NOT NULL AND expires_at < %s",
                current_time("mysql")
            )
        );

        // Optional: Limit total number of cache entries
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        if ($count > 5000) {
            // Delete oldest entries if count exceeds 5000
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$this->table_name}
                    ORDER BY created_at ASC
                    LIMIT %d",
                    $count - 5000
                )
            );
        }
    }

    public function addCustomMenuItemToAdminBar()
    {
        global $wp_admin_bar, $wpdb;

        // Get the count of cache rows
        $cache_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name}
                WHERE expires_at IS NULL OR expires_at > %s",
                current_time("mysql")
            )
        );

        $menu_title = "Clear RF Query Cache";
        if ($cache_count > 0) {
            $menu_title .= " (" . $cache_count . ")";
        }

        // Add the menu item
        $wp_admin_bar->add_menu([
            "id" => "clear-rf-query-cache",
            "parent" => "MLS-on-the-fly-admin-tab",
            "title" => $menu_title,
            "href" => admin_url("admin.php?clear_rf_query_cache=true"),
            "meta" => [
                "title" => __("Clear RF Query Cache"),
            ],
        ]);
    }

    public function clearRfQueryCachePage()
    {
        if (
            isset($_GET["clear_rf_query_cache"]) &&
            $_GET["clear_rf_query_cache"] == "true"
        ) {
            global $wpdb;

            // Clear all cache entries
            $wpdb->query("TRUNCATE TABLE {$this->table_name}");

            // Optional: Clear any related access tokens or other specific caches
            $cache_key_prefix = "RF-Query-Cache";

            // Step 1: Get all transients with the RF-Query-Cache prefix
            $transients = $wpdb->get_col(
                $wpdb->prepare(
                    "
                SELECT option_name
                FROM $wpdb->options
                WHERE option_name LIKE %s OR option_name LIKE %s
                ",
                    "_transient_timeout_" .
                        $wpdb->esc_like($cache_key_prefix) .
                        "%",
                    "_transient_" . $wpdb->esc_like($cache_key_prefix) . "%"
                )
            );

            // Step 2: Delete all the transients
            foreach ($transients as $transient) {
                // Delete the transient
                $transient = str_replace("_transient_", "", $transient);
                delete_transient($transient);
            }

            // Step 3: Delete the access token transient
            delete_transient("realtyna_mls_on_the_fly_rf_access_token");

            // Step 4: Delete all options with the prefix realtyna_mls_on_the_fly_taxonomy_after_key_
            $taxonomy_option_prefix =
                "realtyna_mls_on_the_fly_taxonomy_after_key_";
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM $wpdb->options WHERE option_name LIKE %s",
                    $wpdb->esc_like($taxonomy_option_prefix) . "%"
                )
            );

            // Get the referring URL and redirect back with cache_cleared=true
            $referer = wp_get_referer();

            // Redirect back to the referring URL with cache_cleared=true
            wp_safe_redirect(add_query_arg("cache_cleared", "true", $referer));
            exit();
        }
    }

    public function displayCacheClearedNotice()
    {
        if (isset($_GET["cache_cleared"]) && $_GET["cache_cleared"] == "true") {
            echo '<div class="notice notice-success is-dismissible"><p>RF Query Cache has been cleared!</p></div>';
        }
    }
}