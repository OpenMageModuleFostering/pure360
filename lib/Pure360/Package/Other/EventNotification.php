<?php

/**
 * Generic facade containing utility methods that do not relate to any one bean.
 *
 * @package other
 * @subpackage facade
 *
 * @author	Stewart Waller	<stewart.waller@pure360.co.uk>
 */
class Pure360_Package_Other_EventNotification extends Pure360_Entity
{
	public $__FACADE = "bus_facade_eventNotification";

	/**
	 * Call this method to retrieve a batch of event notifications.  Event notifications are read in reverse
	 * data order for the notification types selected.  Note that notification data is returned in a base 64
	 * encoded CSV format string.  Each row in the string will contain the data relevant to the notification
	 * type as the the Pure Interfaces specification.
	 *
	 * @param	array	$notificationTypes	Array of notification types to retrieve
	 * @param	integer	$maxNotifications	The maximum number of notifications to return.  Note that large numbers mean slow  responses
	 * @param	string	$markAsReadInd		If set Y then the events will be marked as read and not returned again (default Y)
	 *
	 * @throws	bean_exception_system
	 */
	public function getBatch()
	{
		
	}

	/**
	 * Call this method to process batch report of Event Notification handling.
	 *
	 * @param	array	$batchReport	Contains Summary and Details of batch 'Event Notification' handling
	 *
	 * @throws	bean_exception_system
	 */
	public function processBatchReport()
	{
		
	}

}

