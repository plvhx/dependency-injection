<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace Experiments\DependencyInjection\Fixtures;

class Dosen
{
	/**
	 * @var BaseInstance
	 */
	private $base;

	public function __construct(Base $base)
	{
		$this->base = $base;
	}

	public function __toString()
	{
		return $this->base->unify();
	}
}