<?php

/**
 * @abstract
 */
abstract class Pure360_Entity
{
	protected $__FACADE;
	
	protected $client;

	public function __construct($client)
	{
		$this->client = $client;
	}

	public function __get($name)
	{
		switch ($name)
		{
			case 'entityBean' : {
					return str_replace("bus_facade", "bus_entity", $this->__FACADE);
					break;
				}
			case 'facadeBean' : {
					return str_replace("bus_facade", "bus_facade", $this->__FACADE);
					break;
				}
			case 'searchBean' : {
					return $searchBean = str_replace("bus_facade", "bus_search", $this->__FACADE);
					break;
				}
			default : {
					$method = 'get' . $name;
					if (!method_exists($this, $method))
					{
						throw new Exception('Invalid ' . __CLASS__ . ' property');
					}
					return $this->$method();
				}
		}
	}

	protected function call($process, $entityInput, $processInput)
	{
		$result = $this->client->call($this->facadeBean, $process, $entityInput, $processInput);
		return $result;
	}

	/**
	 * "process=search"
	 *
	 * Call to instanciate the search bean associated with this facade.  The search will then use the 
	 * matching search parameters to retrieve the bean records and return a list of ids.
	 */
	public function _search($inputData = null)
	{
		$results = null;
		$results = $this->client->call($this->facadeBean, "search", $inputData, null);
		$results = $results[$this->searchBean];
		return $results["idData"];
	}

	/**
	 * "process=load"
	 *
	 * Load the bean that is associated with this facade.  The input parameters will be used to populate
	 * the bean key, and this bean key will be used to load the bean.
	 */
	public function _load($inputData)
	{
		$result = $this->client->call($this->facadeBean, "load", $inputData, null);
		return $result[$this->entityBean];
	}

	/**
	 * "process=create"
	 *
	 * Create a new blank instance of the bean associated with this facade.  Some default data
	 * will be populated.
	 */
	public function _create($inputData = null)
	{
		$result = $this->client->call($this->facadeBean, "create", $inputData, $inputData);
		return $result[$this->entityBean];
	}

	/**
	 * "process=update"
	 * 
	 * Call to validate the requested fields and update the bean store with new data 
	 * but not save to the database.
	 */
	public function _update($inputData)
	{
		$result = $this->client->call($this->facadeBean, "update", $inputData, null);
		return $result;
	}

	/**
	 * "process=store"
	 *
	 * Call to validate all fields, update the bean and then save to the database.  This will remove the 
	 * bean from the permanent bean store and place it in the temporary request bean store for this 
	 * request only.  That is, requests after this one will need to load the bean again if they want
	 * to use the data from it.
	 */
	public function _store($inputData)
	{
		$result = $this->client->call($this->facadeBean, "store", $inputData, null);
		return $result;
	}

	/**
	 * "process=remove"
	 *
	 * Call to remove the entity (referenced by the bean in the input) from the database.
	 */
	public function _remove($inputData)
	{
		$result = $this->client->call($this->facadeBean, "remove", $inputData, null);
		return $result[$this->entityBean];
	}

	/**
	 * "process=clean"
	 *
	 * Call to cancel the current bean instance and remove it from the bean store only (not the db).
	 */
	public function _clean($inputData)
	{
		$result = $this->client->call($this->facadeBean, "clean", $inputData, null);
		return $result[$this->entityBean];
	}

}