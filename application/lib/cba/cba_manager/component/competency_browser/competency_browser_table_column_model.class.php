<?php
require_once dirname(__FILE__).'/../../../tables/competency_table/default_competency_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../competency.class.php';

/**
 * Table column model for the competency browser table
 *
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */

class CompetencyBrowserTableColumnModel extends DefaultCompetencyTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function CompetencyBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
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