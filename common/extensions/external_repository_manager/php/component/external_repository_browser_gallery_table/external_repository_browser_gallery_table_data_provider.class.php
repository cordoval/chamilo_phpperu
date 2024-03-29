<?php
namespace common\extensions\external_repository_manager;
use common\libraries\GalleryObjectTableDataProvider;
class ExternalRepositoryBrowserGalleryTableDataProvider extends GalleryObjectTableDataProvider
{
    /**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
    	$order_property = $this->get_order_property($order_property);

        // We always use title as second sorting parameter
        //		$order_property[] = ContentObject :: PROPERTY_TITLE;

        return $this->get_browser()->retrieve_external_repository_objects($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_external_repository_objects($this->get_condition());
    }
}
?>