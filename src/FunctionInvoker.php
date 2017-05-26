<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace Experiments\DependencyInjection;

class FunctionInvoker implements InvokerInterface
{
	/**
	 * @var \Closure|function
	 */
	private $function;

	public function __construct($function)
	{
		if (!is_callable($function) && !($function instanceof \Closure)) {
			throw Internal\Exception\ReflectionExceptionFactory::invalidArgument(
				sprintf("Parameter 1 of %s must be a function or callback.", __METHOD__)
			);
		}

		$this->function = $function;
	}

	/**
	 * {@inheritdoc}
	 */
	public function invoke()
	{
		return call_user_func_array(
			[Internal\ReflectionFunctionFactory::create($this->function), 'invoke'], func_get_args()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function invokeArgs($args = [])
	{
		if (!is_array($args)) {
			throw Internal\Exception\ReflectionExceptionFactory::invalidArgument(
				sprintf("Parameter 1 of %s must be an array.", __METHOD__)
			);
		}

		return Internal\ReflectionFunctionFactory::create($this->function)->invokeArgs($args);
	}
}