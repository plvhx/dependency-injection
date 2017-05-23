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

	public function setFirstName($firstName)
	{
		$this->base->setFirstName($firstName);
	}

	public function setMiddleName($middleName)
	{
		$this->base->setMiddleName($middleName);
	}

	public function setLastName($lastName)
	{
		$this->base->setLastName($lastName);
	}
	
	public function __toString()
	{
		return $this->base->unify();
	}
}