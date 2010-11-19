<?php

namespace application\cda;

use common\libraries\ObjectTableCellRenderer;


/**
 * @package cda.tables.cda_language_table
 */
/**
 * Default cell renderer for the cda_language table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultCdaLanguageTableCellRenderer extends ObjectTableCellRenderer
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
	 * @param CdaLanguage $cda_language - The cda_language
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $cda_language)
	{
		switch ($column->get_name())
		{
			case CdaLanguage :: PROPERTY_ID :
				return $cda_language->get_id();
			case CdaLanguage :: PROPERTY_ORIGINAL_NAME :
				return $cda_language->get_original_name();
			case CdaLanguage :: PROPERTY_ENGLISH_NAME :
				return $cda_language->get_english_name();
			case CdaLanguage :: PROPERTY_ISOCODE :
				return $cda_language->get_isocode();
			case CdaLanguage :: PROPERTY_RTL :
				return $cda_language->get_rtl();
			case 'Status' :
			    return $cda_language->get_status_icon();
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