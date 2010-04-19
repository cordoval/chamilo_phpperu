<?php
/**
 * $Id: course_type_user_category.class.php 216 2009-11-13 14:08:06Z Tristan $
 * @package application.lib.weblcms.course_type
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

class CourseTypeUserCategory extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_COURSE_USER_CATEGORY_ID = 'course_user_category_id';
    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';

    /**
     * Get the default properties of all user course user categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_USER_ID,
        			 self :: PROPERTY_COURSE_USER_CATEGORY_ID,
        			 self :: PROPERTY_COURSE_TYPE_ID);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }
    
    function get_course_user_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_USER_CATEGORY_ID);
    }
    
    function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
    
    function set_course_user_category_id($course_user_category_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_USER_CATEGORY_ID, $course_user_category_id);
    }

    function set_course_type_id($course_type_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>