<?php
/**
 * $Id: gutenberg_publication_browser_table_data_provider.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenbergr.gutenbergr_manager.component.gutenbergpublicationbrowser
 */
/**
 * Data provider for a gutenberg browser table.
 *
 * This class implements some functions to allow gutenberg browser tables to
 * retrieve information about the gutenberg objects to display.
 */
class GutenbergPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param GutenbergManagerComponent $browser
     * @param Condition $condition
     */
    function GutenbergPublicationBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the gutenberg objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching gutenberg objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        return $this->get_browser()->retrieve_gutenberg_publications($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of gutenberg objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_gutenberg_publications($this->get_condition());
    }
}
?>