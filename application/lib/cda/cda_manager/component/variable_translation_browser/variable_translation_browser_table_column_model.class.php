<?php
/**
 * @package cda.tables.variable_translation_table
 */

require_once dirname(__FILE__).'/../../../tables/variable_translation_table/default_variable_translation_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../variable_translation.class.php';

/**
 * Table column model for the variable_translation browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class VariableTranslationBrowserTableColumnModel extends DefaultVariableTranslationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function VariableTranslationBrowserTableColumnModel()
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