<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
abstract class Pure360_Cron_Job_Abstract implements Pure360_Cron_Job_Interface
{
	/** @var Pure360_Cron_Model_Job */
	protected $_data;

	public function setData(Pure360_Cron_Model_Job $data)
	{
		$this->_data = $data;
	}

	public function getData()
	{
		return $this->_data;
	}

}
