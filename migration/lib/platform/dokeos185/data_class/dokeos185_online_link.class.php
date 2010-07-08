<?php
/**
 * $Id: dokeos185_online_link.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_online_link.class.php';

/**
 * This class presents a Dokeos185 online_link
 *
 * @author Sven Vanpoucke
 */
class Dokeos185OnlineLink extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185OnlineLink properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_URL = 'url';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185OnlineLink object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185OnlineLink($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_URL);
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
     * Returns the id of this Dokeos185OnlineLink.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the name of this Dokeos185OnlineLink.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the url of this Dokeos185OnlineLink.
     * @return the url.
     */
    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    /**
     * Check if the online link is valid
     * @param array $array the parameters for the validation
     * @return true if the online link is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new online link
     * @param array $array the parameters for the conversion
     * @return the new online link
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all online links from the database
     * @param array $parameters parameters for the retrieval
     * @return array of online links
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'online_link';
        $classname = 'Dokeos185OnlineLink';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'online_link';
        return $array;
    }
}

?>