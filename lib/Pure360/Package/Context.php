<?php

/**
 * Description of Context
 *
 * @author stewart.waller
 */
class Pure360_Package_Context extends Pure360_Package
{
	/**
	 * @var \Pure360\Paint\Package\Context\Context
	 */
	public $context;

	/**
	 * @var \Pure360\Paint\Package\Context\Preference
	 */
	public $preference;

	public function __construct($client)
	{
		$this->context = new Pure360_Package_Context_Context($client);
		$this->preference = new Pure360_Package_Context_Preference($client);
	}

}
