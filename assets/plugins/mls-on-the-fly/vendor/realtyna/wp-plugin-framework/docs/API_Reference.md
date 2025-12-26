# API Reference

This section provides a detailed reference for all the core classes, methods, and functions available in the Realtyna Base Plugin framework. It is intended as a quick lookup guide for developers who are implementing or extending the framework.

## Table of Contents
1. [Core Classes](#core-classes)
    - [App](#app)
    - [Log](#log)
    - [ComponentAbstract](#componentabstract)
    - [AdminPageAbstract](#adminpageabstract)
    - [AjaxHandlerAbstract](#ajaxhandlerabstract)
    - [CustomTaxonomyAbstract](#customtaxonomyabstract)
    - [PostTypeAbstract](#posttypeabstract)
    - [RestApiEndpointAbstract](#restapiendpointabstract)
    - [ShortcodeAbstract](#shortcodeabstract)
    - [WidgetAbstract](#widgetabstract)
    - [MigrationAbstract](#migrationabstract)
2. [Utilities](#utilities)
    - [Container](#container)
    - [Logger](#logger)

## 1. Core Classes

### App

The `App` class provides a facade for managing dependency injection and accessing services within the plugin.

#### Methods

- **setContainer(Container $container): void**
  - Sets the container instance.

- **getContainer(): Container**
  - Retrieves the current container instance.

- **get(string $abstract): mixed**
  - Retrieves a service or component from the container.

- **bind(string $abstract, callable|string|null $concrete = null): void**
  - Binds a class or interface to the container.

- **singleton(string $abstract, callable|string|null $concrete = null): void**
  - Binds a class or interface as a singleton in the container.

- **has(string $abstract): bool**
  - Checks if the container has a binding or instance for the given abstract.

- **resolve(string $concrete): mixed**
  - Manually resolves a class and its dependencies.

### Log

The `Log` class provides a static interface for logging messages at various levels.

#### Methods

- **init(string $logFilePath, string $minLogLevel = 'debug'): void**
  - Initializes the logger with a file path and minimum log level.

- **debug(string $message, array $context = []): void**
  - Logs a message at the debug level.

- **info(string $message, array $context = []): void**
  - Logs a message at the info level.

- **notice(string $message, array $context = []): void**
  - Logs a message at the notice level.

- **warning(string $message, array $context = []): void**
  - Logs a message at the warning level.

- **error(string $message, array $context = []): void**
  - Logs a message at the error level.

- **critical(string $message, array $context = []): void**
  - Logs a message at the critical level.

- **alert(string $message, array $context = []): void**
  - Logs a message at the alert level.

- **emergency(string $message, array $context = []): void**
  - Logs a message at the emergency level.

### ComponentAbstract

The `ComponentAbstract` class provides a base for creating modular components in the plugin.

#### Methods

- **register(): void**
  - Registers the component, including any additional functionality that needs to be implemented by the subclass.

- **postTypes(): void**
  - Defines the custom post types that the component should register.

- **subComponents(): void**
  - Defines the subcomponents that the component should register.

- **adminPages(): void**
  - Defines the admin pages that the component should register.

- **ajaxHandlers(): void**
  - Defines the AJAX handlers that the component should register.

- **shortcodes(): void**
  - Defines the shortcodes that the component should register.

- **restApiEndpoints(): void**
  - Defines the REST API endpoints that the component should register.

- **widgets(): void**
  - Defines the widgets that the component should register.

- **customTaxonomies(): void**
  - Defines the custom taxonomies that the component should register.

### AdminPageAbstract

The `AdminPageAbstract` class provides a base for creating admin pages in the WordPress admin area.

#### Methods

- **register(): void**
  - Initializes the admin page by adding it to the admin menu.

- **getPageTitle(): string**
  - Returns the title of the admin page.

- **getMenuTitle(): string**
  - Returns the title of the menu item for the admin page.

- **getCapability(): string**
  - Returns the capability required to access the admin page.

- **getMenuSlug(): string**
  - Returns the slug for the admin page.

- **getPageTemplate(): string**
  - Returns the path to the template file used for rendering the admin page.

- **enqueuePageAssets(): void**
  - Enqueues styles and scripts for the admin page.

### AjaxHandlerAbstract

The `AjaxHandlerAbstract` class provides a base for handling AJAX requests in WordPress.

#### Methods

- **getAction(): string**
  - Returns the action name for the AJAX handler.

- **handle(): void**
  - Handles the AJAX request.

- **sendJsonResponse(mixed $data, int $status_code = 200): \WP_REST_Response**
  - Sends a JSON response and terminates the script.

- **sendJsonError(string $message, int $status_code = 400): \WP_Error**
  - Sends a JSON error response and terminates the script.

- **verifyNonce(string $nonce_name, string $action): bool**
  - Verifies the AJAX request nonce.

### CustomTaxonomyAbstract

The `CustomTaxonomyAbstract` class provides a base for creating custom taxonomies in WordPress.

#### Methods

- **registerTaxonomy(): void**
  - Registers the taxonomy with WordPress.

- **getTaxonomySlug(): string**
  - Returns the slug for the custom taxonomy.

- **getPostTypes(): array**
  - Returns the post types that the taxonomy applies to.

- **getTaxonomyArgs(): array**
  - Returns the arguments for registering the taxonomy.

- **getTaxonomyLabels(): array**
  - Returns the labels for the taxonomy.

### PostTypeAbstract

The `PostTypeAbstract` class provides a base for defining custom post types in WordPress.

#### Methods

- **definePostType(): string**
  - Defines the slug for the custom post type.

- **modifyArgs(array $args): array**
  - Modifies the arguments for the custom post type.

- **register(): void**
  - Registers the custom post type with WordPress.

- **setDefaultLabels(): void**
  - Sets the default labels for the custom post type.

- **setDefaultArgs(): void**
  - Sets the default arguments for the custom post type.

- **setLabels(array $labels): void**
  - Sets or updates the labels for the custom post type.

- **setArgs(array $args): void**
  - Sets or updates the arguments for the custom post type.

### RestApiEndpointAbstract

The `RestApiEndpointAbstract` class provides a base for handling REST API endpoints in WordPress.

#### Methods

- **registerRoutes(): void**
  - Registers the routes for the REST API endpoint.

- **handleRequest(\WP_REST_Request $request): \WP_Error|\WP_REST_Response**
  - Handles the REST API request and returns a response.

- **sendJsonResponse(mixed $data, int $status_code = 200): \WP_REST_Response**
  - Sends a JSON response.

- **sendJsonError(string $message, int $status_code = 400): \WP_Error**
  - Sends a JSON error response.

### ShortcodeAbstract

The `ShortcodeAbstract` class provides a base for handling shortcodes in WordPress.

#### Methods

- **getTag(): string**
  - Returns the tag name for the shortcode.

- **render(array $atts, ?string $content = null): string**
  - Handles the rendering of the shortcode.

- **output(array $atts, ?string $content = null): string**
  - Generates and returns the output for the shortcode.

### WidgetAbstract

The `WidgetAbstract` class provides a base for creating widgets in WordPress.

#### Methods

- **getWidgetId(): string**
  - Returns the widget's ID.

- **getWidgetName(): string**
  - Returns the widget's name.

- **getWidgetOptions(): array**
  - Returns the widget's options, including description and any other settings.

- **form($instance): void**
  - Outputs the widget's form in the admin area.

- **update($new_instance, $old_instance): array**
  - Saves the widget's settings.

- **widget($args, $instance): void**
  - Outputs the content of the widget on the front end.

### MigrationAbstract

The `MigrationAbstract` class provides a base for creating database migrations in WordPress.

#### Methods

- **up(): void**
  - Applies the migration (e.g., creates tables, adds columns).

- **down(): void**
  - Rolls back the migration (e.g., drops tables, removes columns).

- **runQuery(string $query): void**
  - Executes the provided SQL query using the WordPress database API.

## 2. Utilities

### Container

The `Container` class is responsible for managing dependency injection and resolving dependencies within the plugin.

#### Methods

- **

bind(string $abstract, callable|string|null $concrete = null): void**
  - Binds a class or interface to the container.

- **singleton(string $abstract, callable|string|null $concrete = null): void**
  - Binds a class or interface as a singleton in the container.

- **get(string $abstract): mixed**
  - Resolves a service or component from the container.

- **has(string $abstract): bool**
  - Checks if the container has a binding or instance for the given abstract.

- **resolve(string $concrete): mixed**
  - Manually resolves a class and its dependencies.

### Logger

The `Logger` class is used internally by the `Log` class to manage logging. It handles writing log messages to a file, filtering messages by severity, and formatting log entries.

#### Methods

- **__construct(string $logFilePath, string $minLogLevel = 'debug')**
  - Initializes the logger with a file path and minimum log level.

- **log(string $level, string $message, array $context = []): void**
  - Logs a message with the specified level.

- **debug(string $message, array $context = []): void**
  - Logs a debug message.

- **info(string $message, array $context = []): void**
  - Logs an info message.

- **notice(string $message, array $context = []): void**
  - Logs a notice message.

- **warning(string $message, array $context = []): void**
  - Logs a warning message.

- **error(string $message, array $context = []): void**
  - Logs an error message.

- **critical(string $message, array $context = []): void**
  - Logs a critical message.

- **alert(string $message, array $context = []): void**
  - Logs an alert message.

- **emergency(string $message, array $context = []): void**
  - Logs an emergency message.

## Conclusion

This API reference provides a detailed guide to the core classes and utilities in the Realtyna Base Plugin framework. By understanding these components, you can effectively leverage the framework's capabilities to build robust, scalable, and maintainable WordPress plugins.
