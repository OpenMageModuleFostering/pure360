<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_List_Helper_Data extends Pure360_Common_Helper_Data
{
	/**
	 * Sales data constants
	 */

	const SALES_LAST_ORDER_DATE 	= 'last_order_date';
	const SALES_LAST_ORDER_VALUE 	= 'last_order_value';
	const SALES_LAST_PRODUCT 		= 'last_product';
	const SALES_TOTAL_PURCHASES 	= 'total_purchases';
	const SALES_TOTAL_SPEND 		= 'total_spend';
	const SALES_YEAR_TO_DATE_SPEND 	= 'year_to_date_spend';

	const DEFAULT_DATE_FORMAT =  'Y-m-d';
	
	/**
	 * Retrieve helper module name
	 *
	 * @return string
	 */
	protected function getModuleName()
	{
		return 'pure360_list';
	}

	/**
	 * 
	 * @return type
	 */
	function getCustomerGroupLookup()
	{
		$customerGroupsLookup = array();
		$customerGroupsCollection = array();
		$customerGroupsModel = Mage::getModel("customer/group");

		if (!empty($customerGroupsModel))
		{
			$customerGroupsCollection = $customerGroupsModel->getCollection();
		}
		foreach ($customerGroupsCollection as $ck => $cv)
		{
			$customerGroupsLookup[$cv->getCustomerGroupId()] = $cv->getCustomerGroupCode();
		}

		return $customerGroupsLookup;
	}

	/**
	 * 
	 * @return type
	 */
	function getCustomerSegmentLookup()
	{
		$customerSegmentsLookup = array();

		if (Mage::helper('pure360_common')->isEnterprise())
		{
			$customerSegmentsCollection = array();
			$customerSegmentsModel = Mage::getModel("enterprise_customersegment/segment");

			if (!empty($customerSegmentsModel))
			{
				$customerSegmentsCollection = $customerSegmentsModel->getCollection();
			}

			foreach ($customerSegmentsCollection as $ck => $cv)
			{
				$customerSegmentsLookup[$cv->getSegmentId()] = $cv->getName();
			}
		}
		
		return $customerSegmentsLookup;
	}

	/**
	 * 
	 * @param Mage_Customer_Model_Customer $customer
	 * @return array
	 */
	public function getCustomerSegmentData($customer)
	{
		$lookup = $this->getCustomerSegmentLookup();
		
		$segmentData = array();
		$customerSegmentIds = array();
		
		if (Mage::helper('pure360_common')->isEnterprise())
		{
			// Retrieve customer segment ids
			$customerSegmentCustomerModel = Mage::getModel("enterprise_customersegment/customer");
			if (!empty($customerSegmentCustomerModel))
			{
				$customerSegmentIds = $customerSegmentCustomerModel
					->getCustomerSegmentIdsForWebsite($customer->getEntityId(), $customer->getWebsiteId());
			}
			
			// Resolve segment names from lookup
			foreach ($customerSegmentIds as $segmentId)
			{
				if(isset($lookup[$segmentId]))
				{
					$segment = $lookup[$segmentId];
					$segmentData[] = $segment;
				}
			}
		}

		return $segmentData;
	}

	/**
	 * 
	 * @param Mage_Customer_Model_Customer $customer
	 * @return array
	 */
	public function getCustomerGroupData($customer)
	{
		$groupData = array();
		$lookup = $this->getCustomerGroupLookup();
		if(isset($lookup[$customer->getGroupId()]))
		{
			$groupData = array($lookup[$customer->getGroupId()]);
			
		}
		return $groupData;
	}
	
	/**
	 * 
	 * @param type $list
	 * @return type
	 */
	public function getListKeys($list)
	{
		$listKeys = array();
		
		$fields = $list->getListFields();
		
		if(is_array($fields))
		{
			foreach ($fields as $field)
			{
				$listKeys[] = $field['field_value'];
			}
		}
		
		return $listKeys;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getDateKeyLookup()
	{
		$dateKeys = array();

		$m = Mage::getSingleton('eav/config')
				->getEntityType('customer')
				->getAttributeCollection()
				->addSetInfo();

		foreach ($m as $attr)
		{
			if($attr->getBackendType() == 'datetime')
			{
				$dateKeys[] = $attr->getAttributeCode();
			}
		}

		$m = Mage::getSingleton('eav/config')
			->getEntityType('customer_address')
			->getAttributeCollection()
			->addSetInfo();

		foreach ($m as $attr)
		{
			if($attr->getBackendType() == 'datetime')
			{
				$dateKeys[] = $attr->getAttributeCode();
			}
		}
	
		$dateKeys[] = 'last_order_date';
		$dateKeys[] = 'subscription_date';
		$dateKeys[] = 'created_at';
		
		return $dateKeys;
	}

	/**
	 * 
	 * @param type $list
	 * @return type
	 */
	public function getSalesFields($list)
	{
		$salesFields = array();
		
		$fields = $list->getListFields();
		
		if(is_array($fields))
		{
			foreach ($fields as $field)
			{
				if($field['field_type'] == 3)
				{
					$salesFields[] = $field['field_value'];
				}
			}
		}
		return $salesFields;
	}

	/**
	 * 
	 * @param Mage_Customer_Model_Customer $customer
	 * @param array $fields
	 * @return type
	 */
	public function getSalesData($customer, array $fields)
	{

		$statistics = Mage::getResourceModel('sales/sale_collection')
				->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true)
				->setOrder('entity_id', Varien_Data_Collection::SORT_ORDER_DESC)
				->setCustomerFilter($customer)->load()->getTotals();

		$salesOrders = Mage::getModel('sales/order')->getCollection()
			->addFilter('customer_id', $customer->getId())
			->setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC);

		$lastOrder = $salesOrders->getFirstItem();

		$data = array();

		foreach ($fields as $field)
		{
			switch ($field)
			{
				case self::SALES_LAST_ORDER_DATE :
					$data[$field] = $lastOrder ? $lastOrder->getCreatedAt() : '';
					break;

				case self::SALES_LAST_ORDER_VALUE :
					$data[$field] = $lastOrder ? $lastOrder->getBaseGrandTotal() : '';
					break;

				case self::SALES_LAST_PRODUCT :
					$data[$field] = Mage::getResourceModel('sales/order_item_collection')
							->setOrderFilter($lastOrder->getId())->getLastItem()->getProductId();
					break;

				case self::SALES_TOTAL_PURCHASES :
					$data[$field] = $statistics->getNumOrders();
					break;

				case self::SALES_TOTAL_SPEND :
					$data[$field] = $statistics->getLifetime();
					break;

				case self::SALES_YEAR_TO_DATE_SPEND :
					$data[$field] = 0;

					$datetime = new DateTime();
					$year = $datetime->format('Y');

					foreach ($salesOrders as $order)
					{
						/* @var $order Mage_Sales_Model_Order */
						$datetime = new DateTime($order->getCreatedAt());
						if ($datetime->format('Y') == $year)
						{
							$data[$field] += $order->getBaseGrandTotal();
						}
						else
							break;
					}
					break;
			}
		}

		return $data;
	}

	/**
	 * 
	 * @param type $storeId
	 * @return Pure360_List_Model_Resource_List
	 */
	public function getListForStore($storeId)
	{
		// Find list
		$list = null;

		$scope = Mage::helper('pure360_common')->getActiveModuleScopeForStore('pure360_list', $storeId);

		if ($scope)
		{
			switch ($scope)
			{
				case 'stores' : {
						$scopeId = $storeId;
						break;
					}
				case 'websites' : {
						$store = Mage::getModel('core/store')->load($storeId);
						$scopeId = $store->getWebsiteId();
						break;
					}
				default : {
						$scopeId = 0;
					}
			}

			$list = $collection = Mage::getModel('pure360_list/list')->getCollection()
				->addFieldToFilter('scope', $scope)
				->addFieldToFilter('scope_id', $scopeId)
				->getFirstItem();
		}

		return $list;
	}
	
	public function toDate($value)
	{
		$date = date_create($value);
		return date_format($date, self::DEFAULT_DATE_FORMAT);
	}

}
