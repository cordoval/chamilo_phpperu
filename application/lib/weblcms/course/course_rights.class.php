<?php
/**
 * $Id: course_rights.class.php 216 2009-11-13 14:08:06Z Tristan $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class CourseRights extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE = 'direct_subscribe_available';
    const PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE = 'request_subscribe_available';
    const PROPERTY_CODE_SUBSCRIBE_AVAILABLE = 'code_subscribe_available';
    const PROPERTY_UNSUBSCRIBE_AVAILABLE = 'unsubscribe_available';
    const PROPERTY_CODE = 'code';

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        if(empty($extended_property_names)) $extended_property_names = array(self :: PROPERTY_COURSE_ID);
        return array_merge($extended_property_names,
        		array(self :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE,
        			  self :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE,
        			  self :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE,
        			  self :: PROPERTY_UNSUBSCRIBE_AVAILABLE,
        			  self :: PROPERTY_CODE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    function get_direct_subscribe_available()
    {
        return $this->get_default_property(self :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE);
    }

    function get_request_subscribe_available()
    {
        return $this->get_default_property(self :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE);
    } 
    
    function get_code_subscribe_available()
    {
        return $this->get_default_property(self :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE);
    }
    
    function get_unsubscribe_available()
    {
        return $this->get_default_property(self :: PROPERTY_UNSUBSCRIBE_AVAILABLE);
    } 
    
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    function set_course_id($course_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }

    function set_direct_subscribe_available($direct_subscribe_available)
    {
        return $this->set_default_property(self :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE, $direct_subscribe_available);
    }

    function set_request_subscribe_available($request_subscribe_available)
    {
        return $this->set_default_property(self :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE, $request_subscribe_available);
    } 
    
    function set_code_subscribe_available($code_subscribe_available)
    {
        return $this->set_default_property(self :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE, $code_subscribe_available);
    } 
    
    function set_unsubscribe_available($unsubscribe_available)
    {
        return $this->set_default_property(self :: PROPERTY_UNSUBSCRIBE_AVAILABLE, $unsubscribe_available);
    } 
    
    function set_code($code)
    {
        return $this->set_default_property(self :: PROPERTY_CODE, $code);
    } 
    
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}
?>