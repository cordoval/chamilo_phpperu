<?php
/**
 * $Id: location_course_group_browser_table_data_provider.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component.location_course_group_bowser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class LocationCourseGroupBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function LocationCourseGroupBrowserTableDataProvider($browser, $condition)
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
        return WeblcmsDataManager :: get_instance()->retrieve_course_groups($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return WeblcmsDataManager :: get_instance()->count_course_groups($this->get_condition());
    }
}
?>