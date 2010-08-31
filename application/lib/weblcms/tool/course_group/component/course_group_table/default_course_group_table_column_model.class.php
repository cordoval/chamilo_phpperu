<?php
/**
 * $Id: default_course_group_table_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.course_group_table
 */
require_once dirname(__FILE__) . '/course_group_table_column_model.class.php';
require_once dirname(__FILE__) . '/course_group_table_column.class.php';

class DefaultCourseGroupTableColumnModel extends CourseGroupTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;
    /**
     * The tables number of members column
     */
    private static $number_of_members_column;
    /**
     *
     */
    private $course_group_tool;

    /**
     * Constructor
     */
    function DefaultCourseGroupTableColumnModel($course_group_tool)
    {
        $this->course_group_tool = $course_group_tool;
        parent :: __construct($this->get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return UserTableColumn[]
     */
    private function get_default_columns()
    {
        $columns = array();
        $columns[] = new CourseGroupTableColumn(CourseGroup :: PROPERTY_NAME, true);
        $columns[] = new CourseGroupTableColumn(CourseGroup :: PROPERTY_DESCRIPTION, true);
        $columns[] = self :: get_number_of_members_column();
        $columns[] = new CourseGroupTableColumn(CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS, true);
        if ($this->course_group_tool->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $columns[] = new CourseGroupTableColumn(CourseGroup :: PROPERTY_SELF_UNREG, true);
            $columns[] = new CourseGroupTableColumn(CourseGroup :: PROPERTY_SELF_REG, true);
        }
        $columns[] = self :: get_modification_column();
        return $columns;
    }

    /**
     * Gets the modification column
     * @return CourseGroupTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new CourseGroupTableColumn('');
        }
        return self :: $modification_column;
    }

    /**
     * Gets the number of members column
     * @return CourseGroupTableColumn
     */
    static function get_number_of_members_column()
    {
        if (! isset(self :: $number_of_members_column))
        {
            self :: $number_of_members_column = new CourseGroupTableColumn(Translation :: get('NumberOfMembers'), false);
        }
        return self :: $number_of_members_column;
    }
}
?>