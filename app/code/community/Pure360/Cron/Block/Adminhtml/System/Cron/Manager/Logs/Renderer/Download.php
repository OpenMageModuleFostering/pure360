<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Logs_Renderer_Download extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	/**
	 * @param Varien_Object $row
	 * @return mixed
	 */
	public function render(Varien_Object $row)
	{
		
		return $this->getLayout()->createBlock('adminhtml/widget_button')
						->setType('button')
						->setClass('add')
						->setLabel(Mage::helper('adminhtml')->__('Download'))
						->setOnClick("pure360Cron.download('" . $row->getName() . "')")
						->toHtml();
	}

}
