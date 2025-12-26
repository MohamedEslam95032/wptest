<?php

namespace Realtyna\MlsOnTheFly\Boot;
use Realtyna\Core\Utilities\Container;

class App
{
    /**
     * @var Container|null
     */
    private static ?Container $container = null;

    /**
     * Set the container instance.
     *
     * @param Container $container
     */
    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    /**
     * Get the container instance.
     *
     * @return Container
     * @throws \Exception
     */
    public static function getContainer(): Container
    {
        if (!self::$container) {
            throw new \Exception('Container has not been set.');
        }

        return self::$container;
    }

    /**
     * Get a service or component from the container.
     *
     * @param string $abstract
     * @return mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function get(string $abstract): mixed
    {
        return self::getContainer()->get($abstract);
    }

    /**
     * Bind an existing instance to the container.
     *
     * This method allows you to store a pre-existing object instance
     * within the container via the static interface of the App class.
     *
     * @param string $abstract The class or interface name.
     * @param mixed $instance The object instance to bind.
     *
     * @throws \Exception If the container is not set.
     *
     * @return void
     */
    public static function set(string $abstract, $instance): void
    {
        self::getContainer()->set($abstract, $instance);
    }


    /**
     * Bind a class or interface to the container.
     *
     * @param string $abstract
     * @param callable|string|null $concrete
     * @throws \Exception
     */
    public static function bind(string $abstract, callable|string|null $concrete = null): void
    {
        self::getContainer()->bind($abstract, $concrete);
    }

    /**
     * Bind a class or interface as a singleton in the container.
     *
     * @param string $abstract
     * @param callable|string|null $concrete
     * @throws \Exception
     */
    public static function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        self::getContainer()->singleton($abstract, $concrete);
    }

    /**
     * Check if the container has a binding or instance for the given abstract.
     *
     * @param string $abstract
     * @return bool
     * @throws \Exception
     */
    public static function has(string $abstract): bool
    {
        return self::getContainer()->has($abstract);
    }

    /**
     * Manually resolve a class and its dependencies.
     *
     * @param string $concrete
     * @return mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function resolve(string $concrete): mixed
    {
        return self::getContainer()->resolve($concrete);
    }
}
