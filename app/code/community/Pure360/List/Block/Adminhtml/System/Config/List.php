<?php

/**
 * @package   Pure360\List
 * @copyright 2013 Pure360.com
 * @version   1.0.0
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */

class Pure360_List_Block_Adminhtml_System_Config_List extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

	public $list = null;

	public function _construct()
	{
		parent::_construct();

		$scope		= Mage::helper('pure360_common')->getScope();
		$scopeId	= Mage::helper('pure360_common')->getScopeId();
		$collection = Mage::getModel('pure360_list/list')->getCollection();

		foreach ($collection as $list)
		{
			if ($list->getScope() == $scope && $list->getScopeId() == $scopeId)
			{
				$this->list = $list;
				$this->list->load($list->getListId());

				break;
			}
		}
			
		if (!$this->list)
		{
			$this->list = Mage::getModel('pure360_list/list');
			$this->list->setListName('');
			$this->list->setListId(0);
		}

		$this->setTemplate('pure360/list/list.phtml');
	}

	/**
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		return $this->toHtml();
	}

	public function getDataFields()
	{
		$options = Mage::getModel('pure360_list/data')->toOptionArray();
		
		return $this->getTwoColumnsHtml($options, '<p>Choose which customer data fields to map:</p><p><em><strong>Note:</strong> Email, Website and Store are mapped by default.</em></p>');
	}

	public function getAddressFields()
	{
		$options = Mage::getModel('pure360_list/address')->toOptionArray();
		
		return $this->getTwoColumnsHtml($options, '<p>Choose which customer address fields to map:</p>');
	}

	public function getSalesFields()
	{
		$options = Mage::getModel('pure360_list/sales')->toOptionArray();

		return $this->getOneColumnHtml($options, '<p>Choose which customer sales data to map:</p><p><em><strong>Note:</strong> Selecting any of these fields will cause the list syncronization to slow considerably.</em></p>');
	}

	private function getOneColumnHtml($options, $description = null)
	{
		$html = '<tr>';
		$html .= '<td class="label" style="padding-right: 10px;">'.$description.'</td>';
		$html .= '<td class="value" colspan="2" style="width: 400px;">';
		$html .= '<div style="float: left; display: block; width: 400px;">';
		
		foreach($options as $item)
		{	
			$html .= '<div style="padding-bottom: 10px;">';
				$selected =  $this->checkField($item['value'])? 'checked="checked"' : '';
				$html .= '<input '.$selected.' id="' . $item['value'] . '" type="checkbox" name = "' . $item['name'] . '" value="' . $item['value'] . '"/>';
				$html .= '&nbsp;&nbsp;<label for="' . $item['value'] . '">' . $item['label'] . '</label>';
			$html .= '</div>';
		}
		
		$html .= '</div>';
		$html .= '</td>';
		$html .= '</tr>';
		
		return $html;	
	}
	
	private function checkField($fieldValue)
	{
		$listFields = $this->list->getListFields();
	
		if(is_array($listFields))
		{
			foreach($listFields as $field)
			{
				if($field["field_value"] == $fieldValue)
					return true;
			}
		}
		return false;
	}
	
	private function getTwoColumnsHtml($options, $description = null)
	{
		$split = ceil(count($options) / 2);
		$col1 = array_slice($options, 0, $split);
		$col2 = array_slice($options, $split);
		$html = '<tr>';
		$html .= '<td class="label" style="padding-right: 10px;">'.$description.'</td>';
		$html .= '<td class="value" colspan="2" style="width: 400px;">';
		$html .= '<div style="float: left; display: block; width: 200px;">';
		foreach($col1 as $item)
		{	
			$html .= '<div style="padding-bottom: 10px;">';
				$selected =  $this->checkField($item['value'])? 'checked="checked"' : '';
				$html .= '<input '.$selected.' id="' . $item['value'] . '" type="checkbox" name = "' . $item['name'] . '" value="' . $item['value'] . '"/>';
				$html .= '&nbsp;&nbsp;<label for="' . $item['value'] . '">' . $item['label'] . '</label>';
			$html .= '</div>';
		}
		$html .= '</div>';
		$html .= '<div style="float: left; display: block; width: 200px;">';
		foreach($col2 as $item)
		{
			$html .= '<div style="padding-bottom: 10px;">';
				$selected =  $this->checkField($item['value'])? 'checked="checked"' : '';
				$html .= '<input '.$selected.' id="' . $item['value'] . '" type="checkbox" name = "' . $item['name'] . '" value="' . $item['value'] . '"/>';
				$html .= '&nbsp;&nbsp;<label for="' . $item['value'] . '">' . $item['label'] . '</label>';
			$html .= '</div>';
		}		
		$html .= '</div>';
		$html .= '</td>';
		$html .= '</tr>';
		
		return $html;	
	}
}
