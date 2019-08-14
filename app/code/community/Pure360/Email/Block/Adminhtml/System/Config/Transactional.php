<?php

/**
 * @package   Pure360\Email
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Email_Block_Adminhtml_System_Config_Transactional extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	const XML_PATH_TEMPLATE_EMAIL = 'global/template/email/';

	public $transactionalProfileEnabled = false;
	private $messages = array();
	private $scope = null;
	private $scopeId = null;

	public function _construct()
	{
		parent::_construct();

		$this->scope = Mage::helper('pure360_common')->getScope();

		$this->scopeId = Mage::helper('pure360_common')->getScopeId();

		$this->transactionalProfileEnabled = Mage::helper('pure360_common')->getScopedConfig('pure360/' . $this->scope . '_settings_transactional/enabled', $this->scope, $this->scopeId);

		if ($this->transactionalProfileEnabled)
		{
			try
			{
				// $this->messages = Mage::getModel('pure360_email/messages')->toOptionArray(true);
				
			} catch (Exception $e)
			{
				Mage::helper('pure360_common')->setErrorTemplate($this, $e);
				return false;
			}
		} else
		{
			if(Mage::helper('pure360_common')->getScopedConfig('pure360_email/' . $this->scope . '_settings/enabled', $this->scope, $this->scopeId))
			{
				// Disable module
				Mage::helper('pure360_common')->setScopedConfig('pure360_email/' . $this->scope . '_settings/enabled', 0, $this->scope, $this->scopeId);
			
				// Set Error
				Mage::getSingleton('core/session')->addError("The Transactional Profile has not been configured for this scope.<br />Please try a different configuration scope or check the API Configuration has been set correctly.");
				
				// Redirect
				Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("*/*/edit/section/pure360_email"));
			}
		}

		$this->setTemplate('pure360/email/transactional.phtml');
	}

	/**
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		return $this->toHtml();
	}

	/**
	 * Generate list of email templates
	 *
	 * @return array
	 */
	public function getTemplates($templateId = null)
	{
		$templates = Mage_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray();

		$html = '<option value="0">- - Not mapped - -</option>';

		foreach ($templates as $option)
		{
			if ($option['value'])
			{
				$selected = ($templateId == $option['value']) ? 'selected="selected"' : '';
				$html .= '<option value="' . $option['value'] . '" ' . $selected . '>' . $option['label'] . '</option>';
			}
		}

		return $html;
	}

	/**
	 * 
	 * @return string
	 */
	public function getMessages($messageId = null)
	{
		$html = '<option value="0">- - Not mapped - -</option>';

		foreach ($this->messages as $option)
		{
			$selected = ($messageId == $option['value']) ? 'selected="selected"' : '';
			$html .= '<option value="' . $option['value'] . '" ' . $selected . '>' . $option['label'] . '</option>';
		}

		return $html;
	}

	/**
	 * 
	 * @return string
	 */
	public function getTransactionals()
	{
		$transactionals = Mage::getModel('pure360_email/transactional')->getCollection()
			->addFieldToFilter('scope', $this->scope)
			->addFieldToFilter('scope_id', $this->scopeId);
		
		return $transactionals;
	}

}
