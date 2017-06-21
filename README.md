# Simple Dependency Injection Library

[![Join the chat at https://gitter.im/dependency-injection-container/Lobby](https://badges.gitter.im/dependency-injection-container/Lobby.svg)](https://gitter.im/dependency-injection-container/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Coverage Status](https://coveralls.io/repos/github/plvhx/dependency-injection/badge.svg)](https://coveralls.io/github/plvhx/dependency-injection)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/plvhx/dependency-injection/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/plvhx/dependency-injection/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/plvhx/dependency-injection/badges/build.png?b=master)](https://scrutinizer-ci.com/g/plvhx/dependency-injection/build-status/master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/14e50be8-c441-4d03-b65d-cb07e33c0672/small.png)](https://insight.sensiolabs.com/projects/14e50be8-c441-4d03-b65d-cb07e33c0672)

This is my simple dependency injection library in PHP

## Features:
```
  - Can resolve class dependency that placed only on constructor (autowiring)
  - Binding concrete dependency into unresolved abstract, either closure or class name.
  - Can resolve concrete implementation on typehinted interface on constructor method.
  - Can resolve concrete implementation which bound on interface directly.
  - Registering service under an alias.
```

Setter injection and method injection not yet implemented.
Feel free to look, or clone it for your own needs.

## Autowiring:

Assume you have a class:
```php
<?php

namespace Unused;

class Foo
{
	/**
	 * @var \SplPriorityHeap
	 */
	private $heap;

	public function __construct(\SplPriorityHeap $heap)
	{
		$this->heap = $heap;
	}	
}
```

And you have a class that depends on class Unused\Foo, however class Unused\Foo
depends on class \SplPriorityHeap
```php
<?php

namespace Unused;

class Bar
{
	/**
	 * @var Foo
	 */
	private $foo;

	public function __construct(Foo $foo)
	{
		$this->foo = $foo;
	}
}
```

You can resolve an instance of class Bar without resolving Bar and \SplPriorityHeap manually
```php
<?php

use Unused\Bar;

$container = new Container();

$bar = $container->make(Bar::class);
```

## Binding concrete dependency into unresolved abstract (only class name)

```php
<?php

use Unused\Bar;
use Unused\Foo;

$container = new Container();

$container->bind(Bar::class, Foo::class);

$bar = $container->make(Bar::class);
```

Now, $bar is an instance of Bar::class.

## Binding concrete dependency into unresolved abstract (with closure)

```php
<?php

use Unused\Bar;
use Unused\Foo;

$container = new Container();

$container->bind(Bar::class, function($container) {
	return $container->make(Foo::class);
});

$bar = $container->make(Bar::class);
```

Now, $bar is an instance of Bar::class too.

## Binding typehinted interface into unresolved abstract (class based and with closure)

Assume you have an BaseInterface interface:
```php
<?php

namespace Unused;

interface BaseInterface
{
	public function setFirstName($firstName);

	public function setMiddleName($middleName);

	public function setLastName($lastName);
}
```

And a class which implements BaseInterface interface under the same namespace:
```php
<?php

namespace Unused;

class Base implements BaseInterface
{
	/**
	 * @var string
	 */
	private $firstName;

	/**
	 * @var string
	 */
	private $middleName;

	/**
	 * @var string
	 */
	private $lastName;

	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	public function setMiddleName($middleName)
	{
		$this->middleName = $middleName;
	}

	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}
}
```

And a class which have typehinted interface in it's constructor
```php
<?php

namespace Unused;

class Foo
{
	/**
	 * @var BaseInterface
	 */
	private $base;

	public function __construct(BaseInterface $base)
	{
		$this->base = $base;
	}
}
```

You can resolve class Foo with binding class Base into BaseInterface first.
```php
<?php

use Unused\BaseInterface;
use Unused\Base;
use Unused\Foo;

$container = new Container();

$container->bind(BaseInterface::class, Base::class);

$foo = $container->make(Foo::class);
```

Or, you and bind concrete implementation of BaseInterface with closure
```php
<?php

use Unused\BaseInterface;
use Unused\Base;
use Unused\Foo;

$container = new Container();

$container->bind(BaseInterface::class, function($container) {
	return $container->make(Base::class);
});

$foo = $container->make(Foo::class);
```

## Resolve concrete implementation which bound on interface directly

Assume you have an interface:
```php
<?php

namespace Unused;

interface BaseInterface
{
	public function setFirstName($firstName);

	public function setMiddleName($middleName);

	public function setLastName($lastName);
}
```

And, concrete class which implements Unused\BaseInterface
```php
<?php

namespace Unused;

class Base implements BaseInterface
{
	/**
	 * @var string
	 */
	private $firstName;

	/**
	 * @var string
	 */
	private $middleName;

	/**
	 * @var string
	 */
	private $lastName;

	/**
	 * @implements
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	/**
	 * @implements
	 */
	public function setMiddleName($middleName)
	{
		$this->middleName = $middleName;
	}

	/**
	 * @implements
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}
}
```

Bind concrete implementation on that interface first (use either direct class name or closure)
```php
<?php

use Unused\Base;
use Unused\BaseInterface;

$container = new Container();

// with direct class name.
$container->bind(BaseInterface::class, Base::class);

// or, use a closure.
$container->bind(BaseInterface::class, function($container) {
	return $container->make(Base::class);
});
```

Then, get it directly.
```php
$base = $container->make(BaseInterface::class);
```

## Registering service under an alias (PSR-11 compatible.)

Assume you have a service which require a concrete implementation of a BaseInterface:
```php
<?php

namespace Unused;

class FooService
{
	/**
	 * @var BaseInterface
	 */
	private $base;

	public function __construct(BaseInterface $base)
	{
		$this->base = $base;
	}
}
```

Just bind a concrete implementation of BaseInterface, then register FooService under an alias (e.g: foo.service)
```php
<?php

$container = new Container();

$container->bind(BaseInterface::class, function($container) {
	return $container->make(Base::class);
});

$container->register('foo.service', FooService::class);

$service = $container->get('foo.service');
```

## Unit Testing

If you want to run unit tests:
```
vendor/bin/phpunit
```

If you need more verbose:
```
vendor/bin/phpunit --verbose
```
