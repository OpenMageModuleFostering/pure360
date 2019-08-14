<?php

/**
 * Description of Data
 *
 */
class Pure360_Package_Data extends Pure360_Package
{
	/**
	 * @var \Pure360\Paint\Package\Data\Meta
	 */
	public $meta;

	/**
	 * @var \Pure360\Paint\Package\Data\Scheduler
	 */
	public $scheduler;

	public function __construct($client)
	{
		$this->meta = new Pure360_Package_Data_Meta($client);
		$this->scheduler = new Pure360_Package_Data_Meta($client);
	}

}
