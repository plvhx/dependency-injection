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
	 * @var array
	 */
	private $parameter = array();

	/**
	 * @var object
	 */
	private $activeInstance;

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

		if (in_array($alias, array_keys($this->containerStack), true)) {
			throw new \RuntimeException(
				sprintf("Cannot redeclare service %s. Try another alias.", $alias)
			);
		}

		$this->containerStack[$alias] = $this->resolve($instance, $alias)->get($alias);

		$this->parameter = [];
		
		return $this;
	}

	public function addArgument($args)
	{
		if (!isset($args)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be exist.", __METHOD__)
			);
		}

		$this->parameter[] = $args;

		return $this;
	}


	/**
	 * Resolving all dependencies in the supplied class or object instance constructor.
	 *
	 * @return Container
	 */
	public function make($instance, $alias)
	{
		$reflection = new \ReflectionClass($instance);

		if (!($reflection instanceof \ReflectionClass)) {
			throw new \RuntimeException(
				"Unable to get an instance of ReflectionClass."
			);
		}

		$constructor = $reflection->getConstructor();

		$this->parameter = (empty($constructor->getParameters())
			? array()
			: $constructor->getParameters());

		foreach ($this->parameter as $key => $val) {
			$class = $val->getClass();

			if ($class !== null) {
				$class = $class->getName();
				$this->parameter[$key] = new $class;
			}
		}

		$this->containerStack[$alias] = $reflection->newInstanceArgs($this->parameter);

		return $this;
	}

	/**
	 * Manually resolve class dependency.
	 *
	 * @return Container
	 */
	private function resolve($instance, $alias)
	{
		$reflection = new \ReflectionClass($instance);

		if (!($reflection instanceof \ReflectionClass)) {
			throw new \RuntimeException(
				"Unable to get an instance of ReflectionClass"
			);
		}

		$this->containerStack[$alias] = $reflection->newInstanceArgs($this->parameter);

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