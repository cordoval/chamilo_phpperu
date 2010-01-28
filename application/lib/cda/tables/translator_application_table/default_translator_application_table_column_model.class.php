<?php
/**
 * @package cda.tables.variable_table
 */
require_once dirname(__FILE__).'/../../translator_application.class.php';

/**
 * Default column model for the variable table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultTranslatorApplicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultTranslatorApplicationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();

		$columns[] = new ObjectTableColumn(TranslatorApplication :: PROPERTY_USER_ID);
		$columns[] = new ObjectTableColumn(TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID);
		$columns[] = new ObjectTableColumn(TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID);
		$columns[] = new ObjectTableColumn(TranslatorApplication :: PROPERTY_DATE);
		$columns[] = new ObjectTableColumn(TranslatorApplication :: PROPERTY_STATUS);

		return $columns;
	}
}
?>