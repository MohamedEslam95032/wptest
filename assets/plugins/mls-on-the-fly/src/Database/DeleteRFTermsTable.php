<?php
namespace Realtyna\MlsOnTheFly\Database;

use Realtyna\Core\Abstracts\Database\MigrationAbstract;

class DeleteRFTermsTable extends MigrationAbstract
{
    public function up(): void
    {
        global $wpdb;
        $this->runQuery("DROP TABLE IF EXISTS {$wpdb->prefix}realtyna_rf_terms");
    }

    public function down(): void
    {
        global $wpdb;
        $this->runQuery("DROP TABLE IF EXISTS {$wpdb->prefix}realtyna_rf_terms");
    }
}
