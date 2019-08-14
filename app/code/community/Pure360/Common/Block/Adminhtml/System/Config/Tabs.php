<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Block_Adminhtml_System_Config_Tabs extends Mage_Adminhtml_Block_System_Config_Tabs
{

	public function addSection($code, $tabCode, $config)
	{
		if ($tabCode == 'pure360' &&
			$code != 'pure360'
			&& !Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/enabled', 'default', 0))
		{
			return $this;
		}

		return parent::addSection($code, $tabCode, $config);
	}

}