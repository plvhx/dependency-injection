<?php

namespace DependencyInjection\Internal;

interface ReflectionFactoryInterface
{
    /**
     * Get reflection instance.
     *
     * @return \Reflector
     */
    public function getReflector();
}
