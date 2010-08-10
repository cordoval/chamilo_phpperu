<?php

/**
 * $Id: dokeos185_track_ecourse_access.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 track_e_course_access
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackECourseAccess extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_e_course_access';
    const DATABASE_NAME = 'statistics_database';

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
    function Dokeos185TrackECourseAccess($defaultProperties = array())
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
     * @todo
     */
    function is_valid()
    {
        $new_user_id = $this->get_id_reference($this->get_user_id(), 'main_database.user');

        if (!$new_user_id) //if the user id doesn't exist anymore, the data can be ignored
        {
            $this->create_failed_element($this->get_id());
            return false;
        }
        return true;
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_data()
    {
        $visit_tracker = new VisitTracker();
        $new_course_id = $this->get_id_reference($this->get_course_code(), 'main_database.course');
        $new_user_id = $this->get_id_reference($this->get_user_id(), 'main_database.user');

        $url="/hg/run.php?go=courseviewer&course=$new_course_id&application=weblcms";

        $visit_tracker->set_enter_date(strtotime($this->get_login_course_date()));
        $visit_tracker->set_leave_date(strtotime($this->get_logout_course_date()));
        $visit_tracker->set_location($url);
        $visit_tracker->set_user_id($new_user_id);

        $visit_tracker->create();
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