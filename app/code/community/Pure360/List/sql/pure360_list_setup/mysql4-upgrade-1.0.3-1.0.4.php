<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

try
{
	// Create New Table
	$installer->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('pure360_optout')}` (
          `optout_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Optout Id',
		  `scope` varchar(10) DEFAULT 'default' COMMENT 'Scope',
		  `scope_id` smallint(5) unsigned NOT NULL COMMENT 'Scope Id',
          `email` varchar(100) COMMENT 'Subscriber email',
		  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created At',
          `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated At',
          PRIMARY KEY (`optout_id`),
          KEY `IDX_PURE360_OPTOUT_SCOPE_ID` (`scope_id`),
          KEY `IDX_PURE360_OPTOUT_CREATED_AT` (`created_at`),
          KEY `IDX_PURE360_OPTOUT_UPDATED_AT` (`updated_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pure360 Optout';
    ");
} catch(Exception $e)
{
	// throw new RuntimeException('Failed Creating and Populating Table: ' . $e->getMessage());
}

$installer->endSetup();