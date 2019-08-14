<?php

class Pure360_Session
{

	protected $client;
	
	private $properties;
	private $context;
	private $identity;
	private $login;
	private $beanData;
	
	/** @var Pure360_Package_Account */
	public $account;

	/** @var Pure360_Package_Campaign */
	public $campaign;

	/** @var Pure360_Package_Data */
	public $data;

	/** @var Pure360_Package_Other */
	public $other;

	/**
	 * Constructor
	 */
	public function __construct($properties)
	{
		// Set properties
		$this->properties = $properties;

		// Set client
		$this->client = Pure360_Client::getInstance($this->properties);

		// Login to paint
		$context = new Pure360_Package_Context_Context($this->client);

		$this->beanData = $context->login($this->properties["username"], $this->properties["password"]);

		// Set context
		$this->context						= $this->beanData["bus_entity_context"];
		$this->context['defaultLanguage']	= $this->beanData["bus_entity_account_identity"]["defaultLanguage"];
		$this->client->setContext($this->context);

		// Set identity
		$this->identity = $this->beanData["bus_entity_account_identity"];
		$this->client->setIdentity($this->identity);

		// Set login
		$this->login = $this->beanData["bus_entity_account_login"];
		$this->client->setLogin($this->login);

		// Declare 
		$this->account = new Pure360_Package_Account($this->client);
		$this->campaign = new Pure360_Package_Campaign($this->client);
		$this->data = new Pure360_Package_Data($this->client);
		$this->other = new Pure360_Package_Other($this->client);
	}

		
	/**
	 * Get Properties
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * Get Context
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Get Identity
	 */
	public function getIdentity()
	{
		return $this->identity;
	}

	/**
	 * Get Login
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * Get Last Response
	 */
	public function getLastResponse()
	{
		return $this->client->getLastResponse();
	}

	/**
	 * Get Last Request
	 */
	public function getLastRequest()
	{
		return $this->client->getLastRequest();
	}

	public function getBeanData()
	{
		return $this->beanData;
	}
	
	public function logout()
	{
		// Logout of paint
		$context = new Pure360_Package_Context_Context($this->client);
		return $context->logout();
	}

}
