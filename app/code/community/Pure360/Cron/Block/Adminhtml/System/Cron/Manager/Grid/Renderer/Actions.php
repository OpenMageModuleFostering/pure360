<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Grid_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	/**
	 * @param Varien_Object $row
	 * @return mixed
	 */
	public function render(Varien_Object $row)
	{
		$html = $this->getForceButtonHtml($row);
		$html .= "&nbsp;&nbsp;";
		$html .= $this->getCancelButtonHtml($row);
		
		return $html;
	}

	protected function getForceButtonHtml($row)
	{
		return $this->getLayout()->createBlock('adminhtml/widget_button')
				->setType('button')
				->setClass('add')
				->setLabel(Mage::helper('adminhtml')->__('Force Job'))
				->setOnClick("pure360Cron.forceJob('" . $row->getJobId() . "')")
				->toHtml();
	}

	protected function getCancelButtonHtml($row)
	{
		return $this->getLayout()->createBlock('adminhtml/widget_button')
				->setType('button')
				->setClass('go')
				->setLabel(Mage::helper('adminhtml')->__('Cancel Job'))
				->setOnClick("pure360Cron.cancelJob('" . $row->getJobId() . "')")
				->toHtml();
	}

}
