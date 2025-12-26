<?php

use Realtyna\Core\Utilities\Container;

class ContainerTest extends WP_UnitTestCase
{
    protected $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testBindAndResolveSimpleClass()
    {
        // Binding a class to the container
        $this->container->bind('SimpleClass', SimpleClass::class);

        // Resolving the class
        $instance = $this->container->get('SimpleClass');

        // Assertions
        $this->assertInstanceOf(SimpleClass::class, $instance);
    }

    public function testSingletonBinding()
    {
        // Binding a class as a singleton
        $this->container->singleton('SingletonClass', SingletonClass::class);

        // Resolving the singleton class
        $instance1 = $this->container->get('SingletonClass');
        $instance2 = $this->container->get('SingletonClass');

        // Assertions: Both instances should be the same
        $this->assertSame($instance1, $instance2);
    }

    public function testResolveClassWithDependencies()
    {
        // Binding a class with dependencies
        $this->container->bind('DependentClass', DependentClass::class);

        // Resolving the class with dependencies
        $instance = $this->container->get('DependentClass');

        // Assertions
        $this->assertInstanceOf(DependentClass::class, $instance);
        $this->assertInstanceOf(SimpleClass::class, $instance->simpleClass);
    }

    public function testExceptionForUninstantiableClass()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class InterfaceClass is not instantiable.');

        // Attempt to resolve an interface (which cannot be instantiated)
        $this->container->get('InterfaceClass');
    }

}

// Mock classes for testing
class SimpleClass {}

class SingletonClass {}

class DependentClass {
    public $simpleClass;
    public function __construct(SimpleClass $simpleClass)
    {
        $this->simpleClass = $simpleClass;
    }
}

interface InterfaceClass {}
