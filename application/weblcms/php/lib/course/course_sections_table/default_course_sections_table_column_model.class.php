<?php
/**
 * $Id: default_course_sections_table_column_model.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course.course_sections_table
 */
require_once dirname(__FILE__) . '/../course_section.class.php';

class DefaultCourseSectionsTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCourseSectionsTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return CourseSectionTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        //$columns[] = new ObjectTableColumn(CourseSection :: PROPERTY_ID);
        $columns[] = new StaticTableColumn(Translation :: get(Utilities :: underscores_to_camelcase(CourseSection :: PROPERTY_NAME)));
        return $columns;
    }
}
?>