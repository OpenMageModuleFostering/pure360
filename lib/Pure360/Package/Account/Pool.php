<?php

/**
 * Access to the pool object
 *
 * @package account
 * @subpackage facade
 *
 */
class Pure360_Package_Account_Pool extends Pure360_Entity
{

	public $__FACADE = "bus_facade_account_pool";

	/**
	 * Load the bean that is associated with this facade.  The input parameters will be used to populate
	 * the bean key, and this bean key will be used to load the bean.
	 * 
	 * Overrides the default load behaviour as only one pool object can exist.
	 * 
	 * @return array An instance of bus_entity_account_pool.
	 */
	public function _load($inputData)
	{
		return parent::_load($inputData);
	}

}
