<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Helper_Data extends Mage_Core_Helper_Abstract
{

	const XML_PATH_GLOBAL_SETTINGS = 'pure360/default_settings/';
	const XML_PATH_DEBUG = 'pure360/default_settings/debug';
	const XML_PATH_NOTICES = 'pure360/default_settings/notices';
	const XML_PATH_ENABLED = 'pure360/default_settings/enabled';
	const XML_PATH_ADVANCED = 'pure360/default_settings/advanced';

	/**
	 * Retrieve helper module name
	 *
	 * @return string
	 */
	protected function getModuleName()
	{
		return 'pure360';
	}

	/**
	 * @param string $moduleName
	 * @return string
	 */
	public function getModuleVersion($moduleName = null)
	{
		$modules = (array) Mage::getConfig()->getNode('modules')->children();

		if ($moduleName === null)
		{
			$moduleName = $this->getModuleName();
		}

		return isset($modules[$moduleName]) ? (string) $modules[$moduleName]->version : null;
	}
	
	/**
	 * @return bool
	 */
	public function isEnabled($frontend = false)
	{
		if ($frontend)
		{
			return $this->getModuleGroupKeyValue($this->getModuleName(), 'settings', 'enabled');
		
		} else
		{
			$scope		= $this->getScope();
			$scopeId	= $this->getScopeId();
			$path		= $this->getModuleName() . '/' . $scope . '_settings/enabled';
			
			return (bool) $this->getScopedConfig($path, $scope, $scopeId);
		}
	}

	/**
	 * @return bool
	 */
	public function isEnabledForStore($storeId)
	{
		return $this->getModuleGroupKeyValue($this->getModuleName(), 'settings', 'enabled', $storeId);
	}

	/**
	 * @return bool
	 */
	public function isDebugEnabled()
	{
		return (bool) $this->getScopedConfig(self::XML_PATH_DEBUG, 'default', 0);
	}
	
	/**
	 * @return bool
	 */
	public function isAdvancedConfig()
	{
		return (bool) $this->getScopedConfig(self::XML_PATH_ADVANCED, 'default', 0);
	}
	
	/**
	 * @return bool
	 */
	public function isNoticesEnabled()
	{
		return false; //(bool) $this->getScopedConfig(self::XML_PATH_NOTICES, 'default', 0);
	}

	/**
	 * 
	 * @return boolean
	 */
	public function isEnterprise()
	{
		$enterprise = false;

		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = array_keys((array) $modules);
		
		if (in_array('Enterprise_Enterprise', $modulesArray) &&
			in_array('Enterprise_CustomerSegment', $modulesArray))
		{
			$enterprise = true;
		}

		return $enterprise;
	}

	public function getActiveModuleScopeForStore($module, $storeId)
	{
		$store = Mage::getModel("core/store")->load($storeId);
		$websiteId = $store->getWebsiteId();

		if ($this->getScopedConfig('pure360/stores_settings/enabled', 'stores', $storeId))
		{
			return 'stores';
		} else if ($this->getScopedConfig($module . '/websites_settings/enabled', 'websites', $websiteId) &&
			$this->getScopedConfig('pure360/websites_settings/enabled', 'websites', $websiteId) &&
			$this->getScopedConfig('pure360/websites_settings/global', 'websites', $websiteId))
		{
			return 'websites';
		} else if ($this->getScopedConfig($module . '/default_settings/enabled', 'default', 0) &&
			$this->getScopedConfig('pure360/default_settings/enabled', 'default', 0) &&
			$this->getScopedConfig('pure360/default_settings/global', 'default', 0))
		{
			return 'default';
		} else
		{
			return null;
		}
	}

	public function getModuleGroupKeyValue($module, $group, $key, $storeId = null)
	{
		$store		= Mage::app()->getStore($storeId);
		$storeId	= $store->getId();
		$websiteId	= $store->getWebsiteId();

		if ($this->getScopedConfig('pure360/stores_settings/enabled', 'stores', $storeId))
		{
			return $this->getScopedConfig($module . '/stores_' . $group . '/' . $key, 'stores', $storeId);
			
		} else if ($this->getScopedConfig($module . '/websites_settings/enabled', 'websites', $websiteId) &&
			$this->getScopedConfig('pure360/websites_settings/enabled', 'websites', $websiteId) &&
			$this->getScopedConfig('pure360/websites_settings/global', 'websites', $websiteId))
		{
			return $this->getScopedConfig($module . '/websites_' . $group . '/' . $key, 'websites', $websiteId);
			
		} else if ($this->getScopedConfig($module . '/default_settings/enabled', 'default', 0) &&
			$this->getScopedConfig('pure360/default_settings/enabled', 'default', 0) &&
			$this->getScopedConfig('pure360/default_settings/global', 'default', 0))
		{
			return $this->getScopedConfig($module . '/default_' . $group . '/' . $key, 'default', 0);
			
		} else
		{
			return null;
		}
	}

	/**
	 * Returns current scope in admin
	 * 
	 * @return string
	 */
	public function getScope()
	{
		$website = Mage::app()->getRequest()->getParam('website');
		$store = Mage::app()->getRequest()->getParam('store');

		if (!empty($store))
		{
			return 'stores';
		} else if (!empty($website))
		{
			return 'websites';
		} else
		{
			return 'default';
		}
	}

	/**
	 * Returns current scope in admin
	 * 
	 * @return string
	 */
	public function getScopeId()
	{
		$website = Mage::app()->getRequest()->getParam('website');
		$store = Mage::app()->getRequest()->getParam('store');

		if (!empty($store))
		{
			return Mage::getModel("core/store")->load($store)->getStoreId();
		} else if (!empty($website))
		{
			return Mage::getModel("core/website")->load($website)->getWebsiteId();
		} else
		{
			return 0;
		}
	}

	/**
	 * @param string $path
	 * @param string $scope
	 * @param int    $scopeId
	 * @return mixed
	 */
	public function getScopedConfig($path, $scope, $scope_id = 0)
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$table = $resource->getTableName('core/config_data');

		$query = "SELECT value FROM {$table} " .
			" WHERE path = '{$path}' " .
			" AND scope = '{$scope}' " .
			" AND scope_id = '{$scope_id}' ";

		$result = $read->fetchOne($query);
		return $result;
	}

	/**
	 * @param string $path
	 * @param mixed $value
	 * @param string $scope
	 * @param int $scopeId
	 * @return mixed
	 */
	public function setScopedConfig($path, $value, $scope, $scope_id = 0)
	{
		$resource = Mage::getSingleton('core/resource');
		$write = $resource->getConnection('core_write');
		$table = $resource->getTableName('core/config_data');

		$query = "UPDATE {$table} SET value = '{$value}' " .
			" WHERE path = '{$path }' " .
			" AND scope = '{$scope}' " .
			" AND scope_id = '{$scope_id}' ";

		$result = $write->query($query);
		return $result;
	}

	/**
	 * 
	 * @param string $scope
	 * @param int $scopeId
	 * @return array
	 */
	public function getStoreIdsForScope($scope, $scopeId)
	{
		$storeIds = array();
		switch ($scope)
		{
			case 'default' : {
					foreach (Mage::app()->getWebsites() as $website)
					{
						foreach ($website->getGroups() as $group)
						{
							$stores = $group->getStores();
							foreach ($stores as $store)
							{
								$storeIds[] = $store->getId();
							}
						}
					}
					break;
				}
			case 'websites' : {
					foreach (Mage::app()->getWebsite($scopeId)->getGroups() as $group)
					{
						$stores = $group->getStores();
						foreach ($stores as $store)
						{
							$storeIds[] = $store->getId();
						}
					}
					break;
				}
			case 'stores' : {
					$storeIds[] = $scopeId;
					break;
				}
		}
		return $storeIds;
	}

	/**
	 * Verify that all required PHP extensions are loaded
	 *
	 * @param string  $module
	 * @param array   $required
	 * @return boolean
	 */
	public function checkRequirements($module, $required = array())
	{
		// Check for required PHP extensions
		$verified = true;
		$missing = array();
		$defaultRequired = array('soap', 'openssl');
		$required = array_merge($required, $defaultRequired);

		/*
		 * Run through PHP extensions to see if they are loaded
		 * if no, add them to the list of missing and set verified = false flag
		 */
		foreach ($required as $extName)
		{
			if (!extension_loaded($extName))
			{
				$missing[] = $extName;
				$verified = false;
			}
		}

		// If not verified, create a message telling the user what they are missing
		if (!$verified)
		{
			// If module is enabled, disable it
			if ($this->isEnabled())
			{
				Mage::helper($module)->enableModules(false, 'default', 0, true);
			}
			// Create message informing of missing extensions
			$message = Mage::getSingleton('core/message')->error(
				Mage::helper('pure360_common')->__(
					sprintf(
						'The module "' . $module . '" has been automatically disabled due to missing PHP extensions: %s', implode(',', $missing)
					)
				)
			);
			$message->setIdentifier($module);
			Mage::getSingleton('adminhtml/session')->addMessage($message);
			return false;
		}

		return true;
	}

	/**
	 * 
	 * @param array $arr
	 * @return string
	 */
	public function arrayToCsv($arr)
	{
		$output = fopen('php://output', 'w');
		ob_start();
		fputcsv($output, $arr);
		fclose($output);
		$csv = ob_get_clean();

		return trim($csv);
	}

	public function setErrorTemplate(&$block, Exception $e)
	{
		$block
			->setError($e)
			->setTemplate('pure360/common/error.phtml');
		
		return false;
	}

	/**
	 * @param string      $message
	 * @param string|null $file
	 * @return bool|void
	 */
	public function writeDebug($message, $file = 'debug')
	{
		if ($this->isDebugEnabled())
		{
			return $this->writeLog($message, $file, Zend_Log::DEBUG);
		}
	}

	/**
	 * @param string      $message
	 * @param string|null $file
	 * @return bool|void
	 */
	public function writeInfo($message, $file = 'info')
	{
		if ($this->isNoticesEnabled())
		{
			if (Mage::getSingleton('admin/session')->isLoggedIn())
			{
				/* @var $message Mage_Core_Model_Message_Notice */
				$message = Mage::getSingleton('core/message')->notice("[Pure360] {$message}");
				Mage::getSingleton('adminhtml/session')->addMessage($message);
			} else
			{
				Mage::getSingleton('core/session')->addNotice("[Pure360] {$message}");
			}
		}
		return $this->writeLog($message, $file, Zend_Log::INFO);
	}

	/**
	 * @param Exception|string $message
	 * @param string|null      $file
	 * @return bool|void
	 */
	public function writeError($message, $file = 'error')
	{
		if (is_object($message) && $message instanceOf Exception)
		{
			$message = $message->getMessage();
		}
		if ($this->isNoticesEnabled())
		{
			if (Mage::getSingleton('admin/session')->isLoggedIn())
			{
				/* @var $message Mage_Core_Model_Message_Error */
				$message = Mage::getSingleton('core/message')->error("[Pure360] {$message}");
				Mage::getSingleton('adminhtml/session')->addMessage($message);
			} else
			{
				Mage::getSingleton('core/session')->addError("[Pure360] {$message}");
			}
		}
		return $this->writeLog($message, $file, Zend_Log::ERR);
	}

	/**
	 * @param string      $message
	 * @param string|null $file
	 * @param int         $level
	 * @return bool|void
	 */
	public function writeLog($message, $file = null, $level = Zend_Log::DEBUG)
	{
		if (!empty($file))
		{
			$file = '-' . $file;
		}

		$file = 'pure360' . $file . '.log';

		if (!is_string($message))
		{
			if (method_exists($message, '__toString'))
			{
				$message = $message->__toString();
			} else
			{
				return false;
			}
		}

		return Mage::log($message, $level, $file, true);
	}
						
}
