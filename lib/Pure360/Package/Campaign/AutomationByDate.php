<?php

/**
 * Facade to manage date automations.  To search across all automations of all types, please refer to the
 * automations bean.
 *
 * @package campaign
 * @subpackage facade
 *
 * @author	Stewart Waller	<stewart.waller@pure360.com>
 */
class Pure360_Package_Campaign_AutomationByDate extends Pure360_Entity
{

	public $__FACADE = "bus_facade_campaign_automationByDate";

	/**
	 * Call this to add a new rule item to the existing set and to return default values for the rule
	 * 
	 * @param	$beanId BeanId of bus_entity_campaign_automationByDate.
	 * @param	$dateRuleId	integer	Id of the rule to insert in front of (empty to append).
	 * @return	$outputData	Object	An instance of bus_entity_campaign_automationByDate.
	 */
	public function createNewDateRule($beanId, $dateRuleId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;
		$entityInput["dateRuleId"] = $dateRuleId;

		$outputData = $this->call("createNewDateRule", $entityInput, null);

		return $outputData;
	}

	/**
	 * Call this to remove a specific rule item from the existing set
	 * 
	 * @param	$beanId BeanId of bus_entity_campaign_automationByDate.
	 * @param	$dateRuleId	integer	Id of the rule to remove.
	 * @return	$outputData	Object	An instance of bus_entity_campaign_automationByDate.
	 */
	public function removeDateRule($beanId, $dateRuleId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;
		$entityInput["dateRuleId"] = $dateRuleId;

		$outputData = $this->call("removeDateRule", $entityInput, null);

		return $outputData;
	}

}
