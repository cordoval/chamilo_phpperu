<?php
/**
 * @package cda.tables.variable_translation_table
 */
require_once dirname(__FILE__).'/../../variable_translation.class.php';

/**
 * Default column model for the variable_translation table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultVariableTranslationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultVariableTranslationTableColumnModel()
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

		$columns[] = new ObjectTableColumn(VariableTranslation :: PROPERTY_VARIABLE_ID, false);
		$columns[] = new ObjectTableColumn('EnglishTranslation', false);
		$columns[] = new ObjectTableColumn(VariableTranslation :: PROPERTY_TRANSLATION);
		$columns[] = new ObjectTableColumn(VariableTranslation :: PROPERTY_RATING);

		return $columns;
	}
}
?>