<?php
namespace application\metadata;
use common\libraries\ObjectTableDataProvider;
use group\GroupDataManager;

/**
 * Data provider for a metadata_property_value table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class GroupMetadataPropertyValueBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ApplicationComponent $browser
   * @param Condition $condition
   */
  function __construct($browser, $condition)
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

        //return $this->get_browser()->retrieve_metadata_property_values($this->get_condition(), $offset, $count, $order_property);
        $rdm = GroupDataManager :: get_instance();
        return $rdm->retrieve_groups($this->get_condition(), $offset, $count, $order_property);
        //return $rdm->retrieve_content_objects();
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
        $rdm = GroupDataManager :: get_instance();
        return $rdm->count_groups($this->get_condition());
    }
}
?>