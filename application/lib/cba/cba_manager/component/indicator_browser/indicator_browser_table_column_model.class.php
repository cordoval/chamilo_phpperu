<?php
require_once dirname(__FILE__).'/../../../tables/indicator_table/default_indicator_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../indicator.class.php';

/**
 * Table column model for the indicator browser table
 *
 * @author Nick Van Loocke
 */

class IndicatorBrowserTableColumnModel extends DefaultIndicatorTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function IndicatorBrowserTableColumnModel()
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