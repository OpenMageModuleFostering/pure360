<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 */
class Pure360_List_AjaxController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Save / Update list from request parameters
	 */
	public function saveListAction()
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$listId		= Mage::app()->getRequest()->getParam('listId', null);
		$listName	= Mage::app()->getRequest()->getParam('listName', null);
		$oldName	= null;
		$scope		= Mage::app()->getRequest()->getParam('scope', null);
		$scopeId	= Mage::app()->getRequest()->getParam('scopeId', null);
		$replace	= Mage::app()->getRequest()->getParam('replace', false);
				
		try
		{
			// Create timestamp for created/updated
			$now = time();
			
			// Create flag for new Mage list
			$newlist = false;
			
			/* @var $list Pure360_List_Model_List */
			$list = Mage::getModel('pure360_list/list')->load($listId);

			// Save old name
			$oldName = $list->getListName();
			
			// Perform Validation for new lists
			if ($list->isObjectNew())
			{
				// New list needs createdDt
				$list->setCreatedAt($now);

				$newlist = true;
			}
			
			// Set default list data from request parameters
			$data = array(
				'scope' => $scope,
				'scope_id' => $scopeId,
				'list_id' => ($listId == 0 ? null : $listId),
				'list_name' => $listName,
				'list_filter' => Mage::app()->getRequest()->getParam('listFilter'),
				'success_tracking_enabled' => Mage::app()->getRequest()->getParam('successTrackingEnabled'),
				'success_tracking_token' => Mage::app()->getRequest()->getParam('successTrackingToken'),
				'double_optin_enabled' => Mage::app()->getRequest()->getParam('doubleOptinEnabled'),
				'list_data_fields' => explode(",", Mage::app()->getRequest()->getParam('listDataFields')),
				'list_address_fields' => explode(",", Mage::app()->getRequest()->getParam('listAddressFields')),
				'list_sales_fields' => explode(",", Mage::app()->getRequest()->getParam('listSalesFields')),
				'list_groups' => explode(",", Mage::app()->getRequest()->getParam('listGroups')),
				'list_segments' => explode(",", Mage::app()->getRequest()->getParam('listSegments'))
			);
			
			$list->setData($data);
			$listFields	= array_filter(array_merge($data['list_data_fields'], $data['list_address_fields'], $data['list_sales_fields']));
			$listCheck	= $this->listCheck($list, ($newlist? $listName: $oldName), $listFields);
			
			// Check list exists
			if ((($listCheck === 'DIFFERENT') || ($listCheck === 'EXISTS' && $newlist)) && !$replace)
			{
				// Send response
				Mage::helper('pure360_common/ajax')->sendResponse($listCheck);
			
			} else
			{
				// Always set updatedDt
				$list->setUpdatedAt($now);

				// Try to save list
				$list->save();

				// Reload list to refresh fields
				$list->load($list->getId());

				switch($listCheck)
				{
					case 'DIFFERENT' :
					{
						if(!$newlist && $listName !== $oldName)
						{
							// Try to Update list in Response before replace/append
							$this->updateList($oldName, $list);
						}
					}
					case 'NEW' :
					{
						// Try to Create/Replace list in Response
						$this->createAppendReplaceList($list);
						
						// Set to resync
						$this->resetSync($list);
						
						// Set list status
						$list->setListStatus(Pure360_List_Model_List::LIST_STATUS_PENDING);
						break;
					}
					default :
					{
						// Try to Update list in Response
						$this->updateList($oldName, $list);
						
						if($newlist)
						{
							// Set to resync
							$this->resetSync($list);
							
							// Set list status
							$list->setListStatus(Pure360_List_Model_List::LIST_STATUS_PENDING);
						}

						break;
					}
				}
				
				// Create job
				$this->createJob($list->getId(), $list->getListName(), $list->getScope(), $list->getScopeId());

				// Try to save list again
				$list->save();

				// Reload list to refresh fields
				$list->load($list->getId());
				
				Mage::getSingleton('adminhtml/session')->setPure360CurrentList($list);
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The list was successfully saved'));

				// Send response
				Mage::helper('pure360_common/ajax')->sendResponse('OK');
			}
		} catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error while saving your list: ' . $e->getMessage()));

			Mage::helper('pure360_list')->writeError($e->getTraceAsString());

			Mage::helper('pure360_common/ajax')->sendException($e);
		}

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * Resync a list from request parameters
	 */
	public function resyncListAction()
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$listId = Mage::app()->getRequest()->getParam('listId', null);

		try
		{
			/* @var $list Pure360_List_Model_List */
			$list = Mage::getModel('pure360_list/list')->load($listId);

			// Try to Force Replace list in Response
			$this->createAppendReplaceList($list, true);
			
			// Reset to unsynced
			$this->resetSync($list);
					
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The list was successfully marked for resync'));
			
		} catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error while resyncing your list'));

			Mage::helper('pure360_list')->writeError($e->getTraceAsString());

			Mage::helper('pure360_common/ajax')->sendException($e);
		}

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * Remove a list from request parameters
	 */
	public function removeListAction()
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$listId = Mage::app()->getRequest()->getParam('listId', null);

		try
		{
			/* @var $list Pure360_List_Model_List */
			$list = Mage::getModel('pure360_list/list')->load($listId);

			// Reset to unsynced
			$this->resetSync($list);
			
			// Delete list
			$list->delete();

			// Remove job
			$this->removeJob($list->getId());

			Mage::getSingleton('adminhtml/session')->setPure360CurrentList(null);

			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The list was successfully removed'));
			
		} catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error while removing your list'));

			Mage::helper('pure360_list')->writeError($e->getTraceAsString());

			Mage::helper('pure360_common/ajax')->sendException($e);
		}

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * Create a job
	 * @param integer $listId
	 * @param string $listName
	 */
	private function createJob($listId, $listName, $scope, $scopeId)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		/* @var $job Pure360_Cron_Model_Job */
		$job = Mage::getModel('pure360_cron/job')->getCollection()
			->addFieldToFilter('job_code', Pure360_List_Job_Sync::JOB_CODE)
			->addFieldToFilter('job_data', $listId)
			->getFirstItem();

		$job->setData('job_code', Pure360_List_Job_Sync::JOB_CODE);
		$job->setData('job_data', $listId);
		$job->setData('module', Pure360_List_Job_Sync::MODULE);
		$job->setData('scope', $scope);
		$job->setData('scope_id', $scopeId);
		$job->setData('details', $listName);
		$job->setData('paused', 0);
		$job->setData('forced', 0);
		$job->setData('status', Mage_Cron_Model_Schedule::STATUS_PENDING);
		$job->setData('message', null);
		$job->setScheduledAt(null);
		$job->setCreatedAt(time());
		$job->save();

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * Remove a job
	 * @param integer $listId
	 */
	private function removeJob($listId)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		/* @var $job Pure360_Cron_Model_Job */
		$job = Mage::getModel('pure360_cron/job')->getCollection()
			->addFieldToFilter('job_code', Pure360_List_Job_Sync::JOB_CODE)
			->addFieldToFilter('job_data', $listId)
			->getFirstItem();

		$job->delete();

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * Creates temp list in Response ready for sync
	 *
	 * @param Pure360_List_Model_List $list
	 */
	private function listCheck(Pure360_List_Model_List $list, $listName, $listFields)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		$listCheck = null;

		try
		{
			// Get scope
			$scope		= $list->getScope();
			$scopeId	= $list->getScopeId();

			// Get API credentials
			$filter		= ($scope === 'default' ? 'default_' : $scope . '_');
			$url		= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings/api_url', $scope, $scopeId);
			$username	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings_marketing/username', $scope, $scopeId);
			$password	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings_marketing/password', $scope, $scopeId);

			/* @var $api Pure360_List_Helper_Api */
			$api		= Mage::helper('pure360_list/api');

			/* @var	$client Pure360_Session */
			$client		= $api->getClient($url, $username, $password);
			
			// Check list
			$listCheck	= $api->listCheck($client, $listName, $listFields);

			// Logout
			$client->logout();
			
		} catch (Pure360_Exception_ValidationException $e)
		{
			Mage::helper('pure360_list')->writeError($e->getTraceAsString());
		}

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');

		return $listCheck;
	}

	/**
	 * Creates temp list in Response ready for sync
	 *
	 * @param Pure360_List_Model_List $list
	 */
	private function createAppendReplaceList(Pure360_List_Model_List $list, $forceReplace = false)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		try
		{
			// Get scope
			$scope		= $list->getScope();
			$scopeId	= $list->getScopeId();

			// Get API credentials
			$filter		= ($scope === 'default' ? 'default_' : $scope . '_');
			$url		= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings/api_url', $scope, $scopeId);
			$username	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings_marketing/username', $scope, $scopeId);
			$password	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings_marketing/password', $scope, $scopeId);

			/* @var $api Pure360_List_Helper_Api */
			$api		= Mage::helper('pure360_list/api');

			/* @var	$client Pure360_Session */
			$client		= $api->getClient($url, $username, $password);
			$context	= $client->getContext();

			// Setup file transfer
			$chunkCount		= 1;
			$chunkId		= 1;
			$chunkData		= "\nnobody@pure360.com\n";
			$fileCategory	= "PAINT";
			$fileName		= $context['beanId'];
			$listName		= $list->getListName();
			$listFields		= $list->getListFields();

			// Create file
			$api->createFile($client, $fileCategory, $fileName, $chunkCount, "Y");

			// Upload file chunk
			$api->uploadFileChunk($client, $fileCategory, $fileName, $chunkId, $chunkData, "plain");

			// Create/Replace list		
			$api->createAppendReplaceList($client, $listName, $listFields, $forceReplace);

			// Load file data onto list
			$api->loadFileData($client, $fileCategory, $fileName, $listName);

			// Finally perform a logout
			$client->logout();
			
		} catch (Pure360_Exception_ValidationException $e)
		{
			Mage::helper('pure360_list')->writeError($e->getTraceAsString());

			foreach ($e->getErrors() as $key => $value)
			{
				if (strstr($value, 'name is already pending on the list upload queue'))
				{
					Mage::getSingleton('adminhtml/session')->addError('A list with this name is already pending on the list upload queue. Please try again later.');
				}
				else
				{
					Mage::getSingleton('adminhtml/session')->addError($key . ': ' .$value);
				}
			}
			
			throw($e);
		}

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}
	/**
	 * Creates temp list in Response ready for sync
	 *
	 * @param string $listName
	 * @param Pure360_List_Model_List $list
	 */
	private function updateList($listName, Pure360_List_Model_List $list)
	{
		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');

		try
		{
			// Get scope
			$scope		= $list->getScope();
			$scopeId	= $list->getScopeId();

			// Get API credentials
			$filter		= ($scope === 'default' ? 'default_' : $scope . '_');
			$url		= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings/api_url', $scope, $scopeId);
			$username	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings_marketing/username', $scope, $scopeId);
			$password	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $filter . 'settings_marketing/password', $scope, $scopeId);

			/* @var $api Pure360_List_Helper_Api */
			$api		= Mage::helper('pure360_list/api');

			/* @var	$client Pure360_Session */
			$client		= $api->getClient($url, $username, $password);

			// Create file
			$api->updateList($client, $listName, $list);

			// Finally perform a logout
			$client->logout();
			
		} catch (Pure360_Exception_ValidationException $e)
		{
			Mage::helper('pure360_list')->writeError($e->getTraceAsString());

			foreach ($e->getErrors() as $key => $value)
			{
				Mage::getSingleton('adminhtml/session')->addError($key . ': ' .$value);
			}
			
			throw($e);
		}

		Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
	}

	private function resetSync(&$list)
	{
		$resource = Mage::getSingleton('core/resource');

		// Get handle for write
		$write = $resource->getConnection('core_write');

		// Get storeIds for list
		$storeIds = Mage::helper('pure360_common')->getStoreIdsForScope($list->getScope(), $list->getScopeId());

		// Clean Subscriber Sync Statuses.
		$newsletterSubscriberTable	= $resource->getTableName('newsletter_subscriber');

		$sql = "UPDATE $newsletterSubscriberTable
			SET pure360_sync_status = 0
			WHERE store_id IN(" . implode(',', $storeIds) . ")";

		$write->query($sql);

		// Clean Customer Sync Statuses.
		$model = Mage::getModel('customer/entity_setup', 'core_setup');

		$customerEntityTable	= $resource->getTableName('customer_entity');
		$customerEntityIntTable = $resource->getTableName('customer_entity_int');
		$attribute				= 'pure360_sync_status';
		$attributeId			= $model->getAttributeId('customer', $attribute);

		$sql = "DELETE at_pure360_sync_status FROM $customerEntityTable AS e
				LEFT JOIN $customerEntityIntTable AS at_pure360_sync_status ON (at_pure360_sync_status.entity_id = e.entity_id)  
				WHERE (at_pure360_sync_status.attribute_id = $attributeId) 
				AND e.store_id IN(" . implode(',', $storeIds) . ")";

		$write->query($sql);

		$list->setListStatus(Pure360_List_Model_List::LIST_STATUS_PENDING);
		
		$list->save();
	}
	
}
