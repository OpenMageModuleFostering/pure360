<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_List_Model_Segments extends Mage_Core_Model_Abstract
{

	protected $_options;

	public function toOptionArray($isMultiselect = false)
	{
		if (!$this->_options)
		{	
			$this->_options = array();
			if (Mage::helper('pure360_common')->isEnterprise())
			{	
				$customerSegmentsLookup = Mage::helper('pure360_list')->getCustomerSegmentLookup();

				foreach ($customerSegmentsLookup as $key => $value)
				{

					$this->_options[$key] = array(
						'name' => 'pure360_list_segments[]',
						'value' => $value,
						'label' => $value,
						'class' => 'pureCheckbox');
				}

				// Sort options alphabetically
				asort($this->_options);
			}
		}
		return $this->_options;
	}

}