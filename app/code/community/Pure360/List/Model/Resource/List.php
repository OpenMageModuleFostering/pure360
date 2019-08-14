<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_List_Model_Resource_List extends Mage_Core_Model_Mysql4_Abstract
{

	/**
	 * Primary key auto increment flag
	 *
	 * @var bool
	 */
	protected $_isPkAutoIncrement = false;

	/**
	 * Initialize Model
	 * 
	 * @return void  
	 * @access public
	 */
	public function _construct()
	{
		$this->_init('pure360_list/list', 'list_id');
	}

	// from Mage_Cms_Model_Resource_Page_Collection::_afterLoad()
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		if($object != null && parent::_afterLoad($object))
		{
			$fields = array();
			
			$listId = $object->getData('list_id');

			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');

			// Load fields
			$select = $connection->select('*')
					->from(array('lf' => $this->getTable('pure360_list/field')))
					->where('lf.list_id = ?', $listId)
					->columns('lf.*');

			foreach ($connection->fetchAll($select) as $arr_row) 
			{
				$fields[] = $arr_row;
			}

			$object->setData('list_fields', $fields);
		}

		return $this;
	}

	// from Mage_Cms_Model_Resource_Page_Collection::_afterSave()
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if($object != null && parent::_afterSave($object))
		{
			if($object->getData('list_data_fields') || 
					$object->getData('list_address_fields') || 
						$object->getData('list_sales_fields'))
			{
				$listId = $object->getData('list_id');

				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

				// Remove existing fields
				$table = $this->getTable('pure360_list/field');
				$connection->query("delete from $table where list_id = $listId");

				// Create insert query
				$query = "insert into $table (list_id, field_type, field_value) 
										values ($listId, :fieldType, :fieldValue)";

				// Add data fields
				$listDataFields = $object->getData('list_data_fields');
				if(is_array($listDataFields))
				{
					foreach($listDataFields as $field)
					{
						if($field)
						{
							$binds = array(
								'fieldType'		=> '1',
								'fieldValue'	=> $field
							);
							$connection->query($query, $binds);
						}
					}
				}

				// Add address fields
				$listAddressFields = $object->getData('list_address_fields');
				if(is_array($listAddressFields))
				{
					foreach($listAddressFields as $field)
					{
						if($field)
						{
							$binds = array(
								'fieldType' => '2',
								'fieldValue' => $field
							);
							$connection->query($query, $binds);
						}
					}
				}

				// Add sales fields
				$listSalesFields = $object->getData('list_sales_fields');
				if(is_array($listSalesFields))
				{
					foreach($listSalesFields as $field)
					{
						if($field)
						{
							$binds = array(
								'fieldType' => '3',
								'fieldValue' => $field
							);
							$connection->query($query, $binds);
						}
					}
				}
			}
		}

		return $this;
	}

}