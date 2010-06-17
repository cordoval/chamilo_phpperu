<?php

//require_once dirname(__FILE__).'/../../../tables/period_rel_user_table/default_period_rel_user_table_column_model.class.php';
require_once Path::get_user_path () . '/lib/user_table/default_user_table_column_model.class.php';

class InternshipOrganizerPeriodUserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function InternshipOrganizerPeriodUserBrowserTableColumnModel($browser)
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
