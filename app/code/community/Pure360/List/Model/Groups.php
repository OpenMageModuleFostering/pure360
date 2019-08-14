<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_List_Model_Groups extends Mage_Core_Model_Abstract
{
	protected $_options;

	public function toOptionArray($isMultiselect = null)
	{
		if (!$this->_options)
		{
			$this->_options = array();
			
			$customerGroupsLookup = Mage::helper('pure360_list')->getCustomerGroupLookup();

			foreach($customerGroupsLookup as $key => $value)
			{
				$this->_options[$key] = array(
					'name' => 'pure360_list_groups[]',
					'value' => $value, 
					'label' => $value, 
					'class' => 'pureCheckbox');
			}		
			
			// Sort options alphabetically
			asort($this->_options);
		}

		return $this->_options;
	}

}