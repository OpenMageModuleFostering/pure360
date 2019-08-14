<?php

/**
 * Description of Campaign
 *
 */
class Pure360_Package_Campaign extends Pure360_Package
{
	/** @var Pure360_Package_Campaign_Automation */
	public $automation;
	
	/** @var Pure360_Package_Campaign_AutomationByDate */
	public $automationByDate;
	
	/** @var Pure360_Package_Campaign_AutomationBySignup */
	public $automationBySignup;
	
	/** @var Pure360_Package_Campaign_ContentItem */
	public $contentItem;
	
	/** @var Pure360_Package_Campaign_Delivery */
	public $delivery;
	
	/** @var Pure360_Package_Campaign_DynamicRegion */
	public $dynamicRegion;
	
	/** @var Pure360_Package_Campaign_Email */
	public $email;
	
	/** @var Pure360_Package_Campaign_Filter */
	public $filter;
	
	/** @var Pure360_Package_Campaign_GroupDelivery */
	public $groupDelivery;
	
	/** @var Pure360_Package_Campaign_MarketingList */
	public $marketingList;
	
	/** @var Pure360_Package_Campaign_Message */
	public $message;
	
	/** @var Pure360_Package_Campaign_One2One */
	public $one2One;
	
	/** @var Pure360_Package_Campaign_Person */
	public $person;
	
	/** @var Pure360_Package_Campaign_Sms */
	public $sms;
	
	
	public function __construct($client)
	{
		$this->automation = new Pure360_Package_Campaign_Automation($client);
		$this->automationByDate = new Pure360_Package_Campaign_AutomationByDate($client);
		$this->automationBySignup = new Pure360_Package_Campaign_AutomationBySignup($client);
		$this->contentItem = new Pure360_Package_Campaign_ContentItem($client);
		$this->delivery = new Pure360_Package_Campaign_Delivery($client);
		$this->dynamicRegion = new Pure360_Package_Campaign_DynamicRegion($client);
		$this->email = new Pure360_Package_Campaign_Email($client);
		$this->filter = new Pure360_Package_Campaign_Filter($client);
		$this->groupDelivery = new Pure360_Package_Campaign_GroupDelivery($client);
		$this->marketingList = new Pure360_Package_Campaign_MarketingList($client);
		$this->message = new Pure360_Package_Campaign_Message($client);
		$this->one2One = new Pure360_Package_Campaign_One2One($client);
		$this->person = new Pure360_Package_Campaign_Person($client);
		$this->sms = new Pure360_Package_Campaign_Sms($client);
	}

}

