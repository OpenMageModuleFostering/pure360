<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 */
class Pure360_Common_Model_System_Config_Backend_Enable extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
		$value = $this->getValue();

		if(!$value)
		{
			$scope = Mage::helper('pure360_common')->getScope();
			$scopeId = Mage::helper('pure360_common')->getScopeId();
			Mage::helper('pure360_common')->setScopedConfig('pure360_email/' . $scope . '_settings/enabled', 0, $scope, $scopeId);
			Mage::helper('pure360_common')->setScopedConfig('pure360_cart/' . $scope . '_settings/enabled', 0, $scope, $scopeId);
		}
		
        return parent::_beforeSave();
    }
}
