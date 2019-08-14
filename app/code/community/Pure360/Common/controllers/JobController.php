<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Sync_Controllers_JobController extends Mage_Adminhtml_Controller_Action
{

	function getScheduledJobs()
	{
		$now = time();
		$jobs = Mage::models('pure360_sync_job')->getJobs($now);

		return $jobs;
	}

	function processJobs()
	{
		$jobs = $this->getScheduledJobs();

		foreach ($jobs as $job)
		{
			/* @var $job Pure360_Sync_Interface_Job */
			$result = $job->process();
			Mage::helper('pure360_common')->auditJob($result);
		}
	}

}
