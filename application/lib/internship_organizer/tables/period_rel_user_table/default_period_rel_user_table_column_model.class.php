<?php

class DefaultInternshipOrganizerPeriodUserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerPeriodUserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	
	private static function get_default_columns()
	{
		
		$columns = array();
		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, true);
		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, true);
		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, true);
		return $columns;
		
	}
}
?>