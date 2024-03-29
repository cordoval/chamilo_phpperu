<?php
namespace application\internship_organizer;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

use user\UserDataManager;
use user\User;

class DefaultInternshipOrganizerPeriodRelUserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	
	private static function get_default_columns()
	{
		
		$user_alias = UserDataManager::get_instance()->get_alias(User :: get_table_name());
		
		$columns = array();
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
		$columns[] = new ObjectTableColumn(InternshipOrganizerPeriodRelUser :: PROPERTY_USER_TYPE, true);
		return $columns;
		
	}
}
?>