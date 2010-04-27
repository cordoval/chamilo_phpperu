<?php
/**
 * $Id: course_group_create_right.class.php 216 2009-11-13 14:08:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course_type
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
class CourseTypeGroupCreationRight extends DataClass
{

	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_COURSE_TYPE_ID = "course_type_id";
	const PROPERTY_GROUP_ID = "group_id";
	const PROPERTY_CREATE = "creation_right";
    
	const CREATE_NONE = 0;
    const CREATE_DIRECT = 2;
    const CREATE_REQUEST = 1;
    
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
        return array( self :: PROPERTY_COURSE_TYPE_ID,
        			  self :: PROPERTY_GROUP_ID,
        		  	  self :: PROPERTY_CREATE);
    }
    
    /*
     * Getters
     */
    
    function get_course_type_id()
    {
    	return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }
    
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }
    
    function get_create()
    {
        return $this->get_default_property(self :: PROPERTY_CREATE);
    }
    
    /*
     * Setters
     */
    
    function set_course_type_id($course_type_id)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    } 
    
    
   	function set_group_id($group_id)
    {
    	$this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    } 
    
    function set_create($create)
    {
    	$this->set_default_property(self :: PROPERTY_CREATE, $create);
    }
    
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
