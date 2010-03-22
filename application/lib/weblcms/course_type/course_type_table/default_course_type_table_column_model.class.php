<?php
/**
 * $Id: default_course_type_table_column_model.class.php 216 2010-03-12 14:08:06Z yannick $
 * @package application.lib.weblcms.course_type.course_type_table
 */
require_once dirname(__FILE__) . '/../course_type.class.php';

class DefaultCourseTypeTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCourseTypeTableColumnModel()
    {
        //parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return CourseTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(CourseType :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(CourseType :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>