<?php

/**
 * @package   Pure360\Cart
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cart_Block_Jsinit extends Mage_Adminhtml_Block_Template
{
    /**
     * Include JS in head if section is pure360
     */
    protected function _prepareLayout()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'pure360_cart') {
            $this->getLayout()
                ->getBlock('head')
                ->addJs('mage/adminhtml/pure360/cart.js');
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
        if ($section == 'pure360_cart') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
