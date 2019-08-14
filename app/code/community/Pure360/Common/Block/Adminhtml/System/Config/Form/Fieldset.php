<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Block_Adminhtml_System_Config_Form_Fieldset extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

	/**
	 * Override method to output our custom HTML with JavaScript
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return String
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		if (!Mage::helper('pure360_common')->isEnabled() &&
			$element->getId() != 'pure360_marketing_profile')
		{
			return null;
		}
		return parent::_getElementHtml($element);
	}

	/**
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		if (!Mage::helper('pure360_common')->isEnabled() &&
			$element->getId() != 'pure360_marketing_profile')
		{
			return null;
		}
		return parent::render($element);
	}

}
