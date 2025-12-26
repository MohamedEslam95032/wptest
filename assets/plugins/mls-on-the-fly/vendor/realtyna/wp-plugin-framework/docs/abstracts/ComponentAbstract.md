# ComponentAbstract

The `ComponentAbstract` class serves as the foundational building block for all components within the Realtyna Base Plugin framework. It provides a standardized structure for managing various subcomponents, such as admin pages, custom post types, AJAX handlers, shortcodes, REST API endpoints, widgets, and custom taxonomies. By extending `ComponentAbstract`, developers can easily create modular, reusable components that encapsulate specific functionalities of their plugin.

## Purpose

The primary purpose of `ComponentAbstract` is to provide a common framework for:

- **Modularity**: Allowing different parts of the plugin to be broken down into manageable, independent components.
- **Reusability**: Enabling components to be reused across different plugins or different parts of the same plugin.
- **Consistency**: Ensuring that all components follow a similar structure, making the plugin easier to understand and maintain.

## Key Properties

The `ComponentAbstract` class defines several key properties that store the class names of various subcomponents:

- **`$subComponents`**: Stores the class names of subcomponents.
- **`$adminPages`**: Stores the class names of admin pages.
- **`$postTypes`**: Stores the class names of custom post types.
- **`$ajaxHandlers`**: Stores the class names of AJAX handlers.
- **`$shortcodes`**: Stores the class names of shortcodes.
- **`$restApiEndpoints`**: Stores the class names of REST API endpoints.
- **`$widgets`**: Stores the class names of widgets.
- **`$customTaxonomies`**: Stores the class names of custom taxonomies.

## Key Methods

### Abstract Methods

These methods must be implemented by any class that extends `ComponentAbstract`:

- **`register()`**: This method is where you define the core functionality of the component. It is called after all other subcomponents and elements have been registered.
- **`postTypes()`**: Define the custom post types that the component should register.
- **`subComponents()`**: Define the subcomponents that the component should register.
- **`adminPages()`**: Define the admin pages that the component should register.
- **`ajaxHandlers()`**: Define the AJAX handlers that the component should register.
- **`shortcodes()`**: Define the shortcodes that the component should register.
- **`restApiEndpoints()`**: Define the REST API endpoints that the component should register.
- **`widgets()`**: Define the widgets that the component should register.
- **`customTaxonomies()`**: Define the custom taxonomies that the component should register.

### Registration Methods

These methods handle the actual registration of the various subcomponents and elements:

- **`registerAdminPages()`**: Loops through the `$adminPages` array and registers each admin page.
- **`registerPostTypes()`**: Loops through the `$postTypes` array and registers each custom post type.
- **`registerSubComponents()`**: Loops through the `$subComponents` array and registers each subcomponent.
- **`registerAjaxHandlers()`**: Loops through the `$ajaxHandlers` array and registers each AJAX handler.
- **`registerShortcodes()`**: Loops through the `$shortcodes` array and registers each shortcode.
- **`registerRestApiEndpoints()`**: Loops through the `$restApiEndpoints` array and registers each REST API endpoint.
- **`registerWidgets()`**: Loops through the `$widgets` array and registers each widget.
- **`registerCustomTaxonomies()`**: Loops through the `$customTaxonomies` array and registers each custom taxonomy.

### Utility Methods

These methods are used to add subcomponents and other elements to the component:

- **`addAdminPage($adminPage)`**: Adds an admin page to the `$adminPages` array.
- **`addPostType($postType)`**: Adds a custom post type to the `$postTypes` array.
- **`addSubComponent($subComponent)`**: Adds a subcomponent to the `$subComponents` array.
- **`addAjaxHandler($ajaxHandler)`**: Adds an AJAX handler to the `$ajaxHandlers` array.
- **`addShortcode($shortcode)`**: Adds a shortcode to the `$shortcodes` array.
- **`addRestApiEndpoint($restApiEndpoint)`**: Adds a REST API endpoint to the `$restApiEndpoints` array.
- **`addWidget($widget)`**: Adds a widget to the `$widgets` array.
- **`addCustomTaxonomy($customTaxonomy)`**: Adds a custom taxonomy to the `$customTaxonomies` array.

## Example Usage

Here’s an example of how you might use the `ComponentAbstract` class to create a `TestComponent`:

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
        // Register additional functionality here
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
