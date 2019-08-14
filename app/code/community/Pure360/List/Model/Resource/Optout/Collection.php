<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 */
class Pure360_List_Model_Resource_Optout_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	const MAX_COLLECTION_SIZE = 5;
	
	/**
	 * Initialize Model
	 * 
	 * @return void  
	 * @access public
	 */
	public function _construct()
	{
		parent::_construct();
		$this->_init('pure360_list/optout');
	}

	/**
	 * @param string $scope 
	 * @param integer $scopeIds
	 */
	public function addScopeFilter($scope, $scopeId)
	{
		$this->getSelect()->where('scope = ?', $scope);
		$this->getSelect()->where('scope_id = ?', $scopeId);
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