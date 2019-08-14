<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 */
class Pure360_Common_Model_System_Config_Source_Message
{

	/**
	 * @var array
	 */
	protected $_options = array();

	/**
	 * @return array
	 */
	public function toOptionArray($token = null)
	{
		if (!empty($this->_options))
		{
			return $this->_options;
		}

		array_unshift($this->_options, array(
			'label' => '-- None Selected --',
			'value' => '',
		));

		return $this->_options;
	}

}
