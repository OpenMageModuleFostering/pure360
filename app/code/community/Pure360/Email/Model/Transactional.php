<?php

/**
 * @package   Pure360\Email
 * @copyright 2013 Pure360.com
 */
class Pure360_Email_Model_Transactional extends Mage_Core_Model_Abstract
{

	public function _construct()
	{
		parent::_construct();
		$this->_init('pure360_email/transactional');
	}

}