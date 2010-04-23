<?php

require_once dirname(__FILE__).'/../../format.class.php';

class DefaultEvaluationFormatsTableCellRenderer implements ObjectTableCellRenderer
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
			case Format :: PROPERTY_ACTIVE :
				return Utilities :: display_true_false_icon($format->get_active());
				break;
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>