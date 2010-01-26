<?php
/**
 * @package cda.tables.cda_language_table
 */

require_once dirname(__FILE__).'/../../cda_language.class.php';

/**
 * Default cell renderer for the cda_language table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultCdaLanguageTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultCdaLanguageTableCellRenderer()
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