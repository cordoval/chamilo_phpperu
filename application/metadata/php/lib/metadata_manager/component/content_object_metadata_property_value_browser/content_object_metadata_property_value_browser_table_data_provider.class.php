<?php
namespace application\metadata;
use common\libraries\ObjectTableDataProvider;
use repository\RepositoryDataManager;

/**
 * Data provider for a metadata_property_value table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContentObjectMetadataPropertyValueBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ApplicationComponent $browser
   * @param Condition $condition
   */
  function ContentObjectMetadataPropertyValueBrowserTableDataProvider($browser, $condition)
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
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_objects($this->get_condition(),$order_property, $offset, $count);
        //return $rdm->retrieve_content_objects();
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
      //return $this->get_browser()->count_metadata_property_values($this->get_condition());
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->count_content_objects($this->get_condition());

    }
}
?>