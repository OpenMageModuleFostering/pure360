<?php

/**
 * @package   Pure360\Newsletter
 * @copyright 2013 Pure360.com
 */
class Pure360_Newsletter_Model_Observer extends Mage_Core_Model_Abstract
{

	const NOTICE_IDENTIFER = 'pure360_newsletter';

	const BOX_UNCHECKED = 0;

	const BOX_CHECKED = 1;

	const BOX_NOT_CHANGED = 2;

	/**
	 * This event fires when customer continues past the Billing Info step
	 * on the onepage checkout. We set a flag here in the session to avoid
	 * actually doing anything until checkout is complete.
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function setSubscriptionAtBillingStep(Varien_Event_Observer $observer)
	{
		if(!Mage::helper('pure360_newsletter')->isEnabled(true))
		{
			return;
		}

		Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - start');

		Mage::getSingleton('checkout/session')->unsIsSubscribed();
		$params = Mage::app()->getRequest()->getParams();

		if(
				isset($params['billing']['is_subscribed']) &&
				($params['billing']['is_subscribed'] === '1' ||
				$params['billing']['is_subscribed'] === '0')
		)
		{
			$isSubscribed = (int) $params['billing']['is_subscribed'];
			Mage::getSingleton('checkout/session')->setIsSubscribed($isSubscribed);
		} else
		{
			Mage::getSingleton('checkout/session')->setIsSubscribed(self::BOX_NOT_CHANGED);
		}

		Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - end');

		return $observer;
	}

	/**
	 * Observe checkout event and handle assigning status
	 * @param Varien_Event_Observer $observer
	 * @return boolean|Varien_Event_Observer
	 */
	public function handleSubscriptionAtCheckout(Varien_Event_Observer $observer)
	{
		if(!Mage::helper('pure360_newsletter')->isEnabled(true))
		{
			return;
		}

		Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - start');

		try
		{
			// Get e-mail address we are working with
			$email = $observer->getEvent()->getOrder()->getData('customer_email');

			if(empty($email))
			{
				Mage::helper('pure360_newsletter')->writeError('No customer_email was provided.');
				return false;
			}

			/* @var $subscriber Mage_Newsletter_Model_Subscriber */
			if(!$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email))
			{
				Mage::helper('pure360_newsletter')->writeError('Unable to create subscriber object');
				return false;
			}

			/* @var $contact Mage_Customer_Model_Customer */
			if(!$contact = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email))
			{
				Mage::helper('pure360_newsletter')->writeError('Unable to create contact object');
				return false;
			}

			// Get Subscription status
			$isSubscribed = Mage::getSingleton('checkout/session')->getIsSubscribed();

			// Determine action
			switch($isSubscribed)
			{
				case self::BOX_CHECKED:
					// Subscribe the Customer
					if(!$subscriber->isSubscribed())
					{
						return $subscriber->subscribe($email);
					}
					break;
				case self::BOX_UNCHECKED:
					// Unsubscribe the Customer if subscribed
					if($subscriber->isSubscribed())
					{
						return $subscriber->unsubscribe();
					}
					break;
				case self::BOX_NOT_CHANGED:

					break;
				default:
					// Intentionally blank
					break;
			}
		} catch(Exception $e)
		{
			Mage::helper('pure360_newsletter')->writeError($e);
		}

		Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - end');

		return $observer;
	}

}
