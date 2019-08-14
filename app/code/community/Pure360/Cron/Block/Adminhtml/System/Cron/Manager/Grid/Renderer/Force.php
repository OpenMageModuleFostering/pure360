<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Grid_Renderer_Force extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	/**
	 * @param Varien_Object $row
	 * @return mixed
	 */
	public function render(Varien_Object $row)
	{
		$html = $this->getForceButtonHtml($row);
		
		return $html;
	}

	protected function getForceButtonHtml($row)
	{
		return $this->getLayout()->createBlock('adminhtml/widget_button')
				->setType('button')
				->setClass('add')
				->setLabel(Mage::helper('adminhtml')->__('Force'))
				->setOnClick("pure360Cron.forceJob('" . $row->getJobId() . "')")
				->toHtml();
	}

}
