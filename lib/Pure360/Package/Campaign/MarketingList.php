<?php

/**
 * Facade into all list editing processes
 *
 * @package campaign
 * @subpackage facade
 *
 * @author	Stewart Waller	<stewart.waller@pure360.com>
 */
class Pure360_Package_Campaign_MarketingList extends Pure360_Entity
{

	public $__FACADE = "bus_facade_campaign_list";

	/**
	 * Use this method to add file data to an existing list upload queue record.
	 * 
	 * @param	$beanId
	 * @param	$extSystemId
	 * @param	$extSystemType
	 * 
	 */
	public function loadExternalMeta($beanId, $extSystemId, $extSystemType)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$processInput = array();
		$processInput["extSystemId"] = $extSystemId;
		$processInput["extSystemType"] = $extSystemType;

		$outputData = $this->call("loadExternalMeta", $entityInput, $processInput);

		return $outputData;
	}

	/**
	 * Use this method to add file data to an existing list upload queue record.
	 * 
	 * @param type $listName
	 * @param type $uploadFileCategory
	 * @param type $uploadFileName
	 * @return type
	 */
	public function loadFileData($listName, $uploadFileCategory, $uploadFileName)
	{
		// Name of the list (from the DestinationData/ListName element in the queue item)
		$entityInput["listName"] = $listName;

		// Upload file category. The file must be uploaded first using the bus_facade_file method
		$entityInput["uploadFileCategory"] = $uploadFileCategory;

		// Upload file name. The file must be uploaded first using the bus_facade_file method
		$entityInput["uploadFileName"] = $uploadFileName;

		$outputData = $this->call("loadFileData", $entityInput, null);

		return $outputData;
	}
	
	/**
	 * Replace list from external system e.g. MS CRM Marketing List
	 * The method re-schedules a 'transferList' for an existing list
	 * 
	 * @param	$listName
	 * 
	 */
	public function replaceListExternal($listName)
	{
		$processInput = array();
		$processInput["listName"] = $listName;

		$outputData = $this->call("replaceListExternal", null, $processInput);

		return $outputData;
	}

}
