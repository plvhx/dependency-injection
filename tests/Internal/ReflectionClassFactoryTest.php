<?php

namespace DependencyInjection\Tests\Internal;

use DependencyInjection\Internal\ReflectionClassFactory;
use DependencyInjection\Tests\Fixtures\Base;

class ReflectionClassFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileTryingToGetInstance()
    {
        $internalReflector = new ReflectionClassFactory(null);
    }

    public function testIfCanGetInstance()
    {
        $internalReflector = new ReflectionClassFactory(Base::class);

        $this->assertInstanceOf(ReflectionClassFactory::class, $internalReflector);
    }

    public function testIfCanStaticallyGetInstance()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInstanceOf(ReflectionClassFactory::class, $internalReflector);
    }

    public function testIfCanGetConstant()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertFalse($internalReflector->getConstant('SHIT'));
    }

    public function testIfCanGetConstants()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getConstants());
        $this->assertEmpty($internalReflector->getConstants());
    }

    public function testIfCanGetConstructor()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('object', $internalReflector->getConstructor());
        $this->assertNotNull($internalReflector->getConstructor());
    }

    public function testIfCanGetDefaultProperties()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getDefaultProperties());
        $this->assertNotEmpty($internalReflector->getDefaultProperties());
    }

    public function testIfCanGetDocblocksComment()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->getDocComment());
        $this->assertEmpty($internalReflector->getDocComment());
    }

    public function testIfCanEndLine()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('int', $internalReflector->getEndLine());
        $this->assertNotEmpty($internalReflector->getEndLine());
    }

    public function testIfCanGetExtension()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('null', $internalReflector->getExtension());
        $this->assertNull($internalReflector->getExtension());
    }

    public function testIfCanGetExtensionName()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->getExtensionName());
        $this->assertFalse($internalReflector->getExtensionName());
    }

    public function testIfCanGetFileName()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('string', $internalReflector->getFileName());
        $this->assertNotEmpty($internalReflector->getFileName());
    }

    public function testIfCanGetInterfaceNames()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getInterfaceNames());
        $this->assertNotEmpty($internalReflector->getInterfaceNames());
    }

    public function testIfCanGetInterfaces()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getInterfaces());
        $this->assertNotEmpty($internalReflector->getInterfaces());
    }

    public function testIfCanGetMethod()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInstanceOf(\ReflectionMethod::class, $internalReflector->getMethod('setFirstName'));
    }

    public function testIfCanGetMethods()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getMethods());
        $this->assertNotEmpty($internalReflector->getMethods());
    }

    public function testIfCanGetModifiers()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('int', $internalReflector->getModifiers());
        $this->assertNotEmpty($internalReflector->getModifiers());
    }

    public function testIfCanGetName()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('string', $internalReflector->getName());
        $this->assertNotEmpty($internalReflector->getName());
    }

    public function testIfCanGetNamespaceName()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('string', $internalReflector->getNamespaceName());
        $this->assertNotEmpty($internalReflector->getNamespaceName());
    }

    public function testIfCanGetParentClass()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->getParentClass());
        $this->assertFalse($internalReflector->getParentClass());
    }

    public function testIfCanGetProperties()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getProperties());
        $this->assertNotEmpty($internalReflector->getProperties());
    }

    public function testIfCanGetProperty()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInstanceOf(\ReflectionProperty::class, $internalReflector->getProperty('firstName'));
        $this->assertNotEmpty($internalReflector->getProperty('firstName'));
    }

    public function testIfCanGetShortName()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('string', $internalReflector->getShortName());
        $this->assertNotEmpty($internalReflector->getShortName());
    }

    public function testIfCanGetStartLine()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('int', $internalReflector->getStartLine());
    }

    public function testIfCanGetStaticProperties()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getStaticProperties());
        $this->assertNotEmpty($internalReflector->getStaticProperties());
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testIfCanThrowExceptionWhileGetStaticPropertyValue()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        var_dump($internalReflector->getStaticPropertyValue('nonExistentProperty'));
    }

    public function testIfCanThrowExceptionWhileGetStaticPropertyValueWithDefaultValue()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);
        $defaultValue = 31337;

        var_dump($internalReflector->getStaticPropertyValue('nonExistentProperty', $defaultValue));
    }

    public function testIfCanGetTraitAliases()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getTraitAliases());
        $this->assertEmpty($internalReflector->getTraitAliases());
    }

    public function testIfCanGetTraitNames()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getTraitNames());
        $this->assertEmpty($internalReflector->getTraitNames());
    }

    public function testIfCanGetTraits()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('array', $internalReflector->getTraits());
        $this->assertEmpty($internalReflector->getTraits());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileDeterminingIfInstanceHasConstant()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->hasConstant(1337);
    }

    public function testIfHasConstant()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->hasConstant('SHIT'));
        $this->assertFalse($internalReflector->hasConstant('SHIT'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileDeterminingIfInstanceHasMethod()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->hasMethod(null);
    }

    public function testIfHasMethod()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->hasMethod('setFirstName'));
        $this->assertTrue($internalReflector->hasMethod('setFirstName'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileDeterminingIfInstanceHasProperty()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->hasProperty(false);
    }

    public function testIfHasProperty()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->hasProperty('firstName'));
        $this->assertTrue($internalReflector->hasProperty('firstName'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileDeterminingIfInstanceImplementsSpecificInterface()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->implementsInterface(null);
    }

    public function testIfImplementsInterface()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->implementsInterface(
            'DependencyInjection\\Tests\\Fixtures\\BaseInterface'));
        $this->assertTrue($internalReflector->implementsInterface(
            'DependencyInjection\\Tests\\Fixtures\\BaseInterface'));
    }

    public function testIfInNamespace()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->inNamespace(
            'DependencyInjection\\Tests\\Fixtures'));
        $this->assertTrue($internalReflector->inNamespace('DependencyInjection\\Tests\\Fixtures'));
    }

    public function testIsAbstract()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isAbstract());
        $this->assertFalse($internalReflector->isAbstract());
    }

    public function testIsCloneable()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isCloneable());
        $this->assertTrue($internalReflector->isCloneable());
    }

    public function testIsFinal()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isFinal());
        $this->assertFalse($internalReflector->isFinal());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileDeterminingIfInstanceIsAnInstanceOfOthers()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->isInstance('shit');
    }

    public function testIsInstance()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isInstance(new Base()));
        $this->assertTrue($internalReflector->isInstance(new Base()));
    }

    public function testIsInstantiable()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isInstantiable());
        $this->assertTrue($internalReflector->isInstantiable());
    }

    public function testIsInterface()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isInterface());
        $this->assertFalse($internalReflector->isInterface());
    }

    public function testIsInternal()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isInternal());
        $this->assertFalse($internalReflector->isInternal());
    }

    public function testIsIterateable()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isIterateable());
        $this->assertFalse($internalReflector->isIterateable());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileDeterminingInstanceIsSubclassOfOthers()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->isSubclassOf(null);
    }

    public function testIsSubclassOf()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isSubclassOf(
            'DependencyInjection\\Tests\\Fixtures\\BaseInterface'));
        $this->assertTrue($internalReflector->isSubclassOf(
            'DependencyInjection\\Tests\\Fixtures\\BaseInterface'));
    }

    public function testIsTrait()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isTrait());
        $this->assertFalse($internalReflector->isTrait());
    }

    public function testIsUserDefined()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('bool', $internalReflector->isUserDefined());
        $this->assertTrue($internalReflector->isUserDefined());
    }

    public function testIsANewInstance()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInstanceOf(Base::class, $internalReflector->newInstance('a', 'b', 'c'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileGetNewInstanceWithArrayOfArguments()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->newInstanceArgs('a');
    }

    public function testIsANewInstanceWithArguments()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInstanceOf(Base::class, $internalReflector->newInstanceArgs(['a', 'b', 'c']));
    }

    public function testIsANewInstanceWithoutConstructor()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInstanceOf(Base::class, $internalReflector->newInstanceWithoutConstructor());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhenNameOfTheStaticPropertyIsNotSet()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->setStaticPropertyValue(null, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhenValueOfTheStaticPropertyIsNotSet()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->setStaticPropertyValue('SHIT', null);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testIfCanSetStaticPropertyValue()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $internalReflector->setStaticPropertyValue('SHIT', 31337);
    }

    public function testIfCanCastedToString()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInternalType('string', (string)$internalReflector);
        $this->assertNotEmpty((string)$internalReflector);
    }

    public function testIfCanGetReflectionClassInstance()
    {
        $internalReflector = ReflectionClassFactory::create(Base::class);

        $this->assertInstanceOf(\ReflectionClass::class, $internalReflector->getReflector());
    }
}
