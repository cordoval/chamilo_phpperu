<?php

require_once dirname(__FILE__).'/../../../tables/gradebook_subscribe_user_table/default_gradebook_subscribe_user_table_column_model.class.php';

class GradebookSubscribeUserBrowserTableColumnModel extends DefaultGradebookSubscribeUserTableColumnModel
{

	private static $modification_column;

	function GradebookSubscribeUserBrowserTableColumnModel($browser)
	{
		parent :: __construct();
		//$this->add_column(new ObjectTableColumn(User :: PROPERTY_USERNAME, true));
		$this->add_column(new ObjectTableColumn(User :: PROPERTY_EMAIL, true));
		//$this->add_column(new ObjectTableColumn(User :: PROPERTY_STATUS, true));
		//$this->add_column(new ObjectTableColumn(User :: PROPERTY_PLATFORMADMIN, true));
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