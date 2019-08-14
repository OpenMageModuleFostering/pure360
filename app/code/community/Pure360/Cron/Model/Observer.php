<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 */
class Pure360_Cron_Model_Observer extends Mage_Core_Model_Abstract
{
	/**
	 * Constant for job scheduling read-ahead
	 */
	const XML_PATH_SCHEDULE_AHEAD_FOR = 5;

	/**
	 * Constant for reset jobs to pending
	 */
	const XML_PATH_SCHEDULE_RESET_AFTER = 10;

	private $jobSchedule = array();

	/**
	 * This method is called from Magento cron and manages job activity for the 
	 * integration.
	 */
	public function monitorJobs()
	{
		if(Mage::helper('pure360_common')->getScopedConfig('pure360/default_settings/enabled', 'default', 0))
		{
			$this->cleanSchedule();
			$this->scheduleJobs();
			$this->processSchedule();
		}
	}

	/**
	 * This method is called from config_changed events and used to enable/disable
	 * jobs depending on scope.
	 */
	public function checkJobs()
	{
		// Get all jobs
		$collection = Mage::getModel('pure360_cron/job')->getCollection();

		/* var $job Pure360_Cron_Model_Job */
		foreach($collection as $job)
		{
			$scope = $job->getScope();
			$scopeId = $job->getScopeId();
			$module = strtolower($job->getModule());
			$enabled = false;

			$scopeEnabled = Mage::helper('pure360_common')->getScopedConfig('pure360/' . $scope . '_settings/enabled', $scope, $scopeId);
			$scopeGobal = Mage::helper('pure360_common')->getScopedConfig('pure360/' . $scope . '_settings/global', $scope, $scopeId);
			$moduleEnabled = Mage::helper('pure360_common')->getScopedConfig($module . '/' . $scope . '_settings/enabled', $scope, $scopeId);

			switch($scope)
			{
				case 'default' :
				case 'websites' : {
						$enabled = $scopeEnabled && $scopeGobal && $moduleEnabled;
						break;
					}
				case 'stores' : {
						$enabled = $scopeEnabled && $moduleEnabled;
						break;
					}
			}

			$job->setEnabled($enabled);
			$job->save();
		}
	}

	/**
	 * Private function used to reset successful/error jobs to pending after set 
	 * period of time.
	 */
	private function cleanSchedule()
	{
		$collection = Mage::getModel('pure360_cron/job')->getCollection()
				->addFieldToFilter('enabled', 1)
				->load();

		$now = time();

		$resetAfter = self::XML_PATH_SCHEDULE_RESET_AFTER * 60;

		foreach($collection->getIterator() as $job)
		{
			if($job->getStatus() == Mage_Cron_Model_Schedule::STATUS_SUCCESS || $job->getStatus() == Mage_Cron_Model_Schedule::STATUS_ERROR)
			{
				if((strtotime($job->getFinishedAt()) + $resetAfter) < $now)
				{
					$job->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING);
					$job->setMessage(null);
					$job->setScheduledAt(null);
					$job->save();
				}
			}
		}
	}

	/**
	 * Private function used to schedule jobs. Uses Mage cron/schedule for 
	 * parsing crontab expression.
	 */
	private function scheduleJobs()
	{
		$collection = Mage::getModel('pure360_cron/job')->getCollection()
				->addFieldToFilter('enabled', 1)
				->load();

		$exists = array();

		$scheduleAheadFor = self::XML_PATH_SCHEDULE_AHEAD_FOR * 60;

		$schedule = Mage::getModel('cron/schedule');

		foreach($collection->getIterator() as $job)
		{
			if($job->getStatus() == Mage_Cron_Model_Schedule::STATUS_PENDING)
			{
				$jobId = $job->getJobId();
				$cronExpr = $job->getCrontab();
				$code = $job->getJobCode();
				$forced = $job->getForced();
				$paused = $job->getPaused();
				$scheduled = $job->getScheduledAt();

				if(!$cronExpr || !empty($scheduled))
				{
					continue;
				}

				$now = time();

				if($paused === 1)
				{
					$job->setScheduledAt(null);
					$job->setMessage('Job paused');
					$job->setStartedAt(null);
					$job->setFinishedAt(null);
					$job->save();

					unset($this->jobSchedule[$jobId]);
					unset($exists[$jobId . '/' . $job->getScheduledAt()]);
					continue;
				}

				$timeAhead = $now + $scheduleAheadFor;

				$schedule->setJobCode($code)->setCronExpr($cronExpr)->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING);

				for($time = $now; $time < $timeAhead; $time += 60)
				{
					$ts = strftime('%Y-%m-%d %H:%M', $time);

					if(!empty($exists[$jobId . '/' . $ts]))
					{
						// already scheduled
						continue;
					}

					if($schedule->trySchedule($time) || $forced == 1)
					{
						$job->setScheduledAt(time());
						$job->setMessage(null);
						$job->setStartedAt(null);
						$job->setFinishedAt(null);

						// Unset forced
						if($forced == 1)
						{
							$job->setMessage('Job forced');
							$job->setForced(0);
						}

						$job->save();

						$this->jobSchedule[$jobId] = $job;
						$exists[$jobId . '/' . $job->getScheduledAt()] = 1;
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Private function used to process all schedules jobs. Sets job status
	 * through pending > running > success or error.
	 */
	private function processSchedule()
	{
		foreach($this->jobSchedule as $jobId => $job)
		{
			$code = $job->getJobCode();
			$errorMessage = null;

			try
			{
				// Mark job as running
				$start = time();
				$job->setStatus(Mage_Cron_Model_Schedule::STATUS_RUNNING);
				$job->setExecutedAt($start);
				$job->save();

				// ie: Pure360_List_Job_Sync
				$parts = explode('_', $code);
				$module = ucfirst(strtolower($parts[1]));
				$name = ucfirst(strtolower($parts[2]));
				$class = 'Pure360_' . $module . '_Job_' . $name;

				// Instantiate and run job
				$c = new $class;
				$c->setData($job);
				$c->process();

				// Mark job as processed
				$finish = time();
				$job->setStatus(Mage_Cron_Model_Schedule::STATUS_SUCCESS);
				$job->setFinishedAt($finish);
				$job->save();
			} catch(Pure360_Exception_ValidationException $ve)
			{
				$errorMessage = $ve->getMessage() . ': ';

				foreach($ve->getErrors() as $error => $message)
				{
					$errorMessage .= $error . ' [' . $message . "]; \n";
				}
			} catch(Exception $e)
			{
				$errorMessage = $e->getMessage();
			}

			if(!is_null($errorMessage))
			{
				// Mark job as error
				$finish = time();

				$job->setStatus(Mage_Cron_Model_Schedule::STATUS_ERROR);
				$job->setMessage($errorMessage);
				$job->setFinishedAt($finish);
				$job->save();
			}
		}
	}

}
