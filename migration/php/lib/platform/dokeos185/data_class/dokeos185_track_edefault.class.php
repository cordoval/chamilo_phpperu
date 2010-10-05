<?php

/**
 * $Id: dokeos185_track_edefault.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 track_e_default
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEDefault extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_e_default';
    const DATABASE_NAME = 'statistics_database';

    /**
     * Dokeos185TrackEDefault properties
     */
    const PROPERTY_DEFAULT_ID = 'default_id';
    const PROPERTY_DEFAULT_USER_ID = 'default_user_id';
    const PROPERTY_DEFAULT_COURS_CODE = 'default_cours_code';
    const PROPERTY_DEFAULT_DATE = 'default_date';
    const PROPERTY_DEFAULT_EVENT_TYPE = 'default_event_type';
    const PROPERTY_DEFAULT_VALUE_TYPE = 'default_value_type';
    const PROPERTY_DEFAULT_VALUE = 'default_value';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackEDefault object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackEDefault($defaultProperties = array())
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
        return array(self :: PROPERTY_DEFAULT_ID, self :: PROPERTY_DEFAULT_USER_ID, self :: PROPERTY_DEFAULT_COURS_CODE, self :: PROPERTY_DEFAULT_DATE, self :: PROPERTY_DEFAULT_EVENT_TYPE, self :: PROPERTY_DEFAULT_VALUE_TYPE, self :: PROPERTY_DEFAULT_VALUE);
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
     * Returns the default_id of this Dokeos185TrackEDefault.
     * @return the default_id.
     */
    function get_default_id()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_ID);
    }

    /**
     * Returns the default_user_id of this Dokeos185TrackEDefault.
     * @return the default_user_id.
     */
    function get_default_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_USER_ID);
    }

    /**
     * Returns the default_cours_code of this Dokeos185TrackEDefault.
     * @return the default_cours_code.
     */
    function get_default_cours_code()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_COURS_CODE);
    }

    /**
     * Returns the default_date of this Dokeos185TrackEDefault.
     * @return the default_date.
     */
    function get_default_date()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_DATE);
    }

    /**
     * Returns the default_event_type of this Dokeos185TrackEDefault.
     * @return the default_event_type.
     */
    function get_default_event_type()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_EVENT_TYPE);
    }

    /**
     * Returns the default_value_type of this Dokeos185TrackEDefault.
     * @return the default_value_type.
     */
    function get_default_value_type()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_VALUE_TYPE);
    }

    /**
     * Returns the default_value of this Dokeos185TrackEDefault.
     * @return the default_value.
     */
    function get_default_value()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_VALUE);
    }

    /**
     * Validation checks
     * @param Array $array
     * @todo
     */
    function is_valid()
    {
    }

    /**
     * Convertion
     * @param Array $array
     * @todo
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