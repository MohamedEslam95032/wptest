<?php

namespace Realtyna\Core\Utilities;

use ReflectionClass;
use ReflectionException;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    /**
     * Bind a class or interface to a specific implementation or callable.
     *
     * @param string $abstract
     * @param callable|string|null $concrete
     */
    public function bind(string $abstract, callable|string|null $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?: $abstract;
    }

    /**
     * Bind an existing instance to the container.
     *
     * This method allows you to store a pre-existing object instance
     * within the container. This can be useful when you want to bind an
     * object that is already instantiated and reuse it across your application.
     *
     * @param string $abstract The class or interface name.
     * @param mixed $instance The object instance to bind.
     *
     * @return void
     */
    public function set(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Bind a class or interface as a singleton.
     *
     * @param string $abstract
     * @param callable|string|null $concrete
     */
    public function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        $this->bindings[$abstract] = function ($container) use ($concrete, $abstract) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $container->resolve($concrete ?: $abstract);
            }
            return $this->instances[$abstract];
        };
    }

    /**
     * Resolve a class or interface from the container.
     *
     * @param string $abstract
     * @return mixed
     * @throws ReflectionException
     */
    public function get(string $abstract): mixed
    {
        // Check if an instance is directly set
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // If the binding is a closure, execute it.
        if (isset($this->bindings[$abstract]) && is_callable($this->bindings[$abstract])) {
            return $this->bindings[$abstract]($this);
        }

        // Otherwise, resolve the class or interface
        return $this->resolve($abstract);
    }


    /**
     * Automatically resolve the class dependencies and instantiate the class.
     *
     * @param string $concrete
     * @return mixed
     * @throws ReflectionException
     */
    public function resolve(string $concrete): mixed
    {
        $reflection = new ReflectionClass($concrete);

        // Check if the class is instantiable.
        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            // If there is no constructor, just instantiate the class.
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $instances = [];

        foreach ($dependencies as $dependency) {
            $type = $dependency->getType();
            if ($type && !$type->isBuiltin()) {
                $instances[] = $this->get($type->getName());
            } else {
                $instances[] = $dependency->isDefaultValueAvailable()
                    ? $dependency->getDefaultValue()
                    : null;
            }
        }

        return $reflection->newInstanceArgs($instances);
    }

    /**
     * Check if an abstract has been bound.
     *
     * @param string $abstract
     * @return bool
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }
}
