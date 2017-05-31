<?php

namespace DependencyInjection\Tests;

use DependencyInjection\Internal\ReflectionPropertyFactory;
use DependencyInjection\Tests\Fixtures\Base;

class ReflectionPropertyFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileTryingGetInstanceOf()
    {
        $reflector = ReflectionPropertyFactory::create(null, 'firstName');
    }

    public function testCanGetInstance()
    {
        $reflector = new ReflectionPropertyFactory(Base::class, 'firstName');

        $this->assertInstanceOf(ReflectionPropertyFactory::class, $reflector);
    }

    public function testCanGetInstanceStatically()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertInstanceOf(ReflectionPropertyFactory::class, $reflector);
    }

    public function testCanGetExportedInstanceMetadata()
    {
        $exported = ReflectionPropertyFactory::export(Base::class, 'firstName', true);

        $this->assertInternalType('string', $exported);
        $this->assertNotEmpty($exported);
    }

    public function testCanGetDeclaringClass()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertInstanceOf(\ReflectionClass::class, $reflector->getDeclaringClass());
    }

    public function testCanGetDocblockComment()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertInternalType('string', $reflector->getDocComment());
        $this->assertNotEmpty($reflector->getDocComment());
    }

    public function testCanGetModifiers()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertInternalType('int', $reflector->getModifiers());
    }

    public function testCanGetName()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertInternalType('string', $reflector->getName());
        $this->assertNotEmpty($reflector->getName());
    }

    /**
     * @expectedException \LogicException
     */
    public function testCanThrowExceptionWhenTryingToGetValueFromProperty()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $reflector->getValue(null);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testCanGetValue()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertNull($reflector->getValue(new Base));
    }

    public function testIsDefault()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertTrue($reflector->isDefault());
    }

    public function testIsPrivate()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertTrue($reflector->isPrivate());
    }

    public function testIsProtected()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertFalse($reflector->isProtected());
    }

    public function testIsPublic()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertFalse($reflector->isPublic());
    }

    public function testCanSetAccessControlIntoProperty()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $reflector->setAccessible(true);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCanThrowExceptionWhileTryingToSetValue()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $reflector->setValue(null, 31337);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testCanSetValue()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $reflector->setValue(new Base, 31337);
    }

    public function testCanSetValueStatically()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'dummy');

        $reflector->setValue(null, 31337);
    }

    public function testCanGetCastedInstanceIntoString()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertInternalType('string', (string)$reflector);
        $this->assertNotEmpty((string)$reflector);
    }

    public function testCanGetReflectorInstance()
    {
        $reflector = ReflectionPropertyFactory::create(Base::class, 'firstName');

        $this->assertInstanceOf(\ReflectionProperty::class, $reflector->getReflector());
    }
}
