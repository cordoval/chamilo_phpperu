<?php
/**
 * $Id: dokeos185_permission_task.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_permission_task.class.php';

/**
 * This class presents a Dokeos185 permission_task
 *
 * @author Sven Vanpoucke
 */
class Dokeos185PermissionTask extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185PermissionTask properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TASK_ID = 'task_id';
    const PROPERTY_TOOL = 'tool';
    const PROPERTY_ACTION = 'action';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185PermissionTask object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185PermissionTask($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_TASK_ID, self :: PROPERTY_TOOL, self :: PROPERTY_ACTION);
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
     * Returns the id of this Dokeos185PermissionTask.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the task_id of this Dokeos185PermissionTask.
     * @return the task_id.
     */
    function get_task_id()
    {
        return $this->get_default_property(self :: PROPERTY_TASK_ID);
    }

    /**
     * Returns the tool of this Dokeos185PermissionTask.
     * @return the tool.
     */
    function get_tool()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL);
    }

    /**
     * Returns the action of this Dokeos185PermissionTask.
     * @return the action.
     */
    function get_action()
    {
        return $this->get_default_property(self :: PROPERTY_ACTION);
    }

    /**
     * Check if the task permission is valid
     * @param array $array the parameters for the validation
     * @return true if the task permission is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new task permission
     * @param array $array the parameters for the conversion
     * @return the new task permission
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all task permissions from the database
     * @param array $parameters parameters for the retrieval
     * @return array of task permissions
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'permission_task';
        $classname = 'Dokeos185PermissionTask';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'permission_task';
        return $array;
    }
}

?>