<?php
/**
 * $Id: navigation_item_browser_table_data_provider.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.menu_manager.component.navigation_item_browser
 */
/**
 * Data provider for a menu browser table.
 *
 * This class implements some functions to allow menu browser tables to
 * retrieve information about the menu objects to display.
 */
class NavigationItemBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param MenuManagerManagerComponent $browser
     * @param Condition $condition
     */
    function NavigationItemBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the menu objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching menu objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        $order_property[] = new ObjectTableOrder(NavigationItem :: PROPERTY_SORT);
        return $this->get_browser()->retrieve_navigation_items($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of menu objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_navigation_items($this->get_condition());
    }
}
?>