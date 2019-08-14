<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_List_Model_Customer extends Mage_Customer_Model_Customer
{
	/**
	 * Processing object before save data
	 *
	 * @return Pure360_List_Model_Customer
	 */
	protected function _beforeSave()
	{
		parent::_beforeSave();

		if (Mage::helper('pure360_list')->isEnabledForStore($this->getStoreId()))
		{
			$this->setPure360SyncStatus(0);
		}
		
		return $this;
	}

	/**
	 * Processing object after delete data
	 *
	 * @return Pure360_List_Model_Customer
	 */
	protected function _afterDelete()
	{
		parent::_afterDelete();
	}
}

