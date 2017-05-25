<?php

namespace Experiments\DependencyInjection\Tests;

use Experiments\DependencyInjection\InvokerAdapter;
use Experiments\DependencyInjection\FunctionInvoker;
use Experiments\DependencyInjection\Container;

class FunctionInvokerTest extends \PHPUnit_Framework_TestCase
{
	public function testCanInvokeFunction()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$container->bind(InvokerAdapter::class, function($container) {
			return $container->make(FunctionInvoker::class, array('file_get_contents'));
		});

		$invoker = $container->make(InvokerAdapter::class);

		$this->assertInstanceOf(InvokerAdapter::class, $invoker);

		echo sprintf("%s", $invoker->invoke('/etc/passwd'));
	}
}