# Installation

This guide will walk you through the process of setting up the Realtyna Base Plugin framework for developing WordPress plugins. Whether you're setting up a new project or integrating the framework into an existing one, these instructions will help you get started quickly.

## Prerequisites

Before you begin, make sure you have the following:

- **WordPress Environment**: A working WordPress installation on your local machine or server.
- **Composer**: The PHP dependency manager, which you can download from [getcomposer.org](https://getcomposer.org/).
- **Basic PHP Knowledge**: Familiarity with PHP and WordPress plugin development.

## Step 1: Install the Framework

To create a new plugin using the Realtyna Base Plugin framework, run the following command in your terminal:

```bash
composer create-project realtyna/base-plugin {PluginName}
```

Replace `{PluginName}` with the desired name of your plugin. This command will:

- Download the Realtyna Base Plugin framework.
- Set up the basic structure of your plugin.
- Automatically update the namespace, constant name, plugin name, and configuration settings based on the name you provide.

### Example

If you want to create a plugin called "My Awesome Plugin," you would run:

```bash
composer create-project realtyna/base-plugin my-awesome-plugin
```

This will generate a new directory called `my-awesome-plugin` with all the necessary files and folders.

## Step 2: Configure Your Plugin

After creating your project, the framework will automatically configure some basic settings. However, you may want to review and customize these settings further.

### Update `config.php`

The `config.php` file located in the `./src/Config/config.php` directory contains the configuration for your plugin. Here's an example configuration:

```php
return [
    'name' => 'My Awesome Plugin',
    'slug' => 'my-awesome-plugin',
    'text-domain' => 'my-awesome-plugin',
    'log' => [
        'active' => true,
        'level' => 'error',
        'path' => MY_AWESOME_PLUGIN_DIR . '/logs'
    ],
];
```

- **name**: The name of your plugin.
- **slug**: The slug used for your plugin (usually a lowercase, hyphen-separated version of the name).
- **text-domain**: The text domain used for translation purposes.
- **log**: Configuration for logging. Set `active` to `true` to enable logging, and specify the `level` of logging (e.g., `error`, `warning`, `info`).

### Overview of Key Configuration Settings

- **Namespace**: The namespace for your plugin classes will be automatically set based on the plugin name. You can find and modify it in the `composer.json` file if needed.
- **Constants**: The main constants used throughout the plugin will be updated to reflect your plugin name. These are typically defined in the main plugin file.

## Step 3: Test Your Installation

Once you have set up the plugin, it's a good idea to test the installation to ensure everything is working correctly.

1. **Activate the Plugin**: Go to your WordPress admin dashboard, navigate to the "Plugins" section, and activate your newly created plugin.
2. **Verify the Structure**: Check that the expected files and folders have been created in your plugin directory.
3. **Check the Admin Area**: Make sure any admin pages or settings defined by your plugin are appearing correctly.

## Step 4: Begin Development

With the installation complete, you can start developing your plugin. Refer to the [Getting Started](Getting_Started.md) guide for an introduction to the core concepts and how to structure your plugin using the Realtyna Base Plugin framework.

## Troubleshooting

If you encounter any issues during installation, consider the following tips:

- **Composer Errors**: Ensure Composer is installed and updated to the latest version.
- **Permissions Issues**: Make sure your web server has the necessary permissions to write to the plugin directory.
- **Namespace Conflicts**: If you encounter namespace conflicts, double-check the configuration in your `composer.json` file.

For more help, see the [Troubleshooting and FAQ](Troubleshooting_and_FAQ.md) section.