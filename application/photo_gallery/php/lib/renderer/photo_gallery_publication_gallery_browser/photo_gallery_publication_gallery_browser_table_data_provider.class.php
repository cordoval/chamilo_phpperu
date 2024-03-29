<?php
namespace application\photo_gallery;

use common\libraries\GalleryObjectTableDataProvider;

class PhotoGalleryPublicationGalleryBrowserTableDataProvider extends GalleryObjectTableDataProvider
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
        return $this->get_browser()->retrieve_photo_gallery_publications($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_photo_gallery_publications($this->get_condition());
    }
}
?>