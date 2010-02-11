<?php

require_once dirname(__FILE__).'/../../../tables/gradebook_rel_user_table/default_gradebook_rel_user_table_column_model.class.php';

class GradebookRelUserBrowserTableColumnModel extends DefaultGradebookRelUserTableColumnModel
{
	
	private static $modification_column;
	
	function GradebookRelUserBrowserTableColumnModel()
	{
		parent :: __construct();
		//$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
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
