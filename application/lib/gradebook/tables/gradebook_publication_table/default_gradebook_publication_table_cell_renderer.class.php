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
			case ContentObject :: PROPERTY_CREATION_DATE :
				return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $content_object->get_creation_date());
				break;
			case ContentObject :: PROPERTY_TITLE :
				return $content_object->get_title();
				break;
			case ContentObject :: PROPERTY_DESCRIPTION :
				return $content_object->get_description();
				break;
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>