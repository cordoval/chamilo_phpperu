<?php

namespace application\package;

use common\libraries\ObjectTableDataProvider;
/**
 * @package package.tables.package_language_table
 */
/**
 * Data provider for a package_language table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class AuthorBrowserTableDataProvider extends ObjectTableDataProvider
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

     	return PackageDataManager::get_instance()->retrieve_authors($this->get_condition(), $offset, $count, $order_property);
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
      return PackageDataManager::get_instance()->count_authors($this->get_condition());
    }
}
?>