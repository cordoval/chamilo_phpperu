<?php
/**
 * Data provider for a photo_gallery browser table.
 *
 * This class implements some functions to allow photo_gallery browser tables to
 * retrieve information about the photo_gallery objects to display.
 */
class PhotoGalleryBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param PhotoGalleryManagerComponent $browser
     * @param Condition $condition
     */
    function PhotoGalleryBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the photo_gallery objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching photo_gallery objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        return $this->get_browser()->retrieve_photos_gallery($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of photo_gallery objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_photo_gallery($this->get_condition());
    }
}
?>