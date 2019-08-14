<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Block_Adminhtml_System_Config_About extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

	/**
	 * Name of module
	 * @var string
	 */
	protected $_module = 'pure360_common';

	/**
	 * Module display name
	 * @var string
	 */
	protected $_name = 'Pure360 Extension for Magento';

	public function _construct()
	{
		parent::_construct();
		$this->setTemplate('pure360/common/about.phtml');
	}

	/**
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		return $this->toHtml();
	}

	/**
	 * Get the module namespace
	 * @return string
	 */
	public function getModuleNamespace()
	{
		return $this->_module;
	}

	/**
	 * Get the module name
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->_name;
	}

	/**
	 * Get the module version
	 * @return string
	 */
	public function getModuleVersion()
	{
		$version = Mage::helper($this->_module)->getModuleVersion();
		return empty($version) ? null : "v{$version}";
	}

}
