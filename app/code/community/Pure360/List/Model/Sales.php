<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 */
class Pure360_List_Model_Sales extends Mage_Core_Model_Abstract
{

	public function toOptionArray()
	{

		$result = array();

		$result[] = array('name' => 'pure360_list_sales_fields[]', 'value' => 'last_order_date', 'label' => 'Last Order Date', 'class' => 'input-checkbox');
		$result[] = array('name' => 'pure360_list_sales_fields[]', 'value' => 'last_order_value', 'label' => 'Last Order Value', 'class' => 'input-checkbox');
		$result[] = array('name' => 'pure360_list_sales_fields[]', 'value' => 'last_product', 'label' => 'Last Product', 'class' => 'input-checkbox');
		$result[] = array('name' => 'pure360_list_sales_fields[]', 'value' => 'total_purchases', 'label' => 'Total Purchases', 'class' => 'input-checkbox');
		$result[] = array('name' => 'pure360_list_sales_fields[]', 'value' => 'total_spend', 'label' => 'Total Spend', 'class' => 'input-checkbox');
		$result[] = array('name' => 'pure360_list_sales_fields[]', 'value' => 'year_to_date_spend', 'label' => 'Year To Date Spend', 'class' => 'input-checkbox');

		// Sort options alphabetically
		usort($result, array(__CLASS__, "cmp"));

		return $result;
	}

	protected static function cmp($a, $b) {
        return strcmp($a["label"], $b["label"]);
	}

}