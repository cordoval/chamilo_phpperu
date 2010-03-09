<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';

class DefaultGradebookScoreTableColumnModel extends ObjectTableColumnModel
{

	function DefaultGradebookScoreTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns());
	}

	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn('docent', false);
		$columns[] = new ObjectTableColumn(Gradebook :: PROPERTY_NAME, false);
		$columns[] = new ObjectTableColumn(Gradebook :: PROPERTY_DESCRIPTION, false);
		$columns[] = new ObjectTableColumn(Gradebook :: PROPERTY_SCALE, false);
		$columns[] = new ObjectTableColumn(GradebookRelUser :: PROPERTY_SCORE, false);

		return $columns;
	}
}
?>