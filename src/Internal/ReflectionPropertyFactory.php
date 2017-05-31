<?php

namespace DependencyInjection\Internal;

class ReflectionPropertyFactory implements ReflectionFactoryInterface
{
    /**
     * @var \ReflectionProperty
     */
    private $reflectionProperty;

    public function __construct($class, $name)
    {
        if (!is_string($class) && !class_exists($class)) {
            throw Exception\ReflectionExceptionFactory::invalidArgument(
                sprintf("Parameter 1 of %s must be either string and valid class name.", __METHOD__)
            );
        }

        $this->reflectionProperty = new \ReflectionProperty($class, $name);
    }

    public static function create($class, $name)
    {
        return new static($class, $name);
    }

    public static function export($class, $name, $return = false)
    {
        return \ReflectionProperty::export($class, $name, $return);
    }

    public function getDeclaringClass()
    {
        $decl = $this->reflectionProperty->getDeclaringClass();

        return ($decl instanceof \ReflectionClass ? $decl : null);
    }

    public function getDocComment()
    {
        return $this->reflectionProperty->getDocComment();
    }

    public function getModifiers()
    {
        return $this->reflectionProperty->getModifiers();
    }

    public function getName()
    {
        return $this->reflectionProperty->getName();
    }

    public function getValue($object = null)
    {
        if (is_null($object) && !$this->isStatic()) {
            throw Exception\ReflectionExceptionFactory::logic(
                sprintf(
                    "Parameter 1 of %s must be a string because the current property being analyze is not static.",
                    __METHOD__
                )
            );
        }

        $object = (is_string($object) && class_exists($object)
            ? ReflectionClassFactory::create($object)->newInstance()
            : (is_object($object)
                ? $object
                : null));

        return ($this->isStatic() ? $this->reflectionProperty->getValue()
            : (is_null($object) ? null : $this->reflectionProperty->getValue($object)));
    }

    public function isDefault()
    {
        return $this->reflectionProperty->isDefault();
    }

    public function isPrivate()
    {
        return $this->reflectionProperty->isPrivate();
    }

    public function isProtected()
    {
        return $this->reflectionProperty->isProtected();
    }

    public function isPublic()
    {
        return $this->reflectionProperty->isPublic();
    }

    public function isStatic()
    {
        return $this->reflectionProperty->isStatic();
    }

    public function setAccessible($accessible = false)
    {
        $this->reflectionProperty->setAccessible($accessible);
    }

    public function setValue($object, $value)
    {
        if (is_null($object) && !$this->isStatic()) {
            throw Exception\ReflectionExceptionFactory::logic(
                sprintf(
                    "Parameter 1 of %s must be a string because the current property being analyze is not static.",
                    __METHOD__
                )
            );
        }

        $object = (is_string($object) && class_exists($object)
            ? ReflectionClassFactory::create($object)->newInstance()
            : (is_object($object)
                ? $object
                : null));

        if ($this->isStatic()) {
            $this->reflectionProperty->setValue($value);
        } else {
            if (!is_null($object)) {
                $this->reflectionProperty->setValue($object, $value);
            }
        }
    }

    public function __toString()
    {
        return (string)$this->reflectionProperty;
    }

    public function getReflector()
    {
        return $this->reflectionProperty;
    }
}
