<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 */
class Pure360_Common_Block_Adminhtml_System_Config_Form_Field extends Mage_Adminhtml_Block_System_Config_Form_Field
{

	/**
	 * Override method to output our custom HTML with JavaScript
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return String
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		if (!Mage::helper('pure360_common')->isEnabled())
		{
			$element->setDisabled('disabled')->setValue('');
		} else
		{
			$element->setDisabled('disabled')->setValue('');
		}

		return parent::_getElementHtml($element);
	}

}
