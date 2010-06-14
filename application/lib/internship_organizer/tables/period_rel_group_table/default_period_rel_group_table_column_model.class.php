<?php

class DefaultInternshipOrganizerPeriodGroupTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerPeriodGroupTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	
	private static function get_default_columns()
	{
		
		$columns = array();
		$columns[] = new ObjectTableColumn(Group :: PROPERTY_NAME, false, Group :: get_table_name());
		$columns[] = new ObjectTableColumn(Group :: PROPERTY_DESCRIPTION, false, Group :: get_table_name());
//		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, true);
//		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_ID, true);
		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelGroup :: PROPERTY_USER_TYPE, true);
		return $columns;
		
	}
}
?>