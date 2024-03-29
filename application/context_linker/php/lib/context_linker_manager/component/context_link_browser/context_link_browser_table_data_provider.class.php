<?php
namespace application\context_linker;
use common\libraries\ObjectTableDataProvider;

/**
 * Data provider for a context_link table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkBrowserTableDataProvider extends ObjectTableDataProvider
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
        return $this->get_browser()->retrieve_full_context_links($this->get_condition(), $offset, $count, $order_property);
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
      return count($this->get_browser()->retrieve_full_context_links($this->get_condition(), $offset, $count, $order_property));
    }
}
?>