<?php

/**
 * @package   Pure360\Email
 * @copyright 2013 Pure360.com
 */
class Pure360_Email_Model_Messages extends Mage_Core_Model_Abstract
{
	protected $_options;

	public function toOptionArray($isMultiselect = null)
	{
		if (!$this->_options)
		{
			$this->_options = array();

			$scope		= Mage::helper('pure360_common')->getScope();
			$scopeId	= Mage::helper('pure360_common')->getScopeId();
			$username	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $scope . '_settings_transactional/username', $scope, $scopeId);
			$password	= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $scope . '_settings_transactional/password', $scope, $scopeId);
			$url		= Mage::helper('pure360_common')->getScopedConfig('pure360/' . $scope . '_settings/api_url', $scope, $scopeId);
			
			$client		= Mage::helper('pure360_email/api')->getClient($url, $username, $password);
			
			$messages	= Mage::helper('pure360_email/api')->getMessages($client);
			
			$client->logout();
/*
			$messages = array(
				'Dummy_1' => 'Dummy Message Template 1',
				'Dummy_2' => 'Dummy Message Template 2',
				'Dummy_3' => 'Dummy Message Template 3',
				'Dummy_4' => 'Dummy Message Template 4'
			);
*/
			
			foreach($messages as $key => $value)
			{
				$this->_options[$value] = array(
					'name' => 'pure360_email_messages[]',
					'value' => $key, 
					'label' => $value, 
					'class' => 'pureSelect');
			}		
			
			// Sort options alphabetically
			asort($this->_options);
		}

		return $this->_options;
	}

}