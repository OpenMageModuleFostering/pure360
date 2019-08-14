<?php

/**
 * Description of Other
 *
 * @author stewart.waller
 */
class Pure360_Package_Other extends Pure360_Package
{
	/** @var Pure360_Package_Other_EventNotification */
	public $eventNotification;

	/** @var Pure360_Package_Other_File */
	public $file;

	public function __construct($client) 
	{
		$this->eventNotification = new Pure360_Package_Other_EventNotification($client);
		$this->file = new Pure360_Package_Other_File($client);
	}
}
