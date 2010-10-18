<?php
/**
 * @package metadata.tables.metadata_property_type_table
 */
/**
 * Data provider for a metadata_property_type table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataPropertyTypeBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ApplicationComponent $browser
   * @param Condition $condition
   */
  function MetadataPropertyTypeBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
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
		$order_property = $this->get_order_property($order_property);

     	return $this->get_browser()->retrieve_metadata_property_types($this->get_condition(), $offset, $count, $order_property);
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_metadata_property_types($this->get_condition());
    }
}
?>