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
			return $container->make(FunctionInvoker::class, 'printf');
		});

		$invoker = $container->make(InvokerAdapter::class);

		$this->assertInstanceOf(InvokerAdapter::class, $invoker);

		$invoker->invoke('My name: %s' . PHP_EOL, 'Paulus Gandung Prakosa');
	}

	public function testCanInvokeFunctionWithArrayOfArguments()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$container->bind(InvokerAdapter::class, function($container) {
			return $container->make(FunctionInvoker::class, 'printf');
		});

		$invoker = $container->make(InvokerAdapter::class);

		$this->assertInstanceOf(InvokerAdapter::class, $invoker);

		$invoker->invokeArgs(array("My name: %s" . PHP_EOL, "Paulus Gandung Prakosa"));
	}

	public function testCanInvokeClosure()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$container->bind(InvokerAdapter::class, function($container) {
			return $container->make(FunctionInvoker::class, function($msg) {
				printf("%s" . PHP_EOL, $msg);
			});
		});

		$invoker = $container->make(InvokerAdapter::class);

		$this->assertInstanceOf(InvokerAdapter::class, $invoker);

		$invoker->invoke("Taufik Hidayat");
	}

	public function testCanInvokeClosureWithArrayOfArguments()
	{
		$container = new Container();

		$this->assertInstanceOf(Container::class, $container);

		$container->bind(InvokerAdapter::class, function($container) {
			return $container->make(FunctionInvoker::class, function($msg) {
				printf("%s" . PHP_EOL, $msg);
			});
		});

		$invoker = $container->make(InvokerAdapter::class);

		$this->assertInstanceOf(InvokerAdapter::class, $invoker);

		$invoker->invokeArgs(array("Taufik Hidayat"));
	}
}