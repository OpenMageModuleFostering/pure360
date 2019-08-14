<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 */
interface Pure360_Cron_Job_Interface
{

	public function setData(Pure360_Cron_Model_Job $data);

	public function getData();

	public function process();
}

