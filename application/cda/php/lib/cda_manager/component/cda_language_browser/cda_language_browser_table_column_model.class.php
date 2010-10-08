<?php
/**
 * @package cda.tables.cda_language_table
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'tables/cda_language_table/default_cda_language_table_column_model.class.php';

/**
 * Table column model for the cda_language browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class CdaLanguageBrowserTableColumnModel extends DefaultCdaLanguageTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function CdaLanguageBrowserTableColumnModel()
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