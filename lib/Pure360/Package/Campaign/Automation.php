<?php

/**
 * Facade into all automation (header only) processes.  Please refer to the specific automation bean if you
 * need data processing functionality.  The header level automation bean is for searching across all automations
 * only
 *
 * @package campaign
 * @subpackage facade
 *
 */
class Pure360_Package_Campaign_Automation extends Pure360_Entity
{

	public $__FACADE = "bus_facade_campaign_automation";

	/**
	 * Toggle the active/inactive state of an automation
	 * 
	 * @param string $automationId Unique identifier for this automation.
	 * @param string $approvalStatus 
	 * @return array An instance of bean_facadeOutput.
	 */
	public function toggleStatus($automationId, $approvalStatus)
	{
		$entityInput = array();
		$entityInput["automationId"] = $automationId;
		$entityInput["approvalStatus"] = $approvalStatus;

		$outputData = $this->call("toggleStatus", $entityInput, null);

		return $outputData;
	}

}
