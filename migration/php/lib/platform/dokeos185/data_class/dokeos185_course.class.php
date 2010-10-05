<?php
/**
 * $Id: dokeos185_course.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
require_once Path :: get(SYS_PATH) . 'application/weblcms/php/course/course.class.php';
require_once dirname(__FILE__) . '/dokeos185_user.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke
 */

class Dokeos185Course extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'course';
    const DATABASE_NAME = 'main_database';
    
    /**
     * course properties
     */
    const PROPERTY_CODE = 'code';
    const PROPERTY_DIRECTORY = 'directory';
    const PROPERTY_DB_NAME = 'db_name';
    const PROPERTY_COURSE_LANGUAGE = 'course_language';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CATEGORY_CODE = 'category_code';
    const PROPERTY_VISIBILITY = 'visibility';
    const PROPERTY_TUTOR_NAME = 'tutor_name';
    const PROPERTY_VISUAL_CODE = 'visual_code';
    const PROPERTY_DEPARTMENT_NAME = 'department_name';
    const PROPERTY_DEPARTMENT_URL = 'department_url';
    const PROPERTY_LAST_VISIT = 'last_visit';
    const PROPERTY_LAST_EDIT = 'last_edit';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_EXPIRATION_DATE = 'expiration_date';
    const PROPERTY_TARGET_COURSE_CODE = 'target_course_code';
    const PROPERTY_SUBSCRIBE = 'subscribe';
    const PROPERTY_UNSUBSCRIBE = 'unsubscribe';
    const PROPERTY_REGISTRATION_CODE = 'registration_code';

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(
                self :: PROPERTY_CODE, self :: PROPERTY_DIRECTORY, self :: PROPERTY_DB_NAME, self :: PROPERTY_COURSE_LANGUAGE, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CATEGORY_CODE, 
                self :: PROPERTY_VISIBILITY, self :: PROPERTY_TUTOR_NAME, self :: PROPERTY_VISUAL_CODE, self :: PROPERTY_DEPARTMENT_URL, self :: PROPERTY_LAST_VISIT, self :: PROPERTY_LAST_EDIT, self :: PROPERTY_CREATION_DATE, 
                self :: PROPERTY_EXPIRATION_DATE, self :: PROPERTY_TARGET_COURSE_CODE, self :: PROPERTY_SUBSCRIBE, self :: PROPERTY_UNSUBSCRIBE, self :: PROPERTY_REGISTRATION_CODE, self :: PROPERTY_DEPARTMENT_NAME);
    }

    /**
     * COURSE GETTERS AND SETTERS
     */
    
    /**
     * Returns the code of this course.
     * @return String The code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Returns the directory of this course.
     * @return String The directory.
     */
    function get_directory()
    {
        return $this->get_default_property(self :: PROPERTY_DIRECTORY);
    }

    /**
     * Returns the db_name of this course.
     * @return String The db_name.
     */
    function get_db_name()
    {
        return $this->get_default_property(self :: PROPERTY_DB_NAME);
    }

    /**
     * Returns the course_language of this course.
     * @return String The course_language.
     */
    function get_course_language()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_LANGUAGE);
    }

    /**
     * Returns the title of this course.
     * @return String The title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this course.
     * @return String The discription.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the category_code of this course.
     * @return String The category_code.
     */
    function get_category_code()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_CODE);
    }

    /**
     * Returns the visibility of this course.
     * @return int The visibility.
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Returns the tutor_name of this course.
     * @return String The tutor_name.
     */
    function get_tutor_name()
    {
        return $this->get_default_property(self :: PROPERTY_TUTOR_NAME);
    }

    /**
     * Returns the visual_code of this course.
     * @return String The visual_code.
     */
    function get_visual_code()
    {
        return $this->get_default_property(self :: PROPERTY_VISUAL_CODE);
    }

    /**
     * Returns the department_name of this course.
     * @return String The department_name.
     */
    function get_department_name()
    {
        return $this->get_default_property(self :: PROPERTY_DEPARTMENT_NAME);
    }

    /**
     * Returns the department_url of this course.
     * @return String The department_url.
     */
    function get_department_url()
    {
        return $this->get_default_property(self :: PROPERTY_DEPARTMENT_URL);
    }

    /**
     * Returns the last_visit of this course.
     * @return String The last_visit.
     */
    function get_last_visit()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_VISIT);
    }

    /**
     * Returns the last_edit of this course.
     * @return String The last_edit.
     */
    function get_last_edit()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_EDIT);
    }

    /**
     * Returns the creation_date of this course.
     * @return String The creation_date.
     */
    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    /**
     * Returns the expiration_date of this course.
     * @return String The expiration_date.
     */
    function get_expiration_date()
    {
        return $this->get_default_property(self :: PROPERTY_EXPIRATION_DATE);
    }

    /**
     * Returns the target_course_code of this course.
     * @return String The target_course_code.
     */
    function get_target_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_TARGET_COURSE_CODE);
    }

    /**
     * Returns the subscribe of this course.
     * @return int The subscribe.
     */
    function get_subscribe()
    {
        return $this->get_default_property(self :: PROPERTY_SUBSCRIBE);
    }

    /**
     * Returns the unsubscribe of this course.
     * @return int The unsubscribe.
     */
    function get_unsubscribe()
    {
        return $this->get_default_property(self :: PROPERTY_UNSUBSCRIBE);
    }

    /**
     * Returns the registration_code of this course.
     * @return String The registration_code.
     */
    function get_registration_code()
    {
        return $this->get_default_property(self :: PROPERTY_REGISTRATION_CODE);
    }

    /** Sets the code of this course.
     * @param String $code The code.
     */
    function set_code($code)
    {
        $this->set_default_property(self :: PROPERTY_CODE, $code);
    }

    /**
     * Check if the course is valid
     * @return true if the course is valid
     */
    function is_valid()
    {
        if (! $this->get_code() || $this->get_failed_element($this->get_category_code(), 'main_database.course_category'))
        {
            $this->create_failed_element($this->get_code());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'course', 'ID' => $this->get_code())));
            return false;
        }
        
        return true;
    }

    //check if visual code is avalaible
    function check_visual_code($visual_code, $index = 0)
    {
        if (WeblcmsDataManager :: is_course_code_available($visual_code))
        {
            return $visual_code;
        }
        else
        {
            $index ++;
            return $this->check_visual_code($visual_code . $index, $index);
        }
    
    }

    /**
     * Convert to new course
     * @return the new course
     */
    function convert_data()
    {
        //control if the weblcms application exists
        $is_registered = AdminDataManager :: is_registered('weblcms');
        
        if ($is_registered)
        {
            //Course - General
            $chamilo_course = new Course();
            
            //title & visual_code
            $chamilo_course->set_name($this->get_title());
            $new_visual_code = $this->check_visual_code($this->get_visual_code());
            $chamilo_course->set_visual($new_visual_code);
            
            //titular id
            $titular = $this->get_data_manager()->retrieve_user_by_fullname($this->get_tutor_name());
            
            if ($titular)
            {
                $titular_id = $this->get_id_reference($titular->get_optional_property('user_id'), 'main_database.user');
            }
            else
            {
                $titular_id = 0;
            }
            
            $chamilo_course->set_titular($titular_id);
            
            //category
            $category_id = $this->get_id_reference($this->get_category_code(), 'main_database.course_category');
            if ($category_id)
            {
                $chamilo_course->set_category($category_id);
            }
            
            //departement_name & url
            $chamilo_course->set_extlink_name($this->get_department_name());
            $chamilo_course->set_extlink_url($this->get_department_url());
            
            //Course - Settings
            if (AdminDataManager :: is_language_active($this->get_course_language()))
            {
                $chamilo_course->set_language($this->get_course_language());
            
            }
            else
            {
                $chamilo_course->set_language('english');
            }
            
            //visibility = 3: Open - access allowed for the whole world
            //visibility = 2: Open - access allowed for users registered on the platform
            //visibility = 1: Private access (site accessible only to people on the user list)
            //visibility = 0: Completely closed; the course is only accessible to the course admin.
            

            //chamilo: only 1 and 0 (open and closed)
            if ($this->get_visibility() >= 1) //visibility=2 is also possible
                $chamilo_course->set_visibility(1);
            else
                $chamilo_course->set_visibility(0);
            
            $chamilo_course->set_max_number_of_members(0);
            
            //Course - Lay-out
            

            //Course - Tools
            

            //Course - Rights
            $chamilo_course->set_direct_subscribe_available($this->get_subscribe());
            //$chamilo_course->set_subscribe_allowed($this->get_subscribe());
            $chamilo_course->set_unsubscribe_available($this->get_unsubscribe());
            
            //Courses - Creation/Modification Dates
            $chamilo_course->set_default_property(Course :: PROPERTY_LAST_VISIT, $this->get_last_visit());
            $chamilo_course->set_default_property(Course :: PROPERTY_LAST_EDIT, $this->get_last_edit());
            $chamilo_course->set_default_property(Course :: PROPERTY_CREATION_DATE, $this->get_creation_date());
            $chamilo_course->set_default_property(Course :: PROPERTY_EXPIRATION_DATE, $this->get_expiration_date());
            
            //create course in database
            $chamilo_course->create();
            
            //Add id references to temp table
            $this->create_id_reference($this->get_code(), $chamilo_course->get_id());
            
            $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'course', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_course->get_code())));
        }
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