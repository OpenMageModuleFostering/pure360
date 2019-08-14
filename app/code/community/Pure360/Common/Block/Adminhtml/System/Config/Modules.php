<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Block_Adminhtml_System_Config_Modules extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

	/**
	 * List of module
	 * @var string
	 */
	protected $_moduleList = array();

	public function _construct()
	{
		parent::_construct();
		$this->setTemplate('pure360/common/modules.phtml');
	}

	protected function _prepareLayout()
	{
		$head = $this->getLayout()->getBlock('head');
		if ($head)
		{
			$head->addCss('pure360/common.css');
		}
		return parent::_prepareLayout();
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
	public function getModuleList()
	{
		if (empty($this->moduleList))
		{
			$this->_moduleList = Mage::helper('pure360_common')->getPure360Modules();
		}

		return $this->_moduleList;
	}

}
