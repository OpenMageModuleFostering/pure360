<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 */
class Pure360_List_Model_Optout extends Mage_Core_Model_Abstract
{

	public function _construct()
	{
		parent::_construct();

		$this->_init('pure360_list/optout');
	}

}