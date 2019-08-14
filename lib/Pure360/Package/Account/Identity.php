<?php

/**
 * Access to the identity object.
 *
 * @package account
 * @subpackage facade
 *
 */
class Pure360_Package_Account_Identity extends Pure360_Entity
{

	public $__FACADE = "bus_facade_account_identity";

	/**
	 * Load the bean that is associated with this facade.  The input parameters will be used to populate
	 * the bean key, and this bean key will be used to load the bean.
	 * 
	 * Overrides the default load behaviour as only one identity object can exist.
	 * 
	 * @return array An instance of bus_entity_account_identity.
	 */
	public function _load($inputData)
	{
		return parent::_load($inputData);
	}

	/**
	 * Log an event to the specified log file.
	 * 
	 * @param string $eventDescription A description of the error.
	 * @param string $eventType The type of error.
	 * @return array An instance of bus_entity_account_identity.
	 */
	public function logEvent($eventDescription, $eventType)
	{
		$entityInput = array();
		$entityInput["eventDescription"] = $eventDescription;
		$entityInput["eventType"] = $eventType;

		$outputData = $this->call("logEvent", $entityInput, null);

		return $outputData;
	}

}
