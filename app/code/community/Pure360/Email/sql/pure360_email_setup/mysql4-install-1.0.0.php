<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

try {
    // Create New Table
    $installer->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('pure360_email_transactional')}` (
          `transactional_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Transactional Id',
		  `scope` varchar(10) DEFAULT 'default' COMMENT 'Scope',
		  `scope_id` smallint(5) unsigned NOT NULL COMMENT 'Scope Id',
		  `message_id` int(10) unsigned NOT NULL COMMENT 'Message Id',
          `template_id` varchar(100) DEFAULT NULL COMMENT 'Template Id',
          `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created At',
          `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated At',
          PRIMARY KEY (`transactional_id`),
          KEY `IDX_PURE360_EMAIL_TRANSACTIONAL_SCOPE_ID` (`scope_id`),
          KEY `IDX_PURE360_EMAIL_TRANSACTIONAL_TEMPLATE_ID` (`template_id`),
          KEY `IDX_PURE360_EMAIL_TRANSACTIONAL_CREATED_AT` (`created_at`),
          KEY `IDX_PURE360_EMAIL_TRANSACTIONAL_UPDATED_AT` (`updated_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pure360 Email Transactional';
    ");

} catch (Exception $e) {
   // throw new RuntimeException('Failed Creating and Populating Table: ' . $e->getMessage());
}

$installer->endSetup();
