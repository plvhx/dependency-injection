<?php

namespace Experiments\DependencyInjection;

interface InvokerInterface
{
	/**
	 * Executing function or \Closure with supplied arguments.
	 *
	 * @return mixed
	 */
	public function invoke();

	/**
	 * Executing function or \Closure with packed arguments.
	 *
	 * @return mixed
	 */
	public function invokeArgs($args = []);
}