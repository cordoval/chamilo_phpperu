<?php
/**
 * $Id: course_request.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/course/common_request.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class CourseRequest extends CommonRequest
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_NAME_USER = 'name_user';
    const PROPERTY_COURSE_NAME = 'course_name';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
        		  self :: PROPERTY_COURSE_ID,
         		  self :: PROPERTY_NAME_USER,
         		  self :: PROPERTY_COURSE_NAME));
    }

    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }
    
    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }
    
	function get_name_user()
    {
        return $this->get_default_property(self :: PROPERTY_NAME_USER);
    }
    
    function get_course_name()
    {
    	return $this->get_default_property(self :: PROPERTY_COURSE_NAME);
    }

    function set_course_id($course_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }
    
    function set_name_user($name_user)
    {
        $this->set_default_property(self :: PROPERTY_NAME_USER, $name_user);
    }
    
    function set_course_name($course_name)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_NAME, $course_name);
    }
    
	static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>