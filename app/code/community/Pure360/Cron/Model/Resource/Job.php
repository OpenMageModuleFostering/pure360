<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cron_Model_Resource_Job extends Mage_Core_Model_Mysql4_Abstract
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
		$this->_init('pure360_cron/job', 'job_id');
	}

	// from Mage_Cms_Model_Resource_Page::_getLoadSelect()
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = parent::_getLoadSelect($field, $value, $object);
		return $select;
	}

}