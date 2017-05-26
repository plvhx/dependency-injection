<?php

namespace Experiments\DependencyInjection\Internal;

class ReflectionFunctionFactory
{
	/**
	 * @var \ReflectionFunction
	 */
	private $reflectionFunction;

	public function __construct($name)
	{
		if (!is_string($name) && !is_a($name, 'Closure', true)) {
			throw Exception\ReflectionExceptionFactory::invalidArgument(
				sprintf("Parameter 1 of %s must be a string or instance of \\Closure", __METHOD__)
			);
		}

		$this->reflectionFunction = new \ReflectionFunction($name);

		if (!is_a($this->reflectionFunction, 'ReflectionFunction')) {
			throw Exception\ReflectionExceptionFactory::reflectionInternal(
				"Unable to get an instance of \\ReflectionFunction."
			);
		}
	}

	public static function create($name)
	{
		return new static($name);
	}

	public static function export($name, $return = false)
	{
		return \ReflectionFunction::export($name, $return);
	}

	public function getClosure()
	{
		$closure = $this->reflectionFunction->getClosure();

		return ($closure instanceof \Closure ? $closure : null);
	}

	public function invoke()
	{
		return call_user_func_array([$this->reflectionFunction, 'invoke'], func_get_args());
	}

	public function invokeArgs($args = [])
	{
		if (!is_array($args)) {
			throw Exception\ReflectionExceptionFactory::invalidArgument(
				sprintf("Parameter 1 of %s must be an array.", __METHOD__)
			);
		}

		return $this->reflectionFunction->invokeArgs($args);
	}

	public function isDisabled()
	{
		return $this->reflectionFunction->isDisabled();
	}

	public function __toString()
	{
		return (string)$this->reflectionFunction;
	}

	public function getReflectionFunction()
	{
		return $this->reflectionFunction;
	}
}