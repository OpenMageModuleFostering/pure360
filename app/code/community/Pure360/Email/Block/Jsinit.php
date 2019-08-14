<?php

/**
 * @package   Pure360\Email
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Email_Block_Jsinit extends Mage_Adminhtml_Block_Template
{
    /**
     * Include JS in head if section is pure360
     */
    protected function _prepareLayout()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'pure360_email') {
            $this->getLayout()
                ->getBlock('head')
                ->addJs('mage/adminhtml/pure360/email.js');
        }
        parent::_prepareLayout();
    }

    /**
     * Print init JS script into body
     * @return string
     */
    protected function _toHtml()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'pure360_email') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
