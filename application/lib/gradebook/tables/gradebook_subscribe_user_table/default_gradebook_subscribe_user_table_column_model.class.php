<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';


class DefaultGradebookSubscribeUserTableColumnModel extends ObjectTableColumnModel
{

	function DefaultGradebookSubscribeUserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}

	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true);
		return $columns;
	}
}
?>