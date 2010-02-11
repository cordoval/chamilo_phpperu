<?php
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';

class GradebookRelUserBrowserTableDataProvider extends ObjectTableDataProvider
{
  
  function GradebookRelUserBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
  }
  
    function get_objects($offset, $count, $order_property = null)
    {
		$order_property = $this->get_order_property($order_property);
 
      return $this->get_browser()->retrieve_gradebook_rel_users($this->get_condition(), $offset, $count, $order_property);
    }
 
    function get_object_count()
    {
      return $this->get_browser()->count_gradebook_rel_users($this->get_condition());
    }
}
?>