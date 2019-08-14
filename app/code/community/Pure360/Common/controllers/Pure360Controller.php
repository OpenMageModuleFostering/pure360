<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Pure360Controller extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Validate Profile Credentials
	 */
	public function errorAction()
	{
		$username = Mage::app()->getRequest()->getParam('username');
		$password = Mage::app()->getRequest()->getParam('password');
		$url = Mage::app()->getRequest()->getParam('url');
		
		
		
$block = $this->getLayout()->createBlock(
    'pure360_common/adminhtml_system_config_error',
    'system.config.error');

			echo $block->toHtml(); 
		exit;
	}

}
