<?php
namespace application\weblcms\tool\course_group;

use application\weblcms\WeblcmsDataManager;

/**
 * $Id: course_group_table_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.course_group_table
 */
class CourseGroupTableDataProvider
{
    private $course_group_tool;

    function __construct($course_group_tool)
    {
        $this->course_group_tool = $course_group_tool;
    }

    function get_parent()
    {
        return $this->course_group_tool;
    }

    function get_course_groups($offset, $count, $order_property)
    {
        $dm = WeblcmsDataManager :: get_instance();

        if ($order_property && ! is_array($order_property))
        {
            $order_property = array($order_property);
        }

        return $dm->retrieve_course_groups($this->course_group_tool->get_condition(), $offset, $count, $order_property);
    }

    function get_course_group_count()
    {
        $dm = WeblcmsDataManager :: get_instance();
        return $dm->retrieve_course_groups($this->course_group_tool->get_condition())->size();
    }
}
?>