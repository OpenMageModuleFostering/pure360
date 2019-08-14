<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Model_System_Config_Source_Field
{

	/**
	 * @var array
	 */
	protected $_options = array();

	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		if (!empty($this->_options))
		{
			return $this->_options;
		}

		array_unshift($this->_options, array(
			'label' => '-- not mapped --',
			'value' => '_none_',
		));

		return $this->_options;
	}

	/**
	 * Get Field Object by ID
	 * @param string $id
	 * @return boolean|Pure360_Api_Field_Row
	 */
	public function getFieldObjectById($id)
	{
		return false;
	}

}
