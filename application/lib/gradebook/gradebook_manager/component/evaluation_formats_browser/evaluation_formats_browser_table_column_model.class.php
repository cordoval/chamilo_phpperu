<?php
require_once dirname(__FILE__).'/../../../tables/evaluation_formats_table/default_evaluation_formats_table_column_model.class.php';

class EvaluationFormatsBrowserTableColumnModel extends DefaultEvaluationFormatsTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function EvaluationFormatsBrowserTableColumnModel($browser)
	{
		parent :: __construct();
//        $this->add_column(new ObjectTableColumn(Format :: PROPERTY_TITLE));
//        $this->add_column(new ObjectTableColumn(Format :: PROPERTY_ACTIVE));
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