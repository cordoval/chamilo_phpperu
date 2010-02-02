<?php
/**
 * @package cda.tables.translator_application
 */

require_once dirname(__FILE__).'/../../translator_application.class.php';

/**
 * Default cell renderer for the translator application table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultTranslatorApplicationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultTranslatorApplicationTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param TranslatorApplication $translator_application - translator_application
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $translator_application)
	{
		switch ($column->get_name())
		{
			case TranslatorApplication :: PROPERTY_USER_ID :
				return $translator_application->get_user()->get_fullname();
			case TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID :
				return $translator_application->get_source_language()->get_english_name();
			case TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID :
				return $translator_application->get_destination_language()->get_english_name();
			case TranslatorApplication :: PROPERTY_DATE :
				return $translator_application->get_date();
			case TranslatorApplication :: PROPERTY_STATUS :
				return $translator_application->get_status_icon();
			default :
				return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>