<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Block_Adminhtml_System_Config_Form_Field_Hidden extends Mage_Adminhtml_Block_System_Config_Form_Field
{

	/**
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		if (!extension_loaded('soap') || !Mage::helper('pure360_common')->isEnabled())
		{
			return null;
		}

		return parent::render($element);
	}

}
