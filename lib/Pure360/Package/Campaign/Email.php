<?php

/**
 * Facade into all email editing processes. Standard processes are implemented in parent
 *
 * @package campaign
 * @subpackage facade
 *
 * @author	Stewart Waller	<stewart.waller@pure360.com>
 */
class Pure360_Package_Campaign_Email extends Pure360_Entity
{

	public $__FACADE = "bus_facade_campaign_email";

	/**
	 * Create a copy of an existing email, removing or replacing unique data
	 * where necessary.
	 *
	 * @return	$outputData	Object	Email copy
	 */
	public function createCopy($emailBean)
	{
		$outputData = $this->call("createCopy", $emailBean, null);

		return $outputData;
	}

	/**
	 * If a previewType is passed in then the output is a complete HTML document in the 
	 * output character set.  If no preview type is received then all parts of the 
	 * final email are returned in the result.
	 *
	 * @param	$beanId
	 * @param	$previewType This is the type of content to send: plain or html
	 * @param	$useTestValues
	 * @param	$one2OneBeanId
	 * @param	$variationId
	 * @param	$variationIndex
	 * 
	 */
	public function loadForPreview($beanId, $previewType, $useTestValues, $one2OneBeanId, $variationId, $variationIndex)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$processInput = array();
		$processInput["previewType"] = $previewType;
		$processInput["useTestValues"] = $useTestValues;
		$processInput["one2OneBeanId"] = $one2OneBeanId;
		$processInput["variationId"] = $variationId;
		$processInput["variationIndex"] = $variationIndex;

		$outputData = $this->call("loadForPreview", $entityInput, $processInput);

		return $outputData;
	}

	/**
	 * Returns the email client test results for an email referenced by it's message ID.
	 *
	 * @param	$beanId
	 * 
	 */
	public function loadInboxPreviewResultsForView($beanId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$outputData = $this->call("loadInboxPreviewResultsForView", $entityInput, null);

		return $outputData;
	}

	/**
	 * Send the email to the requested email address.  This facade expects the following two
	 * items of process data be be sent along with a valid email bean:
	 *
	 * @param	$beanId
	 * @param	$testAddressTo The email address to send the test to.
	 * @param	$useTestValues
	 * 
	 */
	public function send($beanId, $testAddressTo, $useTestValues)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$processInput = array();
		$processInput["testAddressTo"] = $testAddressTo;
		$processInput["useTestValues"] = $useTestValues;

		$outputData = $this->call("send", $entityInput, $processInput);

		return $outputData;
	}

	/**
	 * Return the image that is attached to the email as a file as image output.
	 * 
	 * @param	$beanId
	 * 
	 */
	public function getImage($beanId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$outputData = $this->call("getImage", $entityInput, null);

		return $outputData;
	}

	/**
	 * Remove the image from the bean
	 * 
	 * @param	$beanId
	 * 
	 */
	public function removeImage($beanId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$outputData = $this->call("removeImage", $entityInput, null);

		return $outputData;
	}

	/**
	 * Return the file that is attached to the email as a downloadable attachment.
	 * 
	 * @param	$beanId
	 * 
	 */
	public function getAttachment($beanId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$outputData = $this->call("getAttachment", $entityInput, null);

		return $outputData;
	}

	/**
	 * Remove the image from the bean
	 * 
	 * @param	$beanId
	 * 
	 */
	public function removeAttachment($beanId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$outputData = $this->call("removeAttachment", $entityInput, null);

		return $outputData;
	}

	/**
	 * Return the availability info for each email client which can be tested.
	 * 
	 * @param	$inboxPreviewTestType
	 * 
	 */
	public function checkInboxPreviewAvailability($inboxPreviewTestType)
	{
		$processInput = array();
		$processInput["inboxPreviewTestType"] = $inboxPreviewTestType;

		$outputData = $this->call("checkInboxPreviewAvailability", null, $processInput);

		return $outputData;
	}

	/**
	 * Request an Inbox Preview using the third party testing aplication
	 * 
	 * @param	$beanId
	 * @param	$variationIds
	 * @param	$inboxPreviewTestType
	 * 
	 */
	public function requestInboxPreview($beanId, $variationIds, $inboxPreviewTestType)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$processInput = array();
		$processInput["variationIds"] = $variationIds;
		$processInput["inboxPreviewTestType"] = $inboxPreviewTestType;

		$outputData = $this->call("requestInboxPreview", $entityInput, $processInput);

		return $outputData;
	}

	/**
	 * Load the spam check results onto the bean using the third-party spam check software/application
	 * 
	 * @param	$beanId
	 * @param	$subjectLineIndex
	 * 
	 */
	public function generateSpamReport($beanId, $subjectLineIndex)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$processInput = array();
		$processInput["subjectLineIndex"] = $subjectLineIndex;

		$outputData = $this->call("generatespamReport", $entityInput, $processInput);

		return $outputData;
	}

	/**
	 * Converts the HTML email body into a readable plaintext email body using a utility class
	 * Implemented Armin Hartinger March 2011
	 * 
	 * @param	$beanId
	 * @param	$bodyField
	 * 
	 */
	public function htmlEmailToTextEmail($beanId, $bodyField)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$processInput = array();
		$processInput["bodyField"] = $bodyField;

		$outputData = $this->call("htmlEmailToTextEmail", $entityInput, $processInput);

		return $outputData;
	}

	/**
	 * Load the contents of a remote web page into the HMTL part of this message, replacing any existing
	 * content.  The function looks for the parameter webPageUri on the input data.
	 * 
	 * @param	$webPageUri
	 * @param	$bodyField
	 * 
	 */
	public function loadWebPageContent($webPageUri, $bodyField)
	{
		$processInput = array();
		$processInput["webPageUri"] = $webPageUri;
		$processInput["bodyField"] = $bodyField;

		$outputData = $this->call("loadWebPageContent", null, $processInput);

		return $outputData;
	}

	/**
	 * Check to see if the dynamic regions have already been loaded for this bean.  If they
	 * have then they will be in the datastore and can be accessed via the dynamicRegionBeanId
	 * variables.  If they haven't, then we need to load them from the database and place them 
	 * in the bean store.
	 * 
	 * @param	$beanId
	 * 
	 */
	public function loadDynamicRegions($beanId)
	{
		$entityInput = array();
		$entityInput["beanId"] = $beanId;

		$outputData = $this->call("loadDynamicRegions", $entityInput, null);

		return $outputData;
	}

	/**
	 * Call this process to change the test configuration data for the current email.  Cannot
	 * use the standard update process due to the dynamic nature of the custom fields and 
	 * region names.
	 * 
	 * @param	$emailBean
	 * 
	 */
	public function updateTestConfiguration($emailBean)
	{
		$outputData = $this->call("updateTestConfiguration", $emailBean, null);

		return $outputData;
	}

	/**
	 * Load the format check results onto the bean
	 * 
	 * @param	$emailBean
	 * 
	 */
	public function generateFormatCheck($emailBean)
	{
		$outputData = $this->call("generateFormatCheck", $emailBean, null);

		return $outputData;
	}

}
