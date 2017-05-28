<?php

namespace DependencyInjection\Internal;

class ReflectionMethodFactory
{
    /**
     * @var \ReflectionMethod
     */
    private $reflectionMethod;

    public function __construct($class, $name)
    {
        if (!is_string($class) || !is_object($class)) {
            throw Exception\ReflectionExceptionFactory::invalidArgument(
                sprintf("Parameter 1 of %s must be either class name or object.", __METHOD__)
            );
        }

        $this->reflectionMethod = new \ReflectionMethod($class, $name);

        if (!($this->reflectionMethod instanceof \ReflectionMethod)) {
            throw Exception\ReflectionExceptionFactory::reflectionInternal(
                "Unable to get an instance of \\ReflectionMethod."
            );
        }
    }

    public static function create($class, $name)
    {
        return new static($class, $name);
    }

    public static function export($class, $name, $return = false)
    {
        return \ReflectionMethod::export($class, $name, $return);
    }

    public function getClosure($object)
    {
        $closure = $this->reflectionMethod->getClosure($object);

        return ($closure instanceof \Closure ? $closure : null);
    }

    public function getDeclaringClass()
    {
        $declaringClass = $this->reflectionMethod->getDeclaringClass();

        return ($declaringClass instanceof \ReflectionClass ? $declaringClass : null);
    }

    public function getModifiers()
    {
        return $this->reflectionMethod->getModifiers();
    }

    public function getPrototype()
    {
        $prototype = $this->reflectionMethod->getPrototype();

        return ($prototype instanceof \ReflectionMethod ? $prototype : null);
    }

    public function invoke($object)
    {
        return call_user_func_array([$this->reflectionMethod, 'invoke'], array_slice(func_get_args(), 1));
    }

    public function invokeArgs($object, $args = [])
    {
        if (!is_array($args)) {
            throw Exception\ReflectionExceptionFactory::invalidArgument(
                sprintf("Parameter 1 of %s must be an array.", __METHOD__)
            );
        }

        return $this->reflectionMethod->invokeArgs($object, $args);
    }

    public function isAbstract()
    {
        return $this->reflectionMethod->isAbstract();
    }

    public function isConstructor()
    {
        return $this->reflectionMethod->isConstructor();
    }

    public function isDestructor()
    {
        return $this->reflectionMethod->isDestructor();
    }

    public function isFinal()
    {
        return $this->reflectionMethod->isFinal();
    }

    public function isPrivate()
    {
        return $this->reflectionMethod->isPrivate();
    }

    public function isProtected()
    {
        return $this->reflectionMethod->isProtected();
    }

    public function isPublic()
    {
        return $this->reflectionMethod->isPublic();
    }

    public function isStatic()
    {
        return $this->reflectionMethod->isStatic();
    }

    public function setAccessible($accessible)
    {
        if (!is_bool($accessible)) {
            throw Exception\ReflectionExceptionFactory::invalidArgument(
                sprintf("Parameter 1 of %s must be a boolean.", __METHOD__)
            );
        }

        $this->reflectionMethod->setAccessible($accessible);
    }

    public function __toString()
    {
        return (string)$this->reflectionMethod;
    }

    public function getReflectionMethod()
    {
        return $this->reflectionMethod;
    }
}
