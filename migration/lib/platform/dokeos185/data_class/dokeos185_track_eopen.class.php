<?php

/**
 * $Id: dokeos185_track_eopen.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 track_e_open
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEOpen extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_e_open';
    const DATABASE_NAME = 'statistics_database';
    /**
     * Dokeos185TrackEOpen properties
     */
    const PROPERTY_OPEN_ID = 'open_id';
    const PROPERTY_OPEN_REMOTE_HOST = 'open_remote_host';
    const PROPERTY_OPEN_AGENT = 'open_agent';
    const PROPERTY_OPEN_REFERER = 'open_referer';
    const PROPERTY_OPEN_DATE = 'open_date';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackEOpen object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackEOpen($defaultProperties = array())
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
        return array(self :: PROPERTY_OPEN_ID, self :: PROPERTY_OPEN_REMOTE_HOST, self :: PROPERTY_OPEN_AGENT, self :: PROPERTY_OPEN_REFERER, self :: PROPERTY_OPEN_DATE);
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
     * Returns the open_id of this Dokeos185TrackEOpen.
     * @return the open_id.
     */
    function get_open_id()
    {
        return $this->get_default_property(self :: PROPERTY_OPEN_ID);
    }

    /**
     * Returns the open_remote_host of this Dokeos185TrackEOpen.
     * @return the open_remote_host.
     */
    function get_open_remote_host()
    {
        return $this->get_default_property(self :: PROPERTY_OPEN_REMOTE_HOST);
    }

    /**
     * Returns the open_agent of this Dokeos185TrackEOpen.
     * @return the open_agent.
     */
    function get_open_agent()
    {
        return $this->get_default_property(self :: PROPERTY_OPEN_AGENT);
    }

    /**
     * Returns the open_referer of this Dokeos185TrackEOpen.
     * @return the open_referer.
     */
    function get_open_referer()
    {
        return $this->get_default_property(self :: PROPERTY_OPEN_REFERER);
    }

    /**
     * Returns the open_date of this Dokeos185TrackEOpen.
     * @return the open_date.
     */
    function get_open_date()
    {
        return $this->get_default_property(self :: PROPERTY_OPEN_DATE);
    }

    /**
     * Validation checks
     * @param Array $array
     */
    function is_valid()
    {
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_data()
    {
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

    function get_database_name()
    {
        return self :: DATABASE_NAME;
    }

}
?>