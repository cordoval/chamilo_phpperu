<?php
/**
 * $Id: dokeos185_track_elogin.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_elogin.class.php';
require_once dirname(__FILE__) . '/../../../user/trackers/login_logout_tracker.class.php';

/**
 * This class presents a Dokeos185 track_e_login
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackELogin extends MigrationDataClass
{
    private static $mgdm;
    
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
    function Dokeos185TrackELogin($defaultProperties = array ())
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
    function is_valid($array)
    {
        $mgdm = MigrationDataManager :: get_instance();
        
        if (! $this->get_login_user_id() || ! $this->get_login_date() || ! $this->get_login_ip() || $mgdm->get_failed_element($this->get_login_user_id(), 'dokeos_main.user') || ! $mgdm->get_id_reference($this->get_login_user_id(), 'user_user'))
        {
            $mgdm->add_failed_element($this->get_login_id(), 'track_e_login');
            return false;
        }
        return true;
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_data
    {
        $login = new LoginLogoutTracker();
        $mgdm = MigrationDataManager :: get_instance();
        
        $new_user_id = $mgdm->get_id_reference($this->get_login_user_id(), 'user_user');
        $login->set_user_id($new_user_id);
        $login->set_ip($this->get_login_ip());
        $login->set_date($mgdm->make_unix_time($this->get_login_date()));
        $login->set_type('login');
        $login->create();
        
        if ($this->get_logout_date() != null)
        {
            $login = new LoginLogoutTracker();
            $login->set_user_id($new_user_id);
            $login->set_ip($this->get_login_ip());
            $login->set_date($mgdm->make_unix_time($this->get_logout_date()));
            $login->set_type('logout');
            $login->create();
        }
        return $login;
    }

    /**
     * Gets all the trackers
     * @param Array $array
     * @return Array
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = 'statistics_database';
        $tablename = 'track_e_login';
        $classname = 'Dokeos185TrackELogin';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'statistics_database';
        $array['table'] = 'track_e_login';
        return $array;
    }
}

?>