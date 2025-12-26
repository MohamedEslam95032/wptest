<?php

namespace Realtyna\MlsOnTheFly\Database;

use Realtyna\Core\Abstracts\Database\MigrationAbstract;

class AddCountAndTypeToCacheTable extends MigrationAbstract
{
    public function up(): void
    {
        global $wpdb;

        if (is_multisite()) {
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);
                $this->alterCacheTable($wpdb);
                restore_current_blog();
            }
        } else {
            $this->alterCacheTable($wpdb);
        }
    }

    public function down(): void
    {
        global $wpdb;

        if (is_multisite()) {
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);
                $this->revertCacheTable($wpdb);
                restore_current_blog();
            }
        } else {
            $this->revertCacheTable($wpdb);
        }
    }

    private function alterCacheTable($wpdb): void
    {
        $table = $wpdb->prefix . 'mls_on_the_fly_cache';

        // Get existing columns
        $columns = $wpdb->get_col("SHOW COLUMNS FROM {$table}", 0);

        $queries = [];

        if (!in_array('count', $columns)) {
            $queries[] = "ADD COLUMN count INT(11) NOT NULL DEFAULT 0";
        }

        if (!in_array('type', $columns)) {
            $queries[] = "ADD COLUMN type ENUM('temporary', 'permanent') NOT NULL DEFAULT 'temporary'";
        }

        if (!empty($queries)) {
            $this->runQuery("ALTER TABLE {$table} " . implode(", ", $queries));
        }
    }


    private function revertCacheTable($wpdb): void
    {
        $this->runQuery(
            "
            ALTER TABLE {$wpdb->prefix}mls_on_the_fly_cache
            DROP COLUMN count,
            DROP COLUMN type;
            "
        );
    }
}
