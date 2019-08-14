<?php

/**
 * Description of Account
 *
 */
class Pure360_Package_Account extends Pure360_Package
{

	/**
	 * @var Pure360_Package_Account_Identity
	 */
	public $identity;

	/**
	 * @var Pure360_Package_Account_Login
	 */
	public $login;

	/**
	 * @var Pure360_Package_Account_Pool
	 */
	public $pool;

	public function __construct($client)
	{
		try
		{
			$this->identity = new Pure360_Package_Account_Identity($client);
			$this->login = new Pure360_Package_Account_Login($client);
			$this->pool = new Pure360_Package_Account_Pool($client);
		} catch (Exception $e)
		{
			die($e->getMessage);
		}
	}

}
