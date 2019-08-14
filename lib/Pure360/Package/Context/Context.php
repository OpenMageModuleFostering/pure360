<?php

/**
 * Processes related to the context within which other processes are run.  This sets up the 
 * required data and keeps it in the context entity in the bean store.
 *
 * @package context
 * @subpackage facade
 *
 */
class Pure360_Package_Context_Context extends Pure360_Entity
{

	public $__FACADE = "bus_facade_context";

	/**
	 * Call this function to set-up the required information for a new user and also to confirm
	 * what access the user has to the application.
	 *
	 * @throws	bean_exception_validation
	 */
	public function login($username = null, $password = null)
	{
		$entityInput = array();
		$entityInput["userName"] = $username;
		$entityInput["password"] = $password;

		$results = $this->call("login", $entityInput, null);

		return $results;
	}

	/**
	 * Log out of this application and destroy the context
	 */
	public function logout()
	{
		// Send the profile key to switch the current context
		$resultOutput = $this->call("logout", null, null);

		return $resultOutput;
	}

	/**
	 * Switch the curent context to a specific identity or pool role
	 */
	public function switchProfile($profileKey)
	{
		$entityInput = null;
		$resultOutput = null;

		$entityInput = array("currentRoleKey" => $profileKey);

		// Send the profile key to switch the current context
		$resultOutput = $this->call("bus_facade_context", "switchRole", $entityInput, null);

		return $resultOutput;
	}

}
