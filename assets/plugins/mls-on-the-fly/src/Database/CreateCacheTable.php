<?php

namespace Realtyna\MlsOnTheFly\Database;

use Realtyna\Core\Abstracts\Database\MigrationAbstract;

class CreateCacheTable extends MigrationAbstract
{
    public function up(): void
    {
        global $wpdb;

        if (is_multisite()) {
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);
                $this->createCacheTable($wpdb);
                restore_current_blog();
            }
        } else {
            $this->createCacheTable($wpdb);
        }
    }

    public function down(): void
    {
        global $wpdb;

        if (is_multisite()) {
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);
                $this->dropCacheTable($wpdb);
                restore_current_blog();
            }
        } else {
            $this->dropCacheTable($wpdb);
        }
    }

    private function createCacheTable($wpdb): void
    {
        $this->runQuery(
            "
            CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mls_on_the_fly_cache (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cache_key VARCHAR(128) NOT NULL,
                cache_value LONGTEXT NOT NULL,
                expires_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_cache_key (cache_key),
                INDEX expires_at_index (expires_at)
            ) ENGINE=InnoDB;
            "
        );
    }

    private function dropCacheTable($wpdb): void
    {
        $this->runQuery("DROP TABLE IF EXISTS {$wpdb->prefix}mls_on_the_fly_cache");
    }
}
