<?php

namespace DependencyInjection\Internal;

class ReflectionObjectFactory implements ReflectionFactoryInterface
{
    /**
     * @var \ReflectionObject
     */
    private $reflectionObject;

    public function __construct($object)
    {
        if (!is_object($object)) {
            throw Exception\ReflectionExceptionFactory::invalidArgument(
                sprintf("Parameter 1 of %s must be an object.", __METHOD__)
            );
        }

        $this->reflectionObject = new \ReflectionObject($object);
    }

    public static function create($object)
    {
        return new static($object);
    }

    public static function export($object, $return = false)
    {
        return \ReflectionObject::export($object, $return);
    }

    public function getReflector()
    {
        return $this->reflectionObject;
    }
}
