<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

try {
    $installer->run("
		ALTER TABLE {$installer->getTable('sales/quote')} 
	 	CHANGE pure360_trigger_count pure360_trigger_id INT(11);
    ");
} catch (Exception $e) {
	//
}

try {
    $installer->run("
		ALTER TABLE {$installer->getTable('sales/quote')}  
		DROP INDEX IDX_PURE360_CART_TRIG_CNT,
		ADD INDEX IDX_PURE360_CART_TRIG_ID(pure360_trigger_id);
    ");
} catch (Exception $e) {
	//
}

try {
    $installer->run("
		ALTER TABLE {$installer->getTable('sales/quote')}
		ADD COLUMN pure360_trigger_dt TIMESTAMP DEFAULT '0000-00-00 00:00:00';
    ");
} catch (Exception $e) {
	//
}

try {
    $installer->run("
		UPDATE {$installer->getTable('sales/quote')} 
		SET pure360_trigger_dt = NOW();
    ");
} catch (Exception $e) {
	//
}

$installer->endSetup();
