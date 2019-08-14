<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 */
class Pure360_Cron_AjaxController extends Mage_Core_Controller_Front_Action
{

	/**
	 * Force Job
	 */
	public function forceAction()
	{
		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - start');

		$jobId = (int) $this->getRequest()->getParam('id');
	
		if($jobId)
		{
			$job = Mage::getModel('pure360_cron/job')->load($jobId);
			
			if(false && $job->getStatus() === Mage_Cron_Model_Schedule::STATUS_RUNNING)
			{
				$response = 'Cannot set forced. Job already running: ' . $jobId;
			}
			else
			{
				$job->setMessage('Job forced');
				$job->setForced(1);
				$job->setScheduledAt(null);
				$job->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING);
				$job->setPaused(0);
				$job->save();

				$response = 'Job forced: ' . $jobId;
			}
		} else
		{
			$response = 'Job id not found';
		}
		
		Mage::helper('pure360_common/ajax')->sendResponse($response);

		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * Pause Job
	 */
	public function pauseAction()
	{
		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - start');

		$jobId = (int) $this->getRequest()->getParam('id');
	
		if($jobId)
		{
			$job = Mage::getModel('pure360_cron/job')->load($jobId);
			$job->setPaused(1);
			$job->setMessage('Job paused');
			$job->save();

			$response = 'Job paused: ' . $jobId;
		
		} else
		{
			$response = 'Job id not found';
		}
		
		Mage::helper('pure360_common/ajax')->sendResponse($response);

		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - end');
	}
	
	/**
	 * Resume Job
	 */
	public function resumeAction()
	{
		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - start');

		$jobId = (int) $this->getRequest()->getParam('id');
	
		if($jobId)
		{
			$job = Mage::getModel('pure360_cron/job')->load($jobId);
			$job->setPaused(0);
			$job->setMessage('');
			$job->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING);
			$job->save();

			$response = 'Job resumed: ' . $jobId;
		
		} else
		{
			$response = 'Job id not found';
		}
		
		Mage::helper('pure360_common/ajax')->sendResponse($response);

		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * Update crontab values
	 */
	public function updateAction()
	{
		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - start');

		$jobId = (int) $this->getRequest()->getParam('id');
		$value = $this->getRequest()->getParam('value');

		$response = '';

		$result = preg_match(
				"/(\*|[0-5]?[0-9]|\*\/[0-9]+)\s+"
				. "(\*|1?[0-9]|2[0-3]|\*\/[0-9]+)\s+"
				. "(\*|[1-2]?[0-9]|3[0-1]|\*\/[0-9]+)\s+"
				. "(\*|[0-9]|1[0-2]|\*\/[0-9]+|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\s+"
				. "(\*\/[0-9]+|\*|[0-7]|sun|mon|tue|wed|thu|fri|sat)\s*"
				. "(\*\/[0-9]+|\*|[0-9]+)?/i", $value, $matches);

		if($result)
		{
			if($jobId)
			{
				$job = Mage::getModel('pure360_cron/job')->load($jobId);
				// $job->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING);
				$job->setCrontab($value);
				$job->save();

				$response = 'Job updated: ' . $jobId;
			} else
			{
				$response = 'Job id not found';
			}
		} else
		{
			$response = 'Invalid crontab pattern: ' . $value;
		}

		Mage::helper('pure360_common/ajax')->sendResponse($response);

		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - end');
	}

	public function downloadAction()
	{		
		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - start');

		$path = Mage::getBaseDir('log') . DS .$this->getRequest()->getParam('fileName');

		$file = $path;
				
		if (file_exists($file)) 
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			
			readfile($file);
			exit;	
			
		} else
		{
			die("Error: File not found: " . $file);
		}
	}
	
	public function cleanAction()
	{		
		Mage::helper('pure360_cron')->writeDebug(__METHOD__ . ' - start');

		$path = Mage::getBaseDir('log') . DS .$this->getRequest()->getParam('fileName');

		$fileName = $path;
				
		if (file_exists($fileName)) 
		{
			Mage::helper('pure360_common/file')->wipeFile($fileName);
		}		
	}
}
