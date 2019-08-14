<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 */
class Pure360_List_Helper_Api extends Pure360_Common_Helper_Api
{

	const CHUNK_UPLOAD_RETRY_COUNT = 5;

	/**
	 * 
	 * @param Pure360_Session $client
	 * @param string $fileCategory
	 * @param string $fileName
	 * @param integer $chunkCount
	 * @param string $overwrite
	 * @return string
	 */
	public function createFile($client, $fileCategory, $fileName, $chunkCount, $overwrite = "Y")
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$result = $client->other->file->create($fileCategory, $fileName, $chunkCount, $overwrite);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return $result;
	}

	/**
	 * Uploads file chunk data
	 * 
	 * @param Pure360_Session $client
	 * @param string $fileCategory
	 * @param string $fileName
	 * @param integer $chunkId
	 * @param string $chunkData
	 * @param string $fileEncoding
	 * @return string
	 */
	public function uploadFileChunk($client, $fileCategory, $fileName, $chunkId, $chunkData, $fileEncoding = "plain")
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');
	
		$retries = self::CHUNK_UPLOAD_RETRY_COUNT;
		$result = null;
		$exception = null;

		do
		{
			try
			{
				$result = $client->other->file->upload($fileCategory, $fileName, $fileEncoding, $chunkId, $chunkData);
				
			} catch(Exception $e)
			{
				$exception = $e;
				sleep(3);
			}
		} while(is_null($result) && --$retries > 0);

		if(!is_null($result) && !is_null($exception))
		{
			throw $exception;
		}

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return $result;
	}

	/**
	 * Sets uploaded data fileName to list upload queue record
	 * 
	 * @param Pure360_Session $client
	 * @param string $fileCategory
	 * @param string $fileName
	 * @param string $listName
	 * @return boolean
	 */
	public function loadFileData($client, $fileCategory, $fileName, $listName)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		// Load file data
		$result = $client->campaign->marketingList->loadFileData($listName, $fileCategory, $fileName);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return $result;
	}

	/**
	 * Check list exists
	 *
	 * @param Pure360_Session $client
	 * @param string $listName
	 * @param array $listFields
	 * @return string NEW|EXISTS|DIFFERENT
	 */
	public function listCheck($client, $listName, $listFields)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$status = 'NEW';

		// Search to see if a lists already exists with this name
		$searchOutput = $client->campaign->marketingList->_search(array("listName" => $listName));

		// Does the list already exist?
		if(!empty($searchOutput))
		{
			for($index = 0; $index < count($searchOutput); $index++)
			{
				// Use the id data returned from the search to load the specific email
				$loadOutput = $client->campaign->marketingList->_load($searchOutput[$index]);

				if($loadOutput["listName"] == $listName)
				{
					$status = 'EXISTS';
					
					
					if(!empty($listFields))
					{
				
						// Add System Fields from Magento list
						$systemFields	= array(
							"store", "website", "subscription_date", "customer_id", "email"
							);
						
						// Get Magento Fields from list
						$mageFields = array();
						foreach($listFields as $listField)
						{
							$mageFields[$listField] = $listField;
						}
						
						// Get Pure Fields from Pure list
						$pureFields		= array();
						for($i = 1; $i <= 40; $i++)
						{
							if(isset($loadOutput['field'.$i.'Name']))
							{
								if(!empty($loadOutput['field'.$i.'Name']))
								{
									$pureFields[$loadOutput['field'.$i.'Name']] = $loadOutput['field'.$i.'Name'];
								}
							}
						}
						
						// Clean system fields
						foreach($systemFields as $systemField)
						{
							unset($mageFields[$systemField]);
							unset($pureFields[$systemField]);
						}
						
						// Get differences
						$differences = array_diff($mageFields, $pureFields);
						$differences = array_merge($differences, array_diff($pureFields, $mageFields));
					
						if(!empty($differences))
						{
							$status = 'DIFFERENT';
						}
					}
					
					break;
				}
			}
		}
		
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
		
		return $status;
	}
	
	/**
	 * Check list exists
	 *
	 * @param Pure360_Session $client
	 * @param string $listName
	 * @param array $listFields
	 * @return string NEW|EXISTS|DIFFERENT
	 */
	public function updateList($client, $listName, $list)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$listData	= array();
		$status		= null;
		
		// Search to see if a lists already exists with this name
		$searchOutput = $client->campaign->marketingList->_search(array("listName" => $listName));

		// Does the list already exist?
		if(!empty($searchOutput))
		{
			for($index = 0; $index < count($searchOutput); $index++)
			{
				// Use the id data returned from the search to load the specific list
				$loadOutput = $client->campaign->marketingList->_load($searchOutput[$index]);

				if($loadOutput["listName"] == $listName)
				{
					$status = 'EXISTS';
					$listData = $loadOutput;
					break;
				}
			}
		}

		if(!empty($listData))
		{
			$inputData = array();
			
			// Set settable properties
			$inputData['listName'] = $list->getListName();
			$inputData['listId'] = $listData['listId'];
			$inputData['beanId'] = $listData['beanId'];

			//update list
			$client->campaign->marketingList->_update($inputData);
			
			//store list
			$client->campaign->marketingList->_store($inputData);
		}
		
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
		
		return $status;
	}
	
	/**
	 * Creates create/append list upload record ready for file chunking
	 *
	 * @param Pure360_Session $client
	 * @param string $listName
	 * @param array $listFields
	 * @param boolean $forceReplace
	 * @return array
	 */
	public function createAppendReplaceList($client, $listName, $listFields, $forceReplace = false)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		// Calculate list fields
		$fieldNames = array('email', 'store', 'website', 'subscription_date', 'customer_id');

		foreach($listFields as $field)
		{
			$fieldNames[] = $field['field_value'];
		}

		$listCheck = $this->listCheck($client, $listName, $fieldNames);

		// Set list properties ready for replace
		$listInput = array(
			"listName" => $listName,
			"languageCode" => "en_GB.UTF-8",
			"uploadFileNotifyEmail" => "IGNORE",
			"uploadTransactionType" => ($listCheck === 'NEW' ? "CREATE" : 
				(( $listCheck === 'EXISTS' && !$forceReplace) ? "APPEND" : "REPLACE")),
			"uploadFileCategory" => "PAINT",
			"externalSystemKey" => "magento");

		$customFieldCount = 0;

		// Get date key lookup:
		$dateKeyLookup = Mage::helper('pure360_list')->getDateKeyLookup();
		
		// Set field names
		for($index = 0; ($index < count($fieldNames) & $customFieldCount <= 40); $index++)
		{
			$fieldName = $fieldNames[$index];

			switch($fieldName)
			{
				case "email":
					$listInput["emailCol"] = $index;
					break;

				case "mobile":
					$listInput["mobileCol"] = $index;
					break;

				case "signupDate":
					$listInput["signupDateCol"] = $index;
					break;

				default:
					$fieldColStr = "field" . $index . "Col";
					$fieldNameStr = "field" . $index . "Name";
					$fieldName = str_replace(' ', '_', $fieldName);

					$listInput[$fieldColStr] = $index;
					$listInput[$fieldNameStr] = $fieldName;

					if(in_array($fieldName, $dateKeyLookup))
					{
						$fieldTypeStr = "field" . $index . "DataType";
						$listInput[$fieldTypeStr] = 'date';
						$fieldFormatStr = "field" . $index . "DataFormat";
						$listInput[$fieldFormatStr] = 'yyyy-mm-dd';
					}
					
					$customFieldCount++;
					break;
			}
		}

		// Create/Replace list
		$createOutput = $client->campaign->marketingList->_create($listInput);
		$storeInput = array("beanId" => $createOutput["beanId"]);
		$result = $client->campaign->marketingList->_store($storeInput);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return $result;
	}

	/**
	 * Performs post-click tracking
	 */
	function clickTrack($client, $list, $eventData, $mailId)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$identity = $client->getIdentity();

		// Build domain
		$domain = $identity['customDomain'];
		if(empty($domain))
		{
			$domain = Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings', 'api_url');
		}
		//$domain = str_replace("http://", "", $domain);

		$successTrackingToken = $list->getSuccessTrackingToken();
		$successTrackingToken = empty($successTrackingToken) ? 'magento' : $successTrackingToken;

		$orderTotal = 0;

		foreach($eventData['order_ids'] as $order_id)
		{
			$order = Mage::getModel('sales/order')->load($order_id);
			$orderTotal += $order->getBaseGrandTotal();
		}

		$postfields = 'id=' . urlencode($mailId);
		$postfields .= '&type=' . urlencode($successTrackingToken);
		$postfields .= '&desc=' . urlencode($orderTotal);

		$curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $domain . '/_act/tracking.php');
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);

		$res = curl_exec($curl);

		curl_close($curl);
			
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

}
