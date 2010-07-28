<?php

/**
 * $Id: dokeos185_course_rel_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
require_once Path :: get(SYS_PATH) . 'application/lib/weblcms/course/course_user_relation.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_user
 *
 * @author David Van Wayenberghµ
 * @author Sven Vanpoucke
 */

class Dokeos185CourseRelUser extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'course_rel_user';   
	const DATABASE_NAME = 'main_database';
    
    /**
     * course relation user properties
     */
    const PROPERTY_CODE = 'course_code';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_ROLE = 'role';
    const PROPERTY_GROUP_ID = 'group_id';
    const PROPERTY_TUTOR_ID = 'tutor_id';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_USER_COURSE_CAT = 'user_course_cat';
    
    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_CODE, self :: PROPERTY_USER_ID, self :: PROPERTY_STATUS, self :: PROPERTY_ROLE, self :: PROPERTY_GROUP_ID, self :: PROPERTY_TUTOR_ID, self :: PROPERTY_SORT, self :: PROPERTY_USER_COURSE_CAT);
    }

    /**
     * RELATION USER GETTERS AND SETTERS
     */
    
    /**
     * Returns the course_code of this rel_user.
     * @return String The course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Returns the user_id of this rel_user.
     * @return int The user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the status of this rel_user.
     * @return int The status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns the role of this rel_user.
     * @return String The role.
     */
    function get_role()
    {
        return $this->get_default_property(self :: PROPERTY_ROLE);
    }

    /**
     * Returns the group_id of this rel_user.
     * @return int The group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Returns the tutor_id of this rel_user.
     * @return int The tutor_id.
     */
    function get_tutor_id()
    {
        return $this->get_default_property(self :: PROPERTY_TUTOR_ID);
    }

    /**
     * Returns the sort of this rel_user.
     * @return int The sort.
     */
    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /**
     * Returns the user_course_cat of this rel_user.
     * @return int The user_course_cat.
     */
    function get_user_course_cat()
    {
        return $this->get_default_property(self :: PROPERTY_USER_COURSE_CAT);
    }

    /**
     * Check if the course user relation is valid
     * @return true if the course user relation is valid 
     */
    function is_valid()
    {
        if (! $this->get_course_code() || ! $this->get_user_id() || $this->get_status() == NULL || $this->get_group_id() == NULL || $this->get_tutor_id() == NULL || 
            $this->get_failed_element($this->get_course_code(), 'main_database.course') || $this->get_failed_element('main_database.user', $this->get_user_id()))
        {
            $this->create_failed_element($this->get_user_id() . '-' . $this->get_course_code());
            $this->set_message(Translation :: get('CourseRelUserInvalidMessage', array('USER_ID' => $this->get_user_id(), 'COURSE_ID' => $this->get_course_code())));
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new course user relation
     * @return the new course user relation
     */
    function convert_data()
    {
        //course_rel_user parameters
        $chamilo_course_rel_user = new CourseUserRelation();
        
        $course_code = $this->get_id_reference($this->get_course_code(), 'main_database.course');
        if ($course_code)
        {
            $chamilo_course_rel_user->set_course($course_code);
        }

        $user_id = $this->get_id_reference($this->get_user_id(), 'main_database.user');
        if ($user_id)
        {
            $chamilo_course_rel_user->set_user($user_id);
        }
        
        $chamilo_course_rel_user->set_status($this->get_status());
        $chamilo_course_rel_user->set_role($this->get_role());
        $chamilo_course_rel_user->set_course_group($this->get_group_id());
        $chamilo_course_rel_user->set_tutor($this->get_tutor_id());
        $chamilo_course_rel_user->set_sort($this->get_sort());
        
        $user_course_category_id = $this->get_id_reference($this->get_user_course_cat(), 'user_personal_database.user_course_category');
        if ($user_course_category_id)
        {
            $chamilo_course_rel_user->set_category($user_course_category_id);
        }
        else
        {
            $chamilo_course_rel_user->set_category(0);
        }
        
        //create user in database
        $chamilo_course_rel_user->create();
        $this->set_message(Translation :: get('CourseRelUserConvertedMessage', array('USER_ID' => $this->get_user_id(), 'COURSE_ID' => $this->get_course_code())));
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
