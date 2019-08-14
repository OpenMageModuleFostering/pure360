<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 */
class Pure360_Common_Helper_Api extends Mage_Core_Helper_Abstract
{

	/**
	 * Check credentials are system login
	 * 
	 * @param string $url
	 * @param string $username
	 * @param string $password
	 * 
	 * @return string
	 */
	public function validate($url, $username, $password)
	{
		Mage::helper('pure360_common')->writeDebug(__METHOD__ . ' - start');

		$result = null;

		try
		{
			// Get api client
			$client = $this->getClient($url, $username, $password);
			$result = $client->getBeanData();

			// Finally perform a logout
			$client->logout();
			
		} catch (Exception $e)
		{
			$result = array('error' => $this->parseException($e));
		}

		Mage::helper('pure360_common')->writeDebug(__METHOD__ . ' - end');

		return $result;
	}

	/**
	 * 
	 * @return Pure360_Session
	 */
	public function getClientForWebsite($type = 'marketing')
	{

		$username	= Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings_'.$type, 'username');
		$password	= Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings_'.$type, 'password');
		$url		= Mage::helper('pure360_common')->getModuleGroupKeyValue('pure360', 'settings', 'api_url');

		// Set client
		$client = $this->getClient($url, $username, $password);

		return $client;
	}

	/**
	 * 
	 * @param type $url
	 * @param type $username
	 * @param type $password
	 * @return Pure360_Session
	 */
	public function getClient($url, $username, $password)
	{
		// Parse url
		$wsdl = $this->getWsdl($url);

		// Instantiate client
		$props = array(
			'username' => $username,
			'password' => $password,
			'wsdl' => $wsdl);

		$client = new Pure360_Session($props);

		return $client;
	}

	/**
	 * Generate the WSDL url, replacing the response domain with paint if needed
     *
     *
	 * @param type $url
	 * @return string
	 */
	public function getWsdl($url)
	{
        $urlParts = parse_url($url);
        $defaults = array(
            'host' => '',
            'scheme' => 'http',
            'wsdl' => 'ctrlPaintLiteral.wsdl'
        );

        if (array_key_exists('host', $urlParts))
        {
            $domainParts = explode('.', $urlParts['host']);
            $paintDomain = preg_replace('/^response$/i', 'paint', $domainParts, 1);
            $host = implode('.', $paintDomain);
            $urlParts['host'] = $host;
        }
        // Add any defaults
        $urlParts = array_merge($defaults, $urlParts);
        // Create the path to the WSDL using the correct Scheme and Host
        $wsdl = "${urlParts['scheme']}://${urlParts['host']}/${urlParts['host']}/${urlParts['wsdl']}";

		return $wsdl;
	}

	/**
	 * 
	 * @param Exception $exception
	 * @return string
	 * @throws Exception
	 */
	public function parseException(Exception $exception)
	{
		$error = '';

		try
		{
			throw $exception;
		} catch (Pure360_Exception_ValidationException $ve)
		{
			$error = "Validation Error - " .
				$this->convertResultToDebugString($ve->getErrors());
		} catch (Pure360_Exception_SecurityException $sece)
		{
			$error = "Security Exception - " . $sece->getMessage();
		} catch (Pure360_Exception_SystemException $se)
		{
			$error = "System Exception - " . $se->getMessage();
		} catch (Exception $ex)
		{
			$error = "Unhandled Exception - " . $ex->getMessage();
		}
		return $error;
	}

	/**
	 * 
	 * @param type $result
	 * @return string
	 */
	public function convertResultToDebugString($result)
	{
		$resultStr = "";

		foreach ($result as $tmpKey => $tmpValue)
		{
			if ($tmpValue != null && is_array($tmpValue))
			{
				$resultStr = $resultStr . $this->convertResultToDebugString($tmpValue);
			} else
			{
				$resultStr = $resultStr . $tmpValue . ' ';
			}
		}
		return $resultStr;
	}

	/**
	 * 
	 * @param string $url
	 * @param string $postFields
	 */
	public function postCurl($url, $postFields)
	{
		// Initialize CURL
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

		$res = curl_exec($curl);

		Mage::helper('pure360_common')->writeDebug($res);

		curl_close($curl);
	}

}
