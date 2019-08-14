<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_AjaxController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Validate Profile Credentials
	 */
	public function validateAction()
	{
		Mage::helper('pure360_common')->writeDebug(__METHOD__ . ' - start');

		$username = Mage::app()->getRequest()->getParam('username');
		$password = Mage::app()->getRequest()->getParam('password');
		$url = Mage::app()->getRequest()->getParam('url');

		$response = Mage::helper('pure360_common/api')->validate($url, $username, $password);

		Mage::helper('pure360_common/ajax')->sendResponse($response);

		Mage::helper('pure360_common')->writeDebug(__METHOD__ . ' - end');
	}

}
