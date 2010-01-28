<?php
/**
 * @package cda.tables.translator_application_table
 */

require_once dirname(__FILE__).'/../../../tables/translator_application_table/default_translator_application_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../translator_application.class.php';

/**
 * Table column model for the translator_application browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class TranslatorApplicationBrowserTableColumnModel extends DefaultTranslatorApplicationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function TranslatorApplicationBrowserTableColumnModel()
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