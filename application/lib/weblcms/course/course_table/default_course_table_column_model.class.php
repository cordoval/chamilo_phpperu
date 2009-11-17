<?php
/**
 * $Id: default_course_table_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course.course_table
 */
require_once dirname(__FILE__) . '/../course.class.php';

class DefaultCourseTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCourseTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return CourseTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(Course :: PROPERTY_VISUAL);
        $columns[] = new ObjectTableColumn(Course :: PROPERTY_NAME);
        return $columns;
    }
}
?>