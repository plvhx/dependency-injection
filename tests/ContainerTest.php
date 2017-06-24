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
use DependencyInjection\Tests\Fixtures\Baz;

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

    /**
     * @expectedException \RuntimeException
     */
    public function testCanThrowExceptionWhileTryingToGetConcreteImplementationFromInterface()
    {
        $container = new Container();

        $container->make(BaseInterface::class);
    }

    public function testCanGetConcreteImplementationFromInterface()
    {
        $container = new Container();

        $container->bind(BaseInterface::class, Base::class);

        $base = $container->make(BaseInterface::class);

        $this->assertInstanceOf(Base::class, $base);
        $this->assertInstanceOf(BaseInterface::class, $base);
    }

    public function testCanGetConcreteImplementationFromInterfaceWithClosure()
    {
        $container = new Container();

        $container->bind(BaseInterface::class, function ($container) {
            return $container->make(Base::class);
        });

        $base = $container->make(BaseInterface::class);

        $this->assertInstanceOf(Base::class, $base);
        $this->assertInstanceOf(BaseInterface::class, $base);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileRegisteringServiceAlias()
    {
        $container = new Container();

        $container->register(null, Base::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileRegisteringServiceAliasWithInvalidAbstract()
    {
        $container = new Container();

        $container->register('base.service', null);
    }

    public function testCanRegisterServiceAlias()
    {
        $container = new Container();

        $container->register('base.service', Base::class);

        $this->assertTrue($container->isAliasExists('base.service'));
    }

    /**
     * @expectedException DependencyInjection\Exception\ContainerException
     */
    public function testCanThrowExceptionWhileGetServiceAlias()
    {
        $container = new Container();

        $container->register('base.service', Base::class);

        $container->get('nonexistent.service');
    }

    public function testCanGetServiceAlias()
    {
        $container = new Container();

        $container->register('base.service', Base::class, 'auto');

        $this->assertTrue($container->has('base.service'));
        
        $base = $container->get('base.service');

        $this->assertInstanceOf(Base::class, $base);
    }

    public function testCanGetServiceAliasWithBinding()
    {
        $container = new Container();

        $container->bind(BaseInterface::class, function ($container) {
            return $container->make(Base::class);
        });

        $container->register('foo.service', FooWithInterface::class);

        $foo = $container->get('foo.service');

        $this->assertInstanceOf(FooWithInterface::class, $foo);
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
        $this->assertInternalType('object', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class));
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
        $this->assertInternalType('object', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class));

        $container->bind(Base::class);

        $this->assertTrue($container->isAbstractExists(Base::class));
        $this->assertInternalType('object', $container->getConcrete(Base::class));
        $this->assertNotEmpty($container->getConcrete(Base::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class));
    }

    public function testCanBindClosureConcreteIntoAbstract()
    {
        $container = new Container();

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->bind(Foo::class, $callback);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('object', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class));
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
        $this->assertInternalType('object', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class));
    }

    public function testCanDoConditionalBindConcreteClosureIntoAbstract()
    {
        $container = new Container();

        $callback = function ($container) {
            return $container->make(Base::class);
        };

        $container->bindIf(Foo::class, $callback);

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('object', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class));
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
        $this->assertInternalType('object', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class));

        $container->callInstance(Foo::class, 'Paulus Gandung Prakosa');
    }

    public function testCanBindConcreteClosureThenCallResolvedInstance()
    {
        $container = new Container();

        $container->bind(Foo::class, function ($container) {
            return $container->make(Base::class);
        });

        $this->assertTrue($container->isAbstractExists(Foo::class));
        $this->assertInternalType('object', $container->getConcrete(Foo::class));
        $this->assertNotEmpty($container->getConcrete(Foo::class));
        $this->assertInstanceOf(\Closure::class, $container->getConcrete(Foo::class));

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

    public function testCanSharedBindConcreteIntoAbstract()
    {
        $container = new Container();

        $container->singleton(BaseInterface::class, Base::class);

        $a = $container->make(BaseInterface::class);
        $b = $container->make(BaseInterface::class);

        $this->assertInstanceOf(BaseInterface::class, $a);
        $this->assertInstanceOf(BaseInterface::class, $b);
        $this->assertEquals($a, $b);
        $this->assertSame($a, $b);
    }

    public function testCanSharedBindClosureIntoAbstract()
    {
        $container = new Container();

        $container->singleton(BaseInterface::class, function ($container) {
            return $container->make(Base::class);
        });

        $a = $container->make(BaseInterface::class);
        $b = $container->make(BaseInterface::class);

        $this->assertInstanceOf(BaseInterface::class, $a);
        $this->assertInstanceOf(BaseInterface::class, $b);
        $this->assertEquals($a, $b);
        $this->assertSame($a, $b);
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

    public function testCanResolveConcrete()
    {
        $container = new Container();

        $container->singleton(BaseInterface::class, function ($container) {
            return $container->make(Base::class);
        });

        $container->make(BaseInterface::class);

        $this->assertInstanceOf(BaseInterface::class, $container->getResolvedConcrete(BaseInterface::class));
    }

    public function testCanGetAbstractDependencies()
    {
        $container = new Container();

        $container->singleton(BaseInterface::class, function ($container) {
            return $container->make(Base::class);
        });

        $a = $container->getAbstractDependencies(BaseInterface::class);

        $this->assertInternalType('bool', $a['shared']);
        $this->assertInstanceOf(\Closure::class, $a['concrete']);
    }

    public function testCanResolveSharedWithBoundDependency()
    {
        $container = new Container();

        $container->singleton(Foo::class, Base::class);

        $a = $container->make(Foo::class);

        $this->assertInstanceOf(Foo::class, $a);
    }

    public function testCanResolveWithBoundDependency()
    {
        $container = new Container();

        $container->bind(Foo::class, Base::class);

        $a = $container->make(Foo::class);

        $this->assertInstanceOf(Foo::class, $a);
    }

    public function testCanDirectlyResolveMethodParameters()
    {
        $container = new Container();

        $this->callNonPublicMethod($container, 'resolveMethodParameters', [[function ($container) {
            return $container->make(Base::class);
        }]]);
    }

    public function testCanGetConcreteFromInterface()
    {
        $container = new Container();

        $container->singleton(BaseInterface::class, Base::class);

        $this->callNonPublicMethod($container, 'getConcreteFromInterface', [BaseInterface::class]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIfAbstractSharedWithException()
    {
        $container = new Container();

        $container->isShared(BaseInterface::class);
    }

    public function testCanResolveBaz()
    {
        $container = new Container();

        $baz = $container->make(Baz::class);

        $this->assertInstanceOf(Baz::class, $baz);
    }
}
