<?php
namespace Realtyna\Core\Abstracts\Database;

abstract class MigrationAbstract
{
    /**
     * Apply the migration (e.g., create tables, add columns).
     *
     * @return void
     */
    abstract public function up(): void;

    /**
     * Rollback the migration (e.g., drop tables, remove columns).
     *
     * @return void
     */
    abstract public function down(): void;

    /**
     * Run the SQL query.
     *
     * @param string $query
     * @return void
     */
    protected function runQuery(string $query): void
    {
        global $wpdb;
        $wpdb->query($query);
    }
}
