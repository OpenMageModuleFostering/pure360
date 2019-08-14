<?php

/**
 * @package   Pure360\Newsletter
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Newsletter_Helper_Data extends Pure360_Common_Helper_Data
{
	/**
	 * Retrieve helper module name
	 *
	 * @return string
	 */
	protected function getModuleName()
	{
		return 'pure360_newsletter';
	}

	/**
	 * @param Mage_Customer_Model_Customer $customer
	 * @return boolean
	 */
	public function isCustomerSubscribed(Mage_Customer_Model_Customer $customer = null)
	{
		if (!$customer)
		{
			return false;
		}

		/* @var $subscriber Mage_Newsletter_Model_Subscriber */
		$subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
		return (bool) $subscriber->isSubscribed();
	}

	/**
	 * @return string
	 */
	public function getCheckoutOnepageBillingTemplate()
	{
		return 'checkout/onepage/billing.phtml';
	}

}
