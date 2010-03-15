<?php
/**
 * $Id: course_settings.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class CourseSettings extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_VISIBILITY = 'visibility';
    const PROPERTY_ACCESS = 'access';
    const PROPERTY_MAX_NUMBER_OF_MEMBERS = 'max_number_of_members';

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        return array_merge($extended_property_names,
        	array(self :: PROPERTY_LANGUAGE,
        		  self :: PROPERTY_VISIBILITY,
        		  self :: PROPERTY_ACCESS,
        		  self :: PROPERTY_MAX_NUMBER_OF_MEMBERS));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }
    
    /**
     * Returns the language of this course object
     * @return array() the languages
     */
    function get_language()
    {
        return $this->get_default_property(self :: PROPERTY_LANGUAGE);
    }
    
    /**
     * Returns the visibility of this course object
     * @return boolean the visibility Code
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }
    
    /**
     * Returns the acces of this course object
     * @return boolean the acces Code
     */
    function get_access()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS);
    }
    
    /**
     * Returns the max number of members of this course object
     * @return int the max number of members
     */
    function get_max_number_of_members()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_NUMBER_OF_MEMBERS);
    }
    
    /**
     * Sets the language of this course object
     * @param array $language the language
     */
    function set_language($language)
    {
        return $this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
    }
       
    /**
     * Sets the visibility of this course object
     * @param Boolean $visibility the visibility
     */
    function set_visibility($visibility)
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
    }
    
    /**
     * Sets the access of this course object
     * @param Boolean $access the access
     */
    function set_access($access)
    {
        $this->set_default_property(self :: PROPERTY_ACCESS, $access);
    }   

    /**
     * Sets the the max number of members of this course object
     * @param int $max_number_of_members the max number of members
     */
    function set_max_number_of_members($max_number_of_members)
    {
            $this->set_default_property(self :: PROPERTY_MAX_NUMBER_OF_MEMBERS, $max_number_of_members);
    }
//    function set_max_number_of_admin($max_number_of_admin)
//    {
//            $this->set_default_property(self :: PROPERTY_MAX_NUMBER_OF_ADMIN, $max_number_of_admin);
//    }

//    /**
//     * Creates the course type object in persistent storage
//     * @return boolean
//     */
//    function create()
//    {
//        $wdm = WeblcmsDataManager :: get_instance();
//
//        if (! $wdm->create_course_type($this))
//        {
//            return false;
//        }
//
//        require_once (dirname(__FILE__) . '/../category_manager/content_object_publication_category.class.php');
//        $dropbox = new ContentObjectPublicationCategory();
//        $dropbox->create_dropbox($this->get_id());
//
//        $location = new Location();
//        $location->set_location($this->get_name());
//        $location->set_application(WeblcmsManager :: APPLICATION_NAME);
//        $location->set_type_from_object($this);
//        $location->set_identifier($this->get_id());
//
//        $parent = WeblcmsRights :: get_location_id_by_identifier('course_category', 1);
//        //echo 'parent : ' . $parent;
//
//
//        if ($parent)
//        {
//            $location->set_parent($parent);
//        }
//        else
//        {
//            $location->set_parent(0);
//        }
//
//        if (! $location->create())
//        {
//            return false;
//        }
//
//        return true;
//    }
//
//    function create_course_type_all()
//    {
//        $wdm = WeblcmsDataManager :: get_instance();
//        return $wdm->create_course_type_all($this);
//    }
//    

//    /**
//    * Determines if this course has a theme
//    * @return boolean
//    */
//	function has_theme()
//    {
//        return (! is_null($this->get_theme()) ? true : false);
//    }
    
	static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
//   /**
//     * Checks whether the given user is a course admin in this course
//     * @param User $user
//     * @return boolean
//     */
//    function is_course_type_admin($user)
//    {
//        if ($user->is_platform_admin())
//        {
//            return true;
//        }
//        $wdm = WeblcmsDataManager :: get_instance();
//        return $wdm->is_course_type_admin($this, $user->get_id());
//    }
}
?>