<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace DependencyInjection\Tests;

use DependencyInjection\Container;
use DependencyInjection\Tests\Fixtures\Base;
use DependencyInjection\Tests\Fixtures\Foo;

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
}