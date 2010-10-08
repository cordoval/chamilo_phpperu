<?php
/**
 * $Id: category_browser_table_data_provider.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.category_manager.component.category_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class CategoryBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function CategoryBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property[] = new ObjectTableOrder(PlatformCategory :: PROPERTY_DISPLAY_ORDER);
        return $this->get_browser()->retrieve_categories($this->get_condition(), $offset, $count, $order_property);
    
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_categories($this->get_condition());
    }
}
?>