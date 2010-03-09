<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../gradebook_rel_user.class.php';

class DefaultGradebookScoreTableCellRenderer implements ObjectTableCellRenderer
{
	
	function DefaultGradebookScoreTableCellRenderer()
	{
	}
	
	function render_cell($column, $gradebookreluser)
	{
		
		$gradebook_id = $gradebookreluser->get_gradebook_id();
		$gradebook = DatabaseGradebookDatamanager ::get_instance()->retrieve_gradebook($gradebook_id);
		
		if ($property = $column->get_name())
		{
			switch ($property)
			{
				case Gradebook :: PROPERTY_NAME :
					return $gradebook->get_name();
				case Gradebook :: PROPERTY_DESCRIPTION :
					return $gradebook->get_description();
				case Gradebook :: PROPERTY_SCALE :
					return $gradebook->get_scale();
				case GradebookRelUser :: PROPERTY_SCORE :
					return $gradebookreluser->get_score();	
			}
		}
		return '&nbsp;';
	}

	function render_id_cell($gradebookreluser){
		return $gradebookreluser->get_gradebook_id();
	}
}
?>