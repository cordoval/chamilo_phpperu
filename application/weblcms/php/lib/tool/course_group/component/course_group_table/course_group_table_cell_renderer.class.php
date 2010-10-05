<?php
/**
 * $Id: course_group_table_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.course_group_table
 */
interface CourseGroupTableCellRenderer
{

    function render_cell($column, $content_object);
}
?>