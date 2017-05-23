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
		$this->container->make(Mahasiswa::class, 'mahasiswa.service');
		$this->container->make(Dosen::class, 'dosen.service');
	}

	public function testInstanceOfContainer()
	{
		$this->assertInstanceOf(Container::class, $this->container);
	}

	public function testCanResolveMahasiswaDependency()
	{
		$mahasiswa = $this->container->get('mahasiswa.service');

		$mahasiswa->setFirstName('Paulus');
		$mahasiswa->setMiddleName('Gandung');
		$mahasiswa->setLastName('Prakosa');

		echo sprintf("%s" . PHP_EOL, (string)$mahasiswa);

		$this->assertInstanceOf(Mahasiswa::class, $mahasiswa);
	}

	public function testCanResolveDosenDependency()
	{
		$dosen = $this->container->get('dosen.service');

		$dosen->setFirstName('Yohanes');
		$dosen->setMiddleName('Sunu');
		$dosen->setLastName('Jatmika');

		echo sprintf("%s" . PHP_EOL, (string)$dosen);

		$this->assertInstanceOf(Dosen::class, $dosen);
	}

	public function testCanManuallyResolveMahasiswaDependency()
	{
		$mahasiswa = $this->container
			->addArgument(new Base())
			->register(Mahasiswa::class, 'mahasiswa.manual.service')
			->get('mahasiswa.manual.service');

		$mahasiswa->setFirstName('Achmad');
		$mahasiswa->setMiddleName('Muchlis');
		$mahasiswa->setLastName('Fanani');

		echo sprintf("%s" . PHP_EOL, (string)$mahasiswa);

		$this->assertInstanceOf(Mahasiswa::class, $mahasiswa);
	}

	public function testCanManuallyResolveDosenDependency()
	{
		$dosen = $this->container
			->addArgument(new Base())
			->register(Dosen::class, 'dosen.manual.service')
			->get('dosen.manual.service');

		$dosen->setFirstName('Yohanes');
		$dosen->setMiddleName('Sunu');
		$dosen->setLastName('Jatmika');

		echo sprintf("%s" . PHP_EOL, (string)$dosen);

		$this->assertInstanceOf(Dosen::class, $dosen);
	}
}