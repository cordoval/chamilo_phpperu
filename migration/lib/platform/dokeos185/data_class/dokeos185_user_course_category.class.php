<?php
/**
 * $Id: dokeos185_user_course_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_user_course_category.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/course/course_user_category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 user course category
 *
 * @author David Van Wayenbergh
 */

class Dokeos185UserCourseCategory extends MigrationDataClass
{
    /**
     * Migration data manager
     */
    private static $mgdm;
    
    /**
     * course properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    
    /**
     * Alfanumeric identifier of the course object.
     */
    private $code;
    
    /**
     * Default properties of the user_course_category object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new course object.
     * @param array $defaultProperties The default properties of the user course category
     *                                 object. Associative array.
     */
    function Dokeos185UserCourseCategory($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this user course category object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this user course category.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all user course category.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_TITLE, self :: PROPERTY_SORT);
    }

    /**
     * Sets a default property of this User Course Category by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Checks if the given identifier is the name of a default user course category
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
    function is_valid($parameters)
    {
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_id() || ! $this->get_user_id() || ! $this->get_title() || $mgdm->get_failed_element('dokeos_main.user', $this->get_user_id()) || ! $mgdm->get_id_reference($this->get_user_id(), 'user_user'))
        {
            $mgdm->add_failed_element($this->get_id(), 'dokeos_user.user_course_category');
            return false;
        }
        
        return true;
    }

    /**
     * Migration user course category
     * @return CourseUserCategory
     */
    function convert_data
    {
        $mgdm = MigrationDataManager :: get_instance();
        //Course parameters
        $lcms_user_course_category = new CourseUserCategory();
        
        $user_id = $mgdm->get_id_reference($this->get_user_id(), 'user_user');
        if ($user_id)
            $lcms_user_course_category->set_user($user_id);
        unset($user_id);
        $lcms_user_course_category->set_title($this->get_title());
        
        //create course in database
        $lcms_user_course_category->create();
        
        //Add id references to temp table
        $mgdm->add_id_reference($this->get_id(), $lcms_user_course_category->get_id(), 'weblcms_course_user_category');
        unset($mgdm);
        return $lcms_user_course_category;
    }

    /**
     * Gets all the user course categories
     * @param array $parameters
     * @return Array with dokeos185usercoursecategory
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = 'user_personal_database';
        $tablename = 'user_course_category';
        $classname = 'Dokeos185UserCourseCategory';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'user_personal_database';
        $array['table'] = 'user_course_category';
        return $array;
    }
}
?>