<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Grid_Renderer_Pause extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	/**
	 * @param Varien_Object $row
	 * @return mixed
	 */
	public function render(Varien_Object $row)
	{
		$html = $this->getPauseButtonHtml($row);
		
		return $html;
	}
	
	protected function getPauseButtonHtml($row)
	{
		if($row->getPaused())
		{
			return $this->getLayout()->createBlock('adminhtml/widget_button')
					->setType('button')
					->setClass('go')
					->setLabel(Mage::helper('adminhtml')->__('Resume'))
					->setOnClick("pure360Cron.resumeJob('" . $row->getJobId() . "')")
					->toHtml();
		}
		else
		{
			return $this->getLayout()->createBlock('adminhtml/widget_button')
					->setType('button')
					->setClass('go')
					->setLabel(Mage::helper('adminhtml')->__('Pause'))
					->setOnClick("pure360Cron.pauseJob('" . $row->getJobId() . "')")
					->toHtml();
		}
	}

}
