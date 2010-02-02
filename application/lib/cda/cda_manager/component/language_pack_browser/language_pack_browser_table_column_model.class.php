<?php
/**
 * @package cda.tables.language_pack_table
 */

require_once dirname(__FILE__).'/../../../tables/language_pack_table/default_language_pack_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../language_pack.class.php';

/**
 * Table column model for the language_pack browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class LanguagePackBrowserTableColumnModel extends DefaultLanguagePackTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function LanguagePackBrowserTableColumnModel($browser)
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		
		if(get_class($browser) != 'CdaManagerAdminLanguagePacksBrowserComponent')
			$this->add_column(new StaticTableColumn(Translation :: get('TranslationProgress')));
			
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