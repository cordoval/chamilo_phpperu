<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';

class GradebookSubscribeUserBrowserTableDataProvider extends ObjectTableDataProvider
{
	private $browser;

	function GradebookSubscribeUserBrowserTableDataProvider($browser, $condition)
	{
		parent :: __construct($browser, $condition);

	}
	
	function get_objects($offset, $count, $order_property = null)
	{
		$order_property = $this->get_order_property($order_property);
		return UserDataManager::get_instance()->retrieve_users($this->get_browser()->get_condition(),$offset, $count, $order_property);
		
	}
	
	function get_object_count()
	{
		return UserDataManager::get_instance()->count_users($this->get_browser()->get_condition());
		
	}
}
?>