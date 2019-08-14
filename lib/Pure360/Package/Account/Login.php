<?php

/**
 * Access to the login object
 *
 * @package account
 * @subpackage facade
 *
 */
class Pure360_Package_Account_Login extends Pure360_Entity
{

	public $__FACADE = "bus_facade_account_login";

	/**
	 * Load the bean that is associated with this facade.  The input parameters will be used to populate
	 * the bean key, and this bean key will be used to load the bean.
	 * 
	 * Overrides the default load behaviour as only one login object can exist.
	 * 
	 * @return array An instance of bus_entity_account_login.
	 */
	public function _load($inputData)
	{
		return parent::_load($inputData);
	}

	/**
	 * Change the licence agreement flag to be accepted.
	 * 
	 * @return array The bean id of the instance for use on the page.
	 */
	public function acceptLicence()
	{
		$outputData = $this->call("acceptLicence", null, null);

		return $outputData;
	}

	/**
	 * Call this to accept the blank templates on this account
	 */
	public function acceptBlankTemplate()
	{
		$outputData = $this->call("acceptBlankTemplate", null, null);

		return $outputData;
	}

}
