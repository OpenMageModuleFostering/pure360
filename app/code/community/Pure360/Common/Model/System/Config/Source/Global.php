<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 */
class Pure360_Common_Model_System_Config_Source_Global
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

		$this->_options = array(
			array(
				'label' => 'Yes',
				'value' => '1',
			),
			array(
				'label' => 'No',
				'value' => '0',
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
