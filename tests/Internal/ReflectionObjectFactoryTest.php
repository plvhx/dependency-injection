<?php

namespace DependencyInjection\Tests;

use DependencyInjection\Container;
use DependencyInjection\Internal\ReflectionObjectFactory;
use DependencyInjection\Tests\Fixtures\Base;

class ReflectionObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileTryingToGetInstance()
    {
        $reflector = new ReflectionObjectFactory(Base::class);
    }

    public function testCanGetInstance()
    {
        $reflector = new ReflectionObjectFactory($this->container->make(Base::class));

        $this->assertInstanceOf(ReflectionObjectFactory::class, $reflector);
    }

    public function testCanGetExportedInstance()
    {
        $exportedReflector = ReflectionObjectFactory::export($this->container->make(Base::class), true);

        $this->assertInternalType('string', $exportedReflector);
        $this->assertNotEmpty($exportedReflector);
    }

    public function testCanGetReflectorInstance()
    {
        $reflector = ReflectionObjectFactory::create($this->container->make(Base::class));

        $this->assertInstanceOf(\ReflectionObject::class, $reflector->getReflector());
    }
}
