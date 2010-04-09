<?php
/**
 * $Id: course_order.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class CourseOrder extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_NAME_USER = 'name_user';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_MOTIVATION = 'motivation';
    const PROPERTY_CREATIONDATE = 'creationdate';
    const PROPERTY_ALLOWEDDATE = 'alloweddate';

    static function get_default_property_names($extended_property_names = array())
    {
    	if(empty($extended_property_names)) $extended_property_names = array(self :: PROPERTY_COURSE_ID);
        return array_merge($extended_property_names,
        	array(self :: PROPERTY_NAME_USER,
        		  self :: PROPERTY_TITLE,
        		  self :: PROPERTY_MOTIVATION,
        		  self :: PROPERTY_CREATIONDATE,
        		  self :: PROPERTY_ALLOWEDDATE));
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
    
    function get_title()
    {
    	return $this->get_default_property(self :: PROPERTY_TITLE);
    }
    
    function get_motivation()
    {
        return $this->get_default_property(self :: PROPERTY_MOTIVATION);
    }

    function get_creationdate()
    {
        return $this->get_default_property(self :: PROPERTY_CREATIONDATE);
    }
    
    function get_alloweddate()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOWEDDATE);
    }

    function set_course_id($course_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }
    
    function set_name_user($name_user)
    {
        $this->set_default_property(self :: PROPERTY_NAME_USER, $name_user);
    }
    
    function set_title($title)
    {
    	$this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function set_motivation($motivation)
    {
        $this->set_default_property(self :: PROPERTY_MOTIVATION, $motivation);
    }
    
    function set_creationdate($creationdate)
    {
        $this->set_default_property(self :: PROPERTY_CREATIONDATE, $creationdate);
    }   

    function set_alloweddate($alloweddate)
    {
         $this->set_default_property(self :: PROPERTY_ALLOWEDDATE, $alloweddate);
    }
    
	static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>