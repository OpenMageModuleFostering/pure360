<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 */
class Pure360_Common_Model_Observer
{

	const MODULE_IDENTIFER = 'pure360_common';

	/**
	 * @param Varien_Event_Observer $observer
	 * @return mixed                
	 */
	public function checkRequirements(Varien_Event_Observer $observer)
	{
		if (!Mage::getSingleton('admin/session')->isLoggedIn())
		{
			return;
		}

		// Verify Requirements
		if (!Mage::helper(self::MODULE_IDENTIFER)->checkRequirements(self::MODULE_IDENTIFER, array('soap')))
		{
			return;
		}

		// Verify API tokens are valid
		if (Mage::helper(self::MODULE_IDENTIFER)->isEnabled())
		{
			return false;
		}
	}

	/**
	 * Loop through all scopes and disable settings and jobs where necessary
	 * 
	 * @param Varien_Event_Observer $observer
	 * @return mixed                
	 */
	public function checkEnabled(Varien_Event_Observer $observer)
	{
		$defaultEnabled = Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/enabled', 'default', 0);
		$defaultGlobal = Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/global', 'default', 0);
	
		foreach (Mage::app()->getWebsites() as $website)
		{
			if (!$defaultEnabled || $defaultGlobal)
			{
				Mage::helper('pure360_common')->setScopedConfig('pure360/websites_settings/enabled', 0, 'websites', $website->getId());
			}

			$websitesEnabled = Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/enabled', 'websites', $website->getId());
			$websitesGlobal = Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/global', 'websites', $website->getId());
	
			foreach ($website->getGroups() as $group)
			{
				$stores = $group->getStores();
				foreach ($stores as $store)
				{
					if (!$websitesEnabled || $websitesGlobal)
					{
						Mage::helper('pure360_common')->setScopedConfig('pure360/stores_settings/enabled', 0, 'stores', $store->getId());
					}
				}
			}
		}
	}

}
