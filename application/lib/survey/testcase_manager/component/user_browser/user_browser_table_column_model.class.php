<?php

require_once dirname(__FILE__).'/../../../tables/user_table/default_user_table_column_model.class.php';

class TestCaseManagerUserBrowserTableColumnModel extends DefaultTestCaseManagerUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function TestCaseManagerUserBrowserTableColumnModel($browser)
	{
		parent :: __construct();
	//		$this->add_column(self :: get_modification_column());
	}
	
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>