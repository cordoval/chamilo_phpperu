<?php

require_once dirname(__FILE__).'/../../format.class.php';

class DefaultGradebookPublicationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultGradebookPublicationTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Format $format - The format
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $content_object)
	{
		switch ($column->get_name())
		{
			case ContentObject :: PROPERTY_TITLE :
                return '<a href="' . $content_object->get_view_url() . '">' . htmlspecialchars($content_object->get_title()) . '</a>';
				break;
			case ContentObject :: PROPERTY_DESCRIPTION :
				return Utilities :: truncate_string($content_object->get_description(), 50);
				break;
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>