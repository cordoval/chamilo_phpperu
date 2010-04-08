<?php
/**
 * $Id: dokeos185_course.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_course.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/course/course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke
 */

class Dokeos185Course extends ImportCourse
{
    /**
     * Migration data manager
     */

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
     * Alfanumeric identifier of the course object.
     */
    private $code;
    private $index = 0;

    /**
     * Default properties of the course object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new course object.
     * @param array $defaultProperties The default properties of the user
     *                                 object. Associative array.
     */
    function Dokeos185Course($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this course object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this course.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_CODE, self :: PROPERTY_DIRECTORY, self :: PROPERTY_DB_NAME, self :: PROPERTY_COURSE_LANGUAGE, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CATEGORY_CODE, self :: PROPERTY_VISIBILITY, self :: PROPERTY_TUTOR_NAME, self :: PROPERTY_VISUAL_CODE, self :: PROPERTY_DEPARTMENT_URL, self :: PROPERTY_LAST_VISIT, self :: PROPERTY_LAST_EDIT, self :: PROPERTY_CREATION_DATE, self :: PROPERTY_EXPIRATION_DATE, self :: PROPERTY_TARGET_COURSE_CODE, self :: PROPERTY_SUBSCRIBE, self :: PROPERTY_UNSUBSCRIBE, self :: PROPERTY_REGISTRATION_CODE, self :: PROPERTY_DEPARTMENT_NAME);
    }

    /**
     * Sets a default property of this course by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default course
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
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
    function is_valid($parameters)
    {
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_code() || $mgdm->get_failed_element('dokeos_main.course_category', $this->get_category_code()))
        {
            $mgdm->add_failed_element($this->get_code(), 'dokeos_main.course');
            return false;
        }

        return true;
    }

    //check if visual code is avalaible
    function check_visual_code($visual_code, $course)
    {
        $mgdm = MigrationDataManager :: get_instance();

        if ($mgdm->visual_code_available($visual_code))
            $course->set_visual($visual_code);
        else
        {
            $index = $this->index ++;
            $this->check_visual_code($visual_code . '+' . $index, $course);
        }

    }

    /**
     * Convert to new course
     * @return the new course
     */
    function convert_to_lcms($parameters)
    {
    	//control if the weblcms application exists
		$is_registered = AdminDataManager :: is_registered('weblcms');
        
        if ($is_registered )
        {
        	//Course - General
        	$mgdm = MigrationDataManager :: get_instance();
        	$lcms_course = new Course();

        	//title & visual_code
        	$lcms_course->set_name($this->get_title());
        	$this->check_visual_code($this->get_visual_code(), $lcms_course);
			//titular id
        	$udm = UserDataManager :: get_instance();
        	$titular = $udm->retrieve_user_by_fullname($this->get_tutor_name());
        	if (!($titular) == NULL)
        	{
            	$titular_id = $titular->get_id();
        	}
            else
            {
            	$titular_id = 0;
            }
        	$lcms_course->set_titular($titular_id);

        	//category
        	$category_id = $mgdm->get_id_reference($this->get_category_code(), 'weblcms_course_category');
        	if ($category_id)
        	{
        		$lcms_course->set_category($category_id);
        	}
       		//departement_name & url
       		$lcms_course->set_extlink_name($this->get_department_name());
        	$lcms_course->set_extlink_url($this->get_department_url());
       		
        	//Course - Settings
       		if ($mgdm->is_language_available($this->get_course_language()))
        	{
            	$lcms_course->set_language($this->get_course_language());
        	}
            else
            {
            	$lcms_course->set_language('english');
            }
            $lcms_course->set_visibility($this->get_visibility());
            $lcms_course->set_max_number_of_members(0);
            
            //Course - Lay-out
            
            //Course - Tools
            
            //Course - Rights
        	$lcms_course->set_subscribe_allowed($this->get_subscribe());
        	$lcms_course->set_unsubscribe_allowed($this->get_unsubscribe());
        	
        	//Courses - Creation/Modification Dates
        	$lcms_course->set_default_property(Course :: PROPERTY_LAST_VISIT, $this->get_last_visit());
        	$lcms_course->set_default_property(Course :: PROPERTY_LAST_EDIT, $this->get_last_edit());
        	$lcms_course->set_default_property(Course :: PROPERTY_CREATION_DATE, $this->get_creation_date());
        	$lcms_course->set_default_property(Course :: PROPERTY_EXPIRATION_DATE, $this->get_expiration_date());

        	//create course in database
        	$lcms_course->create();

        	//Add id references to temp table
        	$old_code = $this->get_code();
        	$mgdm->add_id_reference($old_code, $lcms_course->get_id(), 'weblcms_course');
        	unset($old_code);
        	unset($mgdm);

        	return $lcms_course;
    	}
    }

    /**
     * Retrieve all courses from the database
     * @param array $parameters parameters for the retrieval
     * @return array of courses
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];

        $db = 'main_database';
        $tablename = 'course';
        $classname = 'Dokeos185Course';
        $test = $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
        unset($db);
        unset($tablename);
        unset($classname);
        unset($old_mgdm);
        return $test;
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'course';
        return $array;
    }
}
?>