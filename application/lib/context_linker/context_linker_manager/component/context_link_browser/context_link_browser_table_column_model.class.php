<?php
/**
 * @package context_linker.tables.context_link_table
 */

require_once dirname(__FILE__).'/../../../tables/context_link_table/default_context_link_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../context_link.class.php';

/**
 * Table column model for the context_link browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class ContextLinkBrowserTableColumnModel extends DefaultContextLinkTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function ContextLinkBrowserTableColumnModel()
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