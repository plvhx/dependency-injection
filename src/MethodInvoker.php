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
		if (!is_object($object) && !is_string($object)) {
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

		$this->object = (is_string($object) && class_exists($object)
			? Internal\ReflectionClassFactory::create($object)->newInstance()
			: $object);

		$reflector = Internal\ReflectionObjectFactory::create($this->object)
			->getReflector();

		if (!$reflector->hasMethod($method)) {
			throw Internal\Exception\ReflectionExceptionFactory::runtime(
				sprintf("Class %s doesn't have a method named %s", $reflector->getName(), $method)
			);
		}

		$this->method = $method;
	}

	/**
	 * {@inheritdoc}
	 */
	public function invoke()
	{
		$methodReflector = Internal\ReflectionObjectFactory::create($this->object)
			->getReflector()
			->getMethod($this->method);

		return call_user_func_array([$methodReflector, 'invoke'], [$this->object, func_get_args()]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function invokeArgs($args = [])
	{
		return Internal\ReflectionObjectFactory::create($this->object)
			->getReflector()
			->getMethod($this->method)
			->invokeArgs($this->object, $args);
	}
}