<?php

/**
 * $Id: dokeos185_resource.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_resource.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/announcement/announcement.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Resource
 *
 * @author Sven Vanpoucke
 */
class Dokeos185CalendarEvent extends ImportResource
{
    /**
     * Migration data manager
     */
    private static $mgdm;
    
    /**
     * Announcement properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_SOURCE_TYPE = 'source_type';
    const PROPERTY_SOURCE_ID = 'source_id';
    const PROPERTY_RESOURCE_TYPE = 'resource_type';
    const PROPERTY_RESOURCE_ID = 'resource_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new dokeos185 Calender Event object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185CalendarEvent($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_SOURCE_TYPE, self :: PROPERTY_SOURCE_ID, self :: PROPERTY_RESOURCE_TYPE, self :: PROPERTY_RESOURCE_ID);
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
     * Returns the id of this resource.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the source_type of this resource.
     * @return string the source_type.
     */
    function get_source_type()
    {
        return $this->get_default_property(self :: PROPERTY_SOURCE_TYPE);
    }

    /**
     * Returns the source_id of this resource.
     * @return int the source_id.
     */
    function get_source_id()
    {
        return $this->get_default_property(self :: PROPERTY_SOURCE_ID);
    }

    /**
     * Returns the res_type of this resource.
     * @return string the res_type.
     */
    function get_res_type()
    {
        return $this->get_default_property(self :: PROPERTY_RESOURCE_TYPE);
    }

    /**
     * Returns the resource_id of this resource.
     * @return int the resource_id.
     */
    function get_resource_id()
    {
        return $this->get_default_property(self :: PROPERTY_RESOURCE_ID);
    }

    /**
     * Checks if a resource is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * migrate resource, sets category
     * @param Array $array
     * @return
     */
    
    function convert_to_lcms($array)
    {
        $course = $array['course'];
    }

    /**
     * Gets all the resource of a course
     * @param Array $array
     * @return Array of dokeos185resource
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'resource';
        $classname = 'Dokeos185Resource';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'resource';
        return $array;
    }
}
?>
