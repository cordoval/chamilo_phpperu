<?php
/**
 * $Id: course_sections_browser_table_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component.course_sections_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class CourseSectionsBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param WeblcmsManagerComponent $browser
     * @param Condition $condition
     */
    function CourseSectionsBrowserTableDataProvider($browser, $condition)
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
        
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->retrieve_course_sections($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of courses in the table
     * @return int
     */
    function get_object_count()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->count_course_sections($this->get_condition());
    }
}
?>