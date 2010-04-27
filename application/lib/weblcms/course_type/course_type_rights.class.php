<?php
/**
 * $Id: course_rights.class.php 216 2009-11-13 14:08:06Z Tristan $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course_rights.class.php';

class CourseTypeRights extends CourseRights
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    const PROPERTY_DIRECT_SUBSCRIBE_FIXED = 'direct_subscribe_fixed';
    const PROPERTY_REQUEST_SUBSCRIBE_FIXED = 'request_subscribe_fixed';
    const PROPERTY_CODE_SUBSCRIBE_FIXED = 'code_subscribe_fixed';
    const PROPERTY_UNSUBSCRIBE_FIXED = 'unsubscribe_fixed';
    
    const PROPERTY_CREATION_AVAILABLE = 'creation_available';
    const PROPERTY_CREATION_ON_REQUEST_AVAILABLE = 'creation_on_request_available';

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
    	return parent :: get_default_property_names(
        		array(self :: PROPERTY_COURSE_TYPE_ID,
        			  self :: PROPERTY_DIRECT_SUBSCRIBE_FIXED,
        			  self :: PROPERTY_REQUEST_SUBSCRIBE_FIXED,
        			  self :: PROPERTY_CODE_SUBSCRIBE_FIXED,
        			  self :: PROPERTY_UNSUBSCRIBE_FIXED,
        			  self :: PROPERTY_CREATION_AVAILABLE,
        			  self :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    function get_direct_subscribe_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_DIRECT_SUBSCRIBE_FIXED);
    }

    function get_request_subscribe_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_REQUEST_SUBSCRIBE_FIXED);
    } 
    
    function get_code_subscribe_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_CODE_SUBSCRIBE_FIXED);
    }
    
    function get_unsubscribe_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_UNSUBSCRIBE_FIXED);
    } 
    
    function get_creation_available()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_AVAILABLE);
    }
    
    function get_creation_on_request_available()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE);
    } 
    
    function set_course_type_id($course_type_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    function set_direct_subscribe_fixed($direct_subscribe_fixed)
    {
        return $this->set_default_property(self :: PROPERTY_DIRECT_SUBSCRIBE_FIXED, $direct_subscribe_fixed);
    }

    function set_request_subscribe_fixed($request_subscribe_fixed)
    {
        return $this->set_default_property(self :: PROPERTY_REQUEST_SUBSCRIBE_FIXED, $request_subscribe_fixed);
    } 
    
    function set_code_subscribe_fixed($code_subscribe_fixed)
    {
        return $this->set_default_property(self :: PROPERTY_CODE_SUBSCRIBE_FIXED, $code_subscribe_fixed);
    } 
    
    function set_unsubscribe_fixed($unsubscribe_fixed)
    {
        return $this->set_default_property(self :: PROPERTY_UNSUBSCRIBE_FIXED, $unsubscribe_fixed);
    } 
    
     function set_creation_available($creation_available)
    {
        return $this->set_default_property(self :: PROPERTY_CREATION_AVAILABLE, $creation_available);
    } 
    
    function set_creation_on_request_available($creation_on_request_available)
    {
        return $this->set_default_property(self :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE, $creation_on_request_available);
    } 
 
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}
?>