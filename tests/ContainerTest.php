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
	/**
	 * @var Container
	 */
	private $container;

	public function __construct()
	{
		$this->container = new Container();

		$this->registerRequiredDeps();
	}

	private function registerRequiredDeps()
	{
		$this->container->register(Mahasiswa::class, 'mahasiswa.service');
		$this->container->register(Dosen::class, 'dosen.service');
	}

	public function testInstanceOfContainer()
	{
		$this->assertInstanceOf(Container::class, $this->container);
	}

	public function testCanResolveMahasiswaDependency()
	{
		$mahasiswa = $this->container->get('mahasiswa.service');

		$this->assertInstanceOf(Mahasiswa::class, $mahasiswa);
	}

	public function testCanResolveDosenDependency()
	{
		$dosen = $this->container->get('dosen.service');

		$this->assertInstanceOf(Dosen::class, $dosen);
	}
}