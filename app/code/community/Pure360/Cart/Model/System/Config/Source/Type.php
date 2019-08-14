<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 */
class Pure360_Cart_Model_System_Config_Source_Type extends Varien_Object
{

	/**
	 * Generate list of email templates
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options = array(
			array(
				'value' => '',
				'label' => '--Select--'
			), array(
				'value' => '1',
				'label' => 'Events Only'
			),
			array(
				'value' => '2',
				'label' => 'Reminder Email'
			),array(
				'value' => '3',
				'label' => 'Clear Cart'
			) 
		);

		return $options;
	}

}
