# Simple Dependency Injection Experiment

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/14e50be8-c441-4d03-b65d-cb07e33c0672/small.png)](https://insight.sensiolabs.com/projects/14e50be8-c441-4d03-b65d-cb07e33c0672)
[![Build Status](https://travis-ci.org/plvhx/dependency-injection.svg?branch=master)](https://travis-ci.org/plvhx/dependency-injection)

This is my simple dependency injection experiment in PHP

Features:
```
  - can resolve class dependency that placed only on constructor (autowiring)
  - binding concrete dependency into unresolved abstract, either closure or class name.
```

Setter injection and method injection not yet implemented.
Feel free to look, or clone it for your own needs.

Autowiring:

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

$container = new Container();

$bar = $container->make(Bar::class);
```

If you want to run unit tests:
```
vendor/bin/phpunit
```

If you need more verbose:
```
vendor/bin/phpunit --verbose
```
