<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Logs_Renderer_Tail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $object)
	{
		$html = '';

		$data = $object->getTail();

		if(empty($data))
		{
			$html .= 'no data';
		}
		else
		{
			$lines = explode("\n", $data);

			if(count($lines))
			{
				foreach($lines as $line)
				{
					$html .= $line . '<br />';
				}
			}
		}
		return $html;
	}

}