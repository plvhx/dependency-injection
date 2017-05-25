<?php

namespace Experiments\DependencyInjection;

class MethodInvoker implements InvokerInterface
{
	/**
	 * @var string|object
	 */
	private $object;

	/**
	 * @var string
	 */
	private $method;

	public function __construct($object, $method)
	{
		if (!is_object($object) || !is_string($object)) {
			throw Internal\Exception\ReflectionExceptionFactory::invalidArgument(
				sprintf("Parameter 1 of %s must be either object or existing class name.",
					__METHOD__)
			);
		}

		if (!is_string($method)) {
			throw Internal\Exception\ReflectionExceptionFactory::invalidArgument(
				sprintf("Parameter 2 of %s must be a string.", __METHOD__)
			);
		}

		$this->object = (is_string($this->object) && class_exists($this->object)
			? Internal\ReflectionClassFactory::create($this->object)->newInstance()
			: $this->object);

		if (!Internal\ReflectionObjectFactory::create($this->object)->hasMethod($this->method)) {
			throw Internal\Exception\ReflectionExceptionFactory::runtime(
				sprintf("Class %s doesn't have a method named %s",
					Internal\ReflectionObjectFactory::create($this->object)->getName(),
					$this->method)
			);
		}

		$this->object = $object;
		$this->method = $method;
	}

	public function invoke()
	{
		$methodReflector = Internal\ReflectionObjectFactory::create($this->object)
			->getMethod($this->method);

		return call_user_func_array(
			[$methodReflector->getMethod($this->method), 'invoke']
			[$this->object, func_get_args()]);
	}

	public function invokeArgs($args = [])
	{
		return Internal\ReflectionObjectFactory::create($this->object)
			->getMethod($this->method)
			->invokeArgs($this->object, $args);
	}
}