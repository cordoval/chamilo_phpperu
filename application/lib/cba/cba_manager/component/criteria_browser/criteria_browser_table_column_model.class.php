<?php
require_once dirname(__FILE__).'/../../../tables/criteria_table/default_criteria_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../criteria.class.php';

/**
 * Table column model for the criteria browser table
 *
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */

class CriteriaBrowserTableColumnModel extends DefaultCriteriaTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function CriteriaBrowserTableColumnModel()
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