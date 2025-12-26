# MigrationAbstract

The `MigrationAbstract` class provides a standardized structure for creating and managing database migrations in WordPress plugins. By extending this abstract class, developers can easily define the SQL operations required to modify the database schema, such as creating tables, adding columns, or rolling back changes.

## Purpose

The primary purpose of `MigrationAbstract` is to:

- **Simplify Database Migrations**: Provide a reusable framework for defining and executing database migrations in WordPress.
- **Encapsulate Migration Logic**: Allow developers to encapsulate the logic for applying and rolling back database changes in a single, reusable class.
- **Ensure Consistent Database Modifications**: Provide methods for running SQL queries in a consistent and reliable manner.

## Key Methods

### Abstract Methods

These methods must be implemented by any class that extends `MigrationAbstract`:

- **`up()`**: Applies the migration. This method should contain the SQL operations required to modify the database schema, such as creating tables or adding columns.
- **`down()`**: Rolls back the migration. This method should contain the SQL operations required to undo the changes made by the `up()` method, such as dropping tables or removing columns.

### Concrete Methods

These methods are already implemented in the `MigrationAbstract` class and provide common functionality:

- **`runQuery(string $query)`**: Executes the provided SQL query using the WordPress database API. This method ensures that the SQL operations defined in the migration are executed correctly.

## Example Usage

Here’s an example of how you might use the `MigrationAbstract` class to create a migration for a `TestTable`:

```php
namespace Realtyna\BasePlugin\Database;

use Realtyna\Core\Abstracts\Database\MigrationAbstract;

class CreateTestTable extends MigrationAbstract
{
    public function up(): void
    {
        global $wpdb;
        $this->runQuery("
            CREATE TABLE IF NOT EXISTS {$wpdb->prefix}my_table (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT
            )
        ");
    }

    public function down(): void
    {
        global $wpdb;
        $this->runQuery("DROP TABLE IF EXISTS {$wpdb->prefix}my_table");
    }
}
```

### Explanation

- **`up()`**: Defines the SQL query to create a table named `my_table` with columns `id`, `name`, and `description`. The table is only created if it does not already exist.
- **`down()`**: Defines the SQL query to drop the `my_table` if it exists, effectively rolling back the changes made by the `up()` method.

### How It Works

1. **Migration Application**: When the `CreateTestTable` class is instantiated and the `up()` method is called, the SQL query to create the table is executed using the `runQuery()` method.
2. **Migration Rollback**: If the migration needs to be rolled back, the `down()` method is called, which executes the SQL query to drop the table, undoing the changes made by the `up()` method.

## Usage in Plugin Initialization

To integrate the migration into the plugin, you can add it to the `migrations()` method of the `Main` class, which extends the `StartUp` class:

```php
class Main extends StartUp
{
    protected function migrations(): void
    {
        $this->addMigration(CreateTestTable::class);
    }
}
```

### Explanation

- **`migrations()`**: This method registers the `CreateTestTable` migration with the plugin's initialization process. When the plugin is activated or updated, the migration is applied to ensure the database schema is up to date.

## Conclusion

The `MigrationAbstract` class simplifies the process of managing database migrations in WordPress plugins by providing a consistent structure and handling much of the boilerplate code for you. By extending this class, developers can focus on the specific SQL operations needed for their plugin while ensuring that best practices are followed.
