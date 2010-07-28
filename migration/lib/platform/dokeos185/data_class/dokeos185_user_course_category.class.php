<?php
/**
 * $Id: dokeos185_user_course_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
require_once Path :: get(SYS_PATH) . 'application/lib/weblcms/course/course_user_category.class.php';
require_once Path :: get(SYS_PATH) . 'application/lib/weblcms/course_type/course_type_user_category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 user course category
 *
 * @author David Van Wayenbergh
 */

class Dokeos185UserCourseCategory extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'user_course_category';   
	const DATABASE_NAME = 'user_personal_database';
    
    /**
     * course properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';

    /**
     * Get the default properties of all user course category.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_TITLE, self :: PROPERTY_SORT);
    }

    /**
     * USER CATEGORY COURSE GETTERS AND SETTERS
     */
    
    /**
     * Returns the id of this user course category.
     * @return String The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the user id of this user course category.
     * @return String The user id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the title of this user course category.
     * @return String The title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the sort of this user course category.
     * @return String The sort.
     */
    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /**
     * checks if a user course category is valid to be written at the db
     * @return Boolean 
     */
    function is_valid()
    {
        if (! $this->get_id() || ! $this->get_title())
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'user course category', 'ID' => $this->get_id())));
            return false;
        }
        
        return true;
    }

    /**
     * Migration user course category
     * @return CourseUserCategory
     */
    function convert_data()
    {
        //User Course Category
        $chamilo_user_course_category = new CourseUserCategory();
        $chamilo_user_course_category->set_title($this->get_title());
        $chamilo_user_course_category->create();
        
        //User Course Category rel course type
        $course_type_user_category = new CourseTypeUserCategory();
        $course_type_user_category->set_course_type_id(0);
        $course_type_user_category->set_course_user_category_id($chamilo_user_course_category->get_id());
        
        $user_id = $this->get_id_reference($this->get_user_id(), 'main_database.user');
        $course_type_user_category->set_user_id($user_id);
        $course_type_user_category->create();
        
        //Add id references to temp table
        $this->create_id_reference($this->get_id(), $chamilo_user_course_category->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'user course category', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_user_course_category->get_id())));
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
    
    function get_database_name()
    {
    	return self :: DATABASE_NAME;
    }
}
?>