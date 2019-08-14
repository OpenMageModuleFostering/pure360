<?php

/**
 * @package   Pure360\Email
 * @copyright 2013 Pure360.com
 */
class Pure360_Email_AjaxController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Save / Update messages from request parameters
	 */
	public function saveMessagesAction()
	{
		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - start');

		$templateIds	= Mage::app()->getRequest()->getParam('templateIds', null);
		$messageIds		= Mage::app()->getRequest()->getParam('messageIds', null);
		$scope			= Mage::app()->getRequest()->getParam('scope', null);
		$scopeId		= Mage::app()->getRequest()->getParam('scopeId', null);

		try
		{
			// Remove old
			$this->cleanTransactional($scope, $scopeId);
			
			// Update settings
			$mappings = array_combine(explode(',',$templateIds), explode(',',$messageIds));
			
			foreach($mappings as $templateId => $messageId)
			{
				$this->updateTransactional($scope, $scopeId, $templateId, $messageId);
			}
			
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The transactional message mappings were successfully saved'));

			// Send response
			Mage::helper('pure360_common/ajax')->sendResponse('OK');
			
		} catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error while saving your transactional messages'));
			
			Mage::helper('pure360_email')->writeError($e->getTraceAsString());
			
			Mage::helper('pure360_common/ajax')->sendException($e);
		}

		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * 
	 * @param type $scope
	 * @param type $scopeId
	 */
	private function cleanTransactional($scope, $scopeId)
	{
		/* @var $transactional Pure360_Email_Model_Transactional */
		$transactionals = Mage::getModel('pure360_email/transactional')->getCollection()
			->addFieldToFilter('scope', $scope)
			->addFieldToFilter('scope_id', $scopeId);
		
		foreach($transactionals as $transactional)
		{
				$transactional->delete();
		}
	}
	
	/**
	 * 
	 * @param type $scope
	 * @param type $scopeId
	 * @param type $messageId
	 * @param type $messageName
	 * @param type $type
	 */
	private function updateTransactional($scope, $scopeId, $templateId, $messageId)
	{
		// Create timestamp for created/updated
		$now = time();

		/* @var $transactional Pure360_Email_Model_Transactional */
		$transactional = Mage::getModel('pure360_email/transactional')->getCollection()
			->addFieldToFilter('scope', $scope)
			->addFieldToFilter('scope_id', $scopeId)
			->addFieldToFilter('template_id', $templateId)
			->getFirstItem();

		// Remove old
		if(!$transactional->isEmpty() && !$messageId)
		{
			$transactional->delete();
		}
		else if($messageId > 0)
		{
			if(!$transactional->getId())
			{
				$transactional = Mage::getModel('pure360_email/transactional')->load();
				$transactional->setCreatedAt($now);
			}
			
			$transactional->setTemplateId($templateId);
			$transactional->setMessageId($messageId);
			$transactional->setUpdatedAt($now);
			$transactional->save();
		}
			
	}
}
