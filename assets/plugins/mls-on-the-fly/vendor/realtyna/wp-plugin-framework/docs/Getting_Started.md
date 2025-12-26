
# Getting Started

Welcome to the Realtyna Base Plugin framework! This guide will help you get started quickly by creating your first plugin using the framework. By the end of this guide, you'll understand the core structure and be ready to begin developing your own WordPress plugins.

## Step 1: Setting Up Your Environment

Before starting plugin development, ensure your environment is set up properly.

### Requirements

- **WordPress Installation**: A local or server-based WordPress setup for development.
- **PHP and Composer**: Ensure that PHP and Composer are installed, as Composer is required to install the framework.

### Installing the Plugin Framework

To create a new plugin, run the following command in your terminal:

```bash
composer create-project realtyna/base-plugin {PluginName}
```

Replace `{PluginName}` with your desired plugin name. This command will generate the base structure for your plugin.

## Step 2: Understanding the Plugin Structure

The Realtyna Base Plugin framework provides a structured approach that organizes your plugin into distinct components, admin pages, configurations, and more.

### Folder Structure Overview

Here’s an overview of the directory structure created by the framework:

```
├── realtyna-base-plugin.php
├── src
│   ├── AdminPages
│   │   └── MainPage.php
│   ├── Boot
│   │   ├── App.php
│   │   ├── Log.php
│   │   └── helper.php
│   ├── Components
│   │   └── Test
│   │       ├── AdminPages
│   │       │   └── TestAdminPage.php
│   │       ├── AjaxHandlers
│   │       │   └── TestAjaxHandler.php
│   │       ├── PostTypes
│   │       │   └── TestPostType.php
│   │       ├── RestApiEndpoints
│   │       │   └── TestRestApiEndpoint.php
│   │       ├── Shortcodes
│   │       │   └── TestShortcode.php
│   │       ├── SubComponents
│   │       │   └── TestSubComponent.php
│   │       ├── Taxonomies
│   │       │   └── GenreTaxonomy.php
│   │       ├── TestComponent.php
│   │       └── Widgets
│   │           └── TestWidget.php
│   ├── Config
│   │   └── config.php
│   ├── Database
│   │   └── CreateTestTable.php
│   ├── Main.php
│   └── Settings
│       └── Settings.php
├── templates
│   └── admin
│       ├── main-page.php
│       └── test-admin-page.php
└── tests
```

### Key Files and Directories

- **`realtyna-base-plugin.php`**: The main plugin file that WordPress uses to identify and load your plugin.
- **`src/`**: Contains the core components of your plugin.
    - **`AdminPages/`**: Houses classes for custom admin pages.
    - **`Boot/`**: Contains essential bootstrap files like `App.php`, `Log.php`, and helper functions.
    - **`Components/`**: Organizes different functional components of your plugin.
        - **Subdirectories**: Separate concerns such as `AdminPages`, `AjaxHandlers`, `PostTypes`, `RestApiEndpoints`, `Shortcodes`, `SubComponents`, `Taxonomies`, and `Widgets`.
    - **`Config/`**: Configuration files for your plugin, including `config.php`.
    - **`Database/`**: Manages database migrations and schema changes.
    - **`Main.php`**: The core file where you initialize your plugin's components, admin pages, and settings.
    - **`Settings/`**: Contains classes for managing plugin settings.
- **`templates/`**: Stores custom template files for admin pages.
- **`tests/`**: Directory for unit and integration tests.

## Step 3: Creating Your First Component

Components are the building blocks of your plugin, allowing you to modularize different functionalities.

### Example: Creating a Test Component

Here is the `TestComponent` example:

```php
namespace Realtyna\BasePlugin\Components\Test;

use Realtyna\BasePlugin\Components\Test\AdminPages\TestAdminPage;
use Realtyna\BasePlugin\Components\Test\AjaxHandlers\TestAjaxHandler;
use Realtyna\BasePlugin\Components\Test\PostTypes\TestPostType;
use Realtyna\BasePlugin\Components\Test\RestApiEndpoints\TestRestApiEndpoint;
use Realtyna\BasePlugin\Components\Test\Shortcodes\TestShortcode;
use Realtyna\BasePlugin\Components\Test\SubComponents\TestSubComponent;
use Realtyna\BasePlugin\Components\Test\Taxonomies\GenreTaxonomy;
use Realtyna\BasePlugin\Components\Test\Widgets\TestWidget;
use Realtyna\Core\Abstracts\ComponentAbstract;

class TestComponent extends ComponentAbstract
{
    public function register(): void
    {
        // Register actions, filters, or any other initialization code here.
    }

    public function postTypes(): void
    {
        $this->addPostType(TestPostType::class);
    }

    public function subComponents(): void
    {
        $this->addSubComponent(TestSubComponent::class);
    }

    public function adminPages(): void
    {
        $this->addAdminPage(TestAdminPage::class);
    }

    public function ajaxHandlers(): void
    {
        $this->addAjaxHandler(TestAjaxHandler::class);
    }

    public function shortcodes(): void
    {
        $this->addShortcode(TestShortcode::class);
    }

    public function restApiEndpoints(): void
    {
        $this->addRestApiEndpoint(TestRestApiEndpoint::class);
    }

    public function widgets(): void
    {
        $this->addWidget(TestWidget::class);
    }

    public function customTaxonomies(): void
    {
        $this->addCustomTaxonomy(GenreTaxonomy::class);
    }
}
```

1. **Register the Component**: In `src/Main.php`, register your `TestComponent` to ensure it loads when the plugin is activated.

   ```php
   protected function components(): void
   {
       $this->addComponent(\Realtyna\BasePlugin\Components\Test\TestComponent::class);
   }
   ```

2. **Activate the Plugin**: Go to the WordPress admin dashboard and activate your plugin to see the component in action.

## Step 4: Creating a Custom Admin Page

Admin pages provide a way to add custom interfaces in the WordPress admin area.

### Example: Adding a Main Admin Page

1. **Create the Admin Page Class**: In the `src/AdminPages/` directory, open `MainPage.php`.

   ```php
   namespace MyCompany\MyPlugin\AdminPages;

   use Realtyna\MvcCore\Abstracts\AdminPageAbstract;

   class MainPage extends AdminPageAbstract
   {
       protected function title(): string
       {
           return __('Main Page');
       }

       protected function menuTitle(): string
       {
           return __('Main Page');
       }

       protected function slug(): string
       {
           return 'main-page';
       }

       public function render()
       {
           echo '<h1>Welcome to the Main Admin Page</h1>';
       }
   }
   ```

2. **Register the Admin Page**: Register your `MainPage` in `src/Main.php`.

   ```php
   protected function adminPages(): void
   {
       $this->addAdminPage(\MyCompany\MyPlugin\AdminPages\MainPage::class);
   }
   ```

3. **Test the Admin Page**: Activate the plugin and check the WordPress admin menu for your new page.

## Step 5: Working with Database Migrations

Manage database schema changes with the migration system provided by the framework.

### Example: Creating a Test Table

Here is the `CreateTestTable` migration example:

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

1. **Register the Migration**: In `src/Main.php`, register the migration.

   ```php
   protected function migrations(): void
   {
       $this->addMigration(\Realtyna\BasePlugin\Database\CreateTestTable::class);
   }
   ```

2. **Run the Migration**: Activate the plugin to automatically run the migration and create the test table.

## Conclusion

You’ve successfully set up a basic plugin, created a custom component, added an admin page, and managed a database migration using the Realtyna Base Plugin framework. From here, you can continue to explore the framework’s features and build more complex functionality into your plugin.

For more details, refer to the [Core Concepts](Core_Concepts.md) section.

Happy coding!