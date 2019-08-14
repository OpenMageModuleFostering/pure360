<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cart_Helper_Data extends Pure360_Common_Helper_Data
{
	/**
	 * Retrieve helper module name
	 *
	 * @return string
	 */
	protected function getModuleName()
	{
		return 'pure360_cart';
	}
	
	/**
	 * Resets existing active baskets
	 * 
	 * @param Mage_Sales_Model_Quote $quote
	 */
	public function resetBaskets($scope, $scopeId)
	{
		$resource	= Mage::getSingleton('core/resource');
		$write		= $resource->getConnection('core_write');
		$table		= $resource->getTableName('sales/quote');
		$storeIds	= Mage::helper('pure360_common')->getStoreIdsForScope($scope, $scopeId);
		
		$sql = "UPDATE $table
					SET pure360_trigger_count = -1
					WHERE is_active = 1
					AND store_id IN (". implode(",", $storeIds) .")";
		
		$write->query($sql);
	}
}
