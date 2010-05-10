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
	function render_cell($column, $internal_item)
	{ 
		$application_manager = WebApplication :: factory($internal_item->get_application());
		$attributes = $application_manager->get_content_object_publication_attribute($internal_item->get_publication_id());
		$rdm = RepositoryDataManager :: get_instance();
		$content_object = $rdm->retrieve_content_object($attributes->get_publication_object_id());
		switch ($column->get_name())
		{
			case ContentObject :: PROPERTY_CREATION_DATE :
				return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $content_object->get_creation_date());
				break;
			case ContentObject :: PROPERTY_TITLE :
                return '<a href="' . $attributes->get_url() . '">' . htmlspecialchars($content_object->get_title()) . '</a>';
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