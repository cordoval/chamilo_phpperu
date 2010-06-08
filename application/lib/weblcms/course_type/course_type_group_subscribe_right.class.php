<?php
/**
 * $Id: course_group_subscribe_right.class.php 216 2009-11-13 14:08:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
/**
 * This class represents a course_rights for a course in the weblcms.
 *
 * To access the values of the properties, this class and its subclasses should
 * provide accessor methods. The names of the properties should be defined as
 * class constants, for standardization purposes. It is recommended that the
 * names of these constants start with the string "PROPERTY_".
 *
 */
class CourseTypeGroupSubscribeRight extends CourseGroupSubscribeRight
{

	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_COURSE_TYPE_ID = "course_type_id";
    
    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
        			array(self :: PROPERTY_COURSE_TYPE_ID));
    }
    
    /*
     * Getters
     */
    
    function get_course_type_id()
    {
    	return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }
    
    /*
     * Setters
     */
    
    function set_course_type_id($course_type_id)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }
    
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
