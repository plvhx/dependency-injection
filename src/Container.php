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
	private $bindings = array();

	/**
	 * @var array
	 */
	private $resolved = array();

	/**
	 * @var object
	 */
	private $activeInstance;

	/**
	 * Resolving all dependencies in the supplied class or object instance constructor.
	 *
	 * @param $instance The class name.
	 * @param $parameters List of needed class dependency.
	 * @return object
	 */
	public function make($instance, $parameters = [])
	{
		if ($this->isAbstractExists($instance)) {
			return $this->resolve($instance, $this->getConcrete($instance));
		}

		return $this->resolve($instance, $parameters);
	}

	/**
	 * Get list of unresolved class name from class binding stack.
	 *
	 * @return string
	 */
	protected function getAbstracts()
	{
		return array_keys($this->bindings);
	}

	/**
	 * Determine if unresolved class name is exists.
	 *
	 * @param $abstract The unresolved class name.
	 * @return bool
	 */
	public function isAbstractExists($abstract)
	{
		return isset($this->bindings[$abstract]);
	}

	/**
	 * Get concrete list of dependencies based on supplied class name.
	 *
	 * @param $abstract The unresolved class name.
	 * @return array
	 */
	public function getConcrete($abstract)
	{
		return ($this->isAbstractExists($abstract) ? $this->bindings[$abstract] : null);
	}

	/**
	 * Resolve class dependencies in the supplied class name.
	 *
	 * @param $instance The class name.
	 * @param $parameters The needed class dependency.
	 * @return object
	 */
	protected function resolve($instance, $parameters = [])
	{
		$reflector = ReflectionClassFactory::create($instance)->getReflection();

		if (!$this->hasConstructor($reflector)) {
			return $this->resolveInstanceWithoutConstructor($reflector);
		}

		if (is_array($parameters) && empty(sizeof($parameters))) {
			$constructorParams = $this->getMethodParameters($reflector, '__construct');

			if (!is_null($constructorParams)) {
				foreach ($constructorParams as $key => $value) {
					$className = $value->getClass();

					if ($className instanceof \ReflectionClass) {
						$constructorParams[$key] = ReflectionClassFactory::create($className->getName())
							->newInstance();
					}
				}

				$resolved = $reflector->newInstanceArgs($constructorParams);
			}
		}
		else if (is_array($parameters) && !empty(sizeof($parameters))) {
			foreach ($parameters as $key => $value) {
				if (is_string($value) && class_exists($value)) {
					$parameters[$key] = ReflectionClassFactory::create($value)
						->newInstance();
				}
				else {
					$parameters[$key] = $value($this);
				}
			}

			$resolved = $reflector->newInstanceArgs($parameters);
		}

		$this->markAsResolved($instance);

		return $resolved;
	}

	/**
	 * Determine if current reflection object has constructor.
	 *
	 * @param \ReflectionClass The current reflection class object.
	 */
	protected function hasConstructor(\ReflectionClass $refl)
	{
		return $refl->hasMethod('__construct'); 
	}

	/**
	 * Resolving class name without constructor.
	 *
	 * @param \ReflectionClass An instance of \ReflectionClass
	 */
	protected function resolveInstanceWithoutConstructor(\ReflectionClass $refl)
	{
		return $refl->newInstanceWithoutConstructor();
	}

	/**
	 * Get method parameters.
	 *
	 * @param \ReflectionClass $refl An reflection class instance.
	 * @param $method The method name.
	 * @return array
	 */
	protected function getMethodParameters(\ReflectionClass $refl, $method)
	{
		return ($refl->hasMethod($method) ? $refl->getMethod($method)->getParameters() : null);
	}

	/**
	 * Mark resolved class name to true.
	 *
	 * @param $abstract The resolved class name.
	 * @return void
	 */
	protected function markAsResolved($abstract)
	{
		if ($this->isAbstractExists($abstract)) {
			$this->resolved[$abstract] = true;

			unset($this->bindings[$abstract]);
		}
	}

	/**
	 * Bind service into binding container stack.
	 *
	 * @param string $abstract The unresolvable class name.
	 * @param \Closure|string $concrete Closure or class name being bound to the class name.
	 */
	public function bind($abstract, $concrete)
	{
		if (!($concrete instanceof \Closure)) {
			$concrete = $this->turnIntoResolvableClosure($abstract, $concrete);
		}

		$this->bindings[$abstract] = (isset($this->bindings[$abstract])
			? array_push($this->bindings[$abstract], $concrete)
			: array($concrete));
	}

	/**
	 * Bind service into binding container stack if supplied class name
	 * not being bound.
	 *
	 * @param string $abstract The unresolvable class name.
	 * @param \Closure|string $concrete Closure or class name begin bound to the class name.
	 */
	public function bindIf($abstract, $concrete)
	{
		if (!$this->isBound($abstract)) {
			$this->bind($abstract, $concrete);
		}
	}

	/**
	 * Determine if class name has been bound or not.
	 *
	 * @param string $abstract The unresolvable class name.
	 * @return bool
	 */
	protected function isBound($abstract)
	{
		return $this->isAbstractExists($abstract);
	}

	/**
	 * Turn class name into resolvable closure.
	 *
	 * @param $abstract The class name
	 * @param $concrete Can be instance of \Closure or class name.
	 * @return \Closure
	 */
	protected function turnIntoResolvableClosure($abstract, $concrete)
	{
		return function($container, $parameters = []) use ($abstract, $concrete) {
			return ($abstract == $concrete ? $container->resolve($abstract)
				: $container->resolve($concrete, $parameters));
		};
	}
}