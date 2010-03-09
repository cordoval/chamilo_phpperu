<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';

class DefaultGradebookRelUserTableColumnModel extends ObjectTableColumnModel
{

	function DefaultGradebookRelUserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns());
	}

	private static function get_default_columns()
	{
		$columns = array();
		//$columns[] = new ObjectTableColumn(User :: PROPERTY_OFFICIAL_CODE, false);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, false);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, false);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_EMAIL, false);
		$columns[] = new ObjectTableColumn(GradebookRelUser :: PROPERTY_SCORE, false);

		return $columns;
	}
}
?>