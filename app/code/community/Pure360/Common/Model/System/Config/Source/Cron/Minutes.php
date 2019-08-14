<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 */
class Pure360_Common_Model_System_Config_Source_Cron_Minutes
{

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return mixed  Return description (if any) ...
     * @access public
     */
    public function toOptionArray()
    {
        return array(
            5  => Mage::helper('cron')->__('5 minutes'),
            10 => Mage::helper('cron')->__('10 minutes'),
            15 => Mage::helper('cron')->__('15 minutes'),
            20 => Mage::helper('cron')->__('20 minutes'),
            30 => Mage::helper('cron')->__('30 minutes'),
        );
    }
}