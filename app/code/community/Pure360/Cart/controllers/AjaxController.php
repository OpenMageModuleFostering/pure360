<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 */
class Pure360_Cart_AjaxController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Save / Update trigger from request parameters
	 */
	private function clearCartsAction()
	{

	}
	
	/**
	 * Save / Update trigger from request parameters
	 */
	public function saveTriggersAction()
	{
		Mage::helper('pure360_cart')->writeDebug(__METHOD__ . ' - start');

		$scope		= Mage::app()->getRequest()->getParam('scope', null);
		$scopeId	= Mage::app()->getRequest()->getParam('scopeId', null);
		$enabling	= Mage::app()->getRequest()->getParam('enabling', null);

		try
		{
			// Create job
			$this->createJob($scope, $scopeId);

			// Send response
			Mage::helper('pure360_common/ajax')->sendResponse('SUCCESS');
			
		} catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error while saving your triggers: ' . $e->getMessage()));

			Mage::helper('pure360_cart')->writeError($e->getTraceAsString());

			Mage::helper('pure360_common/ajax')->sendException($e);
		}

		Mage::helper('pure360_cart')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * Create a job
	 * @param integer $scope
	 * @param string $scopeId
	 */
	private function createJob($scope, $scopeId)
	{
		Mage::helper('pure360_cart')->writeDebug(__METHOD__ . ' - start');

		/* @var $job Pure360_Cron_Model_Job */
		$job = Mage::getModel('pure360_cron/job')->getCollection()
			->addFieldToFilter('job_code', Pure360_Cart_Job_Trigger::JOB_CODE)
			->addFieldToFilter('scope', $scope)
			->addFieldToFilter('scope_id', $scopeId)
			->getFirstItem();

		$job->setData('job_code', Pure360_Cart_Job_Trigger::JOB_CODE);
		$job->setData('module', Pure360_Cart_Job_Trigger::MODULE);
		$job->setData('scope', $scope);
		$job->setData('scope_id', $scopeId);
		$job->setData('paused', 0);
		$job->setData('forced', 0);
		$job->setData('crontab', $job->getCrontab() ? $job->getCrontab() : '15 * * * *');
		$job->setData('status', Mage_Cron_Model_Schedule::STATUS_PENDING);
		$job->setData('message', null);
		$job->setScheduledAt(null);
		$job->setCreatedAt(time());
		$job->save();

		Mage::helper('pure360_cart')->writeDebug(__METHOD__ . ' - end');
	}

}
