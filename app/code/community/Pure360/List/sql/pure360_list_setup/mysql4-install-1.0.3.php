<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

try
{
	// Create New Table
	$installer->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('pure360_list')}` (
          `list_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'List Id',
		  `scope` varchar(10) DEFAULT 'default' COMMENT 'Scope',
		  `scope_id` smallint(5) unsigned NOT NULL COMMENT 'Scope Id',
          `list_status` varchar(10) DEFAULT 'NEW' COMMENT 'List Status',
		  `list_name` varchar(100) DEFAULT NULL COMMENT 'List Name',
		  `list_filter` varchar(100) DEFAULT NULL COMMENT 'List Filter',
		  `success_tracking_enabled` char(1) DEFAULT 'n' COMMENT 'Success Tracking Enabled',
		  `success_tracking_token` varchar(100) DEFAULT NULL COMMENT 'Success Tracking Token',
		  `double_optin_enabled` char(1) DEFAULT 'n' COMMENT 'Double Optin Enabled',
		  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created At',
          `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated At',
          PRIMARY KEY (`list_id`),
          KEY `IDX_PURE360_LIST_SCOPE_ID` (`scope_id`),
          KEY `IDX_PURE360_LIST_STATUS_ID` (`list_status`),
          KEY `IDX_PURE360_LIST_CREATED_AT` (`created_at`),
          KEY `IDX_PURE360_LIST_UPDATED_AT` (`updated_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pure360 List';
    ");
} catch(Exception $e)
{
	// throw new RuntimeException('Failed Creating and Populating Table: ' . $e->getMessage());
}

try
{
	// Create New Table
	$installer->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('pure360_list_field')}` (
          `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Field Id',
		  `list_id` int(10) unsigned DEFAULT NULL COMMENT 'List Id',
          `field_type` int(10) unsigned DEFAULT NULL COMMENT 'Field Type',
          `field_value` varchar(100) DEFAULT NULL COMMENT 'Field Value',
          PRIMARY KEY (`field_id`),
          KEY `IDX_PURE360_LIST_FIELD_LIST_ID` (`list_id`),
          CONSTRAINT `FK_PURE360_LIST_FIELD_LIST_ID_PURE360_LIST_ID` FOREIGN KEY (`list_id`) REFERENCES `{$this->getTable('pure360_list')}` (`list_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pure360 List Field';
    ");
} catch(Exception $e)
{
	// throw new RuntimeException('Failed Creating and Populating Table: ' . $e->getMessage());
}

if(Mage::helper('pure360_common')->isEnterprise())
{
	try
	{
		// Create New Table
		$installer->run("
			CREATE TABLE IF NOT EXISTS `{$this->getTable('pure360_list_customer_segments')}` (
				`segment_id` int(10) unsigned NOT NULL COMMENT 'Segment Id',
				`customer_id` int(10) unsigned NOT NULL COMMENT 'Customer Id',
				`added_date` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT 'Added Date',
				`updated_date` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT 'Updated Date',
				`website_id` smallint(5) unsigned NOT NULL COMMENT 'Website Id',
				PRIMARY KEY  (`segment_id`,`customer_id`,`website_id`),
				UNIQUE KEY `UNQ_IDX_PURE360_LIST_CUSTOMER_SEGMENTS_ID_WS_ID_CSTR_ID` (`segment_id`,`website_id`,`customer_id`),
				KEY `IDX_PURE360_LIST_CUSTOMER_SEGMENTS_WEBSITE_ID` (`website_id`),
				KEY `IDX_PURE360_LIST_CUSTOMER_SEGMENTS_CUSTOMER_ID` (`customer_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pure360 List Customer Segments';
		");
	} catch(Exception $e)
	{
		// throw new RuntimeException('Failed Creating and Populating Table: ' . $e->getMessage());
	}
}

$model = Mage::getModel('customer/entity_setup', 'core_setup');

// Remove legacy properties
$oldAttributes = array(
	'pure360_sync_status',
	'pure360_sync_flag',
	'pure360_sync_trigger',
	'pure360_synced',
	'pure360_synced_flag',
	'pure360_synced_status'
);

foreach($oldAttributes as $oldAttribute)
{
	$model->removeAttribute('customer', $oldAttribute);

	$attributeId = $model->getAttributeId('customer', $oldAttribute);

	$attributeTable = $model->getAttributeTable('customer', $oldAttribute);

	if(!empty($attributeId) && !empty($attributeTable))
	{
		$installer->run('DELETE FROM ' . $attributeTable . ' WHERE attribute_id = ' . $attributeId);
	}
}

// Add attributes to customer
try
{
	$model->addAttribute(
			'customer', 'pure360_sync_status', array(
		'group' => 'Default',
		'type' => 'int',
		'label' => 'Pure360 Sync Status',
		'input' => 'select',
		'source' => 'eav/entity_attribute_source_boolean',
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'required' => 0,
		'default' => 0,
		'visible_on_front' => 0,
		'used_for_price_rules' => 0,
		'adminhtml_only' => 1,
			)
	);
} catch(Exception $e)
{
	
}

$installer->endSetup();
