<?php

/**
 * @package   Pure360\Email
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Email_Helper_Api extends Pure360_Common_Helper_Api
{

	/**
	 * 
	 * @param Pure360_Session $client
	 * @return array
	 */
	public function getMessages($client)
	{
		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - start');

		$emails = array();

		$results = $client->campaign->email->_search();

		foreach($results as $result)
		{
			/* @var $order Pure360_Entity */
			$email = $client->campaign->email->_load($result);
			$emails[$email['messageId']] = $email['messageName'];
		}

		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - end');

		return $emails;
	}

	/**
	 * 
	 * @param Pure360_Session $client
	 * @return array
	 */
	public function getMessage($client, $messageId)
	{
		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - start');

		$message = null;

		$results = $client->campaign->email->_search();

		foreach($results as $result)
		{
			/* @var $order Pure360_Entity */
			$email = $client->campaign->email->_load($result);
			if($email['messageId'] == $messageId)
			{
				$message = $email;
				break;
			}
		}

		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - end');

		return $message;
	}

	/**
	 * 
	 * @param Pure360_Session $client
	 * @param string $messageSubject
	 * @param string $messageName
	 * @param string $messageBodyHtml
	 * @param string $messageBodyPlain
	 * @param string $toAddress
	 * @param string $fromAddress
	 * @param string $fromDesc
	 * @return array
	 */
	public function sendOne2One($client, $messageSubject, $messageName, $messageBodyHtml, $messageBodyPlain, $toAddress, $fromAddress = null, $fromDesc = null)
	{
		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - start');

		$inputData = array(
			'message_subject'		=> $messageSubject,
			'toAddress'				=> $toAddress,
			'message_contentType'	=> 'EMAIL',
			'message_messageName'	=> $messageSubject,
			'message_bodyHtml'		=> $messageBodyHtml,
			'message_bodyPlain'		=> $messageBodyPlain);

		if($fromDesc) 
		{
	//		$inputData['customFromDesc']	= $fromDesc;
		}
		if($fromAddress) 		
		{
	//		$inputData['customFromAddr']	= $fromAddress;
		}

		$beanData = $client->campaign->one2One->_create($inputData);
		
		$result = $client->campaign->one2One->_store($beanData);

		Mage::helper('pure360_email')->writeDebug(__METHOD__ . ' - end');

		return $result;
	}

}
