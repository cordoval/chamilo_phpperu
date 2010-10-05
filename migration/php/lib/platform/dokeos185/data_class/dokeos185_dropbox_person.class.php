<?php
/**
 * $Id: dokeos185_dropbox_person.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_dropbox_person.class.php';

/**
 * This class presents a Dokeos185 dropbox_person
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxPerson extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185DropboxPerson properties
     */
    const PROPERTY_FILE_ID = 'file_id';
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185DropboxPerson object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185DropboxPerson($defaultProperties = array ())
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
        return array(self :: PROPERTY_FILE_ID, self :: PROPERTY_USER_ID);
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
     * Returns the file_id of this Dokeos185DropboxPerson.
     * @return the file_id.
     */
    function get_file_id()
    {
        return $this->get_default_property(self :: PROPERTY_FILE_ID);
    }

    /**
     * Returns the user_id of this Dokeos185DropboxPerson.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Check if the dropbox person is valid
     * @param array $array the parameters for the validation
     * @return true if the dropbox person is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new dropbox person
     * @param array $array the parameters for the conversion
     * @return the new dropbox person
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all dropbox persons from the database
     * @param array $parameters parameters for the retrieval
     * @return array of dropbox persons
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'dropbox_person';
        $classname = 'Dokeos185DropboxPerson';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'dropbox_person';
        return $array;
    }
}

?>