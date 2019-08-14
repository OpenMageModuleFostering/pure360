<?php

/**
 * @package   Pure360\Newsletter
 * @copyright 2013 Pure360.com
 */
class Pure360_Newsletter_Helper_Api extends Pure360_Common_Helper_Api
{

	/**
	 * @param Pure360_Session $client
	 * @param Mage_Newsletter_Model_Subscriber $subscriber
	 * @return type
	 */
	public function listSubscribe($client, $subscriber, $subscribe = true)
	{
		Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - start');

		$identity = $client->getIdentity();

		// Build domain
		$domain = $identity['customDomain'];
		if(empty($domain))
		{
			$domain = Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings', 'api_url');
		}
		$domain = str_replace("http://", "", $domain);

		// Build url
		$url = 'http://' . $domain . '/interface/list.php';

		// Get store and webste ids
		$storeId	= $subscriber->getStoreId();
		$store		= Mage::getModel('core/store')->load($storeId);
		$websiteId	= $store->getWebsiteId();

		// Get list
		$list = Mage::helper('pure360_list')->getListForStore($storeId);

		// Subscribe
		if($list)
		{
			if(!$list->getListFilter() || strstr($subscriber->getSubscriberEmail(), $list->getListFilter()))
			{
				// Build post field data
				$postFields = 'accName=' . urlencode($identity['identityName']);
				$postFields .= '&fullEmailValidationInd=Y';
				$postFields .= '&listName=' . urlencode($list->getListName());
				$postFields .= '&website=' . urlencode(Mage::app()->getWebsite($websiteId)->getName());
				$postFields .= '&store=' . urlencode($store->getName());

				if($list->getDoubleOptinEnabled() != 'y')
				{
					$postFields .= '&doubleOptin=false';
				}

				if($subscribe)
				{
					$postFields .= '&signup_date=' . urlencode(Mage::helper('pure360_list')->toDate($subscriber->getSubscriptionDate()));
					$postFields .= '&subscription_date=' . urlencode(Mage::helper('pure360_list')->toDate($subscriber->getSubscriptionDate()));

				} else
				{
					$postFields .= '&mode=OPTOUT';
				}

				$postFields .= '&email=' . urlencode($subscriber->getSubscriberEmail());

				// Do post
				Mage::helper('pure360_common/api')->postCurl($url, $postFields);
			}
		}

		Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * 
	 * @param Mage_Customer_Model_Customer $customer
	 * @return type
	 */
	public function listSubscribeCustomer($client, $customer, $date, $subscribe = true)
	{
		Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - start');

		$identity = $client->getIdentity();

		// Build domain
		$domain = $identity['customDomain'];
		if(empty($domain))
		{
			$domain = Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings', 'api_url');
		}
		$domain = str_replace("http://", "", $domain);

		// Build url
		$url = 'http://' . $domain . '/interface/list.php';

		// Get store and webste ids
		$storeId = $customer->getStoreId();
		$store = Mage::getModel('core/store')->load($storeId);
		$websiteId = $store->getWebsiteId();

		// Get list
		$list = Mage::helper('pure360_list')->getListForStore($storeId);

		// Subscribe
		if($list)
		{	
			if(!$list->getListFilter() || strstr($customer->getEmail(), $list->getListFilter()))
			{
				// Build post field data
				$postFields = 'accName=' . urlencode($identity['identityName']);
				$postFields .= '&fullEmailValidationInd=Y';
				$postFields .= '&listName=' . urlencode($list->getListName());
				$postFields .= '&website=' . urlencode(Mage::app()->getWebsite($websiteId)->getName());
				$postFields .= '&store=' . urlencode($store->getName());

				if($subscribe)
				{


					// Process Customer
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

						$postFields .= '&' . $key . '=' . $val;
					}

					if($list->getDoubleOptinEnabled() != 'y')
					{
						$postFields .= '&doubleOptin=false';
					}

					$postFields .= '&signup_date=' . urlencode(Mage::helper('pure360_list')->toDate($date));
					
				} else
				{
					$postFields .= '&mode=OPTOUT';
				}
					
				$postFields .= '&email=' . urlencode($customer->getEmail());

				// Do post
				Mage::helper('pure360_common/api')->postCurl($url, $postFields);
			}
		}

		Mage::helper('pure360_newsletter')->writeDebug(__METHOD__ . ' - end');
	}
}
