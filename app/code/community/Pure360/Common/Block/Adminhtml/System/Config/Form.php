<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Block_Adminhtml_System_Config_Form extends Mage_Adminhtml_Block_System_Config_Form
{

	/**
	 * @return string
	 */
	public function getFormHtml()
	{
		$section = $this->getSectionCode();

		if (strstr($section, 'pure360'))
		{
			if (is_object($this->getForm()))
			{
				$form = $this->getForm();
				$html = '';
				foreach ($form->getElements() as $element)
				{
					$group = implode('_', array_slice(explode('_', $element->getId()), ($section == 'pure360'? 1: 2)));
					$result = $this->processGroup($group, $section);
					if ($result)
					{
						$html .= $result;
						
					} else
					{
						$html .= $element->toHtml();
					}
				}
				return $html;
			}
		}
		return parent::getFormHtml();
	}

	/**
	 * @param string $group
	 * @param string $section
	 */
	protected function processGroup($group, $section)
	{
		$scope = Mage::helper('pure360_common')->getScope();
		$scopeId = Mage::helper('pure360_common')->getScopeId();
		$website = Mage::app()->getRequest()->getParam('website');
		$websiteId = Mage::getModel("core/website")->load($website)->getWebsiteId();

		switch ($scope)
		{
			case 'default' :
				if($section == 'pure360_cron' && Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/enabled', 'default', 0))
				{
					return null;
				}
				
				if (!Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/enabled', 'default', 0) ||
					!Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/global', 'default', 0))
				{
					if ($section != 'pure360')
					{
						return $this->disableGroup($group);
					}
				} else if (!Mage::helper('pure360_common')->getScopedConfig($section . '/default_settings/enabled', 'default', 0))
				{
					if ($group != 'default_settings' && $section != 'pure360_cron')
					{
						return $this->hideGroup($group);
					}
				}

				break;

			case 'websites' :

				if($section == 'pure360_cron' && Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/enabled', 'default', 0))
				{
					return null;
				}
				
				if (!Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/enabled', 'default', 0) ||
					Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/global', 'default', 0))
				{
					return $this->disableGroup($group);
					
				} else if (!Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/enabled', 'websites', $websiteId) ||
					!Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/global', 'websites', $websiteId))
				{
					if ($section != 'pure360')
					{
						return $this->disableGroup($group);
					}
				} else if (!Mage::helper('pure360_common')->getScopedConfig($section . '/websites_settings/enabled', 'websites', $websiteId))
				{
					if ($group != 'websites_settings' && $section != 'pure360_cron')
					{
						return $this->hideGroup($group);
					}
				}
				break;

			case 'stores' :

				if($section == 'pure360_cron' && Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/enabled', 'default', 0))
				{
					return null;
				}
				
				if (!Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/enabled', 'websites', $websiteId) ||
					Mage::helper('pure360_common')->getScopedConfig('pure360/websites_settings/global', 'websites', $websiteId))
				{
						return $this->disableGroup($group);
						
				} else if ($section != 'pure360')
				{
					if (!Mage::helper('pure360_common')->getScopedConfig('pure360/stores_settings/enabled', 'stores', $scopeId))
					{

						return $this->disableGroup($group);
						
					} else if (!Mage::helper('pure360_common')->getScopedConfig($section . '/stores_settings/enabled', 'stores', $scopeId))
					{
						if ($group != 'stores_settings' && $section != 'pure360_cron')
						{
							return $this->hideGroup($group);
						} 
						
					} 
				}
				break;
		}

		return null;
	}

	private function hideGroup($group)
	{
		if ($group != 'about')
		{
			return $this->getLayout()->createBlock('pure360_common/adminhtml_system_config_hidden', 'system.config.hidden')->toHtml();
		}
	}

	private function disableGroup($group)
	{
		if ($group == 'about')
		{
			return $this->getLayout()->createBlock('pure360_common/adminhtml_system_config_disabled', 'system.config.disabled')->toHtml();
		
		} else
		{
			return $this->getLayout()->createBlock('pure360_common/adminhtml_system_config_hidden', 'system.config.hidden')->toHtml();
		}
	}

}