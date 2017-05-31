<?php

namespace DependencyInjection\Tests\Internal;

use DependencyInjection\Internal\ReflectionMethodFactory;
use DependencyInjection\Tests\Fixtures\Base;

class ReflectionMethodFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testIfCanGetInstance()
    {
        $reflector = new ReflectionMethodFactory(Base::class, 'setFirstName');

        $this->assertInstanceOf(ReflectionMethodFactory::class, $reflector);
    }

    public function testIfCanGetInstanceStatically()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertInstanceOf(ReflectionMethodFactory::class, $reflector);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhenClassNameOrInstanceIsNotValid()
    {
        $reflector = ReflectionMethodFactory::create(null, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhenMethodNameIsNotValid()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, null);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testCanThrowExceptionWhenMethodNameIsNotExist()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'shit');
    }

    public function testCanExportInstanceIntoString()
    {
        $reflector = ReflectionMethodFactory::export(Base::class, 'setFirstName', true);

        $this->assertInternalType('string', (string)$reflector);
        $this->assertNotEmpty((string)$reflector);
    }

    public function testCanGetClosure()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertNotNull($reflector->getClosure(new Base));
        $this->assertInstanceOf(\Closure::class, $reflector->getClosure(new Base));
    }

    public function testCanGetDeclaringClass()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertNotNull($reflector->getDeclaringClass());
        $this->assertInstanceOf(\ReflectionClass::class, $reflector->getDeclaringClass());
    }

    public function testCanGetModifiers()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertInternalType('int', $reflector->getModifiers());
        $this->assertNotEmpty($reflector->getModifiers());
    }

    public function testCanGetPrototype()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertInstanceOf(\ReflectionMethod::class, $reflector->getPrototype());
    }

    public function testCanInvokeDefinedMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $reflector->invoke(new Base, 'Paulus');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileExecutingDefinedMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $reflector->invoke(null, 'Paulus');
    }

    public function testCanInvokeDefinedMethodWithArrayOfArguments()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $reflector->invokeArgs(new Base, ['Paulus']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileExecutingDefinedMethodWithoutArrayOfArguments()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $reflector->invokeArgs(new Base, 'Paulus');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileExecutingDefinedMethodWithoutSuppliedObject()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $reflector->invokeArgs(null, ['Paulus']);
    }

    public function testIsAbstractMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertFalse($reflector->isAbstract());
    }

    public function testIsConstructorMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertFalse($reflector->isConstructor());
    }

    public function testIsDestructorMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertFalse($reflector->isDestructor());
    }

    public function testIsFinalMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertFalse($reflector->isFinal());
    }

    public function testIsPrivateMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertFalse($reflector->isPrivate());
    }

    public function testIsProtectedMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertFalse($reflector->isProtected());
    }

    public function testIsPublicMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertTrue($reflector->isPublic());
    }

    public function testIsStaticMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertFalse($reflector->isStatic());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileTryingToSetAccessibleOnNonPublicMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $reflector->setAccessible(1337);
    }

    public function testCanEnableOrDisableAccessbilityOnDefinedMethod()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $reflector->setAccessible(true);
    }

    public function testCanGetCastedReflectorInstanceToString()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertInternalType('string', (string)$reflector);
        $this->assertNotEmpty((string)$reflector);
    }

    public function testCanGetReflectorInstance()
    {
        $reflector = ReflectionMethodFactory::create(Base::class, 'setFirstName');

        $this->assertInstanceOf(\ReflectionMethod::class, $reflector->getReflector());
    }
}
