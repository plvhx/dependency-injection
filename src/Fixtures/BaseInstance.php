<?php

/**
 * (c) Paulus Gandung Prakosa (rvn.plvhx@gmail.com)
 */

namespace Experiments\DependencyInjection\Fixtures;

interface BaseInstance
{
	/**
	 * Set First Name.
	 *
	 * @return void
	 */
	public function setFirstName($firstName);

	/**
	 * Set Middle Name.
	 *
	 * @return void
	 */
	public function setMiddleName($middleName);

	/**
	 * Set Last Name.
	 *
	 * @return void
	 */
	public function setLastName($lastName);
}