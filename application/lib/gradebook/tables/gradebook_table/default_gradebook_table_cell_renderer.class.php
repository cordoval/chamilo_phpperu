<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../gradebook.class.php';

class DefaultGradebookTableCellRenderer implements ObjectTableCellRenderer
{

	function DefaultGradebookTableCellRenderer()
	{
	}

	function render_cell($column, $gradebook)
	{
		if ($property = $column->get_name())
		{
			switch ($property)
			{
				case Gradebook :: PROPERTY_ID :
					return $gradebook->get_id();
				case Gradebook :: PROPERTY_NAME :
					return $gradebook->get_name();
				case Gradebook :: PROPERTY_DESCRIPTION :
					return $gradebook->get_description();
				case Gradebook :: PROPERTY_START :
					return $this->get_date($gradebook->get_start());
				case Gradebook :: PROPERTY_END :
					return $this->get_date($gradebook->get_end());
				case Gradebook :: PROPERTY_SCALE :
					return $gradebook->get_scale();
			}
		}
		return '&nbsp;';
	}

	function render_id_cell($object){
		return $object->get_id();
	}

	private function get_date($date){
		return date("Y-m-d H:i:00", $date);
	}

}
?>