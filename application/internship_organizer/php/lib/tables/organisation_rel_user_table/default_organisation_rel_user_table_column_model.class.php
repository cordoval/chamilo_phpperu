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
		
		     
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
	
		$columns = array();
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_EMAIL, true, $user_alias);
		
		return $columns;
		
	}
}
?>