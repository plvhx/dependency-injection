<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace DependencyInjection\Tests;

use DependencyInjection\Container;
use DependencyInjection\Tests\Fixtures\Base;
use DependencyInjection\Tests\Fixtures\BaseInterface;
use DependencyInjection\Tests\Fixtures\Foo;
use DependencyInjection\Tests\Fixtures\FooWithInterface;
use DependencyInjection\Tests\Fixtures\Bar;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
	public function testCanGetInstanceOfContainer()
	{
		$container = new Container();

		$this->assertInstanceOf('DependencyInjection\\Container', $container);
	}

	public function testCanResolveBaseClass()
	{
		$container = new Container();

		$base = $container->make(Base::class);

		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Base', $base);
	}

	public function testCanResolveInheritedClass()
	{
		$container = new Container();

		$foo = $container->make(Foo::class);

		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Foo', $foo);
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Base', $foo->getBase());
	}

	public function testCanBindBaseClass()
	{
		$container = new Container();

		$container->bind(Base::class, function($container) {
			return $container->make(Base::class);
		});

		$this->assertTrue($container->isAbstractExists(Base::class));

		$base = $container->make(Base::class);

		$this->assertFalse($container->isAbstractExists(Base::class));
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Base', $base);
	}

	public function testCanBindInheritedClass()
	{
		$container = new Container();

		$container->bind(Foo::class, function($container) {
			return $container->make(Base::class);
		});

		$this->assertTrue($container->isAbstractExists(Foo::class));

		$foo = $container->make(Foo::class);

		$this->assertFalse($container->isAbstractExists(Foo::class));
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Foo', $foo);
	}

	public function testCanDoConditionalBindingOnBaseClass()
	{
		$container = new Container();

		$container->bindIf(Base::class, function($container) {
			return $container->make(Base::class);
		});

		$this->assertTrue($container->isAbstractExists(Base::class));

		$base = $container->make(Base::class);

		$this->assertFalse($container->isAbstractExists(Base::class));
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Base', $base);
	}

	public function testCanDoConditionalBindingOnInheritedClass()
	{
		$container = new Container();

		$container->bindIf(Foo::class, function($container) {
			return $container->make(Base::class);
		});

		$this->assertTrue($container->isAbstractExists(Foo::class));

		$foo = $container->make(Foo::class);

		$this->assertFalse($container->isAbstractExists(Foo::class));
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Foo', $foo);
	}

	public function testCanBindingConcreteClosureToAbstract()
	{
		$container = new Container();

		$container->bind(BaseInterface::class, function($container) {
			return new Base();
		});

		$this->assertTrue($container->isAbstractExists(BaseInterface::class));

		$foo = $container->make(FooWithInterface::class);

		$this->assertFalse($container->isAbstractExists(BaseInterface::class));
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\FooWithInterface', $foo);
	}

	public function testCanBindingConcreteToAbstract()
	{
		$container = new Container();

		$container->bind(BaseInterface::class, Base::class);

		$this->assertTrue($container->isAbstractExists(BaseInterface::class));

		$foo = $container->make(FooWithInterface::class);

		$this->assertFalse($container->isAbstractExists(BaseInterface::class));
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\FooWithInterface', $foo);
	}

	public function testCanBindingConcreteClosureToAbstractCircularly()
	{
		$container = new Container();

		$container->bind(BaseInterface::class, function($container) {
			return $container->make(Base::class);
		});

		$this->assertTrue($container->isAbstractExists(BaseInterface::class));

		$bar = $container->make(Bar::class);

		$this->assertFalse($container->isAbstractExists(BaseInterface::class));
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Bar', $bar);
	}

	public function testCanBindingConcreteToAbstractCircularly()
	{
		$container = new Container();

		$container->bind(BaseInterface::class, Base::class);

		$this->assertTrue($container->isAbstractExists(BaseInterface::class));

		$bar = $container->make(Bar::class);

		$this->assertFalse($container->isAbstractExists(BaseInterface::class));
		$this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Bar', $bar);
	}
}