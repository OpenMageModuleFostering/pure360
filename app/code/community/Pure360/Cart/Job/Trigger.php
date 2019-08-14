<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 */
class Pure360_Cart_Job_Trigger extends Pure360_Cron_Job_Abstract
{
	
	const JOB_CODE = 'PURE360_CART_TRIGGER';
	const MODULE = 'PURE360_CART';

	const XML_PATH_REMINDER_EMAIL_TEMPLATE = 'pure360/cart/general_template';
	const XML_PATH_REMINDER_GUEST_EMAIL_TEMPLATE = 'pure360/cart/guest_template';
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/order/identity';
	
	const DELAY_TYPE_MINUTES = 'minutes';
	const DELAY_TYPE_HOURS = 'hours';
	const DELAY_TYPE_DAYS = 'days';

	const ACTION_TYPE_NOTHING = 1;
	const ACTION_TYPE_REMINDER = 2;
	const ACTION_TYPE_CLEAR = 3;

	private $max_cart_size = 0;
	private $processed = null;

	public function process()
	{
		Mage::helper('pure360_cart')->writeDebug(__METHOD__ . ' - start');

		// Set job properties
		$scope					= $this->_data->getScope();
		$scopeId				= $this->_data->getScopeId();
		$filter					= ($scope === 'default' ? 'default_' : $scope . '_');
		$this->max_cart_size	= Mage::helper('pure360_common')->getScopedConfig('pure360_cart/' . $filter . 'settings/max_cart_size', $scope, $scopeId);
		$this->processed		= array();

		for($triggerId = 1; $triggerId <= 3;  $triggerId++)
		{
			$triggerName = 'trigger' . ($triggerId);
			$triggerEnabled = Mage::helper('pure360_common')->getScopedConfig('pure360_cart/' . $filter . $triggerName . '/enabled', $scope, $scopeId);

			if($triggerEnabled)
			{
				$triggerTemplate		= Mage::helper('pure360_common')->getScopedConfig('pure360_cart/' . $filter . $triggerName . '/template', $scope, $scopeId);
				$triggerTemplateGuest	= Mage::helper('pure360_common')->getScopedConfig('pure360_cart/' . $filter . $triggerName . '/template_guest', $scope, $scopeId);
				$triggerAction			= Mage::helper('pure360_common')->getScopedConfig('pure360_cart/' . $filter . $triggerName . '/action', $scope, $scopeId);
				$triggerDelayType		= Mage::helper('pure360_common')->getScopedConfig('pure360_cart/' . $filter . $triggerName . '/delay_type', $scope, $scopeId);
				$triggerDelay			= Mage::helper('pure360_common')->getScopedConfig('pure360_cart/' . $filter . $triggerName . '/delay', $scope, $scopeId);

				// Set delay in minutes
				switch($triggerDelayType)
				{
					case self::DELAY_TYPE_DAYS :
					{
						$triggerDelay = $triggerDelay * 24 * 60;
					}
					case self::DELAY_TYPE_HOURS :
					{
						$triggerDelay = $triggerDelay * 60;
					}
				}

				$quotes = $this->getQuotesForTrigger($scope, $scopeId, $triggerDelay, $triggerId);

				foreach($quotes as $quote)
				{
					
					$quote->setPure360TriggerId($triggerId);	
					// Update Trigger Id and date
					$this->updateTriggerIdAndTriggerDate($quote);				
					
					// Store Id as processed
					$this->processed[] = $quote->getEntityId();
					
					// Dispatch pure360_cart_trigger_before Event
					Mage::dispatchEvent('pure360_cart_trigger_before', array('trigger' => $triggerName, 'quote' => $quote));
					
					// Switch action
					switch($triggerAction)
					{
						case self::ACTION_TYPE_REMINDER :
						{
							if($quote->getCustomerId())
							{
								$this->sendReminderEmail($quote, $triggerTemplate);
								
							} else
							{
								$this->sendReminderEmail($quote, $triggerTemplateGuest);
							}
							break;
						}
						case self::ACTION_TYPE_CLEAR :
						{
							$this->clearCart($quote);
							break;
						}
					}
					
					// Dispatch pure360_cart_trigger_after Event
					Mage::dispatchEvent('pure360_cart_trigger_after', array('trigger' => $triggerName, 'quote' => $quote));
				}
				
				Mage::helper('pure360_cart')->writeDebug("quotes: " . $quotes->getSize());
			}
			
			// Break if limit reached
			if($this->max_cart_size <= count($this->processed))
			{
				break;
			}
		}

		// Update job message with abandoned carts processed
		$this->_data->setData('message', count($this->processed) . ' carts processed ');

		// Save job data
		$this->_data->save();

		Mage::helper('pure360_cart')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * 
	 * @param string $scope
	 * @param string $scopeId
	 * @param string $triggerTime minutes
	 * @param string $triggerNumber
	 * @return Mage_Sales_Model_Mysql4_Quote_Collection
	 */
	private function getQuotesForTrigger($scope, $scopeId, $triggerTime, $triggerNumber)
	{
		/** @var $collection Mage_Sales_Model_Mysql4_Quote_Collection */
		$collection = Mage::getModel('sales/quote')->getCollection();
		$collection->addFieldToFilter('is_active', 1);

		// Add storeIds for scope
		$storeIds = Mage::helper('pure360_common')->getStoreIdsForScope($scope, $scopeId);
		$collection->addFieldToFilter('store_id', array('in' => $storeIds));
		$collection->addFieldToFilter('customer_email', array('notnull' => true));
		$collection->addFieldToFilter('items_count', array('gt' => 0));
		
		// Add triggerTime filter
		$time = time();
		$lastTime = $time - (60 * $triggerTime); // minutes ago
		$to = date('Y-m-d H:i:s', $lastTime);
		$collection->addFieldToFilter('updated_at', array('to' => $to));
		
		// Add processed filter
		if(!empty($this->processed))
		{
			$collection->addFieldToFilter('entity_id', array('nin' => $this->processed));
		}
		
		// Add order and limit 
		$collection->getSelect()
				->where(new Zend_Db_Expr('pure360_trigger_id = '.($triggerNumber - 1).' OR updated_at > pure360_trigger_dt')) // Add triggerNumber filter
				->order('updated_at asc')->limit($this->max_cart_size);				

		return $collection;
	}

	/**
	 * Update trigger id and trigger date
	 * 
	 * @param Mage_Sales_Model_Quote $quote
	 */
	private function updateTriggerIdAndTriggerDate($quote)
	{
		$resource		= Mage::getSingleton('core/resource');
		$write			= $resource->getConnection('core_write');
		$table			= $resource->getTableName('sales/quote');
		$entityId		= $quote->getEntityId();
		$triggerId		= $quote->getPure360TriggerId();
				
		$sql = "UPDATE $table
				SET pure360_trigger_id = $triggerId, 
					pure360_trigger_dt = NOW()
				WHERE entity_id = $entityId";
		
		$write->query($sql);
	}

	/**
	 * Sends out reminder email
	 * 
	 * @param Mage_Sales_Model_Quote $quote
	 * @return Pure360_Cart_Job_Trigger
	 */
	public function sendReminderEmail($quote, $templateId)
	{
		// Retrieve corresponding email template id and customer name
		if(!$quote->getCustomerId())
		{
			$customerName = $quote->getBillingAddress()->getName();
		
		} else
		{
			/** @var $customer Mage_Customer_Model_Customer */
			$customer = Mage::getModel('customer/customer')->load($quote->getCustomerId());
			$customerName = $customer->getCustomerName();
		}

		$toAddress = $quote->getCustomerEmail();

		$translate = Mage::getSingleton('core/translate');

		$translate->setTranslateInline(false);

		$email = Mage::getModel('core/email_template');

		$email->sendTransactional(
				$templateId, 
				Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY), 
				$toAddress, 
				$customerName, 
				array('quote' => $quote),
				$quote->getStoreId()
		);

		$translate->setTranslateInline(true);
	
		return $this;
	}

	/**
	 * 
	 * @param Mage_Sales_Model_Mysql4_Quote $quote
	 */
	private function clearCart($quote)
	{
		$resource	= Mage::getSingleton('core/resource');
		$write		= $resource->getConnection('core_write');
		$table		= $resource->getTableName('sales/quote');
		$entityId	= $quote->getEntityId();

		$sql = "DELETE FROM $table
				WHERE entity_id = $entityId
				AND is_active = 1";

		$write->query($sql);
	}
}