<?php
/**
 * $Id: dokeos185_track_ecourse_access.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_ecours_eaccess.class.php';

/**
 * This class presents a Dokeos185 track_e_course_access
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackECourseAccess extends ImportTrackEAccess
{
    private static $mgdm;
    
    /**
     * Dokeos185TrackECourseAccess properties
     */
    const PROPERTY_COURSE_ACCESS_ID = 'course_access_id';
    const PROPERTY_COURSE_CODE = 'course_code';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_LOGIN_COURSE_DATE = 'login_course_date';
    const PROPERTY_LOGOUT_COURSE_DATE = 'logout_course_date';
    const PROPERTY_COUNTER = 'counter';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackECourseAccess object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackECourseAccess($defaultProperties = array ())
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
        return array(self :: PROPERTY_COURSE_ACCESS_ID, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_USER_ID, self :: PROPERTY_LOGIN_COURSE_DATE, self :: PROPERTY_LOGOUT_COURSE_DATE, self :: PROPERTY_COUNTER);
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
     * Returns the course_access_id of this Dokeos185TrackECourseAccess.
     * @return the course_access_id.
     */
    function get_course_access_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ACCESS_ID);
    }

    /**
     * Returns the course_code of this Dokeos185TrackECourseAccess.
     * @return the course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    /**
     * Returns the user_id of this Dokeos185TrackECourseAccess.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the login_course_date of this Dokeos185TrackECourseAccess.
     * @return the login_course_date.
     */
    function get_login_course_date()
    {
        return $this->get_default_property(self :: PROPERTY_LOGIN_COURSE_DATE);
    }

    /**
     * Returns the logout_course_date of this Dokeos185TrackECourseAccess.
     * @return the logout_course_date.
     */
    function get_logout_course_date()
    {
        return $this->get_default_property(self :: PROPERTY_LOGOUT_COURSE_DATE);
    }

    /**
     * Returns the counter of this Dokeos185TrackECourseAccess.
     * @return the counter.
     */
    function get_counter()
    {
        return $this->get_default_property(self :: PROPERTY_COUNTER);
    }

    /**
     * Validation checks
     * @param Array $array
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
    }

    /**
     * Gets all the trackers
     * @param Array $array
     * @return Array
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = 'statistics_database';
        $tablename = 'track_e_course_access';
        $classname = 'Dokeos185TrackECourseAccess';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'statistics_database';
        $array['table'] = 'track_e_course_access';
        return $array;
    }
}

?>