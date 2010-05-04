<?php
class GradebookPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ApplicationComponent $browser
   */
  function GradebookPublicationBrowserTableDataProvider($browser)
  {
		parent :: __construct($browser);
  }
  /**
   * Retrieves the objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @return ResultSet A set of objects
   */
    function get_objects($offset, $count, $order_property = null)
    {
////		$order_property = $this->get_order_property($order_property);
//		$result_set  = $this->get_browser()->retrieve_content_objects_by_ids($this->get_browser()->get_condition(), $offset, $count, $order_property);
//		$objects = array();
//		while ($result = $result_set->next_result())
//		{
//			$object = new $result['type'];
//			$object->set_id($result['id']);
//			$object->set_title($result['title']);
//			$object->set_description($result['description']);
//			$object->set_creation_date($result['created']);
//			$objects[] = $object;
//		}
		return $this->get_browser()->retrieve_internal_items_by_application($this->get_browser()->get_condition(), $offset, $count, $order_property);
//    	return $this->get_browser()->retrieve_content_objects_by_ids($this->get_browser()->get_condition(), $offset, $count, $order_property);
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
		return $this->get_browser()->count_internal_items_by_application($this->get_browser()->get_condition());
    }
}
?>