<?php
/**
 * $Id: unsubscribe_browser_table_data_provider.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.unsubscribe_browser
 */
/**
 * Data provider for a weblcms browser table.
 *
 * This class implements some functions to allow weblcsm browser tables to
 * retrieve information about the course objects to display.
 */
class UnsubscribeBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param WeblcmsManagerComponent $browser
     * @param Condition $condition
     */
    function UnsubscribeBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the courses
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching courses.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        return $this->get_browser()->retrieve_user_courses($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of courses in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_user_courses($this->get_condition());
    }
}
?>