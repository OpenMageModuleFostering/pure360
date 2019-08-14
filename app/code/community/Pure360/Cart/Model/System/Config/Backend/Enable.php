<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 */
class Pure360_Cart_Model_System_Config_Backend_Enable extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
		$scope		= Mage::helper('pure360_common')->getScope();
		$scopeId	= Mage::helper('pure360_common')->getScopeId();
		$value		= $this->getValue();
		$oldValue	= Mage::helper('pure360_common')->getScopedConfig('pure360_cart/' . $scope . '_settings/enabled', $scope, $scopeId);
		
		if($value)
		{
			$this->transactionalProfileEnabled = Mage::helper('pure360_common')->getScopedConfig('pure360/' . $this->scope . '_settings_transactional/enabled', $this->scope, $this->scopeId);

			if (!$this->transactionalProfileEnabled)
			{
				// Disable module
				$this->setValue(0);
				
				// Set Error
				Mage::getSingleton('core/session')->addError("The Transactional Profile has not been configured for this scope.<br />Please try a different configuration scope or check the API Configuration has been set correctly.");

				return;
			}
		
			if($value && !$oldValue)
			{
				// Reset Baskets
				Mage::helper('pure360_cart')->resetBaskets($scope, $scopeId);
			}
		}
        return parent::_beforeSave();
    }
}
