<?php

class DefaultInternshipOrganizerMentorRelLocationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerMentorRelLocationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	
	private static function get_default_columns()
	{
		
		$columns = array();
		$columns[] = new ObjectTableColumn(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, false, Location :: get_table_name());
		$columns[] = new ObjectTableColumn(InternshipOrganizerMentor :: PROPERTY_LASTNAME, false, Location :: get_table_name());
		$columns[] = new ObjectTableColumn(InternshipOrganizerMentor :: PROPERTY_EMAIL, false, Location :: get_table_name());
		
		return $columns;
		
	}
}
?>