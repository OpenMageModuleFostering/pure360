<?php

/**
 * @package   Pure360\Newsletter
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Newsletter_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
	/**
	 * Override default subscribe action.
	 */
	public function subscribe($email, $callback = true)
	{
		if(parent::subscribe($email))
		{
			if(Mage::helper('pure360_newsletter')->isEnabledForStore($this->getStoreId()))
			{
				if($this->getIsStatusChanged() && $this->getStatus() == self::STATUS_SUBSCRIBED)
				{
					$this->tagSubscription();
					
					if(Mage::app()->getWebsite()->getCode() !== 'admin')
					{
						if($callback)
						{
							$client = Mage::helper('pure360_newsletter/api')->getClientForWebsite();
							Mage::helper('pure360_newsletter/api')->listSubscribe($client, $this);
						}
					}
				}
			}
		}
		return $this->getStatus();
	}

	/**
	 * Override default subscribeCustomer action.
	 */
	public function subscribeCustomer($customer, $callback = true)
	{
		if(parent::subscribeCustomer($customer))
		{
			if(Mage::helper('pure360_newsletter')->isEnabledForStore($customer->getStoreId()))
			{
				if($this->getIsStatusChanged() && $this->getStatus() == self::STATUS_SUBSCRIBED)
				{
					$this->tagSubscription();
					
					if(Mage::app()->getWebsite()->getCode() !== 'admin')
					{	
						if($callback)
						{
							$client = Mage::helper('pure360_common/api')->getClientForWebsite();
							Mage::helper('pure360_newsletter/api')->listSubscribeCustomer($client, $customer, $this->getSubscriptionDate());
						}
					}
					
					$customer->save();
				}
			}
		}
		return $this;
	}

	/**
	 * Override default unsubscribe action.
	 */
	public function unsubscribe($callback = true)
	{
		if(parent::unsubscribe())
		{
			$storeId = $this->getStoreId();
			
			if($this->getCustomerId())
			{
				$customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
				$storeId = $customer->getStoreId();	
			}
			
			if(Mage::helper('pure360_newsletter')->isEnabledForStore($storeId))
			{
				if($this->getStatus() == self::STATUS_UNSUBSCRIBED)
				{
					if(Mage::app()->getWebsite()->getCode() !== 'admin')
					{
						if($callback)
						{
							$client = Mage::helper('pure360_newsletter/api')->getClientForWebsite();
							Mage::helper('pure360_newsletter/api')->listSubscribe($client, $this, false);
						}
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Tag subscribe date
	 */
	public function tagSubscription()
	{
		$this->setSubscriptionDate(Mage::getSingleton('core/date')->gmtDate());
		$this->save();
	}

	/**
	 * Suppress default confirmation success email function if Pure360 
	 * module is active.
	 */
	public function sendConfirmationSuccessEmail()
	{
		if(Mage::helper('pure360_newsletter')->isEnabledForStore($this->getStoreId()))
		{
			return $this;
			
		} else
		{
			return parent::sendConfirmationSuccessEmail();
		}
	}

	/**
	 * Suppress default unsubscribe confirmation email function if Pure360 
	 * module is active.
	 */
	public function sendUnsubscriptionEmail()
	{
		if(Mage::helper('pure360_newsletter')->isEnabledForStore($this->getStoreId()))
		{
			return $this;
		} else
		{
			return parent::sendUnsubscriptionEmail();
		}
	}

	/**
	 * Processing object before save data
	 *
	 */
	protected function _beforeSave()
	{
		parent::_beforeSave();

		if(Mage::helper('pure360_newsletter')->isEnabledForStore($this->getStoreId()))
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

		return $this;
	}
	
	/**
	 * Processing object after delete data
	 *
	 * @return Pure360_Newsletter_Model_Subscriber
	 */
	protected function _afterDelete()
	{
		parent::_afterDelete();
	}

}