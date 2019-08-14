<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_List_Model_Address extends Mage_Core_Model_Abstract
{

	/**
	 * @var array<Mage_Customer_Model_Attribute>
	 */
	private $_addressAttributes;

	/**
	 * @var array<string>
	 */
	protected $_ignoreAttributes = array(
		'firstname',
		'lastname',
		'middlename',
		'prefix',
		'region_id',
		'suffix',
		'vat_id',
		'vat_is_valid',
		'vat_request_id',
		'vat_request_date',
		'vat_request_success',
		'created_at',
		'updated_at',
		'entity_id',
		'entity_type_id',
		'increment_id',
		'parent_id',
		'attribute_set_id'
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
					'name' => 'pure360_list_address_fields[]',
					'value' => $value, 
					'label' => $label, 
					'class' => 'pureCheckbox');
			}
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
		return $this->_getAddressAttributes();
	}

	/**
	 * @return array
	 */
	private function _getAddressAttributes()
	{
		if ($this->_addressAttributes === null)
		{
			$address = Mage::getModel('customer/address');
			$this->_addressAttributes = $attributes = $address->getAttributes();
		}

		return $this->_addressAttributes;
	}

}
