<?php

require_once dirname(__FILE__) . "/../../../../../user/trackers/login_logout_tracker.class.php";

require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * $Id: dokeos185_track_elogin.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 track_e_login
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackELogin extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_e_login';
    const DATABASE_NAME = 'statistics_database';
    /**
     * Dokeos185TrackELogin properties
     */
    const PROPERTY_LOGIN_ID = 'login_id';
    const PROPERTY_LOGIN_USER_ID = 'login_user_id';
    const PROPERTY_LOGIN_DATE = 'login_date';
    const PROPERTY_LOGIN_IP = 'login_ip';
    const PROPERTY_LOGOUT_DATE = 'logout_date';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackELogin object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackELogin($defaultProperties = array())
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
        return array(self :: PROPERTY_LOGIN_ID, self :: PROPERTY_LOGIN_USER_ID, self :: PROPERTY_LOGIN_DATE, self :: PROPERTY_LOGIN_IP, self :: PROPERTY_LOGOUT_DATE);
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
     * Returns the login_id of this Dokeos185TrackELogin.
     * @return the login_id.
     */
    function get_login_id()
    {
        return $this->get_default_property(self :: PROPERTY_LOGIN_ID);
    }

    /**
     * Returns the login_user_id of this Dokeos185TrackELogin.
     * @return the login_user_id.
     */
    function get_login_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_LOGIN_USER_ID);
    }

    /**
     * Returns the login_date of this Dokeos185TrackELogin.
     * @return the login_date.
     */
    function get_login_date()
    {
        return $this->get_default_property(self :: PROPERTY_LOGIN_DATE);
    }

    /**
     * Returns the login_ip of this Dokeos185TrackELogin.
     * @return the login_ip.
     */
    function get_login_ip()
    {
        return $this->get_default_property(self :: PROPERTY_LOGIN_IP);
    }

    /**
     * Returns the logout_date of this Dokeos185TrackELogin.
     * @return the logout_date.
     */
    function get_logout_date()
    {
        return $this->get_default_property(self :: PROPERTY_LOGOUT_DATE);
    }

    /**
     * Validation checks
     * @param Array $array
     */
    function is_valid()
    {

        if (!$this->get_login_user_id() || !$this->get_login_date() || !$this->get_login_ip() || !$this->get_id_reference($this->get_login_user_id(), 'main_database.user'))
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
        $login = new LoginLogoutTracker();
        $mgdm = MigrationDataManager :: get_instance();

        $new_user_id = $this->get_id_reference($this->get_login_user_id(), 'main_database.user');
        $login->set_user_id($new_user_id);
        $login->set_ip($this->get_login_ip());
        $login->set_date(strtotime($this->get_login_date()));
        $login->set_type('login');
        $login->create();

        if ($this->get_logout_date() != null)
        {
            $login = new LoginLogoutTracker();
            $login->set_user_id($new_user_id);
            $login->set_ip($this->get_login_ip());
            $login->set_date(strtotime($this->get_logout_date()));
            $login->set_type('logout');
            $login->create();
        }
        return $login;
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