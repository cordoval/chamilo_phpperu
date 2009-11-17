<?php
/**
 * $Id: course_group_table_column.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.course_group_table
 */
class CourseGroupTableColumn
{
    /**
     * The property of the course_group which will be displayed in this column.
     */
    private $course_group_property;
    /**
     * The title of the column.
     */
    private $title;
    /**
     * Whether or not sorting by this column is allowed.
     */
    private $sortable;

    /**
     * Constructor. Either defines a column that displays a default property
     * of course_groups, or arbitrary content.
     * @param string $property_name_or_column_title If the column contains arbitrary content, the title of the column. If
     *   it displays a user property, that particular property, a User::PROPERTY_* constant.
     * @param boolean $contains_user_property True if the column displays a user property, false otherwise.
     */
    function CourseGroupTableColumn($property_name_or_column_title, $contains_course_group_property = false)
    {
        if ($contains_course_group_property)
        {
            $this->course_group_property = $property_name_or_column_title;
            $this->title = Translation :: get(ucfirst($this->course_group_property));
            $this->sortable = true;
        }
        else
        {
            $this->title = $property_name_or_column_title;
            $this->sortable = false;
        }
    }

    /**
     * Gets the course_group property that this column displays.
     * @return string The property name, or null if the column contains
     *                arbitrary content.
     */
    function get_course_group_property()
    {
        return $this->course_group_property;
    }

    /**
     * Gets the title of this column.
     * @return string The title.
     */
    function get_title()
    {
        return $this->title;
    }

    /**
     * Determine if the table's contents may be sorted by this column.
     * @return boolean True if sorting by this column is allowed, false
     *                 otherwise.
     */
    function is_sortable()
    {
        return $this->sortable;
    }

    /**
     * Sets the title of this column.
     * @param string $title The new title.
     */
    function set_title($title)
    {
        $this->title = $title;
    }

    /**
     * Sets whether or not the table's contents may be sorted by this column.
     * @param boolean $sortable True if sorting by this column should be
     *                          allowed, false otherwise.
     */
    function set_sortable($sortable)
    {
        $this->sortable = $sortable;
    }
}
?>