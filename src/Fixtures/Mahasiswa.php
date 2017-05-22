<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace Experiments\DependencyInjection\Fixtures;

class Mahasiswa
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