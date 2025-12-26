# Realtyna Base Plugin
![WordPress Base Plugin Banner](https://realtyna.com/wp-content/uploads/2019/05/logo.png)


Welcome to the Realtyna Base Plugin, a structured and scalable foundation for developing WordPress plugins. This framework is designed to simplify the process of plugin development by providing a set of tools and conventions, enabling developers to create robust and maintainable plugins.

## Introduction

Hi, I'm Chandler, and I'm here working for [Realtyna](https://realtyna.com/). I'm a Senior Backend/WordPress Developer. At Realtyna, we've developed numerous plugins, and this framework is the result of continuous improvement in our development process.

Previously, I created a WordPress framework called [Realtyna MVC Core](https://github.com/realtyna/Realtyna-MVC-Core), which you can find the documentation for [here](https://code.realtyna.com/mvc-core-docs). That structure served us well for almost a year. However, over time, I realized that we were relying heavily on third-party Composer packages that weren't essential. This prompted me to refactor the framework. The idea remains the same, but with what I believe is a much better execution this time.

I would love to hear your opinions and contributions to this project!

## Features

- **Modular Architecture**: Create plugins with a clear separation of concerns using components and subcomponents.
- **Settings Management**: Easily manage plugin settings with a centralized handler.
- **Database Migrations**: Utilize a migration system for database schema management.
- **Admin Page Management**: Simplify the creation and organization of admin pages with custom templates.
- **Custom Utilities**: Implement custom logging, helper functions, and more.
- **Abstract Classes**: Base classes for common WordPress functionalities like admin pages, custom post types, taxonomies, widgets, shortcodes, AJAX handlers, and REST API endpoints.


## Documentation

Comprehensive documentation is available for developers of all levels:

- [Introduction](docs/Introduction.md)
- [Installation](docs/Installation.md)
- [Getting Started](docs/Getting_Started.md)
- [Core Concepts](docs/Core_Concepts.md)
- [Advanced Usage](docs/Advanced_Usage.md)
- [API Reference](docs/API_Reference.md)
- [Examples and Use Cases](docs/Examples_and_Use_Cases.md)
- [Release Script Guide](docs/release.md)
- [Troubleshooting and FAQ](docs/Troubleshooting_and_FAQ.md)
- [Contribution Guide](docs/Contribution_Guide.md)
- [Changelog](docs/Changelog.md)
- [License](docs/License.md)

## Installation

### Creating the Base Plugin

To create a new WordPress plugin using the Realtyna Base Plugin framework, you can use the following Composer command:

```bash
composer create-project realtyna/base-plugin {PluginName}
```

Replace `{PluginName}` with the desired name for your plugin. This command will generate the base structure for your new plugin and automatically update the namespace, constant name, plugin name, and configuration settings based on the project name you provide.

### Configuring Your Plugin

After running the `create-project` command, the script will automatically:

1. **Update Namespace**: The namespace in all class files will be updated to match your project name.
2. **Update Constant Name**: The constant name used throughout the project will reflect your plugin's name.
3. **Update Plugin Name**: The plugin name used in various parts of the code and configuration will be set to the name you provided.
4. **Configure `config.php`**: The `config.php` file will be updated to reflect your plugin’s name, slug, and text domain based on the project name.

#### Example Configuration

The `config.php` file located in the `./src/Config/config.php` directory will automatically be configured, but you can still customize additional settings if needed. Here’s an example:

```php
return [
    'name' => 'Realtyna Base Plugin',
    'slug' => 'realtyna-base-plugin',
    'text-domain' => 'realtyna-base-plugin',
    'log' => [
        'active' => true,
        'level' => 'error',
        'path' => REALTYNA_BASE_PLUGIN_DIR . '/logs'
    ],
];
```

- **name**: The name of your plugin.
- **slug**: The slug used for your plugin (usually a lowercase, hyphen-separated version of the name).
- **text-domain**: The text domain used for translation purposes.
- **log**: Configuration for logging. Set `active` to `true` to enable logging, and specify the `level` of logging (e.g., `error`, `warning`, `info`).

### Overview of `Main.php`

The `Main.php` file is the heart of your plugin. It extends the `StartUp` class and handles the initialization of your plugin’s components, admin pages, logging, and database migrations.

#### Key Methods in `Main.php`:

- **components()**: Register your plugin’s components here. Each component is a separate class that encapsulates specific functionality, such as handling custom post types or AJAX requests.

  ```php
  protected function components(): void
  {
      $this->addComponent(YourComponent::class);
  }
  ```

- **adminPages()**: Register admin pages for your plugin. Admin pages allow you to create custom interfaces in the WordPress dashboard.

  ```php
  protected function adminPages(): void
  {
      $this->addAdminPage(YourAdminPage::class);
  }
  ```

- **boot()**: This method is used for bootstrapping your plugin, setting up the container, and initializing logging if configured.

  ```php
  protected function boot(): void
  {
      // Set the container in the App class for global access.
      App::setContainer($this->container);
      if($this->config->get('log.active')){
          Log::init($this->config->get('log.path'), $this->config->get('log.level'));
      }
  }
  ```

- **migrations()**: Register database migrations here. Migrations handle changes to the database schema, such as creating or altering tables.

  ```php
  protected function migrations(): void
  {
      $this->addMigration(CreateYourTableMigration::class);
  }
  ```

### Developing a Component

Components are modular pieces of your plugin that handle specific tasks. For example, you might have a component for custom post types, another for handling AJAX requests, and another for registering REST API endpoints.

#### Example: Creating a Component

Here is a basic example of how to create a component with all its features:

```php
namespace MyCompany\MyPlugin\Components;

use Realtyna\MvcCore\Abstracts\ComponentAbstract;

class MyComponent extends ComponentAbstract
{
    public function register()
    {
        // Register your component actions and filters here
    }

    public function postTypes()
    {
        $this->addPostType(MyCustomPostType::class);
    }

    public function subComponents()
    {
        $this->addSubComponent(MySubComponent::class);
    }

    public function adminPages()
    {
        $this->addAdminPage(MyAdminPage::class);
    }

    public function ajaxHandlers()
    {
        $this->addAjaxHandler(MyAjaxHandler::class);
    }

    public function shortcodes()
    {
        $this->addShortcode(MyShortcode::class);
    }

    public function restApiEndpoints()
    {
        $this->addRestApiEndpoint(MyRestApiEndpoint::class);
    }

    public function widgets()
    {
        $this->addWidget(MyWidget::class);
    }

    public function customTaxonomies()
    {
        $this->addCustomTaxonomy(MyCustomTaxonomy::class);
    }
}
```

#### Key Methods in the Component:

- **register()**: This method is where you register your component’s actions and filters with WordPress.
- **postTypes()**: Add custom post types handled by this component.
- **subComponents()**: If your component has subcomponents, register them here.
- **adminPages()**: Add any admin pages that belong to this component.
- **ajaxHandlers()**: Register AJAX handlers that will respond to AJAX requests.
- **shortcodes()**: Add any shortcodes provided by this component.
- **restApiEndpoints()**: Register any REST API endpoints.
- **widgets()**: Add custom widgets provided by this component.
- **customTaxonomies()**: Register any custom taxonomies.

By following this structure, you can build well-organized, maintainable, and scalable WordPress plugins using the Realtyna Base Plugin framework.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Acknowledgements

- Thanks to all the open-source projects that make development faster and easier.
