<?php

namespace Experiments\DependencyInjection\Internal\Exception;

use \InvalidArgumentException;
use \RuntimeException;
use \LogicException;
use \ReflectionException;

class ReflectionExceptionFactory
{
	public static function invalidArgument($message, $code = 0)
	{
		return new \InvalidArgumentException($message, $code);
	}

	public static function runtime($message, $code = 0)
	{
		return new \RuntimeException($message, $code);
	}

	public static function logic($message, $code = 0)
	{
		return new \LogicException($message, $code);
	}

	public static function reflectionInternal($message, $code = 0)
	{
		return new \ReflectionException($message, $code);
	}
}