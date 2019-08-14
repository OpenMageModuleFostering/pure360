<?php

/**
 * @package   Pure360\Newsletter
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Newsletter_IndexController extends Mage_Core_Controller_Front_Action
{

	/**
	 * Unsubscribes customer from newsletter and also from Pure360 list.
	 */
	public function unsubscribeAction()
	{
		if(Mage::helper('pure360_newsletter')->isEnabled(true))
		{
			Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - start');

			if(Mage::app()->getRequest()->getParam('email'))
			{
				$email = Mage::app()->getRequest()->getParam('email');

				try
				{
					$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);

					if($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
					{
						$subscriber->unsubscribe(false);
					}
					Mage::helper('pure360_newsletter')->writeDebug("HTTP/1.0 200 OK");
					header("HTTP/1.0 200 OK");
				} catch(Exception $e)
				{
					Mage::helper('pure360_newsletter')->writeError("HTTP/1.0 500 " . $e->getMessage());
					header("HTTP/1.0 500 " . $e->getMessage());
				}
			} else
			{
				Mage::helper('pure360_newsletter')->writeError("HTTP/1.0 400 " . "No email address supplied");
				header("HTTP/1.0 400 " . "No email address supplied");
			}

			Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - end');
		} else
		{
			header("HTTP/1.0 404 Not Found");
		}
		exit;
	}

	/**
	 * Subscribes customer from newsletter and also from Pure360 list.
	 */
	public function subscribeAction()
	{
		if(Mage::helper('pure360_newsletter')->isEnabled(true))
		{
			Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - start');

			if(Mage::app()->getRequest()->getParam('email'))
			{
				$customer = null;
				$subscriber = null;
				$email = Mage::app()->getRequest()->getParam('email');
				$storeId = null;
				$customerId = null;

				try
				{
					// Load up existing subscriber if possible
					$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
					$storeId = $subscriber->getStoreId();

					// Load up existing customer if possible
					$websiteIds = Mage::getModel('core/website')->getCollection()->getAllIds();
					foreach($websiteIds as $websiteId)
					{
						$_customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($email);
						if($_customer->getId())
						{
							$customer = $_customer;
							break;
						}
					}

					if(!is_null($customer))
					{
						$storeId = $customer->getStoreId();
						$customerId = $customer->getId();

						if(!$subscriber->getId() || $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED || $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE)
						{

							$subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
							$subscriber->setSubscriberEmail($email);
							$subscriber->setSubscriberConfirmCode($subscriber->RandomSequence());
						}

						$subscriber->setSubscriptionDate(Mage::getSingleton('core/date')->gmtDate());
						$subscriber->setStoreId($storeId);
						$subscriber->setCustomerId($customer->getId());
						$subscriber->save();
					} else
					{
						Mage::getModel('newsletter/subscriber')->subscribe($email, false);
					}
				} catch(Exception $e)
				{
					Mage::helper('pure360_newsletter')->writeDebug("HTTP/1.0 500 " . $e->getMessage());
					header("HTTP/1.0 500 " . $e->getMessage());
				}
			} else
			{
				Mage::helper('pure360_newsletter')->writeDebug("HTTP/1.0 400 " . "No email address supplied");
				header("HTTP/1.0 400 " . "No email address supplied");
			}

			Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - exit');
		} else
		{

			header("HTTP/1.0 404 Not Found");
		}
		exit;
	}

}
