<?php
/**
 * @package context_linker.tables.content_object_table
 */

require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_column_model.class.php';

/**
 * Table column model for the content_object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class ContentObjectBrowserTableColumnModel extends DefaultContentObjectTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function ContentObjectBrowserTableColumnModel()
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