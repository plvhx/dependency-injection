<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace Experiments\DependencyInjection\Tests;

use Experiments\DependencyInjection\Container;
use Experiments\DependencyInjection\Fixtures\Base;
use Experiments\DependencyInjection\Fixtures\Dosen;
use Experiments\DependencyInjection\Fixtures\Mahasiswa;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
	public function testIfCanGetContainerInstance()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);
	}

	public function testIfCanAutoResolvingDependency()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$dosen = $container->make(Dosen::class);

		$this->assertInstanceOf(Dosen::class, $dosen);

		$dosen->setFirstName('a');
		$dosen->setMiddleName('b');
		$dosen->setLastName('c');

		echo sprintf("%s" . PHP_EOL, $dosen);
	}

	public function testIfCanManuallyResolvingDependency()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$dosen = $container->make(Dosen::class, array(Base::class));

		$this->assertInstanceOf(Dosen::class, $dosen);

		$dosen->setFirstName('a');
		$dosen->setMiddleName('b');
		$dosen->setLastName('c');

		echo sprintf("%s" . PHP_EOL, $dosen);
	}

	public function testIfCanBindWithClosure()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$container->bind(Dosen::class, function($container) {
			return $container->make(Base::class);
		});

		$dosen = $container->make(Dosen::class);

		$this->assertInstanceOf(Dosen::class, $dosen);

		$dosen->setFirstName('Paulus');
		$dosen->setMiddleName('Gandung');
		$dosen->setLastName('Prakosa');

		echo sprintf("%s" . PHP_EOL, $dosen);		
	}

	public function testIfCanBindWithoutClosure()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$container->bind(Dosen::class, Base::class);

		$dosen = $container->make(Dosen::class);

		$this->assertInstanceOf(Dosen::class, $dosen);

		$dosen->setFirstName('Paulus');
		$dosen->setMiddleName('Gandung');
		$dosen->setLastName('Prakosa');

		echo sprintf("%s" . PHP_EOL, $dosen);		
	}

	public function testIfInstanceIsInvokable()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$container->bind(Dosen::class, function($container) {
			return $container->make(Base::class);
		});

		$container->callInstance(Dosen::class, array('Yohanes Sunu Jatmika'));
	}
}