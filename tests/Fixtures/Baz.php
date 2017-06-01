<?php

namespace DependencyInjection\Tests\Fixtures;

class Baz
{
    public function __construct(Base $base, $a, $b = 1337, $c = [])
    {
    }
}
