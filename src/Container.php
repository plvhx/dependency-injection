<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace Experiments\DependencyInjection;

class Container
{
	/**
	 * @var array
	 */
	private $containerStack = array();

	/**
	 * @var \ReflectionClass
	 */
	private $reflection;

	/**
	 * Registering service into container stack.
	 *
	 * @return Container
	 */
	public function register($instance, $alias)
	{
		if (!is_string($instance) && !is_object($instance)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s:%s must be a string or an object instance.", __CLASS__, __METHOD__)
			);
		}

		return $this->constructorArgumentResolver($instance, $alias);
	}

	/**
	 * Resolving all dependencies in the supplied class or object instance constructor.
	 *
	 * @return Container
	 */
	private function constructorArgumentResolver($instance, $alias)
	{
		$this->reflection = new \ReflectionClass($instance);

		if (!($this->reflection instanceof \ReflectionClass)) {
			throw new \RuntimeException(
				"Unable to get an instance ReflectionClass."
			);
		}

		$constructor = $this->reflection->getConstructor();
		$args = (empty($constructor->getParameters()) ? array() : $constructor->getParameters());

		foreach ($args as $key => $val) {
			$class = $val->getClass();

			if ($class !== null) {
				$class = $class->getName();
				$args[$key] = new $class;
			}
		}

		$this->containerStack[$alias] = $this->reflection->newInstanceArgs($args);

		return $this;
	}

	/**
	 * Get service from service container stack.
	 *
	 * @return object
	 */
	public function get($alias)
	{
		if (!is_string($alias)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be a string.", __METHOD__)
			);
		}

		if (!in_array($alias, array_keys($this->containerStack), true)) {
			throw new \RuntimeException(
				sprintf("Service named %s not found in the service container stack.", $alias)
			);
		}

		return $this->containerStack[$alias];
	}
}