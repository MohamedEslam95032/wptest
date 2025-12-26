# AdminPageAbstract

The `AdminPageAbstract` class is an abstract base class that provides a standardized structure for creating admin pages within the WordPress admin area. By extending this class, developers can quickly create custom admin pages with consistent behavior, including menu registration, page rendering, and asset management.

## Purpose

The primary purpose of `AdminPageAbstract` is to:

- **Simplify Admin Page Creation**: Provide a consistent and reusable way to define and manage admin pages.
- **Standardize Rendering**: Ensure that all admin pages follow a similar structure for rendering content.
- **Manage Assets**: Provide a simple mechanism for enqueuing styles and scripts specific to the admin page.

## Key Methods

### Abstract Methods

These methods must be implemented by any class that extends `AdminPageAbstract`:

- **`getPageTitle()`**: Returns the title of the admin page. This title is displayed at the top of the page and in the browser tab.
- **`getMenuTitle()`**: Returns the title of the menu item. This is the text that appears in the WordPress admin menu.
- **`getCapability()`**: Returns the required capability to access the admin page. This ensures that only users with the appropriate permissions can view the page.
- **`getMenuSlug()`**: Returns the slug for the admin page. The slug is a unique identifier used in the URL to access the page.
- **`getPageTemplate()`**: Returns the path to the template file used to render the page content. This allows you to separate your HTML structure into a dedicated template file.
- **`enqueuePageAssets()`**: This method is used to enqueue styles and scripts specific to the admin page. It should be implemented to load any necessary CSS or JavaScript files.

### Concrete Methods

These methods are already implemented in the `AdminPageAbstract` class and handle common tasks such as menu registration and page rendering:

- **`register()`**: Hooks the admin page into the WordPress admin menu and enqueues assets when the page is loaded.
- **`addMenuPage()`**: Registers the admin page or submenu with WordPress using the details provided by the abstract methods.
- **`renderPage()`**: Renders the content of the admin page by including the specified template file.
- **`enqueueAssets()`**: Checks if the current screen matches the page slug and then calls `enqueuePageAssets()` to load the necessary assets.
- **`removeAdminNotices()`**: Removes all admin notices from the page to provide a cleaner interface.
- **`isSubmenu()`**: Determines if the page is a submenu. Override this method in your subclass if you want to create a submenu.
- **`getParentSlug()`**: Returns the parent slug for a submenu. This should be overridden if the page is a submenu.
- **`getIconUrl()`**: Returns the URL of the icon displayed in the admin menu. Override this method to customize the icon.
- **`getPosition()`**: Returns the position of the menu item in the admin menu. Override this method to control where the menu item appears.

## Example Usage

Here’s an example of how you might use the `AdminPageAbstract` class to create a `TestAdminPage`:

```php
namespace Realtyna\BasePlugin\Components\Test\AdminPages;

use Realtyna\Core\Abstracts\AdminPageAbstract;

class TestAdminPage extends AdminPageAbstract
{
    protected function getPageTitle(): string
    {
        return __('Test Admin Page', 'text-domain');
    }

    protected function getMenuTitle(): string
    {
        return __('Tests', 'text-domain');
    }

    protected function getCapability(): string
    {
        return 'manage_options';
    }

    protected function getMenuSlug(): string
    {
        return 'test-admin-page';
    }

    protected function getPageTemplate(): string
    {
        return REALTYNA_BASE_PLUGIN_DIR . 'templates/admin/test-admin-page.php';
    }

    protected function enqueuePageAssets(): void
    {
        // Enqueue specific styles and scripts for this page
    }

    protected function isSubmenu(): bool
    {
        return false; // Set to true if this is a submenu
    }

    protected function getParentSlug(): string
    {
        return ''; // Set the parent slug if this is a submenu
    }

    protected function getIconUrl(): string
    {
        return 'dashicons-admin-tools'; // Customize the icon
    }

    protected function getPosition(): int
    {
        return 20; // Customize the position in the admin menu
    }
}
```