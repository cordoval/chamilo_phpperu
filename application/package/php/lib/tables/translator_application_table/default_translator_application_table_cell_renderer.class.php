<?php

namespace application\package;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\DatetimeUtilities;
/**
 * @package package.tables.translator_application
 */
/**
 * Default cell renderer for the translator application table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultTranslatorApplicationTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function __construct()
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
				return DatetimeUtilities :: format_locale_date(null, $translator_application->get_date());
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