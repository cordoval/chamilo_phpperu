<?php

namespace application\cda;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * @package cda.tables.variable_table
 */

/**
 * Default column model for the variable table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultVariableTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function __construct()
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