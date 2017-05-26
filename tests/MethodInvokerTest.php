<?php

namespace Experiments\DependencyInjection\Tests;

use Experiments\DependencyInjection\Container;
use Experiments\DependencyInjection\InvokerAdapter;
use Experiments\DependencyInjection\MethodInvoker;
use Experiments\DependencyInjection\Fixtures\Dosen;
use Experiments\DependencyInjection\Fixtures\Base;

class MethodInvokerTest extends \PHPUnit_Framework_TestCase
{
	public function testCanInvokeMethodWithDirectlyResolveClassDependency()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$dosen = $container->make(Dosen::class, Base::class);

		$this->assertInstanceOf(Dosen::class, $dosen);

		$container->bind(InvokerAdapter::class, function($container) use ($dosen) {
			return $container->make(MethodInvoker::class, $dosen, 'setFirstName');
		});

		$invoker = $container->make(InvokerAdapter::class);

		$this->assertInstanceOf(InvokerAdapter::class, $invoker);
		
		$invoker->invoke('Paulus');
	}
}