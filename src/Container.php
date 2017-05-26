<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace Experiments\DependencyInjection;

use \ArrayAccess;

class Container implements \ArrayAccess
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
	 * Resolving all dependencies in the supplied class or object instance constructor.
	 *
	 * @param string $instance The class name.
	 * @param array $parameters List of needed class dependency.
	 * @return object
	 */
	public function make($instance, $parameters = [])
	{
		if ($this->isAbstractExists($instance)) {
			return $this->resolve($instance, $this->getConcrete($instance));
		}

		return $this->resolve($instance, is_array($parameters) ? $parameters
			: array_slice(func_get_args(), 1));
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($offset)
	{
		return $this->isBound($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($offset)
	{
		return $this->make($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($offset, $value)
	{
		$this->bind($offset, $value instanceof \Closure ? $value : function() use ($value) {
			return $value;
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($offset)
	{
		unset($this->bindings[$offset], $this->resolved[$offset]);
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
	 * @param string $abstract The unresolved class name.
	 * @return bool
	 */
	public function isAbstractExists($abstract)
	{
		return isset($this->bindings[$abstract]);
	}

	/**
	 * Determine if concrete dependency is exists.
	 *
	 * @param mixed $concrete The concrete dependency.
	 * @return bool
	 */
	public function isConcreteExists($concrete)
	{
		foreach (array_values($this->bindings) as $value) {
			if (in_array($concrete, $value, true)) {
				$isConcreteExists = true;

				break;
			}
		}

		return (isset($isConcreteExists) ? $isConcreteExists : false);
	}

	/**
	 * Get concrete list of dependencies based on supplied class name.
	 *
	 * @param string $abstract The unresolved class name.
	 * @return array
	 */
	public function getConcrete($abstract)
	{
		return ($this->isAbstractExists($abstract) ? $this->bindings[$abstract] : null);
	}

	/**
	 * Resolve class dependencies in the supplied class name.
	 *
	 * @param string $instance The class name.
	 * @param array $parameters The needed class dependency.
	 * @return object
	 */
	protected function resolve($instance, $parameters = [])
	{
		$reflector = Internal\ReflectionClassFactory::create($instance);

		if (!$this->hasConstructor($reflector)) {
			return $this->resolveInstanceWithoutConstructor($reflector);
		}

		if (is_array($parameters) && empty(sizeof($parameters))) {
			$constructorParams = $this->getMethodParameters($reflector, '__construct');

			if (!is_null($constructorParams)) {
				$params = $this->resolveMethodParameters($constructorParams);
			}
		}
		else if (is_array($parameters) && !empty(sizeof($parameters))) {
			$params = $this->resolveMethodParameters($parameters);
		}

		$this->markAsResolved($instance);

		return $reflector->newInstanceArgs($params);
	}

	/**
	 * Resolve method parameters.
	 *
	 * @param array $params The unresolvable method.
	 * @return array
	 */
	protected function resolveMethodParameters($params = [])
	{
		if (!is_array($params)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be an array.", __METHOD__)
			);
		}

		foreach ($params as $key => $value) {
			if ($value instanceof \ReflectionParameter) {
				$className = $value->getClass();

				if ($className instanceof \ReflectionClass) {
					$params[$key] = Internal\ReflectionClassFactory::create($className->getName())
						->newInstance();
				}
			}
			else {
				if (is_string($value) && class_exists($value)) {
					$params[$key] = Internal\ReflectionClassFactory::create($value)->newInstance();
				}
				else if ($value instanceof \Closure) {
					$params[$key] = ($this->isConcreteExists($value) ? $value($this) : $value);
				}
			}
		}

		return $params;
	}

	/**
	 * Determine if current reflection object has constructor.
	 *
	 * @param \ReflectionClass $refl The current reflection class object.
	 * @return boolean
	 */
	public function hasConstructor(Internal\ReflectionClassFactory $refl)
	{
		return $refl->hasMethod('__construct'); 
	}

	/**
	 * Determine if unresolvable class name has invokable.
	 *
	 * @param \ReflectionClass $refl The current reflection class object.
	 * @return boolean
	 */
	public function isInvokable(Internal\ReflectionClassFactory $refl)
	{
		return $refl->hasMethod('__invoke') || $refl->getMethod('__invoke')->isPublic();
	}

	/**
	 * Determine if unresolvable class name has cloneable.
	 *
	 * @param \ReflectionClass $refl The current reflection class object.
	 * @return boolean
	 */
	public function isCloneable(Internal\ReflectionClassFactory $refl)
	{
		return $refl->hasMethod('__clone') || $refl->getMethod('__clone')->isPublic();
	}

	/**
	 * Determine if unresolvable class name has serializable.
	 *
	 * @param \ReflectionClass $refl The current reflection class object.
	 * @return boolean
	 */
	public function isSerializable(Internal\ReflectionClassFactory $refl)
	{
		return $refl->hasMethod('__sleep') || $refl->getMethod('__sleep')->isPublic();
	}

	/**
	 * Resolving class name without constructor.
	 *
	 * @param \ReflectionClass $refl An instance of \ReflectionClass
	 */
	protected function resolveInstanceWithoutConstructor(Internal\ReflectionClassFactory $refl)
	{
		return $refl->newInstanceWithoutConstructor();
	}

	/**
	 * Get method parameters.
	 *
	 * @param \ReflectionClass $refl An reflection class instance.
	 * @param string $method The method name.
	 * @return array
	 */
	protected function getMethodParameters(Internal\ReflectionClassFactory $refl, $method)
	{
		return ($refl->hasMethod($method) ? $refl->getMethod($method)->getParameters() : null);
	}

	/**
	 * Mark resolved class name to true.
	 *
	 * @param string $abstract The resolved class name.
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
	public function bind($abstract, $concrete = null)
	{
		if (is_null($concrete)) {
			$concrete = $abstract;
		}

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
	 * Call defined instance.
	 *
	 * @param string $instance The class name to invoke/call.
	 * @param array $args The class name __invoke method argument.
	 * @return mixed|void
	 */
	public function callInstance($instance, $args = [])
	{
		if (!$this->isAbstractExists($instance)) {
			$this->bind($instance);
		}

		$current = $this->make($instance);
		$reflector = Internal\ReflectionClassFactory::create($current);

		if ($this->isInvokable($reflector)) {
			$this->markAsResolved($instance);

			return call_user_func_array([$current, '__invoke'], $args);
		}
	}

	/**
	 * Determine if class name has been bound or not.
	 *
	 * @param string $abstract The unresolvable class name.
	 * @return bool
	 */
	public function isBound($abstract)
	{
		return $this->isAbstractExists($abstract);
	}

	/**
	 * Turn class name into resolvable closure.
	 *
	 * @param string $abstract The class name
	 * @param \Closure|string $concrete Can be instance of \Closure or class name.
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