<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../gradebook.class.php';

class DefaultGradebookTableColumnModel extends ObjectTableColumnModel
{

	function DefaultGradebookTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}

	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(Gradebook :: PROPERTY_NAME, true);
		$columns[] = new ObjectTableColumn(Gradebook :: PROPERTY_DESCRIPTION, true);
		$columns[] = new ObjectTableColumn(Gradebook :: PROPERTY_START, true);
		$columns[] = new ObjectTableColumn(Gradebook :: PROPERTY_END, true);
		$columns[] = new ObjectTableColumn(Gradebook :: PROPERTY_SCALE, true);
		$columns[] = new ObjectTableColumn('Users', false);
		return $columns;
	}
}
?>