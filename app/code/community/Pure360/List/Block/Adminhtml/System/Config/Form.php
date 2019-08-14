<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */

class Pure360_List_Block_Adminhtml_System_Config_Form extends Mage_Adminhtml_Block_System_Config_Form
{

	/**
	 * Init config group
	 *
	 * @param Varien_Data_Form $form
	 * @param Varien_Simplexml_Element $group
	 * @param Varien_Simplexml_Element $section
	 * @param Varien_Data_Form_Element_Fieldset|null $parentElement
	 */
	protected function _initGroup($form, $group, $section, $parentElement = null)
	{
		if(strstr($section->getName(), 'pure360'))
		{
			if($group->getName() != 'about')
			{
				$scope = Mage::helper('pure360_common')->getScope();
				$scopeId = Mage::helper('pure360_common')->getScopeId();

				switch($scope)
				{
					case 'websites' :
						// Don't show anything if default not enabled or in global mode
						if(Mage::helper('pure360_common')->getScopedConfig('pure360/settings/global', 'default', 0) ||
								!Mage::helper('pure360_common')->getScopedConfig('pure360/settings/enabled', 'default', 0))
						{
							return $this;
						}
						
						if(!Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/enabled', $scope, $scopeId) ||
								!Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/global', $scope, $scopeId))
						{
							if($group->getName() != 'websites_settings')
							{
								return $this;
							}
						}
						
						break;
					case 'stores' :
						
						$website = Mage::app()->getRequest()->getParam('website');
						$websiteId = Mage::getModel("core/website")->load($website)->getWebsiteId();

						// Don't show anything if default or website not enabled or in global mode
						if(Mage::helper('pure360_common')->getScopedConfig('pure360/settings/global', 'default', 0) ||
								!Mage::helper('pure360_common')->getScopedConfig('pure360/settings/enabled', 'default', 0) ||
								Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/global', 'websites', $websiteId) ||
								!Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/enabled', 'websites', $websiteId))
						{
							return $this;
						}
						
						if(!Mage::helper('pure360_common')->getScopedConfig('pure360/stores_settings/enabled', $scope, $scopeId))
						{
							if($group->getName() != 'stores_settings')
							{
								return $this;
							}
						}
						break;
					default :
						if(!Mage::helper('pure360_common')->getScopedConfig('pure360/settings/enabled', $scope, $scopeId) ||
								!Mage::helper('pure360_common')->getScopedConfig('pure360/settings/global', $scope, $scopeId))
						{
							if($group->getName() != 'settings')
							{
								return $this;
							}
						}
						break;
				}
			}
		}

		return parent::_initGroup($form, $group, $section, $parentElement);
	}

}