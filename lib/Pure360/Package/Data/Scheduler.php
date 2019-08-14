<?php

/**
 * Facade to provide access to the data scheduler
 *
 * @package other
 * @subpackage facade
 *
 * @author	Stewart Waller	<stewart.waller@pure360.co.uk>
 */
class Pure360_Package_Data_Scheduler extends Pure360_Entity
{
	public $__FACADE = "bus_facade_data_scheduler";
	
	/**
	 * Call this method to retrieve all queue items currently pending.
	 *
	 * @throws	bean_exception_system
	 */
	public function getQueueItems()
	{
		
	}

}
