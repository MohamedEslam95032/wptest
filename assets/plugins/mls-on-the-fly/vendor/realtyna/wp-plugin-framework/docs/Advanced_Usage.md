# Advanced Usage

This section delves into the more complex aspects of the Realtyna Base Plugin framework, designed for developers who want to leverage the full power of this framework. Here, you'll find techniques and strategies for building modular architectures, managing dependencies, and extending the framework's capabilities.

## Table of Contents
1. [Working with Subcomponents](#working-with-subcomponents)
2. [Advanced Dependency Management with the App Class](#advanced-dependency-management-with-the-app-class)
3. [Advanced Logging with the Log Class](#advanced-logging-with-the-log-class)

## 1. Working with Subcomponents

Subcomponents allow you to create modular, reusable parts within your main components, making your plugin more organized and maintainable. This section covers advanced techniques for managing dependencies between subcomponents, lazy-loading subcomponents to improve performance, and dynamically registering subcomponents based on runtime conditions.

### Example: Dynamic Subcomponent Registration

```php
namespace YourNamespace\Components;

use Realtyna\Core\Abstracts\ComponentAbstract;

class MainComponent extends ComponentAbstract
{
    public function subComponents(): void
    {
        if ($this->someCondition()) {
            $this->addSubComponent(ConditionalSubComponent::class);
        }
    }

    private function someCondition(): bool
    {
        // Some complex logic to determine if the subcomponent should be loaded
        return true;
    }
}
```

## 2. Advanced Dependency Management with the App Class

The `App` class serves as a facade for the dependency injection container in the Realtyna Base Plugin framework. It provides a set of static methods to interact with the container, making it easier to manage services and components throughout your plugin.

### Purpose

The primary purposes of the `App` class are to:

- **Simplify Access to the Container**: The `App` class provides a straightforward API for accessing the dependency injection container, allowing you to retrieve, bind, and manage services without directly interacting with the container.
- **Centralize Dependency Management**: By using the `App` class, you ensure that all dependencies are managed in one place, making your code more maintainable and testable.
- **Facilitate Lazy Loading**: The `App` class supports lazy loading of services, ensuring that components are only initialized when they are needed.

### Example: Managing Dependencies with the App Class

```php
namespace YourNamespace\Components;

use Realtyna\BasePlugin\Boot\App;
use Realtyna\Core\Abstracts\ComponentAbstract;

class ExampleComponent extends ComponentAbstract
{
    public function register(): void
    {
        $logger = App::get('Logger');
        $logger->log('Component Registered');
    }
}

// Somewhere in your plugin setup
App::bind('Logger', function() {
    return new Logger();
});
```

## 3. Advanced Logging with the Log Class

The `Log` class in the Realtyna Base Plugin framework provides a static interface for logging various levels of messages, from debugging information to critical errors. This section explains how to configure and use the `Log` class to monitor and troubleshoot your plugin effectively.

### Purpose

The `Log` class is designed to:

- **Centralize Logging**: Provide a consistent logging mechanism throughout your plugin, ensuring that all logs are managed and output through a single interface.
- **Support Multiple Log Levels**: Allow different levels of logging (e.g., debug, info, warning, error) to help categorize and prioritize log messages.
- **Simplify Log Initialization**: Make it easy to set up and use logging without needing to manage logger instances manually.

### Initialization

Before you can use the `Log` class, you need to initialize it with a file path and an optional minimum log level:

```php
use Realtyna\BasePlugin\Boot\Log;

Log::init(WP_CONTENT_DIR . '/logs/plugin.log', 'info');
```

- **`$logFilePath`**: The path to the log file where log messages will be stored.
- **`$minLogLevel`**: The minimum level of log messages that should be recorded (e.g., 'debug', 'info', 'warning').

### Log Levels

The `Log` class supports multiple log levels, each corresponding to a different severity of messages:

- **Debug**: `Log::debug('Debug message');`
- **Info**: `Log::info('Info message');`
- **Notice**: `Log::notice('Notice message');`
- **Warning**: `Log::warning('Warning message');`
- **Error**: `Log::error('Error message');`
- **Critical**: `Log::critical('Critical message');`
- **Alert**: `Log::alert('Alert message');`
- **Emergency**: `Log::emergency('Emergency message');`

### Example: Using the Log Class

Here's an example of how to use the `Log` class in your plugin:

```php
use Realtyna\BasePlugin\Boot\Log;

class SomeComponent
{
    public function doSomething()
    {
        try {
            // Your logic here
            Log::info('Something was done successfully.');
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());
        }
    }
}
```

### Checking Logger Initialization

The `Log` class includes a method to check if the logger has been initialized, ensuring that logging attempts do not fail silently:

```php
if (Log::isLoggerInitialized()) {
    Log::debug('Logger is ready to use.');
}
```

## Conclusion

The Realtyna Base Plugin framework is designed to be flexible and powerful, enabling developers to create advanced, feature-rich plugins. By leveraging the techniques outlined in this section, such as subcomponents, advanced dependency management, and sophisticated logging, you can build plugins that are not only robust and scalable but also optimized for performance and maintainability.
