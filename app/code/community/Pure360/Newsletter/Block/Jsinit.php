<?php

/**
 * @package   Pure360\Newsletter
 * @copyright 2013 Pure360.com
 */
class Pure360_Newsletter_Block_Jsinit extends Mage_Adminhtml_Block_Template
{
    /**
     * Include JS in head if section is pure360
     */
    protected function _prepareLayout()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'pure360_newsletter') {
            $this->getLayout()
                ->getBlock('head')
                ->addJs('mage/adminhtml/pure360/newsletter.js');
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
        if ($section == 'pure360_newsletter') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
