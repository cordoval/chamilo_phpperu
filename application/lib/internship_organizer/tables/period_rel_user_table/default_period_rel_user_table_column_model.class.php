<?php

class DefaultInternshipOrganizerPeriodRelUserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerPeriodRelUserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	
	private static function get_default_columns()
	{
		
		$columns = array();
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, false, User :: get_table_name());
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, false, User :: get_table_name());
//		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, true);
//		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, true);
		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, true);
		return $columns;
		
	}
}
?>