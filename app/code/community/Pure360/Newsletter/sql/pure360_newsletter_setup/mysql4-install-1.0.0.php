<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

try {
    $installer->run("
		ALTER TABLE {$installer->getTable('newsletter/subscriber')}
		ADD COLUMN subscription_date datetime NULL default NULL;
    ");
} catch (Exception $e) {
	//
}

try {
    $installer->getConnection()->addKey(
        $installer->getTable('newsletter/subscriber'), 'IDX_PURE360_SUB_DATE', 'subscription_date'
    );
} catch (Exception $e) {
	//
}

try {
    $installer->run("
		ALTER TABLE {$installer->getTable('newsletter/subscriber')}
		ADD COLUMN pure360_sync_status int NULL default 0;
    ");
} catch (Exception $e) {
	//
}

try {
    $installer->getConnection()->addKey(
        $installer->getTable('newsletter/subscriber'), 'IDX_PURE360_SYNC_STATUS', 'pure360_sync_status'
    );
} catch (Exception $e) {
	//
}

$installer->endSetup();