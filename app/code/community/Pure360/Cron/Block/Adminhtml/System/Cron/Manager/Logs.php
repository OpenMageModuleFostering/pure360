<?php

/**
 * @package   Pure360\Cron
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Cron_Block_Adminhtml_System_Cron_Manager_Logs extends Mage_Adminhtml_Block_Widget_Grid
{

	protected $_filterVisibility = false;

	const MAX_LINES = 10;

	/**
	 * @param array $attributes
	 * @return void
	 */
	public function __construct($attributes = array())
	{
		$this->setId('logGrid');
		$this->setIdFieldName('log_id');
		$this->setDefaultSort('created_at', 'desc');
		$this->setSaveParametersInSession(true);

		parent::__construct($attributes);
	}

	protected function _prepareCollection()
	{
		$collection = new Varien_Data_Collection();
		$dir = Mage::getBaseDir('log');

		foreach(scandir($dir) as $file)
		{
			if($file !== '.' && $file !== '..' && strstr($file, 'pure360'))
			{
				$logPath = $dir . DS . $file;

				$date = date("d m Y H:i", filemtime($logPath));

				$tail = $this->read_last_lines($logPath, self::MAX_LINES);

				$varienObject = new Varien_Object();
				$varienObject->setId($file);
				$varienObject->setName($file);
				$varienObject->setTail($tail);
				$varienObject->setSize(filesize($logPath));
				$varienObject->setCreatedAt($date);
				$collection->addItem($varienObject);
			}
		}

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
			'name', array(
			'header' => Mage::helper('adminhtml')->__('File Name'),
			'index' => 'name',
			'filter' => false)
		);

		$this->addColumn(
			'size', array(
			'header' => Mage::helper('adminhtml')->__('Size'),
			'index' => 'size',
			'renderer' => 'pure360_cron/adminhtml_system_cron_manager_logs_renderer_bytes',
			'filter' => false)
		);

		$this->addColumn(
			'created_at', array(
			'header' => Mage::helper('adminhtml')->__('Created'),
			'index' => 'created_at',
			'type' => 'date',
			'filter' => false)
		);

		$this->addColumn(
			'tail', array(
			'header' => Mage::helper('adminhtml')->__('Tail'),
			'renderer' => 'pure360_cron/adminhtml_system_cron_manager_logs_renderer_tail',
			'index' => 'tail',
			'filter' => false)
		);

		$this->addColumn(
			'download', array(
			'align' => 'center',
			'renderer' => 'pure360_cron/adminhtml_system_cron_manager_logs_renderer_download',
			'index' => 'download',
			'filter' => false,
			'sortable' => false
		));

		$this->addColumn(
			'clean', array(
			'align' => 'center',
			'renderer' => 'pure360_cron/adminhtml_system_cron_manager_logs_renderer_clean',
			'index' => 'clean',
			'filter' => false,
			'sortable' => false
		));

		return $this;
	}

	// Read the last $num_lines lines from stream $fp
	function read_last_lines($path, $num_lines)
	{
		$tail = ''; // content at end of file
		$fp = fopen($path, 'r');

		if($fp)
		{
			$buf_size = 1024;
			$start_read = $filesize = filesize($path); // where to start reading (end of file)
			$i = 0;
			while($start_read > 0 && count(explode("\n", $tail)) < $num_lines + 1)
			{
				$start_read -= $buf_size; // read from last point minus the size we want to read
				if($start_read < 0)
				{
					$read_size = $buf_size + $start_read;
					$start_read = 0;
				} else
				{
					$read_size = $buf_size;
				}
				fseek($fp, $start_read);
				$tail = fread($fp, $read_size) . $tail;
				$i++;
			}
			fclose($fp);
		}

		$lines = array_slice(explode("\n", trim($tail)), -$num_lines, $num_lines);
		return implode("\n", $lines);
	}

}
