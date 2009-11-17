<?php
/**
 * $Id: dokeos185_php_session.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_php_session.class.php';

/**
 * This class presents a Dokeos185 php_session
 *
 * @author Sven Vanpoucke
 */
class Dokeos185PhpSession extends ImportPhpSession
{
    private static $mgdm;
    
    /**
     * Dokeos185PhpSession properties
     */
    const PROPERTY_SESSION_ID = 'session_id';
    const PROPERTY_SESSION_NAME = 'session_name';
    const PROPERTY_SESSION_TIME = 'session_time';
    const PROPERTY_SESSION_START = 'session_start';
    const PROPERTY_SESSION_VALUE = 'session_value';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185PhpSession object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185PhpSession($defaultProperties = array ())
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
        return array(self :: PROPERTY_SESSION_ID, self :: PROPERTY_SESSION_NAME, self :: PROPERTY_SESSION_TIME, self :: PROPERTY_SESSION_START, self :: PROPERTY_SESSION_VALUE);
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
     * Returns the session_id of this Dokeos185PhpSession.
     * @return the session_id.
     */
    function get_session_id()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_ID);
    }

    /**
     * Returns the session_name of this Dokeos185PhpSession.
     * @return the session_name.
     */
    function get_session_name()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_NAME);
    }

    /**
     * Returns the session_time of this Dokeos185PhpSession.
     * @return the session_time.
     */
    function get_session_time()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_TIME);
    }

    /**
     * Returns the session_start of this Dokeos185PhpSession.
     * @return the session_start.
     */
    function get_session_start()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_START);
    }

    /**
     * Returns the session_value of this Dokeos185PhpSession.
     * @return the session_value.
     */
    function get_session_value()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_VALUE);
    }

    /**
     * Check if the php session is valid
     * @param array $array the parameters for the validation
     * @return true if the php session is valid 
     */
    function is_valid($array)
    {
    
    }

    /**
     * Convert to new php session
     * @param array $array the parameters for the conversion
     * @return the new php session
     */
    function convert_to_lcms($array)
    {
    
    }

    /**
     * Retrieve all php sessions from the database
     * @param array $parameters parameters for the retrieval
     * @return array of php sessions
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'php_session';
        $classname = 'Dokeos185PhpSession';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'php_session';
        return $array;
    }
}

?>