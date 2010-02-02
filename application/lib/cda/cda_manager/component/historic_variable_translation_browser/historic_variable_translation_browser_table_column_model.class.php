<?php
/**
 * @package cda.tables.variable_translation_table
 */

require_once dirname(__FILE__).'/../../../tables/historic_variable_translation_table/default_historic_variable_translation_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../historic_variable_translation.class.php';

/**
 * Table column model for the historic_variable_translation browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class HistoricVariableTranslationBrowserTableColumnModel extends DefaultHistoricVariableTranslationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function HistoricVariableTranslationBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(2);
		$this->set_default_order_direction(SORT_DESC);
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