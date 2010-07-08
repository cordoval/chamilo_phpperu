<?php
/**
 * $Id: dokeos185_track_eaccess.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_eaccess.class.php';

/**
 * This class presents a Dokeos185 track_e_access
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEAccess extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185TrackEAccess properties
     */
    const PROPERTY_ACCESS_ID = 'access_id';
    const PROPERTY_ACCESS_USER_ID = 'access_user_id';
    const PROPERTY_ACCESS_DATE = 'access_date';
    const PROPERTY_ACCESS_COURS_CODE = 'access_cours_code';
    const PROPERTY_ACCESS_TOOL = 'access_tool';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackEAccess object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackEAccess($defaultProperties = array ())
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
        return array(self :: PROPERTY_ACCESS_ID, self :: PROPERTY_ACCESS_USER_ID, self :: PROPERTY_ACCESS_DATE, self :: PROPERTY_ACCESS_COURS_CODE, self :: PROPERTY_ACCESS_TOOL);
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
     * Returns the access_id of this Dokeos185TrackEAccess.
     * @return the access_id.
     */
    function get_access_id()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_ID);
    }

    /**
     * Returns the access_user_id of this Dokeos185TrackEAccess.
     * @return the access_user_id.
     */
    function get_access_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_USER_ID);
    }

    /**
     * Returns the access_date of this Dokeos185TrackEAccess.
     * @return the access_date.
     */
    function get_access_date()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_DATE);
    }

    /**
     * Returns the access_cours_code of this Dokeos185TrackEAccess.
     * @return the access_cours_code.
     */
    function get_access_cours_code()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_COURS_CODE);
    }

    /**
     * Returns the access_tool of this Dokeos185TrackEAccess.
     * @return the access_tool.
     */
    function get_access_tool()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_TOOL);
    }

    /**
     * Validation checks
     * @param Array $array
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_data
    {
        $course = $array['course'];
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
        $tablename = 'track_e_access';
        $classname = 'Dokeos185TrackEAccess';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'statistics_database';
        $array['table'] = 'track_e_access';
        return $array;
    }
}

?>