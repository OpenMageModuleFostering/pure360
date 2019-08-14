<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
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

}