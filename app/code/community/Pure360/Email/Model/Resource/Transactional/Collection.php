<?php

/**
 * @package   Pure360\Email
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Email_Model_Resource_Transactional_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

	/**
	 * Initialize Model
	 * 
	 * @return void  
	 * @access public
	 */
	public function _construct()
	{
		parent::_construct();
		$this->_init('pure360_email/transactional');
	}

	/**
	 * @param mixed $storeIds (null, int|string, array, array may contain null)
	 */
	public function addStoreFilter($storeIds)
	{
		$nullCheck = false;

		if (!is_array($storeIds))
		{
			$storeIds = array($storeIds);
		}

		$storeIds = array_unique($storeIds);

		if ($index = array_search(null, $storeIds))
		{
			unset($storeIds[$index]);
			$nullCheck = true;
		}

		$storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

		if ($nullCheck)
		{
			$this->getSelect()->where('store_id IN(?) OR store_id IS NULL', $storeIds);
		} else
		{
			$this->getSelect()->where('store_id IN(?)', $storeIds);
		}

		return $this;
	}

	/**
	 * Sort order by order created_at date
	 *
	 * @param string $dir
	 */
	public function orderByCreatedAt($dir = self::SORT_ORDER_DESC)
	{
		$this->setOrder('created_at', $dir);
		return $this;
	}

	/**
	 * Sort order by order updated_at date
	 *
	 * @param string $dir
	 */
	public function orderByUpdatedAt($dir = self::SORT_ORDER_DESC)
	{
		$this->setOrder('updated_at', $dir);
		return $this;
	}

}