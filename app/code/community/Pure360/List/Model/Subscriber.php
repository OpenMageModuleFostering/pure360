<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 */
class Pure360_List_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{

	/**
	 * Processing object before save data
	 * 
	 * @return Pure360_Newsletter_Model_Subscriber
	 */
	protected function _beforeSave()
	{
		if(parent::_beforeSave())
		{
			if(Mage::helper('pure360_list')->isEnabledForStore($this->getStoreId()))
			{
				$this->setPure360SyncStatus(0);

				// Trigger Pure360 sync status for customer too
				if($this->getCustomerId() > 0)
				{
					$customer = Mage::getModel('customer/customer')->load($this->getCustomerId());

					if($customer->getId())
					{
						$customer->save();
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Processing object after delete data
	 *
	 * @return Pure360_Newsletter_Model_Subscriber
	 */
	protected function _afterDelete()
	{
		if(parent::_afterDelete())
		{
			if(Mage::helper('pure360_list')->isEnabledForStore($this->getStoreId()))
			{
				// Add to optout list to make sure
				Mage::helper('pure360_list')->listOptout($this->getSubscriberEmail(), $this->getStoreId());
			}
		}
		return $this;
	}
}
