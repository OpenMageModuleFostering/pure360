<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cart_Block_Email_Order_Items extends Mage_Sales_Block_Items_Abstract
{

	public function _construct()
	{
		$this->setTemplate('pure360_cart/email_order_items.phtml');
	}

	public function getTax($_item)
	{
		if(Mage::helper('tax')->displayCartPriceInclTax())
		{
			$subtotal = Mage::helper('tax')->__('Incl. Tax') . ' : ' . Mage::helper('checkout')->formatPrice($_item['row_total_incl_tax']);
		} elseif(Mage::helper('tax')->displayCartBothPrices())
		{
			$subtotal = Mage::helper('tax')->__('Excl. Tax') . ' : ' . Mage::helper('checkout')->formatPrice($_item['row_total']) . '<br>' . Mage::helper('tax')->__('Incl. Tax') . ' : ' . Mage::helper('checkout')->formatPrice($_item['row_total_incl_tax']);
		} else
		{
			$subtotal = Mage::helper('tax')->__('Excl. Tax') . ' : ' . Mage::helper('checkout')->formatPrice($_item['row_total']);
		}
		return $subtotal;
	}

}