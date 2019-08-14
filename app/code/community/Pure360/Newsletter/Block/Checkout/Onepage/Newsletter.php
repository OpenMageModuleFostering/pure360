<?php

/**
 * @package   Pure360\Newsletter
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Newsletter_Block_Checkout_Onepage_Newsletter extends Mage_Checkout_Block_Onepage_Abstract
{

	/**
	 * @return bool
	 */
	public function isSubscribed()
	{
		return Mage::helper('pure360_newsletter')->isCustomerSubscribed($this->getCustomer());
	}

	/**
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('pure360_newsletter')->isEnabled(true);
	}

	/**
	 * @return bool
	 */
	public function isEnabledCheckedByDefault()
	{
		return Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360_newsletter', 'checkout', 'default_checked');
	}

	/**
	 * @return bool
	 */
	public function isEnabledForGuestCheckout()
	{
		return Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360_newsletter', 'checkout', 'show_to_guests');
	}

	/**
	 * @return bool
	 */
	public function isEnabledForRegisterCheckout()
	{
		return Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360_newsletter', 'checkout', 'show_to_registrars');
	}
	/**
	 * @return bool
	 */
	public function isEnabledForCustomerCheckout()
	{
		return Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360_newsletter', 'checkout', 'show_to_customers');
	}

	/**
	 * @return bool
	 */
	public function isEnabledIfAlreadySubscribed()
	{
		return Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360_newsletter', 'checkout', 'show_if_subscribed');
	}

	/**
	 * @return bool
	 */
	public function getCheckboxLabelText()
	{
		return Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360_newsletter', 'checkout', 'label_text');
	}

	/**
	 * 
	 * @param string $method
	 * @return string
	 */
	public function getJsCheckedCode($method)
	{
		$js = "";
		$methodName = 'isEnabledFor' . ucfirst($method) . 'Checkout';

		// Default Values
		$action = 'hide';
		$checked = 'false';
		$value = 'null';

		// If function exists, use it, otherwise we hide and disable values
		if(method_exists($this, $methodName))
		{
			if($this->$methodName())
			{
				$action = 'show';
				if($this->isSubscribed() || $this->isEnabledCheckedByDefault())
				{
					$checked = 'true';
					$value = '1';
				}
			}
		}

		// If user is subscribed and enabled if already subscribed is not allowed,
		// Hide it, but set the values to true
		if($this->isSubscribed() && !$this->isEnabledIfAlreadySubscribed())
		{
			$action = 'hide';
			$checked = 'true';
			$value = '1';
		}

		// Create JS
		$js.= "Element.{$action}('register-customer-newsletter');\r\n";
		$js.= "$('billing:is_subscribed_box').checked    = {$checked};\r\n";
		$js.= "$('billing:is_subscribed').value          = {$value};\r\n";
		$js.= "$('billing:is_subscribed').value          = {$value};\r\n";

		return $js;
	}

}
