<?php

/**
 * @package   Pure360\Newsletter
 * @copyright 2013 Pure360.com
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
				if($this->getIsStatusChanged())
				{
					switch($this->getStatus())
					{
						case self::STATUS_SUBSCRIBED: 
						{
							$this->tagSubscription();

							if(Mage::app()->getWebsite()->getCode() !== 'admin')
							{
								if($callback)
								{
									$client = Mage::helper('pure360_common/api')->getClientForWebsite();
									Mage::helper('pure360_newsletter/api')->listSubscribeCustomer($client, $customer, $this->getSubscriptionDate(), true);
								}
							}

							$customer->save();
							break;
						}
						case self::STATUS_UNSUBSCRIBED: 
						{
							if(Mage::app()->getWebsite()->getCode() !== 'admin')
							{
								if($callback)
								{
									$client = Mage::helper('pure360_common/api')->getClientForWebsite();
									Mage::helper('pure360_newsletter/api')->listSubscribeCustomer($client, $customer, $this->getSubscriptionDate(), false);
								}
							}

							// Add to optout list to make sure
							Mage::helper('pure360_list')->listOptout($this->getSubscriberEmail(), $customer->getStoreId());
							
							$customer->save();
							break;
						}
					}
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
					
					// Add to optout list to make sure
					Mage::helper('pure360_list')->listOptout($this->getSubscriberEmail(), $storeId);
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
	 * 
	 * @return Pure360_Newsletter_Model_Subscriber
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
	 * 
	 * @return Pure360_Newsletter_Model_Subscriber
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
}
