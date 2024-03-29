<?php

namespace application\cda;

use common\libraries\ObjectTableCellRenderer;
/**
 * @package cda.tables.variable_table
 */

/**
 * Default cell renderer for the variable table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultVariableTableCellRenderer extends ObjectTableCellRenderer
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
	 * @param Variable $variable - The variable
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $variable)
	{
		switch ($column->get_name())
		{
			case Variable :: PROPERTY_ID :
				return $variable->get_id();
			case Variable :: PROPERTY_VARIABLE :
				return $variable->get_variable();
			case Variable :: PROPERTY_LANGUAGE_PACK_ID :
				return $variable->get_language_pack_id();
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