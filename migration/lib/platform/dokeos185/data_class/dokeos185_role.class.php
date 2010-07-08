<?php
/**
 * $Id: dokeos185_role.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_role.class.php';

/**
 * This class presents a Dokeos185 role
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Role extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185Role properties
     */
    const PROPERTY_ROLE_ID = 'role_id';
    const PROPERTY_ROLE_NAME = 'role_name';
    const PROPERTY_ROLE_COMMENT = 'role_comment';
    const PROPERTY_DEFAULT_ROLE = 'default_role';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185Role object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Role($defaultProperties = array ())
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
        return array(self :: PROPERTY_ROLE_ID, self :: PROPERTY_ROLE_NAME, self :: PROPERTY_ROLE_COMMENT, self :: PROPERTY_DEFAULT_ROLE);
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
     * Returns the role_id of this Dokeos185Role.
     * @return the role_id.
     */
    function get_role_id()
    {
        return $this->get_default_property(self :: PROPERTY_ROLE_ID);
    }

    /**
     * Returns the role_name of this Dokeos185Role.
     * @return the role_name.
     */
    function get_role_name()
    {
        return $this->get_default_property(self :: PROPERTY_ROLE_NAME);
    }

    /**
     * Returns the role_comment of this Dokeos185Role.
     * @return the role_comment.
     */
    function get_role_comment()
    {
        return $this->get_default_property(self :: PROPERTY_ROLE_COMMENT);
    }

    /**
     * Returns the default_role of this Dokeos185Role.
     * @return the default_role.
     */
    function get_default_role()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_ROLE);
    }

    /**
     * Checks if a role is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * migrate role, sets category
     * @param Array $array
     * @return
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Gets all the role of a course
     * @param Array $array
     * @return Array of dokeos185role
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'role';
        $classname = 'Dokeos185Role';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'role';
        return $array;
    }
}

?>