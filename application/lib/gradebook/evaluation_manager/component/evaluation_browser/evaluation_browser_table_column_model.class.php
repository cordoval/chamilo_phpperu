<?php
require_once dirname(__FILE__).'/../../../tables/evaluation_browser_table/default_evaluation_browser_table_column_model.class.php';

class EvaluationBrowserTableColumnModel extends DefaultEvaluationBrowserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function EvaluationBrowserTableColumnModel($browser)
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
			self :: $modification_column = new StaticTableColumn(Translation :: get('action'));
		}
		return self :: $modification_column;
	}
}
?>