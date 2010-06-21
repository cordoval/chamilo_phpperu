<?php

class DefaultInternshipOrganizerOrganisationRelUserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultInternshipOrganizerOrganisationRelUserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	
	private static function get_default_columns()
	{
		
		$columns = array();
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, false, User :: get_table_name());
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, false, User :: get_table_name());
		$columns[] = new ObjectTableColumn(User :: PROPERTY_EMAIL, false, User :: get_table_name());
		
		return $columns;
		
	}
}
?>