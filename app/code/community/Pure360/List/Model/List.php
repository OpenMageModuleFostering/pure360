<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_List_Model_List extends Mage_Core_Model_Abstract
{

	const LIST_STATUS_NEW		= 'NEW';
	const LIST_STATUS_PENDING	= 'PENDING';
	const LIST_STATUS_SYNCING	= 'SYNCING';
	const LIST_STATUS_SYNCED	= 'SYNCED';

	public function _construct()
	{
		parent::_construct();

		$this->_init('pure360_list/list');

		if($this->isObjectNew())
		{
			$this->setListStatus(self::LIST_STATUS_NEW);
		}
	}

}