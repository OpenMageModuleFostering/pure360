<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

try
{
	// Create New Table
	$installer->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('pure360_cron_job')}` (
          `job_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Job Id',
		  `job_code` varchar(30) NOT NULL COMMENT 'Job Code',
		  `job_data` varchar(100) DEFAULT NULL COMMENT 'Job Data',
		  `module` varchar(20) DEFAULT 'default' COMMENT 'Module',
		  `scope` varchar(10) DEFAULT 'default' COMMENT 'Scope',
		  `scope_id` smallint(5) unsigned NOT NULL COMMENT 'Scope Id',
     	  `enabled` smallint(1) unsigned DEFAULT 1 COMMENT 'Enabled',
     	  `paused` smallint(1) unsigned DEFAULT 0 COMMENT 'Paused flag',
     	  `forced` smallint(1) unsigned DEFAULT 0 COMMENT 'Forced flag',
          `status` varchar(10) DEFAULT 'pending' NOT NULL COMMENT 'Status',
		  `message` text COMMENT 'Message',
		  `details` varchar(255) COMMENT 'Details',
		  `crontab` varchar(100) DEFAULT '0 * * * *'  NOT NULL COMMENT 'Crontab (eg. 0 * * * * = Every hour)',		 
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
		  `scheduled_at` timestamp NULL DEFAULT NULL COMMENT 'Scheduled At',
		  `executed_at` timestamp NULL DEFAULT NULL COMMENT 'Executed At',
		  `finished_at` timestamp NULL DEFAULT NULL COMMENT 'Finished At',
		  PRIMARY KEY (`job_id`),
          KEY `IDX_PURE360_CRON_JOB_CODE` (`job_code`),
		  KEY `IDX_PURE360_CRON_MODULE` (`module`),
          KEY `IDX_PURE360_CRON_JOB_CREATED_AT` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pure360 Cron Job';
    ");
} catch (Exception $e)
{
	 // throw new RuntimeException('Failed Creating and Populating Table: ' . $e->getMessage());
}

$installer->endSetup();
