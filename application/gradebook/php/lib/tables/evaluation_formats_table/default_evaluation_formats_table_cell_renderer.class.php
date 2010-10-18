<?php

require_once WebApplication :: get_application_class_lib_path('gradebook') . 'format.class.php';

class DefaultEvaluationFormatsTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultEvaluationFormatsTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Format $format - The format
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $format)
	{ 
		switch ($column->get_name())
		{
			case Format :: PROPERTY_TITLE :
				return ucfirst($format->get_title());
				break;
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>