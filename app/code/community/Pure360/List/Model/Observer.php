<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 */
class Pure360_List_Model_Observer extends Mage_Core_Model_Abstract
{
	/**
	 * 
	 */
	public function initmail($observer)
	{
		if(Mage::helper('pure360_list')->isEnabledForStore(Mage::app()->getStore()->getStoreId()))
		{
			if(Mage::app()->getRequest()->getParam('mailid'))
			{
				Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');
				Mage::getSingleton('core/session')->setPuremailid(Mage::app()->getRequest()->getParam('mailid'));	
				Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
			}
		}
		
		return $this;
	}

	/**
	 * 
	 */
	public function successPage($observer)
	{
		if(Mage::helper('pure360_list')->isEnabledForStore(Mage::app()->getStore()->getStoreId()))
		{
			if(Mage::getSingleton('core/session')->getPuremailid())
			{
				Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - start');
			
				$mailId		= Mage::getSingleton('core/session')->getPuremailid();
				$list		= Mage::helper('pure360_list')->getListForStore(Mage::app()->getStore()->getStoreId());
				
				if($list->getSuccessTrackingEnabled())
				{
					$client = Mage::helper('pure360_common/api')->getClientForWebsite();
					$eventData = $observer->getEvent()->getData();
					Mage::helper('pure360_list/api')->clickTrack($client, $list, $eventData, $mailId);
				}
				Mage::helper('pure360_list')->writeDebug(__METHOD__ . ' - end');
			}
		}
		
		return $this;
	}

}