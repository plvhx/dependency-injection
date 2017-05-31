<?php

namespace DependencyInjection\Tests\Internal;

use DependencyInjection\Internal\ReflectionFunctionFactory;

class ReflectionFunctionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanGetInstanceOf()
    {
        $reflector = new ReflectionFunctionFactory(function () {
            echo "Hello closure." . PHP_EOL;
        });

        $this->assertInstanceOf(ReflectionFunctionFactory::class, $reflector);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanTriggerInvalidArgumentException()
    {
        $reflector = ReflectionFunctionFactory::create(null);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testCanTriggerReflectionException()
    {
        $reflector = ReflectionFunctionFactory::create('shit');
    }
    
    public function testCanGetInstanceOfStatically()
    {
        $reflector = ReflectionFunctionFactory::create(function () {
            echo "Hello closure." . PHP_EOL;
        });

        $this->assertInstanceOf(ReflectionFunctionFactory::class, $reflector);
    }

    public function testCanExportClosureOrFunctionAsString()
    {
        $exportedReflector = ReflectionFunctionFactory::export(function () {
            echo "Hello closure." . PHP_EOL;
        }, true);

        $this->assertInternalType('string', $exportedReflector);
        $this->assertNotEmpty($exportedReflector);
    }

    public function testCanGetClosureInstance()
    {
        $reflector = ReflectionFunctionFactory::create(function () {
            echo "Hello closure." . PHP_EOL;
        });

        $this->assertNotNull($reflector->getClosure());
        $this->assertInstanceOf(\Closure::class, $reflector->getClosure());
    }

    public function testCanInvoke()
    {
        $reflector = ReflectionFunctionFactory::create(function () {
            echo "Hello closure." . PHP_EOL;
        });

        $this->assertNull($reflector->invoke('a', 'b', 'c'));
    }

    public function testCanInvokeWithArrayOfArguments()
    {
        $reflector = ReflectionFunctionFactory::create(function () {
            echo "Hello closure." . PHP_EOL;
        });

        $this->assertNull($reflector->invokeArgs(['a', 'b', 'c']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowExceptionWhenInvokeWithNotAnArrayOfArgs()
    {
        $reflector = ReflectionFunctionFactory::create(function () {
            echo "Hello closure." . PHP_EOL;
        });

        $reflector->invokeArgs(null);
    }

    public function testIsDisabled()
    {
        $reflector = ReflectionFunctionFactory::create(function () {
            return "Hello closure." . PHP_EOL;
        });

        $this->assertFalse($reflector->isDisabled());
    }

    public function testCanGetCastedInstance()
    {
        $reflector = ReflectionFunctionFactory::create(function () {
            return "Hello closure." . PHP_EOL;
        });

        $this->assertInternalType('string', (string)$reflector);
        $this->assertNotEmpty((string)$reflector);
    }

    public function testCanGetReflector()
    {
        $reflector = ReflectionFunctionFactory::create(function () {
            return "Hello closure." . PHP_EOL;
        });

        $this->assertInstanceOf(\ReflectionFunction::class, $reflector->getReflector());
    }
}
