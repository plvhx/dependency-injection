<?php

namespace DependencyInjection\Tests\Fixtures;

class FooWithInterface
{
    /**
     * @var BaseInterface
     */
    private $base;

    public function __construct(BaseInterface $base)
    {
        $this->base = $base;
    }
}
