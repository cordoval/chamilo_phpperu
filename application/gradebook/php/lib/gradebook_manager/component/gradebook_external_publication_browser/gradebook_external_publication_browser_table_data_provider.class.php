<?php
class GradebookExternalPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ApplicationComponent $browser
   */
  function GradebookExternalPublicationBrowserTableDataProvider($browser)
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
		return $this->get_browser()->retrieve_external_items($this->get_browser()->get_external_condition(), $offset, $count, $order_property);
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
		return $this->get_browser()->count_external_items($this->get_browser()->get_external_condition());
    }
}
?>