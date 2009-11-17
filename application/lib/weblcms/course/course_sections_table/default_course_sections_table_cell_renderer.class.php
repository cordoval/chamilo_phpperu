<?php
/**
 * $Id: default_course_sections_table_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course.course_sections_table
 */

require_once dirname(__FILE__) . '/../course_section.class.php';

class DefaultCourseSectionsTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultCourseSectionsTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param CourseSectionSectionsTableColumnModel $column The column which should be
     * rendered
     * @param CourseSection $course The course object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $course)
    {
        if ($property = $column->get_title())
        {
            switch ($property)
            {
                case Translation :: get(ucfirst(CourseSection :: PROPERTY_NAME)) :
                    return $course->get_name();
            }
        }
        return '&nbsp;';
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>