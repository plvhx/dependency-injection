<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace DependencyInjection\Tests;

use DependencyInjection\Container;
use DependencyInjection\Internal\ReflectionClassFactory;
use DependencyInjection\Tests\Fixtures\Base;
use DependencyInjection\Tests\Fixtures\BaseInterface;
use DependencyInjection\Tests\Fixtures\Foo;
use DependencyInjection\Tests\Fixtures\FooWithInterface;
use DependencyInjection\Tests\Fixtures\NonInvokableFoo;
use DependencyInjection\Tests\Fixtures\Bar;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function callNonPublicMethod(&$object, $method, $parameters = [])
    {
        $reflector = new \ReflectionObject($object);
        $q = $reflector->getMethod($method);
        $q->setAccessible(true);

        return $q->invokeArgs($object, $parameters);
    }

    public function testCanGetInstanceOfContainer()
    {
        $container = new Container();

        $this->assertInstanceOf('DependencyInjection\\Container', $container);
    }

    public function testIfOffsetWasExists()
    {
        $container = new Container();

        $this->assertFalse($container->offsetExists(Base::class));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanThrowExceptionWhenGetOffset()
    {
        $container = new Container();

        $container->offsetGet(FooWithInterface::class);
    }

    public function testCanSetOffset()
    {
        $container = new Container();

        $container->offsetSet(BaseInterface::class, Base::class);

        $foo = $container->offsetGet(FooWithInterface::class);

        $this->assertInstanceOf(FooWithInterface::class, $foo);
    }

    public function testCanUnsetOffset()
    {
        $container = new Container();

        $container->offsetSet(BaseInterface::class, Base::class);

        $container->offsetUnset(BaseInterface::class);
    }

    public function testCanGetListOfAbstracts()
    {
        $container = new Container();

        $this->assertInternalType('array', $this->callNonPublicMethod($container, 'getAbstracts', []));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhenFailedToResolveMethodParameters()
    {
        $container = new Container();

        $this->callNonPublicMethod($container, 'resolveMethodParameters', [null]);
    }

    public function testCanResolveMethodParameters()
    {
        $container = new Container();

        $this->assertInternalType('array', $this->callNonPublicMethod($container, 'resolveMethodParameters',
            [[Fixtures\Base::class, Fixtures\Foo::class]]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhenFailedToCircularlyResolvingDependency()
    {
        $container = new Container();

        $this->callNonPublicMethod($container, 'circularDependencyResolver', [null]);
    }

    public function testCanCircularlyResolvingDependency()
    {
        $container = new Container();

        $a = $this->callNonPublicMethod($container, 'circularDependencyResolver', [\SplPriorityQueue::class]);

        $this->assertInstanceOf(\SplPriorityQueue::class, $a);

        $foo = $this->callNonPublicMethod($container, 'circularDependencyResolver', [Base::class]);

        $this->assertInstanceOf(Base::class, $foo);

        $container->bind(BaseInterface::class, Base::class);

        $bar = $this->callNonPublicMethod($container, 'circularDependencyResolver', [FooWithInterface::class]);

        $this->assertInstanceOf(FooWithInterface::class, $bar);
    }

    public function testCanInternallyResolveDependencies()
    {
        $container = new Container();

        $a = $this->callNonPublicMethod($container, 'resolve', [\SplPriorityQueue::class]);

        $this->assertInstanceOf(\SplPriorityQueue::class, $a);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCanThrowExceptionWhenGetConcreteImplementationFromInterface()
    {
        $container = new Container();

        $container->bind(BaseInterface::class, Base::class);
        $container->bind(BaseInterface::class, \SplPriorityQueue::class);

        $a = $this->callNonPublicMethod($container, 'getConcreteFromInterface', [BaseInterface::class]);
    }

    public function testIfCloneable()
    {
        $container = new Container();

        $this->assertFalse(
            $this->callNonPublicMethod($container, 'isCloneable', [ReflectionClassFactory::create(Base::class)])
        );
    }

    public function testIfSerializable()
    {
        $container = new Container();

        $this->assertFalse(
            $this->callNonPublicMethod($container, 'isSerializable', [ReflectionClassFactory::create(Base::class)])
        );
    }

    public function testCanAutowireClass()
    {
        $container = new Container();

        $foo = $container->make(Foo::class);

        $this->assertInstanceOf('DependencyInjection\\Tests\\Fixtures\\Foo', $foo);
    }

    public function testCanMockThenAutowireClass()
    {
        $container = $this->getMock(Container::class);

        $container->expects($this->any())->method('make')->willReturn(new Foo(new Base));

        $this->assertInstanceOf(Foo::class, $container->make(Foo::class));
    }

    public function testIfAbstractExists()
    {
        $container = new Container();

        $container->bind(Foo::class, function ($container) {
            return $container->make(Base::class);
        });

        $this->assertTrue($container->isAbstractExists(Foo::class));
    }

    public function testCanMockAndDetermineIfAbstractExists()
    {
        $container = $this->getMock(Container::class);

        $container->expects($this->once())->method('bind');
        $container->expects($this->any())->method('isAbstractExists')->willReturn(true);

        $container->bind(Foo::class, function ($q) {
            return $q->make(Base::class);
        });

        $this->assertTrue($container->isAbstractExists(Foo::class));
    }

    public function testIfConcreteExists()
    {
        $container = new Container();

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->bind(Foo::class, $callback);

        $this->assertTrue($container->isConcreteExists($callback));
    }

    public function testCanMockAndDetermineIfConcreteExists()
    {
        $container = $this->getMock(Container::class);

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->expects($this->once())->method('bind');
        $container->expects($this->any())->method('isConcreteExists')->willReturn(true);

        $container->bind(Foo::class, $callback);

        $this->assertTrue($container->isConcreteExists($callback));
    }

    public function testIfAnInterface()
    {
        $container = new Container();

        $this->assertFalse($container->isInterface(Base::class));
        $this->assertTrue($container->isInterface(BaseInterface::class));
    }

    public function testCanMockAndDetermineIfAnInterface()
    {
        $container = $this->getMock(Container::class);

        $container->expects($this->once())->method('isInterface')->willReturn(true);

        $this->assertTrue($container->isInterface(BaseInterface::class));
    }

    public function testCanMockAndDetermineIfNotAnInterface()
    {
        $container = $this->getMock(Container::class);

        $container->expects($this->once())->method('isInterface')->willReturn(false);

        $this->assertFalse($container->isInterface(Base::class));
    }

    public function testCanGetConcreteImplementation()
    {
        $container = new Container();

        $container->bind(Foo::class, function ($container) {
            return $container->make(Base::class);
        });

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCannotGetConcreteImplementation()
    {
        $container = new Container();

        $this->assertFalse($container->isAbstractExists(Foo::class));
        $this->assertInternalType('null', $container->getConcrete(Foo::class));
        $this->assertEmpty($container->getConcrete(Foo::class));
    }

    public function testCanMockAndGetConcreteImplementation()
    {
        $container = $this->getMock(Container::class);

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->expects($this->once())->method('bind');
        $container->expects($this->once())->method('isAbstractExists')->willReturn(true);
        $container->expects($this->any())->method('getConcrete')->willReturn([$callback]);

        $container->bind(Foo::class, $callback);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanMockAndCannotGetConcreteImplementation()
    {
        $container = $this->getMock(Container::class);

        $container->expects($this->once())->method('isAbstractExists')->willReturn(false);
        $container->expects($this->any())->method('getConcrete')->willReturn(null);

        $this->assertFalse($container->isAbstractExists(Foo::class));
        $this->assertInternalType('null', $container->getConcrete(Foo::class));
        $this->assertEmpty($container->getConcrete(Foo::class));
    }

    public function testCanBindConcreteIntoAbstract()
    {
        $container = new Container();

        $container->bind(Foo::class, Base::class);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);

        $container->bind(Base::class);

        $this->assertTrue($container->isAbstractExists(Base::class));
        $this->assertInternalType('array', $container->getConcrete(Base::class));
        $this->assertNotEmpty($container->getConcrete(Base::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanBindClosureConcreteIntoAbstract()
    {
        $container = new Container();

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->bind(Foo::class, $callback);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanMockThenBindConcreteIntoAbstract()
    {
        $container = $this->getMock(Container::class);

        $abstract = Foo::class;
        $concrete = Base::class;

        $container->expects($this->once())->method('bind');
        $container->expects($this->once())->method('isAbstractExists')->willReturn(true);
        $container->expects($this->any())->method('getConcrete')->willReturn(
            [function (Container $container, $parameters = []) use ($abstract, $concrete) {
                return ($abstract == $concrete ? $container->resolve($abstract)
                    : $container->resolve($concrete, $parameters));
            }]
        );

        $container->bind(Foo::class, Base::class);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanMockThenBindClosureConcreteIntoAbstract()
    {
        $container = $this->getMock(Container::class);

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->expects($this->once())->method('bind');
        $container->expects($this->once())->method('isAbstractExists')->willReturn(true);
        $container->expects($this->any())->method('getConcrete')->willReturn([$callback]);

        $container->bind(Foo::class, $callback);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanDoConditionalBindConcreteIntoAbstract()
    {
        $container = new Container();

        $container->bindIf(Foo::class, Base::class);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanDoConditionalBindConcreteClosureIntoAbstract()
    {
        $container = new Container();

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->bindIf(Foo::class, $callback);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanMockThenDoConditionalBindConcreteIntoAbstract()
    {
        $container = $this->getMock(Container::class);

        $abstract = Foo::class;
        $concrete = Base::class;

        $container->expects($this->once())->method('bindIf');
        $container->expects($this->once())->method('isAbstractExists')->willReturn(true);
        $container->expects($this->any())->method('getConcrete')->willReturn(
            [function (Container $container, $parameters = []) use ($abstract, $concrete) {
                return ($abstract === $concrete ? $container->resolve($abstract)
                    : $container->resolve($concrete, $parameters));
            }]
        );

        $container->bindIf(Foo::class, Base::class);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanMockThenDoConditionalBindConcreteClosureIntoAbstract()
    {
        $container = $this->getMock(Container::class);

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->expects($this->once())->method('bindIf');
        $container->expects($this->once())->method('isAbstractExists')->willReturn(true);
        $container->expects($this->any())->method('getConcrete')->willReturn([$callback]);

        $container->bindIf(Foo::class, $callback);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);
    }

    public function testCanCallInstance()
    {
        $container = new Container();

        $this->assertFalse($container->isAbstractExists(Foo::class));
        $this->assertInternalType('null', $container->getConcrete(Foo::class));
        $this->assertEmpty($container->getConcrete(Foo::class));

        $container->callInstance(Foo::class, 'Paulus Gandung Prakosa');
    }

    public function testCanBindConcreteThenCallResolvedInstance()
    {
        $container = new Container();

        $container->bind(Foo::class, Base::class);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);

        $container->callInstance(Foo::class, 'Paulus Gandung Prakosa');
    }

    public function testCanBindConcreteClosureThenCallResolvedInstance()
    {
        $container = new Container();

        $container->bind(Foo::class, function ($container) {
            return $container->make(Base::class);
        });

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);

        $container->callInstance(Foo::class, 'Paulus Gandung Prakosa');
    }

    public function testCanMockThenCallInstance()
    {
        $container = $this->getMock(Container::class);

        $container->expects($this->once())->method('isAbstractExists')->willReturn(false);
        $container->expects($this->any())->method('getConcrete')->willReturn(null);
        $container->expects($this->once())->method('callInstance');

        $this->assertFalse($container->isAbstractExists(Foo::class));
        $this->assertInternalType('null', $container->getConcrete(Foo::class));
        $this->assertEmpty($container->getConcrete(Foo::class));

        $container->callInstance(Foo::class, 'Paulus Gandung Prakosa');
    }

    public function testCanMockThenBindConcreteThenCallResolvedInstance()
    {
        $container = $this->getMock(Container::class);

        $abstract = Foo::class;
        $concrete = Base::class;

        $container->expects($this->once())->method('bind');
        $container->expects($this->once())->method('isAbstractExists')->willReturn(true);
        $container->expects($this->once())->method('callInstance');
        $container->expects($this->any())->method('getConcrete')->willReturn(
            [function (Container $container, $parameters = []) use ($abstract, $concrete) {
                return ($abstract === $concrete ? $container->resolve($abstract)
                    : $container->resolve($concrete, $parameters));
            }]
        );

        $container->bind(Foo::class, Base::class);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);

        $container->callInstance(Foo::class, 'Paulus Gandung Prakosa');
    }

    public function testCanMockThenBindConcreteClosureThenCallResolvedInstance()
    {
        $container = $this->getMock(Container::class);

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->expects($this->once())->method('bind');
        $container->expects($this->once())->method('isAbstractExists')->willReturn(true);
        $container->expects($this->once())->method('callInstance');
        $container->expects($this->any())->method('getConcrete')->willReturn([$callback]);

        $container->bind(Foo::class, $callback);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('array', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class)[0]);

        $container->callInstance(Foo::class, 'Paulus Gandung Prakosa');
    }

    public function testIfAbstractIsBound()
    {
        $container = new Container();

        $this->assertFalse($container->isBound(Foo::class));

        $container->bind(Foo::class, function ($container) {
            return $container->make(Base::class);
        });

        $this->assertTrue($container->isBound(Foo::class));
    }
}
