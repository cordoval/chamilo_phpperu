<?php 
namespace repository\content_object\survey;

use group\GroupDataManager;
use group\Group;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

class DefaultSurveyContextRelGroupTableColumnModel extends ObjectTableColumnModel
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
		
		$group_alias = GroupDataManager::get_instance()->get_alias(Group :: get_table_name());
		$columns = array();
		$columns[] = new ObjectTableColumn(Group :: PROPERTY_NAME, true, $group_alias);
		$columns[] = new ObjectTableColumn(Group :: PROPERTY_DESCRIPTION, true, $group_alias);
		$columns[] = new ObjectTableColumn(SurveyContextRelGroup :: PROPERTY_CREATED, true);
		return $columns;
		
	}
}
?>