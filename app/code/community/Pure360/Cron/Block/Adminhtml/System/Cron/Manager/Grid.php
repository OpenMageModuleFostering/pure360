<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	protected $_filterVisibility = false;

	/**
	 * @param array $attributes
	 * @return void
	 */
	public function __construct($attributes = array())
	{

		$this->setId('jobGrid');
		$this->setIdFieldName('job_id');
		$this->setDefaultSort('created_at', 'desc');
		$this->setSaveParametersInSession(true);

		parent::__construct($attributes);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('pure360_cron/job')
				->getCollection()
				->addFieldToFilter('enabled', 1);

		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	/**
	 * Add columns to the grid
	 *
	 * @return Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn(
				'job_id', array(
			'header' => Mage::helper('adminhtml')->__('Id'),
			'index' => 'job_id',
			'filter' => false
				)
		);

		$this->addColumn(
				'job_code', array(
			'header' => Mage::helper('adminhtml')->__('Code'),
			'index' => 'job_code',
			'filter' => false
				)
		);

		$this->addColumn(
				'status', array(
			'header' => Mage::helper('adminhtml')->__('Status'),
			'index' => 'status',
			'filter' => false
				)
		);

		$this->addColumn(
				'details', array(
			'header' => Mage::helper('adminhtml')->__('Details'),
			'index' => 'details',
			'filter' => false
				)
		);

		$this->addColumn(
				'message', array(
			'header' => Mage::helper('adminhtml')->__('Message'),
			'index' => 'message',
			'filter' => false
				)
		);

		$this->addColumn(
				'scope', array(
			'header' => Mage::helper('adminhtml')->__('Scope'),
			'index' => 'scope',
			'filter' => false
				)
		);

		$this->addColumn(
				'scheduled_at', array(
			'header' => Mage::helper('adminhtml')->__('Scheduled'),
			'index' => 'scheduled_at',
			'gmtoffset' => true,
			'type' => 'datetime',
			'filter' => false
				)
		);

		$this->addColumn(
				'executed_at', array(
			'header' => Mage::helper('adminhtml')->__('Executed'),
			'index' => 'executed_at',
			'gmtoffset' => true,
			'type' => 'datetime',
			'filter' => false
				)
		);

		$this->addColumn(
				'finished_at', array(
			'header' => Mage::helper('adminhtml')->__('Finished'),
			'index' => 'finished_at',
			'gmtoffset' => true,
			'type' => 'datetime',
			'filter' => false
				)
		);

		if(Mage::helper('pure360_common')->isAdvancedConfig())
		{
			$this->addColumn(
					'crontab', array(
				'header' => Mage::helper('adminhtml')->__('Crontab'),
				'index' => 'crontab',
				'filter' => false,
				'type' => 'input',
				'editable' => 'TRUE'
					)
			);

			$this->addColumn(
					'forced', array(
				//		'header' => Mage::helper('adminhtml')->__(''),
				'align' => 'center',
				'renderer' => 'pure360_cron/adminhtml_system_cron_manager_grid_renderer_force',
				'filter' => false,
				'sortable' => false
			));
		} else
		{
			$this->addColumn(
					'crontab', array(
				'header' => Mage::helper('adminhtml')->__('Crontab'),
				'index' => 'crontab',
				'filter' => false
					)
			);
		}

		return $this;
	}

}
