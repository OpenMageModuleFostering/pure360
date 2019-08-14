<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 */
class Pure360_Common_Block_Adminhtml_System_Config_Validate_Marketing extends Mage_Adminhtml_Block_System_Config_Form_Field
{

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		return $this->_getAddRowButtonHtml($this->__('Validate Marketing Credentials'));
	}

	protected function _getAddRowButtonHtml($title)
	{
		return $this->getLayout()->createBlock('adminhtml/widget_button')
				->setType('button')
				->setLabel($this->__($title))
				->setClass('ValidateMarketingProfileButton')
				->setId('pure360_settings_marketing_validate')
				->setOnClick("pure360.validate('marketing')")
				->toHtml();
	}

}