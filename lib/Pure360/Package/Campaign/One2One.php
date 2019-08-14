<?php

/**
 * Facade into all One to One scheduling processes. Standard processes are implemented in parent
 *
 * @package campaign
 * @subpackage facade
 *
 */
class Pure360_Package_Campaign_One2One extends Pure360_Entity
{

	public $__FACADE = "bus_facade_campaign_one2one";

	/**
	 * "process=changeMessage"
	 *
	 * Method to change the message associated with an existing One to One bean.  
	 *
	 * This method checks the process data for the existence of messsage_messageName or 
	 * message_messageId which can refer to an existing message in the system and 
	 * message_contentType which will define the type of bean to be created during the method.
	 * The contentType is mandatory.  If no name or id is specified for the message, a new message
	 * will be created (but not permanently stored).
	 */
	public function changeMessage()
	{
		
	}

}
