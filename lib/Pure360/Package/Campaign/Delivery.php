<?php

/**
 * Facade into all delivery scheduling  processes
 *
 * @package campaign
 * @subpackage facade
 *
 */
class Pure360_Package_Campaign_Delivery extends Pure360_Entity
{

	public $__FACADE = "bus_facade_campaign_delivery";

	/**
	 * Custom process to pause a currently running delivery.  This sets the correct 
	 * status on the delivery record and updates any part processed recipients.
	 *
	 * @param	$beanId
	 * @param	$deliveryStatus
	 * @param	$allowPausedSlsDeliveryInd
	 */
	public function updateStatus($beanId, $deliveryStatus, $allowPausedSlsDeliveryInd)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;
		$entityInput["deliveryStatus"] = $deliveryStatus;
		$entityInput["allowPausedSlsDeliveryInd"] = $allowPausedSlsDeliveryInd;

		$outputData = $this->call("updateStatus", $entityInput, null);

		return $outputData;
	}

}
