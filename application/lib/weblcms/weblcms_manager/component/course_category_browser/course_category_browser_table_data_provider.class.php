<?php
/**
 * $Id: course_category_browser_table_data_provider.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.course_category_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class CourseCategoryBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param WeblcmsManagerComponent $browser
     * @param Condition $condition
     */
    function CourseCategoryBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the course categories
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching course categories.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        return $this->get_browser()->retrieve_course_categories($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of course categories in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_course_categories($this->get_condition());
    }
}
?>