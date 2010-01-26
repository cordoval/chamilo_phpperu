<?php
/**
 * @package cda.tables.variable_table
 */
require_once dirname(__FILE__).'/../../variable.class.php';

/**
 * Default column model for the variable table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultVariableTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultVariableTableColumnModel()
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

		$columns[] = new ObjectTableColumn(Variable :: PROPERTY_VARIABLE);

		return $columns;
	}
}
?>