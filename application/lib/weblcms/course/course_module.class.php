<?php
/**
 * $Id: course_module.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */

/**
 * This class describes a CourseModule data object
 *
 * @author Hans De Bisschop
 */
class CourseModule extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * CourseModule properties
     */
    const PROPERTY_COURSE_CODE = 'course_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_VISIBLE = 'visible';
    const PROPERTY_SECTION = 'section';
    const PROPERTY_SORT = 'sort';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
        	   array(self :: PROPERTY_COURSE_CODE, 
        			 self :: PROPERTY_NAME, 
        			 self :: PROPERTY_VISIBLE, 
        			 self :: PROPERTY_SECTION,
        			 self :: PROPERTY_SORT));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the course_code of this CourseModule.
     * @return the course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    /**
     * Sets the course_code of this CourseModule.
     * @param course_code
     */
    function set_course_code($course_code)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
    }

    /**
     * Returns the name of this CourseModule.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this CourseModule.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the visible of this CourseModule.
     * @return the visible.
     */
    function get_visible()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE);
    }

    /**
     * Sets the visible of this CourseModule.
     * @param visible
     */
    function set_visible($visible)
    {
        $this->set_default_property(self :: PROPERTY_VISIBLE, $visible);
    }

    /**
     * Returns the section of this CourseModule.
     * @return the section.
     */
    function get_section()
    {
        return $this->get_default_property(self :: PROPERTY_SECTION);
    }

    /**
     * Sets the section of this CourseModule.
     * @param section
     */
    function set_section($section)
    {
        $this->set_default_property(self :: PROPERTY_SECTION, $section);
    }

    /**
     * Returns the sort of this CourseModule.
     * @return the sort.
     */
    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /**
     * Sets the sort of this CourseModule.
     * @param sort
     */
    function set_sort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }
    
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>