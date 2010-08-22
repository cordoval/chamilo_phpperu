<?php
/**
 * $Id: gutenberg_publication_gallery_browser_data_provider.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class GutenbergPublicationGalleryBrowserTableDataProvider extends GalleryObjectTableDataProvider
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
        return $this->get_browser()->retrieve_gutenberg_publications($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_gutenberg_publications($this->get_condition());
    }
}
?>