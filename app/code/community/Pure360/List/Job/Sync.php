<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_List_Job_Sync extends Pure360_Cron_Job_Abstract
{
	const JOB_CODE = 'PURE360_LIST_SYNC';

	const MODULE = 'PURE360_LIST';

	const CHUNK_SIZE = 1000;

	private $max_sync_size = 0;

	private $client = null;

	private $stores = array();

	private $websites = array();

	private $chunkData = '';

	public function process()
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		if(Mage::helper('pure360_common')->isEnterprise())
		{
			// Flag Segment Change
			$this->flagSegmentChanges();

			// Copy Segment Data
			$this->copySegmentData();
		}

		// Set job properties
		$scope		= $this->_data->getScope();
		$scopeId	= $this->_data->getScopeId();
		$listId		= $this->_data->getJobData();

		// Get API credentials
		$filter		= ($scope === 'default' ? 'default_' : $scope . '_');
		$url		= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings/api_url', $scope, $scopeId);
		$username	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings_marketing/username', $scope, $scopeId);
		$password	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings_marketing/password', $scope, $scopeId);
	
		// Set max page size
		$this->max_sync_size = Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/max_sync_size', 'default', 0);		
		
		// Set client
		$this->client = Mage::helper('pure360_common/api')->getClient($url, $username, $password);

		// Load list
		$list = Mage::getModel('pure360_list/list')->load($listId);
		
		// Set status to syncing
		$list->rows = 0;
		$list->setListStatus(Pure360_List_Model_List::LIST_STATUS_SYNCING);
		$list->save();
	
		// Export all Customers first
		$this->exportCustomerList($list);

		// Export all Subscribers next
		$this->exportSubscriberList($list);

		// Sync List
		if($list->rows > 0)
		{
			$this->uploadList($list);
		}

		// Set status to synced
		if($list->rows < $this->max_sync_size)
		{
			$list->setListStatus(Pure360_List_Model_List::LIST_STATUS_SYNCED);
			$list->save();
		}

		// Finally perform a logout
		$this->client->logout();

		// Update message with rows uploaded
		$this->_data->setData('message', $list->rows . ' rows uploaded ');

		// Save list data
		$this->_data->save();

		// Set sync status
		$this->syncList($list);

		// Cleanup temp files
		$this->cleanup($list);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	private function flagSegmentChanges()
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$resource = Mage::getSingleton('core/resource');

		// Get handle for write
		$read = $resource->getConnection('core_read');

		// Get handle for write
		$write = $resource->getConnection('core_write');

		$newSegmentsSql = "SELECT DISTINCT ecs.customer_id
			FROM enterprise_customersegment_customer ecs 
				LEFT JOIN pure360_list_customer_segments pcs ON 
					pcs.segment_id = ecs.segment_id AND
					pcs.customer_id = ecs.customer_id AND
					pcs.website_id = ecs.website_id
				WHERE pcs.customer_id IS NULL;";
		
		$oldSegmentsSql = "SELECT DISTINCT pcs.customer_id
			FROM pure360_list_customer_segments pcs 
				LEFT JOIN enterprise_customersegment_customer ecs ON 
					pcs.segment_id = ecs.segment_id AND
					pcs.customer_id = ecs.customer_id AND
					pcs.website_id = ecs.website_id
				WHERE ecs.customer_id IS NULL;";
		
		$diffSegmentsSql = "SELECT customer_id, count(*) cnt
			FROM (
					SELECT ecs.segment_id, ecs.customer_id, ecs.website_id, ecs.updated_date
					FROM enterprise_customersegment_customer ecs
				UNION
					SELECT pcs.segment_id, pcs.customer_id, pcs.website_id, pcs.updated_date
					FROM pure360_list_customer_segments pcs
			) pd
			GROUP BY segment_id, customer_id, website_id
			HAVING cnt > 1;";
		
		$queries = array( $newSegmentsSql, $oldSegmentsSql, $diffSegmentsSql);
		
		$model = Mage::getModel('customer/entity_setup', 'core_setup');

		// Update Sync Statuses.
		$customerEntityTable	= $resource->getTableName('customer_entity');
		$customerEntityIntTable = $resource->getTableName('customer_entity_int');
		$attribute				= 'pure360_sync_status';
		$attributeId			= $model->getAttributeId('customer', $attribute);
		
		foreach($queries as $query)
		{
			$ids = array();
			$count= 0;
			$stmt = $read->query($query);
			while ($row = $stmt->fetch()) {
				$ids[] = $row['customer_id'];
				$count++;
				if ($count>1000) {
					$count = 0;

					$sql = "INSERT INTO $customerEntityIntTable(entity_type_id, attribute_id, entity_id, value) 
							SELECT 1, $attributeId, e.entity_id, 0 FROM $customerEntityTable AS e 
							WHERE e.entity_id IN(" . implode(',', $ids) . ") 
							ON DUPLICATE KEY UPDATE value=0";

					$write->query($sql);

					$ids = array();
				}
			}
			if ($count>0) {
				$sql = "INSERT INTO $customerEntityIntTable(entity_type_id, attribute_id, entity_id, value) 
						SELECT 1, $attributeId, e.entity_id, 0 FROM $customerEntityTable AS e 
						WHERE e.entity_id IN(" . implode(',', $ids) . ") 
						ON DUPLICATE KEY UPDATE value=0";

				$write->query($sql);
			}
		}
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}
	
	private function copySegmentData()
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$resource = Mage::getSingleton('core/resource');

		// Get handle for read
		$read = $resource->getConnection('core_read');
		$readTable = $resource->getTableName('enterprise_customersegment_customer');
    
		// Get handle for write
		$write = $resource->getConnection('core_write');
	   	$writeTable = $resource->getTableName('pure360_list_customer_segments');
	
		// Clean old table
		$write->query("DELETE FROM $writeTable");

        $data = array();
        $count= 0;
        $stmt = $read->query("SELECT * FROM $readTable");
        while ($row = $stmt->fetch()) {
            $data[] = $row;
            $count++;
            if ($count>1000) {
                $count = 0;
                $write->insertMultiple($writeTable, $data);
                $data = array();
            }
        }
        if ($count>0) {
            $write->insertMultiple($writeTable, $data);
        }
		
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}
	
	private function exportCustomerList(&$list)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$fileSlug = 'customer_batch_' . date('d-M-Y_hms') . '_';
		$list->fileSlug = $fileSlug;
		$list->rows = 0;

		$file_ext = '.csv';
		$batchNum = 1;
		$batchSize = self::CHUNK_SIZE;
		$firstEmail = null;

		do
		{
			$time_start = microtime(true);
			$currentBatch = $this->getCustomerBatch($list, $batchSize, $batchNum);
			$condition = count($currentBatch);
			$filePath = Mage::helper('pure360_common/file')->getFilePath($list->fileSlug . $batchNum . $file_ext);
			$firstRow = reset($currentBatch);

			if(empty($firstRow) || $firstEmail == $firstRow->getEmail())
			{
				break;
				
			} else
			{
				$firstEmail = $firstRow->getEmail();
			}

			foreach($currentBatch as $customer)
			{
				if($list->rows < $this->max_sync_size)
				{	
					$data = $this->getCustomerData($customer, $list);
					Mage::helper('pure360_common/file')->outputCSV($filePath, $data);
					$list->rows++;
					
				} else
				{
					break;
				}
			}

			$time_end = microtime(true);
			$time_in_seconds = $time_end - $time_start;

			Mage::helper('pure360_list')->writeDebug('Exported Batch ' . $batchNum . ' in ' . $time_in_seconds . ' seconds - (' . memory_get_usage() . ') to ' . $filePath);

			$batchNum++;

			unset($currentBatch);
			
		} while($condition == $batchSize && $list->rows < $this->max_sync_size);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	private function getCustomerBatch($list, $batchSize = 0, $batchNum = 1)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$customers = array();

		// Configure Collection
		$collection = Mage::getResourceModel('customer/customer_collection');

		// Add Joins
		$collection
				->joinTable('newsletter/subscriber', 'subscriber_email=email', array('subscription_date' => 'subscription_date', 'subscriber_status' => 'subscriber_status'), null, 'left')
		;

		// Add Default Select
		$collection
				->addAttributeToSelect('email')
				->addAttributeToSelect('store_id')
				->addAttributeToSelect('website_id')
				->addAttributeToSelect('group_id')
				->addAttributeToSelect('subscriber_status')
				->addAttributeToSelect('subscription_date')
		;

		// Add Custom Select
		foreach($list->getListFields() as $field)
		{
			switch($field['field_type'])
			{
				case 1:
					$collection->addAttributeToSelect($field['field_value']);
					break;
				case 2:
					$collection->joinAttribute($field['field_value'], 'customer_address/' . $field['field_value'], 'default_billing', null, 'left');
					break;
			}
		}

		// Add sync status join attribute
		$collection->joinAttribute('pure360_sync_status', 'customer/pure360_sync_status', 'entity_id', null, 'left');

		// Get storeIds for list
		$storeIds = Mage::helper('pure360_common')->getStoreIdsForScope($list->getScope(), $list->getScopeId());
		
		$collection->addAttributeToFilter('store_id', array('in' => $storeIds));
		
		// And filter by subscriber_status = 1
		$collection->addAttributeToFilter('subscriber_status', array('eq' => Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED));

		// And filter by sync status = 0
		$collection->addAttributeToFilter('pure360_sync_status', array(array('eq' => 0), array('null' => null)));

		// And email filter
		if($list->getListFilter())
		{
			$collection->addAttributeToFilter('email', array('like' => '%' . $list->getListFilter() . '%'));
		}
		
		// Add paging for batch operation
		$collection->setCurPage($batchNum)->setPageSize($batchSize);

		// Create a customer object for each item in collection
		foreach($collection as $customer)
		{
			$customers[] = $customer;
		}

		unset($collection);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return $customers;
	}

	private function getCustomerData(Varien_Object $customer, $list)
	{
		$salesFields = Mage::helper('pure360_list')->getSalesFields($list);

		$salesData = empty($salesFields) ? array() : Mage::helper('pure360_list')->getSalesData($customer, $salesFields);

		$groupData = array('customer_group' =>
			Mage::helper('pure360_common')->arrayToCsv(
					Mage::helper('pure360_list')->getCustomerGroupData($customer)));

		$segmentData = array('customer_segments' =>
			Mage::helper('pure360_common')->arrayToCsv(
					Mage::helper('pure360_list')->getCustomerSegmentData($customer)));
		
		$customerData = array_merge($customer->toArray(), $salesData, $groupData, $segmentData);
		
		// Get date key lookup:
		$dateKeyLookup = Mage::helper('pure360_list')->getDateKeyLookup();
		
		$dataToSend = array();
	
		foreach(Mage::helper('pure360_list')->getListKeys($list) as $key)
		{
			$val = '';
			
			if(isset($customerData[$key]))
			{
				$val = $customerData[$key];
				if(!empty($val))
				{
					if(in_array($key, $dateKeyLookup))
					{
						// Format to pure360 list date
						$val = Mage::helper('pure360_list')->toDate($val);					
					}
				}
			}
			$dataToSend[] = $val;
		}

		// Get Website Name
		$websiteId = $customer->getWebsiteId();

		if(!isset($this->websites[$websiteId]))
		{
			$website = Mage::getModel('core/website')->load($websiteId);
			$this->websites[$websiteId] = $website;
		}

		$website = $this->websites[$websiteId];

		// Get Store Name
		$storeId = $customer->getStoreId();

		if(!isset($this->stores[$storeId]))
		{
			$store = Mage::getModel('core/store')->load($storeId);
			$this->stores[$storeId] = $store;
		}

		$store = $this->stores[$storeId];

		$data = array_merge(
					array(	'email'				=> $customer->getEmail(),
							'store'				=> $store->getName(),
							'website'			=> $website->getName(),
							'subscription_date' => empty($customerData['subscription_date'])? 
								'' : Mage::helper('pure360_list')->toDate($customerData['subscription_date']),
							'customer_id'		=> $customer->getId()),
					$dataToSend
				);
	
		return $data;
	}

	private function exportSubscriberList(&$list)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$fileSlug = $list->fileSlug . '_subscriber_';

		$file_ext = '.csv';
		$batchNum = 1;
		$batchSize = self::CHUNK_SIZE;
		$firstEmail = null;

		do
		{
			$time_start = microtime(true);
			$currentBatch = $this->getSubscriberBatch($list, $batchSize, $batchNum);
			$condition = count($currentBatch);
			$filePath = Mage::helper('pure360_common/file')->getFilePath($fileSlug . $batchNum . $file_ext);

			$firstRow = reset($currentBatch);

			if(empty($firstRow) || $firstEmail == $firstRow->getSubscriberEmail())
			{
				break;
			} else
			{
				$firstEmail = $firstRow->getSubscriberEmail();
			}

			foreach($currentBatch as $subscriber)
			{
				if($list->rows < $this->max_sync_size)
				{
					Mage::helper('pure360_common/file')->outputCSV($filePath, $this->getSubscriberData($subscriber));
					$list->rows++;
				} else
				{
					break;
				}
			}

			$time_end = microtime(true);
			$time_in_seconds = $time_end - $time_start;

			Mage::helper('pure360_list')->writeDebug('Exported Subscriber Batch ' . $batchNum . ' in ' . $time_in_seconds . ' seconds - (' . memory_get_usage() . ') to ' . $filePath);

			$batchNum++;

			unset($currentBatch);
			
		} while($condition == $batchSize && $list->rows <= $this->max_sync_size);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	private function getSubscriberBatch($list, $batchSize = 0, $batchNum = 1)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$subscribers = array();

		// Get storeIds for list
		$storeIds = Mage::helper('pure360_common')->getStoreIdsForScope($list->getScope(), $list->getScopeId());
		
		// Configure Collection
		$collection = Mage::getModel('newsletter/subscriber')->getCollection()
				->addFieldToFilter('main_table.customer_id', array('eq' => 0))
				->addFieldToFilter('subscriber_status', array('eq' => Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED))
				->addStoreFilter($storeIds);

		// And filter by sync status = 0
		$collection->addFieldToFilter('pure360_sync_status', array(array('eq' => 0), array('null' => null)));

		// And email filter
		if($list->getListFilter())
		{
			$collection->addFieldToFilter('subscriber_email', array('like' => '%' . $list->getListFilter() . '%'));
		}
		
		// Add paging for batch operation
		$collection->setCurPage($batchNum)->setPageSize($batchSize);

		// Create a customer object for each item in collection
		foreach($collection as $subscriber)
		{
			$subscribers[] = $subscriber;
		}

		unset($collection);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return $subscribers;
	}

	private function getSubscriberData(Varien_Object $subscriber)
	{
		$storeId = $subscriber->getStoreId();

		if(!isset($this->stores[$storeId]))
		{
			$store = Mage::getModel('core/store')->load($storeId);
			$this->stores[$storeId] = $store;
		}

		$store = $this->stores[$storeId];

		$websiteId = $store->getWebsiteId();

		if(!isset($this->websites[$websiteId]))
		{
			$website = Mage::getModel('core/website')->load($websiteId);
			$this->websites[$websiteId] = $website;
		}

		$website = $this->websites[$websiteId];

		$subDate = $subscriber->getSubscriptionDate();

		$dataToSend = array('email' => $subscriber->getSubscriberEmail(),
			'store' => $store->getName(),
			'website' => $website->getName(),
			'subscription_date' => empty($subDate)? 
								'' : Mage::helper('pure360_list')->toDate($subDate));

		return $dataToSend;
	}

	private function uploadList(&$list)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		// Get list properties
		$listName	= $list->getListName();
		$listFields = $list->getListFields();
		$files = Mage::helper('pure360_common/file')->getFilenamesForSlug($list->fileSlug);

		/* @var $api Pure360_List_Helper_Api */
		$api = Mage::helper('pure360_list/api');

		// Setup file transfer
		$context = $this->client->getContext();
		$fileCategory = "PAINT";
		$fileName = $context['beanId'];
		$count = count($files);
		$page = 1;

		// Create file
		$api->createFile($this->client, $fileCategory, $fileName, $count, "Y");

		// Upload chunks
		foreach($files as $filename)
		{
			$chunkData = Mage::helper('pure360_common/file')->readFile($filename, true);
			$api->uploadFileChunk($this->client, $fileCategory, $fileName, $page, $chunkData, "base64");
			$page++;
		}

		reset($files);

		// Create/Append list		
		$api->createAppendReplaceList($this->client, $listName, $listFields);

		// Load file data onto list
		$api->loadFileData($this->client, $fileCategory, $fileName, $listName);

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return;
	}

	private function syncList($list)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$resource = Mage::getSingleton('core/resource');

		// Get handle for write
		$write = $resource->getConnection('core_write');

		// Get files
		$files = Mage::helper('pure360_common/file')->getFilenamesForSlug($list->fileSlug);

		$count = 0;
		
		// Mark customers as synced
		foreach($files as $filename)
		{
			$dataArray = Mage::helper('pure360_common/file')->readCsv($filename);

			if(strstr($filename, 'subscribe'))
			{
				$emails = array();

				foreach($dataArray as $dataRow)
				{
					if($count < $list->rows)
					{
						// Email should always be first column for subscriber
						$emails[] = $dataRow[0];
						$count++;
					} else
					{
						break;
					}
				}

				// Update Sync Statuses.
				$newsletterSubscriberTable	= $resource->getTableName('newsletter_subscriber');
				
				$sql = "UPDATE $newsletterSubscriberTable
					SET pure360_sync_status = 1
					WHERE subscriber_email IN('" . implode('\',\'', $emails) . "')";

				$write->query($sql);
				
			} else
			{
				$ids = array();

				foreach($dataArray as $dataRow)
				{
					if($count < $list->rows)
					{
						// Id should always be at fourth column for customer
						$ids[] = $dataRow[4];
						$count++;
					} else
					{
						break;
					}
				}

				$model = Mage::getModel('customer/entity_setup', 'core_setup');

				// Update Sync Statuses.
				$customerEntityTable	= $resource->getTableName('customer_entity');
				$customerEntityIntTable = $resource->getTableName('customer_entity_int');
				$attribute				= 'pure360_sync_status';
				$attributeId			= $model->getAttributeId('customer', $attribute);
				
				$sql = "DELETE at_pure360_sync_status FROM $customerEntityTable AS e
						LEFT JOIN $customerEntityIntTable AS at_pure360_sync_status ON (at_pure360_sync_status.entity_id = e.entity_id)  
						WHERE (at_pure360_sync_status.attribute_id = $attributeId) 
						AND e.entity_id IN(" . implode(',', $ids) . ")";

				$write->query($sql);
				
				$sql = "INSERT INTO $customerEntityIntTable(entity_type_id, attribute_id, entity_id, value)
						SELECT 1,$attributeId, e.entity_id, 1 FROM $customerEntityTable AS e
						WHERE e.entity_id IN(" . implode(',', $ids) . ")";

				$write->query($sql);
			}
			
			if($count >= $list->rows)
			{
				break;
			}
		}

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return;
	}

	private function cleanup($list)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');
		Mage::helper('pure360_common/file')->cleanFiles($list->fileSlug);
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

}