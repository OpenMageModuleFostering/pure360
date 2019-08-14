<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

	public function _construct()
	{
		parent::_construct();
		$this->setTemplate('pure360/cron/manager.phtml');
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
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _prepareLayout()
	{

		$this->setChild('grid', $this->getLayout()->createBlock('pure360_cron/adminhtml_system_cron_manager_grid', 'cron.manager.grid'));

		$this->setChild('logs', $this->getLayout()->createBlock('pure360_cron/adminhtml_system_cron_manager_logs', 'cron.manager.logs'));
		
		return parent::_prepareLayout();
	}

	/**
	 * Get transactional emails page header text
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		if (!Mage::helper('pure360_cron')->isEnabled())
		{
			return parent::getHeaderText();
		}

		return Mage::helper('pure360_cron')->__('Pure360 Cron Manager');
	}

	/**
	 * Get URL for create new email template
	 *
	 * @return string
	 */
	public function getListUrl()
	{
		return $this->getUrl('*/system_cron_manager/list');
	}

	/**
	 * @return boolean
	 */
	protected function showLogs()
	{
		return Mage::helper('pure360_common')->isDebugEnabled();
	}

}
