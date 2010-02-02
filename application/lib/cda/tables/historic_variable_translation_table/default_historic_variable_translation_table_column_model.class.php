<?php
/**
 * @package cda.tables.variable_translation_table
 */
require_once dirname(__FILE__).'/../../historic_variable_translation.class.php';

/**
 * Default column model for the historic_variable_translation table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultHistoricVariableTranslationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultHistoricVariableTranslationTableColumnModel()
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
		$columns[] = new ObjectTableColumn(HistoricVariableTranslation :: PROPERTY_TRANSLATION);
		$columns[] = new StaticTableColumn(Translation :: get('User'));
		$columns[] = new ObjectTableColumn(HistoricVariableTranslation :: PROPERTY_DATE);
		$columns[] = new StaticTableColumn(Translation :: get('Rating'));

		return $columns;
	}
}
?>