<?php
/**
 * $Id: alexia_publication_browser_table_data_provider.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexiar.alexiar_manager.component.alexiapublicationbrowser
 */
/**
 * Data provider for a alexia browser table.
 *
 * This class implements some functions to allow alexia browser tables to
 * retrieve information about the alexia objects to display.
 */
class AlexiaPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param AlexiaManagerComponent $browser
     * @param Condition $condition
     */
    function AlexiaPublicationBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the alexia objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching alexia objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        return $this->get_browser()->retrieve_alexia_publications($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of alexia objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_alexia_publications($this->get_condition());
    }
}
?>