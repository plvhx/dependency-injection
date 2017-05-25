<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace Experiments\DependencyInjection\Internal;

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

	public function getConstant($name)
	{
		return $this->reflection->getConstant($name);
	}

	public function getConstants()
	{
		return $this->reflection->getConstants();
	}

	public function getConstructor()
	{
		return $this->reflection->getConstructor();
	}

	public function getDefaultProperties()
	{
		return $this->reflection->getDefaultProperties();
	}

	public function getDocComment()
	{
		return $this->reflection->getDocComment();
	}

	public function getEndLine()
	{
		return $this->reflection->getEndLine();
	}

	public function getExtension()
	{
		return $this->reflection->getExtension();
	}

	public function getExtensionName()
	{
		return $this->reflection->getExtensionName();
	}

	public function getFileName()
	{
		return $this->reflection->getFileName();
	}

	public function getInterfaceNames()
	{
		return $this->reflection->getInterfaceNames();
	}

	public function getInterfaces()
	{
		return $this->reflection->getInterfaces();
	}

	public function getMethod($name)
	{
		return $this->reflection->getMethod($name);
	}

	public function getMethods()
	{
		return $this->reflection->getMethods();
	}

	public function getModifiers()
	{
		return $this->reflection->getModifiers();
	}

	public function getName()
	{
		return $this->reflection->getName();
	}

	public function getNamespaceName()
	{
		return $this->reflection->getNamespaceName();
	}

	public function getParentClass()
	{
		return $this->reflection->getParentClass();
	}

	public function getProperties($filter = null)
	{
		return (!is_int($filter)
			? $this->reflection->getProperties()
			: $this->reflection->getProperties($filter));
	}

	public function getProperty($name)
	{
		return $this->reflection->getProperty($name);
	}

	public function getShortName()
	{
		return $this->reflection->getShortName();
	}

	public function getStartLine()
	{
		return $this->reflection->getStartLine();
	}

	public function getStaticProperties()
	{
		return $this->reflection->getStaticProperties();
	}

	public function getStaticPropertyValue($name, &$def_value = null)
	{
		return (!isset($def_value)
			? $this->reflection->getStaticPropertyValue($name)
			: $this->reflection->getStaticPropertyValue($name, $def_value));
	}

	public function getTraitAliases()
	{
		return $this->reflection->getTraitAliases();
	}

	public function getTraitNames()
	{
		return $this->reflection->getTraitNames();
	}

	public function getTraits()
	{
		return $this->reflection->getTraits();
	}

	public function hasConstant($name)
	{
		if (!is_string($name)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be a string.", __METHOD__)
			);
		}

		return $this->reflection->hasConstant($name);
	}

	public function hasMethod($name)
	{
		if (!is_string($name)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be a string.", __METHOD__)
			);
		}

		return $this->reflection->hasMethod($name);
	}

	public function hasProperty($name)
	{
		if (!is_string($name)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be a string.", __METHOD__)
			);
		}

		return $this->reflection->hasProperty($name);
	}

	public function implementsInterface($interface)
	{
		if (!is_string($interface)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be a string.", __METHOD__)
			);
		}

		return $this->reflection->implementsInterface($interface);
	}

	public function inNamespace()
	{
		return $this->reflection->inNamespace();
	}

	public function isAbstract()
	{
		return $this->reflection->isAbstract();
	}

	public function isAnonymous()
	{
		return $this->reflection->isAnonymous();
	}

	public function isCloneable()
	{
		return $this->reflection->isCloneable();
	}

	public function isFinal()
	{
		return $this->reflection->isFinal();
	}

	public function isInstance($object)
	{
		if (!is_object($object)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be an object.", __METHOD__)
			);
		}

		return $this->reflection->isInstance($object);
	}

	public function isInstantiable()
	{
		return $this->reflection->isInstantiable();
	}

	public function isInterface()
	{
		return $this->reflection->isInterface();
	}

	public function isInternal()
	{
		return $this->reflection->isInternal();
	}

	public function isIterateable()
	{
		return $this->reflection->isIterateable();
	}

	public function isSubclassOf($class)
	{
		if (!is_string($class)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be a string.", __METHOD__)
			);
		}

		return $this->reflection->isSubclassOf($class);
	}

	public function isTrait()
	{
		return $this->reflection->isTrait();
	}

	public function isUserDefined()
	{
		return $this->reflection->isUserDefined();
	}

	public function newInstance()
	{
		return call_user_func_array([$this->reflection, 'newInstance'], func_get_args());
	}

	public function newInstanceArgs($args)
	{
		if (!is_array($args)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 of %s must be an array.", __METHOD__)
			);
		}

		return $this->reflection->newInstanceArgs($args);
	}

	public function newInstanceWithoutConstructor()
	{
		return $this->reflection->newInstanceWithoutConstructor();
	}

	public function setStaticPropertyValue($name, $value)
	{
		if (!is_string($name) || !is_string($value)) {
			throw new \InvalidArgumentException(
				sprintf("Parameter 1 and 2 of %s must be a string.", __METHOD__)
			);
		}

		$this->reflection->setStaticPropertyValue($name, $value);
	}

	public function __toString()
	{
		return call_user_func([$this->reflection, '__toString']);
	}

	public function getReflection()
	{
		return $this->reflection;
	}
}