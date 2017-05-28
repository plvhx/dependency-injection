<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace DependencyInjection\Tests\Fixtures;

class Foo
{
    /**
     * @var BaseInstance
     */
    private $base;

    public function __construct(Base $base)
    {
        $this->base = $base;
    }

    public function setFirstName($firstName)
    {
        $this->base->setFirstName($firstName);
    }

    public function setMiddleName($middleName)
    {
        $this->base->setMiddleName($middleName);
    }

    public function setLastName($lastName)
    {
        $this->base->setLastName($lastName);
    }
    
    public function getBase()
    {
        return $this->base;
    }
    
    public function __toString()
    {
        return $this->base->unify();
    }

    public function __invoke($name)
    {
        echo sprintf("%s" . PHP_EOL, $name);
    }
}
