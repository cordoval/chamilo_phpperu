<?php
/**
 * $Id: dokeos185_session_rel_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_session_rel_user.class.php';

/**
 * This class presents a Dokeos185 session_rel_user
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SessionRelUser extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185SessionRelUser properties
     */
    const PROPERTY_ID_SESSION = 'id_session';
    const PROPERTY_ID_USER = 'id_user';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SessionRelUser object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SessionRelUser($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID_SESSION, self :: PROPERTY_ID_USER);
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
     * Returns the id_session of this Dokeos185SessionRelUser.
     * @return the id_session.
     */
    function get_id_session()
    {
        return $this->get_default_property(self :: PROPERTY_ID_SESSION);
    }

    /**
     * Returns the id_user of this Dokeos185SessionRelUser.
     * @return the id_user.
     */
    function get_id_user()
    {
        return $this->get_default_property(self :: PROPERTY_ID_USER);
    }

    /**
     * Checks if a sessionreluser is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
    
    }

    /**
     * migrate sessionreluser, sets category
     * @param Array $array
     * @return
     */
    function convert_data
    {
    
    }

    /**
     * Gets all the sessionrelusers of a course
     * @param Array $array
     * @return Array of dokeos185sessionreluser
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'session_rel_user';
        $classname = 'Dokeos185SessionRelUser';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'session_rel_user';
        return $array;
    }
}

?>