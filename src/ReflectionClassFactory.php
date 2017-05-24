<?php

namespace Experiments\DependencyInjection;

class ReflectionClassFactory
{
	/**
	 * @var \ReflectionClass
	 */
	private $reflection;

	public function __construct($instance)
	{
		$this->reflection = new \ReflectionClass($instance);

		if (!($this->reflection instanceof \ReflectionClass)) {
			throw new \RuntimeException(
				"Unable to get instance from ReflectionClass."
			);
		}
	}

	public static function create($instance)
	{
		return new static($instance);
	}

	public function newInstance($args = null)
	{
		$args = (!is_array($args) ? func_get_args() : $args);

		return $this->reflection->newInstanceArgs($args);
	}

	public function getReflection()
	{
		return $this->reflection;
	}
}