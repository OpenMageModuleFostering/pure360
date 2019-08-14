<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 */
class Pure360_Cart_Model_System_Config_Source_Delay extends Varien_Object
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
                'value'=> '',
                'label' => '--Select--'
            ), array(
                'value'=> 'days',
                'label' => 'Days'
            ), array(
                'value'=> 'hours',
                'label' => 'Hours'
            ),
			array(
                'value'=> 'minutes',
                'label' => 'Minutes'
            )
		);
	
        return $options;
    }

}
