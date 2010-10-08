<?php
/**
 * @package cda.tables.variable_table
 */

require_once WebApplication :: get_application_class_lib_path('cda') . 'tables/variable_table/default_variable_table_column_model.class.php';


/**
 * Table column model for the variable browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class VariableBrowserTableColumnModel extends DefaultVariableTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function VariableBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}

	/**
	 * Gets the modification column
	 * @return ContentObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new StaticTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>