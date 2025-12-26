<?php

namespace Realtyna\Core;

use Realtyna\Core\Abstracts\AdminPageAbstract;
use Realtyna\Core\Abstracts\ComponentAbstract;
use Realtyna\Core\Abstracts\Database\MigrationAbstract;
use Realtyna\Core\Exceptions\RequirementsNotMetException;
use Realtyna\Core\Utilities\Container;

/**
 * Class StartUp
 *
 * This abstract class serves as the base class for initializing components,
 * admin pages, and database migrations for the plugin. It manages the registration
 * of components, admin pages, and the execution of database migrations in the
 * correct order during activation and uninstallation.
 *
 * @package Realtyna\Core
 */
abstract class StartUp
{
    /**
     * @var Config The configuration settings for the plugin.
     */
    protected Config $config;

    /**
     * @var Container The dependency injection container.
     */
    protected Container $container;

    /**
     * @var array The list of component class names to be registered.
     */
    private array $components = [];

    /**
     * @var array The list of admin page class names to be registered.
     */
    private array $adminPages = [];

    /**
     * @var array The list of migration class names to be executed in order.
     */
    private static array $migrations = [];

    /**
     * StartUp constructor.
     *
     * Initializes the plugin by setting up the container, registering migrations,
     * admin pages, and components.
     *
     * @param Config $config The configuration instance for the plugin.
     * @throws \ReflectionException
     * @throws RequirementsNotMetException
     */
    public function __construct(Config $config)
    {
        if($this->requirements()){
            $this->config = $config;
            $this->container = new Container();

            $this->boot();

            $this->migrations();
            $this->adminPages();
            $this->components();
        }else{
            throw new RequirementsNotMetException('Plugin\'s requirements was not met.');
        }
    }

    /**
     * Add a component to the list for registration.
     *
     * @param string $component The component class name.
     * @return void
     */
    public function addComponent(string $component): void
    {
        $service = $this->container->get($component);
        if ($service instanceof ComponentAbstract && method_exists($service, 'register')) {
            $service->register();
        }
    }


    /**
     * Add an admin page to the list for registration.
     *
     * @param string $adminPage The admin page class name.
     * @return void
     */
    public function addAdminPage(string $adminPage): void
    {
        $this->adminPages[] = $adminPage;
        $service = $this->container->get($adminPage);
        if ($service instanceof AdminPageAbstract && method_exists($service, 'register')) {
            $service->register();
        }
    }

    /**
     * Add a migration to the list.
     *
     * @param string $migrationClass The migration class name.
     * @return void
     */
    protected function addMigration(string $migrationClass): void
    {
        self::$migrations[] = $migrationClass;
    }

    /**
     * Run all migrations in the order they were added.
     *
     * This method is typically called during plugin activation.
     *
     * @return void
     */
    public function migrate(): void
    {
        foreach (self::$migrations as $migrationClass) {
            $service = $this->container->get($migrationClass);
            if ($service instanceof MigrationAbstract && method_exists($service, 'up')) {
                $service->up();
            }
        }
    }

    /**
     * Rollback all migrations in reverse order.
     *
     * This method is typically called during plugin uninstallation.
     *
     * @return void
     */
    public static function rollback(): void
    {
        for ($i = count(self::$migrations) - 1; $i >= 0; $i--) {
            $service = new self::$migrations[$i]();
            if ($service instanceof MigrationAbstract && method_exists($service, 'down')) {
                $service->down();
            }
        }
    }

    /**
     * Boot method for initializing the plugin.
     *
     * This method should be implemented by the subclass to handle any necessary
     * setup before registering components, admin pages, and migrations.
     *
     * @return void
     */
    abstract protected function boot(): void;

    /**
     * Method to register components.
     *
     * This method should be implemented by the subclass to add components
     * to be registered during the plugin's initialization.
     *
     * @return void
     */
    abstract protected function components(): void;

    /**
     * Method to register admin pages.
     *
     * This method should be implemented by the subclass to add admin pages
     * to be registered during the plugin's initialization.
     *
     * @return void
     */
    abstract protected function adminPages(): void;

    /**
     * Method to register migrations.
     *
     * This method should be implemented by the subclass to add migrations
     * that will be executed during plugin activation.
     *
     * @return void
     */
    abstract protected function migrations(): void;

    /**
     * Method to check plugin requirements.
     *
     * This method should be implemented by the subclass to check if all
     * necessary requirements (e.g., PHP version, WordPress version, other plugins)
     * are met before the plugin is activated.
     *
     * @return bool Returns true if all requirements are met, false otherwise.
     */
    abstract protected function requirements(): bool;


}
