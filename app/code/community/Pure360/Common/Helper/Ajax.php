<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Helper_Ajax extends Mage_Core_Helper_Abstract
{

	/**
	 * 
	 * @param object $response
	 */
	public function sendResponse($response)
	{
		Mage::helper('pure360_common')->writeDebug(__METHOD__ . ' - start');
		
		$data = json_encode($response);

		Mage::app()->getResponse()->setBody($data);
		Mage::app()->getResponse()->setHeader('contentType', 'application/json');
		Mage::helper('pure360_common')->writeDebug($data);
		
		Mage::helper('pure360_common')->writeDebug(__METHOD__ . ' - end');
	}

	/**
	 * 
	 * @param Exception $exception
	 */
	public function sendException($exception)
	{
		Mage::helper('pure360_common')->writeDebug(__METHOD__ . ' - start');
		
		$data = json_encode(
			array(
				"message" => $exception->getMessage(),
				"exception" => $exception)
		);

		Mage::app()->getResponse()->setBody($data);
		Mage::app()->getResponse()->setHeader('contentType', 'application/json');
		Mage::helper('pure360_common')->writeError($data);
		
		Mage::helper('pure360_common')->writeDebug(__METHOD__ . ' - end');
	}

}
