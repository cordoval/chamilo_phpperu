<?php
/**
 * $Id: course_type_tool.class.php 216 2009-11-13 14:08:06Z Tristan $
 * @package application.lib.weblcms.course_type
 */

/**
 * This class describes a CourseTypeTool data object
 *
 * @author Tristan Verheecke
 */
class CourseTypeTool extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * CourseTypeTool properties
     */
    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_VISIBLE_DEFAULT = 'visible_default';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_COURSE_TYPE_ID, self :: PROPERTY_NAME, self :: PROPERTY_VISIBLE_DEFAULT);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the course_type_id of this CourseTypeTool.
     * @return the course_type_id.
     */
    function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    /**
     * Sets the course_code of this CourseTypeTool.
     * @param course_code
     */
    function set_course_type_id($course_type_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    /**
     * Returns the name of this CourseTypeTool.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this CourseTypeTool.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the visible_default of this CourseTypeTool.
     * @return the visible_default.
     */
    function get_visible_default()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE_DEFAULT);
    }

    /**
     * Sets the visible_default of this CourseTypeTool.
     * @param visible_default
     */
    function set_visible_default($visible_default)
    {
        $this->set_default_property(self :: PROPERTY_VISIBLE_DEFAULT, $visible_default);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
}

?>