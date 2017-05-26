<?php

namespace Experiments\DependencyInjection;

class InvokerAdapter implements InvokerAdapterInterface
{
	/**
	 * @var InvokerInterface
	 */
	private $invoker;

	public function __construct(InvokerInterface $invoker)
	{
		if (!($invoker instanceof InvokerInterface)) {
			throw Internal\Exception\ReflectionExceptionFactory::invalidArgument(
				sprintf("Parameter 1 of %s must be implements InvokerInterface.", __METHOD__)
			);
		}

		$this->invoker = $invoker;
	}

	/**
	 * {@inheritdoc}
	 */
	public function invoke()
	{
		return call_user_func_array([$this->invoker, 'invoke'], func_get_args());
	}

	/**
	 * {@inheritdoc}
	 */
	public function invokeArgs($args = [])
	{
		return $this->invoker->invokeArgs($args);
	}
}