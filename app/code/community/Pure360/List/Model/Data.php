<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 */
class Pure360_List_Model_Data extends Mage_Core_Model_Abstract
{

	/**
	 * @var array<Mage_Customer_Model_Attribute>
	 */
	private $_customerAttributes;

	/**
	 * @var array<string>
	 */
	protected $_ignoreAttributes = array(
		'increment_id',
		'updated_at',
		'entity_id',
		'attribute_set_id',
		'entity_type_id',
		'password_hash',
		'default_billing',
		'default_shipping',
		'email',
		'confirmation',
		'reward_update_notification',
		'reward_warning_notification',
		'disable_auto_group_change',
		'rp_token',
		'rp_token_created_at',
		'website_id',
		'store_id',
		'created_in', 
		'group_id',
		'pure360_sync_status', 
	);

	public function toOptionArray()
	{
		$result = array();

		foreach ($this->_getAttributes() as $_attribute)
		{
			$value = $_attribute->getAttributeCode();
			$label = $_attribute->getFrontendLabel();

			if (in_array($value, $this->_ignoreAttributes))
			{
				continue;
				
			} else
			{
				$result[$value] = array(
					'name' => 'pure360_list_data_fields[]',
					'value' => $value,
					'label' => $label,
					'class' => 'pureCheckbox');
			}
		}

		$result['customer_group'] = array(
			'name' => 'pure360_list_data_fields[]',
			'value' => 'customer_group',
			'label' => 'Customer Group',
			'class' => 'pureCheckbox');
		
		if (Mage::helper('pure360_common')->isEnterprise())
		{
			$result['customer_segments'] = array(
				'name' => 'pure360_list_data_fields[]',
				'value' => 'customer_segments',
				'label' => 'Customer Segments',
				'class' => 'pureCheckbox');
		}
		
		// Sort options alphabetically
		usort($result, array(__CLASS__, "cmp"));

		return $result;
	}

	protected static function cmp($a, $b) {
        return strcmp($a["label"], $b["label"]);
	}

	/**
	 * @return array
	 */
	protected function _getAttributes()
	{
		return $this->_getCustomerAttributes();
	}

	/**
	 * @return array
	 */
	private function _getCustomerAttributes()
	{
		if ($this->_customerAttributes === null)
		{
			$this->_customerAttributes = Mage::getSingleton('eav/config')
				->getEntityType('customer')
				->getAttributeCollection()
				->addSetInfo();
		}

		return $this->_customerAttributes;
	}

}
