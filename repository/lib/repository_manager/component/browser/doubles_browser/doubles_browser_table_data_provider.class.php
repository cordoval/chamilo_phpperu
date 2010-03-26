<?php
/**
 * $Id: doubles_browser_table_data_provider.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package doubles.lib.doubles_manager.component.browser
 */
/**
 * Data provider for a doubles browser table.
 *
 * This class implements some functions to allow doubles browser tables to
 * retrieve information about the learning objects to display.
 */
class DoublesBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param DoublesManagerComponent $browser
     * @param Condition $condition
     */
    function DoublesBrowserTableDataProvider($browser, $condition)
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
        $order_property = $this->get_order_property($order_property);
        return RepositoryDataManager :: get_instance()->retrieve_doubles_in_repository($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return RepositoryDataManager :: get_instance()->count_doubles_in_repository($this->get_condition());
    }
}
?>