<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Grid_Renderer_Crontab
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
 
        $html .= '<button onclick="pure360Cron.updateCron(this, '. $row->getId() .'); return false">' . Mage::helper('pure360_cron')->__('Update') . '</button>';
 
        return $html;
    }
 
}