<?php

/**
 * This Paint client is implements the Singleton pattern.
 */
class Pure360_Client
{

// Store the single instance of Paint_Client
	private static $m_pInstance;
	private $wsdl;
	private $context;
	private $identity;
	private $login;
	private $request;
	private $response;

	private function __construct($opts = null)
	{
		$this->identity = null;
		$this->login = null;
		$this->context = null;

		if (isset($opts["wsdl"]))
			$this->wsdl = $opts["wsdl"];
	}

	public static function getInstance($opts)
	{
		if (!self::$m_pInstance)
		{
			self::$m_pInstance = new Pure360_Client($opts);
		}

		return self::$m_pInstance;
	}

	public function setWsdl($wsdl)
	{
		$this->wsdl = $wsdl;
		return $this;
	}

	public function getWsdl()
	{
		return $this->wsdl;
	}

	public function setContext($context)
	{
		$this->context = $context;
	}

	public function getContext()
	{
		return $this->context;
	}

	public function setIdentity($identity)
	{
		$this->identity = $identity;
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function setLogin($login)
	{
		$this->login = $login;
	}

	public function getLogin()
	{
		return $this->login;
	}

	public function getLastRequest()
	{
		return $this->request;
	}

	public function getLastResponse()
	{
		return $this->response;
	}

	public function call($class, $process, $entityInput, $processInput)
	{

		$results = array();

		$response = null;
		
		if (empty($this->wsdl))
		{
			throw new Exception("[Pure360] api url not set");
		}

		try
		{
			// Disable XDebug and use custom error handler to catch any WSDL problems
			if (function_exists('xdebug_disable'))
			{
				xdebug_disable();
			}
			set_error_handler('my_custom_soap_wsdl_error_handler');

			$options = array('trace' => 1, 'exceptions' => TRUE);
			$client = new SoapClient($this->wsdl, $options);

			// Restore XDebug and default error handler
			if (function_exists('xdebug_enable'))
			{
				xdebug_enable();
			}
			restore_error_handler();

			// Convert input arrays to literal arrays before sending as parameters
			$entityInput = self::convertToLiteralArray($entityInput);
			$processInput = self::convertToLiteralArray($processInput);

			// Call handleRequest web service method
			$response = $client->handleRequest($this->context["beanId"], $class, $process, $entityInput, $processInput);

			// Convert resultOutput from literal array to associative array										
			$response = self::convertFromLiteralArray($response);

		} catch (Exception $e)
		{
			throw new Exception('[Pure360] Could not connect to host: '.$class.'::'.$process.' '. $e->getMessage());
		}
		
		// Validate response
		if (isset($response['result']))
		{
			switch ($response["result"])
			{
				case "success":
					if (!empty($response["resultData"]))
					{
						$results = $response["resultData"];
					} else
					{
						$results = array();
					}
					break;

				case "bean_exception_validation":
					if (!empty($response["resultData"]))
					{
						$results = $response["resultData"];
					} else
					{
						$results = array();
					}
					throw new Pure360_Exception_ValidationException($response["resultData"]);

				case "bean_exception_security":
					
					throw new Pure360_Exception_SecurityException($response["resultData"]);

				case "bean_exception_system":
					
					throw new Pure360_Exception_SystemException($response["resultData"]);

				default:
					
					throw new Exception("[Pure360] Unhandled exception");
			}
			
		} else
		{
			throw new Exception("[Pure360] Invalid api response.");
		}

		// Save request and response
		$this->request = $client->__getLastRequest();
		$this->response = $client->__getLastResponse();


		return $results;
	}

	/**
	 * Convert the PAINT array as received via a (literal) SOAP request and convert it into
	 * an associative array for use in the transposer
	 *
	 * @param	array	$litArray		Array of standard objects
	 *
	 * @return	array	Associative array of the data held in the literal object
	 */
	function convertFromLiteralArray($litArray)
	{
		$processedArray = array();

		if (!empty($litArray) && is_object($litArray))
		{
// Use this to cope with PHP as it puts the array into a "Struct" variable
// even though we didn't ask it to
			$pairs = (!empty($litArray->pairs->Struct) ? @$litArray->pairs->Struct : @$litArray->pairs);

// Loop through the data converting the object into a nexted associative array
			if (!empty($pairs))
			{
				if (!is_array($pairs))
				{
					$pairs = array($pairs);
				}

				foreach ($pairs as $pairItem)
				{
					$pairValue = $pairItem->value;
					$pairKey = $pairItem->key;

					if (!empty($pairValue->arr) && is_object($pairValue->arr))
					{
						$processedArray[$pairKey] = self::convertFromLiteralArray($pairValue->arr);
					} else
					{
						$processedArray[$pairKey] = @$pairValue->str;
					}
				}
			}
		}

		return $processedArray;
	}

	/**
	 * Convert an associative array into the nested objects required for responses via literal 
	 * soap requests
	 *
	 * @param	array	$source		Source associative array
	 *
	 * @return	object	Object of type PaintArray	
	 */
	function convertToLiteralArray($source)
	{
		$arrayObj = new paintArray();

		if (!empty($source) && is_array($source))
		{
			foreach ($source as $key => $value)
			{
				$pair = new paintKeyValuePair();
				$pair->key = $key;
				$pair->value = new stdClass();

				if (isset($value) && is_array($value))
				{
					$pair->value->arr = self::convertToLiteralArray($value);
				} else
				{
					$pair->value->str = $value;
				}

				$arrayObj->pairs[] = $pair;
			}
		}

		return $arrayObj;
	}

}

/**
 * Nested classes requried for clients calling PAINT using literal.
 */
class paintArray
{

	public $pairs = array();

}

/**
 * Nested classes requried for clients calling PAINT using literal.
 */
class paintKeyValuePair
{

	public $key;
	public $value;

}

// https://bugs.php.net/bug.php?id=47584
function my_custom_soap_wsdl_error_handler($errno, $errstr, $errfile = NULL, $errline = NULL, $errcontext = NULL)
{
	$wsdl_url = isset($errcontext['wsdl']) ? $errcontext['wsdl'] : '';
	$msg = "Could not load WSDL from url.";
	$e = new Exception($msg, 0);
	throw $e;
}
