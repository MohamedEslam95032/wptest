<?php

namespace Realtyna\MlsOnTheFly\Database;

use Realtyna\Core\Abstracts\Database\MigrationAbstract;

class CreateRFMappingsTable extends MigrationAbstract
{
    public function up(): void
    {
        global $wpdb;

        // Check if it's a multisite setup
        if (is_multisite()) {
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);

                // Create the table for the current site
                $this->runQuery(
                    "
                    CREATE TABLE IF NOT EXISTS {$wpdb->prefix}realtyna_rf_mappings (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        listing_key VARCHAR(255) NOT NULL,
                        post_id BIGINT(20) UNSIGNED NOT NULL,
                        created_at DATETIME NOT NULL,
                        updated_at DATETIME NOT NULL,
                        UNIQUE KEY unique_mapping (listing_key, post_id)
                    )
                "
                );

                restore_current_blog();
            }
        } else {
            // Single-site setup
            $this->runQuery(
                "
                CREATE TABLE IF NOT EXISTS {$wpdb->prefix}realtyna_rf_mappings (
                    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    listing_key VARCHAR(255) NOT NULL,
                    post_id BIGINT(20) UNSIGNED NOT NULL,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL,
                    UNIQUE KEY unique_mapping (listing_key, post_id)
                )
            "
            );
        }
    }

    public function down(): void
    {
        global $wpdb;

        // Check if it's a multisite setup
        if (is_multisite()) {
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);

                // Drop the table for the current site
                $this->runQuery("DROP TABLE IF EXISTS {$wpdb->prefix}realtyna_rf_mappings");

                restore_current_blog();
            }
        } else {
            // Single-site setup
            $this->runQuery("DROP TABLE IF EXISTS {$wpdb->prefix}realtyna_rf_mappings");
        }
    }
}
