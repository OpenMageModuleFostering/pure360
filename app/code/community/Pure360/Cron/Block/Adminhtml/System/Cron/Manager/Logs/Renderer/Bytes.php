<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Logs_Renderer_Bytes extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $object)
	{
		$o = trim($object->getSize());
			
		$html = Mage::helper('pure360_common/file')->formatBytes($o);
		
		return $html;
	}

}