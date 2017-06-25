<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace DependencyInjection;

use Psr\Container\ContainerInterface;
use DependencyInjection\Exception\ContainerException;
use DependencyInjection\Exception\NotFoundException;

class Container implements \ArrayAccess, ContainerInterface
{
    /**
     * @var array
     */
    private $bindings = [];

    /**
     * @var array
     */
    private $resolved = [];

    /**
     * @var array
     */
    private $aliases = [];

    /**
     * Resolving all dependencies in the supplied class or object instance constructor.
     *
     * @param string $instance The class name.
     * @param array $parameters List of needed class dependency.
     * @return object
     */
    public function make($instance, $parameters = [])
    {
        return $this->resolve($instance, is_array($parameters) ? $parameters
            : array_slice(func_get_args(), 1));
    }

    /**
     * Register a service alias.
     *
     * @param string $alias The alias name.
     * @param string $abstract The class name.
     */
    public function register($alias, $abstract)
    {
        if (!is_string($alias) || !is_string($abstract)) {
            throw new \InvalidArgumentException(
                sprintf("Parameter 1 and 2 of %s must be a string.", __METHOD__)
            );
        }

        if (!isset($this->aliases[$alias])) {
            $this->aliases[$alias] = $this->make($abstract);
        }

        return $this;
    }

    /**
     * Determine if registered alias were exists.
     *
     * @param string $alias The alias name.
     */
    public function isAliasExists($alias)
    {
        return isset($this->aliases[$alias]);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->isAliasExists($id)) {
            throw new NotFoundException(
                sprintf("Identifier %s was not found in our service container stack.", $id)
            );
        }

        return $this->aliases[$id];
    }
    
    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return $this->isAliasExists($id);
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
        $this->bind($offset, $value instanceof \Closure ? $value : $this->turnIntoResolvableClosure($offset, $value));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->bindings[$offset], $this->resolved[$offset]);
    }

    /**
     * Determine if defined abstract class name were in resolved concrete stack and it was a
     * singleton.
     *
     * @param string $abstract The resolved abstract class name.
     */
    public function hasResolvedSingleton($abstract)
    {
        $flag = $this->getResolvedConcreteFlag($abstract);

        return in_array('singleton', $flag, true);
    }

    /**
     * Get singleton resolved concrete from defined abstract class name.
     *
     * @param string $abstract The resolved abstract class name.
     */
    public function getResolvedSingleton($abstract)
    {
        return ($this->hasResolvedSingleton($abstract)
            ? $this->resolved[$abstract]['concrete']
            : null);
    }

    /**
     * Determine if defined abstract class name were in resolved concrete stack.
     *
     * @param string $abstract The resolved abstract class name.
     */
    public function hasResolvedConcrete($abstract)
    {
        return isset($this->resolved[$abstract]);
    }

    /**
     * Get flag of resolved concrete behavior on abstract class name.
     *
     * @param string $abstract The resolved abstract class name.
     */
    public function getResolvedConcreteFlag($abstract)
    {
        if (!$this->hasResolvedConcrete($abstract)) {
            throw Internal\Exception\ReflectionExceptionFactory::invalidArgument(
                sprintf(
                    "Parameter 1 of %s must be an abstract class name which exists in resolved concrete stack.",
                     __METHOD__
                )
            );
        }

        return explode('|', $this->resolved[$abstract]['flag']);
    }

    /**
     * Get resolved concrete from defined abstract class name.
     *
     * @param string $abstract The resolved abstract class name.
     */
    public function getResolvedConcrete($abstract)
    {
        return ($this->hasResolvedConcrete($abstract)
            ? $this->resolved[$abstract]['concrete']
            : null);
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
        return array_key_exists($abstract, $this->bindings);
    }

    /**
     * Determine if unresolved abstract is an interface.
     *
     * @param string $abstract The unresolved abstract name.
     */
    public function isInterface($abstract)
    {
        $reflector = Internal\ReflectionClassFactory::create($abstract);

        return $reflector->isInterface();
    }

    /**
     * Get concrete list of dependencies based on supplied class name.
     *
     * @param string $abstract The unresolved class name.
     * @return array
     */
    public function getAbstractDependencies($abstract)
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
        // If the current abstract is an interface,
        // just return the concrete implementation to the callee.
        if ($this->isInterface($instance)) {
            return $this->getConcreteFromInterface($instance);
        }

        // If the current abstract type being managed as a singleton,
        // just return it to the caller instead of reinstantiating it.
        try {
            return $this->getResolvedSingleton($instance);
        } catch (\Exception $e) {
        }
        
        $concrete = $this->getConcrete($instance);

        if (!is_null($concrete)) {
            $object = $this->build($instance,
                $concrete instanceof \Closure ? $concrete($this) : $concrete);

            if ($this->isShared($instance)) {
                $this->markAsResolved($instance, $object, 'singleton');
            } else {
                $this->markAsResolved($instance, $object);
            }
        } else {
            $object = $this->build($instance, $parameters);
        }

        return $object;
    }

    protected function build($instance, $parameters = [])
    {
        $parameters = (is_null($parameters)
            ? []
            : (is_array($parameters)
                ? $parameters
                : array_slice(func_get_args(), 1)));

        var_dump($parameters);

        $reflector = Internal\ReflectionClassFactory::create($instance);

        if (!$this->hasConstructor($reflector)) {
            return $this->resolveInstanceWithoutConstructor($reflector);
        }

        if (is_array($parameters) && empty(sizeof($parameters))) {
            $constructorParams = $this->getMethodParameters($reflector, '__construct');

            if (!is_null($constructorParams)) {
                $params = $this->resolveMethodParameters($constructorParams);
            }
        } elseif (is_array($parameters) && !empty(sizeof($parameters))) {
            $params = $this->resolveMethodParameters($parameters);
        }

        return $reflector->newInstanceArgs(!empty($parameters) ? $parameters : $params);
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
                $class = $value->getClass();

                if ($class instanceof \ReflectionClass) {
                    if ($class->isInterface()) {
                        $params[$key] = $this->getConcreteFromInterface($class->getName());
                    } else {
                        $params[$key] = $this->circularDependencyResolver($class->getName());
                    }
                } else {
                    $params[$key] = ($value->isDefaultValueAvailable()
                        ? $value->getDefaultValue() : null);
                }
            } else {
                if (is_string($value) && class_exists($value)) {
                    $params[$key] = $this->circularDependencyResolver($value);
                } elseif ($value instanceof \Closure) {
                    $params[$key] = $value($this);
                } else {
                    $params[$key] = $value;
                }
            }
        }

        return $params;
    }

    /**
     * Recursively resolving class dependency.
     *
     * @param string $class The valid class name.
     * @return object
     */
    protected function circularDependencyResolver($class)
    {
        if (!is_string($class) && !class_exists($class)) {
            throw Internal\Exception\ReflectionExceptionFactory::invalidArgument(
                sprintf("Parameter 1 of %s must be a string of valid class name.", __METHOD__)
            );
        }

        $reflector = Internal\ReflectionClassFactory::create($class);

        if (!$this->hasConstructor($reflector)) {
            return $this->resolveInstanceWithoutConstructor($reflector);
        } else {
            $param = $this->getMethodParameters($reflector, '__construct');

            if (empty($param)) {
                return $reflector->newInstance();
            } else {
                foreach ($param as $key => $value) {
                    $class = $value->getClass();

                    if ($class instanceof \ReflectionClass) {
                        if ($class->isInterface()) {
                            $param[$key] = $this->getConcreteFromInterface($class->getName());
                        } else {
                            $param[$key] = $this->circularDependencyResolver($class->getName());
                        }
                    }
                }

                return $reflector->newInstanceArgs($param);
            }
        }
    }

    /**
     * Get concrete implementation from given abstract.
     *
     * @param string $abstract
     * @return \Closure|string|null
     */
    public function getConcrete($abstract)
    {
        return (isset($this->bindings[$abstract])
            ? $this->bindings[$abstract]['concrete']
            : null);
    }

    /**
     * Get concrete implementation from abstract.
     *
     * @param string $interface The interface name.
     * @return object
     */
    protected function getConcreteFromInterface($interface)
    {
        if (!$this->isAbstractExists($interface)) {
            throw Internal\Exception\ReflectionExceptionFactory::runtime(
                sprintf("%s has no concrete implementation in the class binding stack.", $interface)
            );
        }

        try {
            return $this->getResolvedSingleton($interface);
        } catch (\Exception $e) {
        }

        $concrete = $this->bindings[$interface]['concrete'];

        $object = $concrete instanceof \Closure ? $concrete($this) : $this->build($concrete);

        if ($this->isShared($interface)) {
            $this->markAsResolved($interface, $object, 'singleton');
        } else {
            $this->markAsResolved($interface, $object);
        }

        return $object;
    }

    /**
     * Determine if current reflection object has constructor.
     *
     * @param \ReflectionClass $refl The current reflection class object.
     * @return boolean
     */
    protected function hasConstructor(Internal\ReflectionClassFactory $refl)
    {
        return $refl->hasMethod('__construct');
    }

    /**
     * Determine if unresolvable class name has cloneable.
     *
     * @param \ReflectionClass $refl The current reflection class object.
     * @return boolean
     */
    protected function isCloneable(Internal\ReflectionClassFactory $refl)
    {
        return $refl->hasMethod('__clone');
    }

    /**
     * Determine if unresolvable class name has serializable.
     *
     * @param \ReflectionClass $refl The current reflection class object.
     * @return boolean
     */
    protected function isSerializable(Internal\ReflectionClassFactory $refl)
    {
        return $refl->hasMethod('__sleep');
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
     * @param object $resolvedInstance The object instance of resolved abstract.
     * @param mixed $flag The concrete-resolving behavior.
     * @return void
     */
    protected function markAsResolved($abstract, $resolvedInstance, $flag = [])
    {
        if (!is_array($flag)) {
            $flag = array_slice(func_get_args(), 2);
        }

        if ($this->isAbstractExists($abstract)) {
            $this->resolved[$abstract] = [
                'concrete' => $resolvedInstance,
                'resolved' => true,
                'flag' => join('|', $flag)
            ];
        }
    }

    /**
     * Register binding into container stack.
     *
     * @param string $abstract The unresolvable class name.
     * @param \Closure|string $concrete Closure or class name being bound to the class name.
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (!($concrete instanceof \Closure)) {
            $concrete = $this->turnIntoResolvableClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * Register shared binding into container stack.
     *
     * @param string $abstract The unresolvable abstract
     * @param \Closure|string|null The concrete form of supplied abstract.
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
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
        $args = (is_array($args) ? $args : array_slice(func_get_args(), 1));
        
        $current = $this->make($instance);

        var_dump($current);

        return call_user_func_array($current, $args);
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
     * Determine if a given type is shared.
     *
     * @param string $abstract
     * @return bool
     */
    public function isShared($abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            throw Internal\Exception\ReflectionExceptionFactory::invalidArgument(
                sprintf("Parameter 1 of %s must be valid keys in binding container stack.", __METHOD__)
            );
        }

        return ($this->bindings[$abstract]['shared'] ? true : false);
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
        return function (Container $container, $parameters = []) use ($abstract, $concrete) {
            return ($abstract == $concrete ? $container->resolve($abstract)
                : $container->resolve($concrete, $parameters));
        };
    }
}
