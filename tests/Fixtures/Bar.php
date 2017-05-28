<?php

namespace DependencyInjection\Tests\Fixtures;

class Bar
{
    /**
     * @var FooWithInterface
     */
    private $foo;

    public function __construct(FooWithInterface $foo)
    {
        $this->foo = $foo;
    }
}
