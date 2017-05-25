<?php

namespace Experiments\DependencyInjection;

interface InvokerInterface
{
	public function invoke();

	public function invokeArgs($args = []);
}