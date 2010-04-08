<?php
require_once dirname(__FILE__).'/../../../tables/gradebook_table/default_gradebook_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../gradebook.class.php';

class GradebookBrowserTableColumnModel extends DefaultGradebookTableColumnModel
{
	private static $modification_column;
	
	function GradebookBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}
	
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