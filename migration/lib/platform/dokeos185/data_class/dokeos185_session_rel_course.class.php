<?php
/**
 * $Id: dokeos185_session_rel_course.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_session_rel_course.class.php';

/**
 * This class presents a Dokeos185 session_rel_course
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SessionRelCourse extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185SessionRelCourse properties
     */
    const PROPERTY_ID_SESSION = 'id_session';
    const PROPERTY_COURSE_CODE = 'course_code';
    const PROPERTY_ID_COACH = 'id_coach';
    const PROPERTY_NBR_USERS = 'nbr_users';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SessionRelCourse object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SessionRelCourse($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID_SESSION, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_ID_COACH, self :: PROPERTY_NBR_USERS);
    }

    /**
     * Sets a default property by name.
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
     * Returns the id_session of this Dokeos185SessionRelCourse.
     * @return the id_session.
     */
    function get_id_session()
    {
        return $this->get_default_property(self :: PROPERTY_ID_SESSION);
    }

    /**
     * Returns the course_code of this Dokeos185SessionRelCourse.
     * @return the course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    /**
     * Returns the id_coach of this Dokeos185SessionRelCourse.
     * @return the id_coach.
     */
    function get_id_coach()
    {
        return $this->get_default_property(self :: PROPERTY_ID_COACH);
    }

    /**
     * Returns the nbr_users of this Dokeos185SessionRelCourse.
     * @return the nbr_users.
     */
    function get_nbr_users()
    {
        return $this->get_default_property(self :: PROPERTY_NBR_USERS);
    }

    /**
     * Checks if a sessionrelcourse is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
    
    }

    /**
     * migrate sessionrelcourse, sets category
     * @param Array $array
     * @return 
     */
    function convert_data
    {
    
    }

    /**
     * Gets all the sessionrelcourse of a course
     * @param Array $array
     * @return Array of dokeos185sessionrelcourse
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'session_rel_course';
        $classname = 'Dokeos185SessionRelCourse';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'session_rel_course';
        return $array;
    }
}

?>