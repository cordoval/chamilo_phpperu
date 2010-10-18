<?php
/**
 * $Id: default_course_category_table_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course.course_category_table
 */
require_once dirname(__FILE__) . '/../course_category.class.php';

class DefaultCourseCategoryTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCourseCategoryTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 0);
    }

    /**
     * Gets the default columns for this model
     * @return CourseCategoryTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(CourseCategory :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(CourseCategory :: PROPERTY_CODE);
        return $columns;
    }
}
?>