<?php
/**
 * @package cda.tables.language_pack_table
 */

require_once dirname(__FILE__).'/../../language_pack.class.php';

/**
 * Default cell renderer for the language_pack table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultLanguagePackTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultLanguagePackTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param LanguagePack $language_pack - The language_pack
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $language_pack)
	{
		switch ($column->get_name())
		{
			case LanguagePack :: PROPERTY_ID :
				return $language_pack->get_id();
			case LanguagePack :: PROPERTY_NAME :
				return $language_pack->get_name();
			case LanguagePack :: PROPERTY_TYPE :
				return $language_pack->get_type();
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