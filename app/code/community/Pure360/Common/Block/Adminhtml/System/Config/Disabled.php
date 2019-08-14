<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Block_Adminhtml_System_Config_Disabled extends Pure360_Common_Block_Adminhtml_System_Config_About
{
	public function _construct()
	{
		parent::_construct();
		$this->setTemplate('pure360/common/disabled.phtml');
	}
}
