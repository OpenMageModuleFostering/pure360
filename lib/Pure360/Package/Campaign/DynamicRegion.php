<?php

/**
 * Facade into all dynamic region editing processes
 *
 * @package campaign
 * @subpackage facade
 *
 */
class Pure360_Package_Campaign_DynamicRegion extends Pure360_Entity
{

	public $__FACADE = "bus_facade_campaign_dynamicRegion";

	/**
	 * When creating a new dynamic region, we must know the bean id of the parent bean.  Otherwise
	 * the rest of the process is the same
	 */
	public function create($parentBeanId)
	{
		$entityInput = array();
		$entityInput["parentBeanId"] = $parentBeanId;

		$outputData = $this->call("create", $entityInput, null);

		return $outputData;
	}

	/**
	 * "process=remove"
	 *
	 * Override the default function and restrict to changing the status of the bean.  The save will
	 * only occur when the email is saved
	 */
	public function remove($beanId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$outputData = $this->call("remove", $entityInput, null);

		return $outputData;
	}

}
