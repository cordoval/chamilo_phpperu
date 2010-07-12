<?php
/**
 * $Id: dokeos185_role_permissions.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_role_permissions.class.php';

/**
 * This class presents a Dokeos185 role_permissions
 *
 * @author Sven Vanpoucke
 */
class Dokeos185RolePermissions extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185RolePermissions properties
     */
    const PROPERTY_ROLE_ID = 'role_id';
    const PROPERTY_TOOL = 'tool';
    const PROPERTY_ACTION = 'action';
    const PROPERTY_DEFAULT_PERM = 'default_perm';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185RolePermissions object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185RolePermissions($defaultProperties = array ())
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
        return array(self :: PROPERTY_ROLE_ID, self :: PROPERTY_TOOL, self :: PROPERTY_ACTION, self :: PROPERTY_DEFAULT_PERM);
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
     * Returns the role_id of this Dokeos185RolePermissions.
     * @return the role_id.
     */
    function get_role_id()
    {
        return $this->get_default_property(self :: PROPERTY_ROLE_ID);
    }

    /**
     * Returns the tool of this Dokeos185RolePermissions.
     * @return the tool.
     */
    function get_tool()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL);
    }

    /**
     * Returns the action of this Dokeos185RolePermissions.
     * @return the action.
     */
    function get_action()
    {
        return $this->get_default_property(self :: PROPERTY_ACTION);
    }

    /**
     * Returns the default_perm of this Dokeos185RolePermissions.
     * @return the default_perm.
     */
    function get_default_perm()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_PERM);
    }

    /**
     * Checks if a role permissions is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * migrate role permissions, sets category
     * @param Array $array
     * @return
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Gets all the role permissions of a course
     * @param Array $array
     * @return Array of dokeos185rolepermissions
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'role_permissions';
        $classname = 'Dokeos185RolePermissions';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'role_permissions';
        return $array;
    }
}

?>