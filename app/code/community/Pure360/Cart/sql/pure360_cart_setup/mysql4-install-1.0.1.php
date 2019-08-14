<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

try {
    $installer->run("
		ALTER TABLE {$installer->getTable('sales/quote')}
		ADD COLUMN pure360_trigger_count int NULL default 0;
    ");
} catch (Exception $e) {
	//
}

try {
    $installer->getConnection()->addKey(
        $installer->getTable('sales/quote'), 'IDX_PURE360_CART_TRIG_CNT', 'pure360_trigger_count'
    );
} catch (Exception $e) {
	//
}

$installer->endSetup();
