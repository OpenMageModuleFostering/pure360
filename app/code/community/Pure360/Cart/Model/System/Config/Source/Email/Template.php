<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 */
class Pure360_Cart_Model_System_Config_Source_Email_Template extends Varien_Object
{
    /**
     * Config xpath to email template node
     *
     */
    const XML_PATH_TEMPLATE_EMAIL = 'global/template/email/';

    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        if(!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('core/email_template_collection')
                ->load();

            Mage::register('config_system_email_template', $collection);
        }
		
        $options = $collection->toOptionArray();
        $templateName = Mage::helper('adminhtml')->__('Default Template from Locale');
        
		$nodeName = 'pure360_cart_general_template';

		$templateLabelNode = Mage::app()->getConfig()->getNode(self::XML_PATH_TEMPLATE_EMAIL . $nodeName . '/label');
        
		if ($templateLabelNode) {
            $templateName = Mage::helper('adminhtml')->__((string)$templateLabelNode);
            $templateName = Mage::helper('adminhtml')->__('%s (Default Template from Locale)', $templateName);
        }
		
        array_unshift(
            $options,
            array(
                'value'=> $nodeName,
                'label' => $templateName
            )
        );
		
        return $options;
    }

}
