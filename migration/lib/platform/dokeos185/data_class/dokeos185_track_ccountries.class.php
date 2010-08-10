<?php

/**
 * $Id: dokeos185_track_ccountries.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 track_c_countries
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCCountries extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_c_countries';
    const DATABASE_NAME = 'statistics_database';

    /**
     * Dokeos185TrackCCountries properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_CODE = 'code';
    const PROPERTY_COUNTRY = 'country';
    const PROPERTY_COUNTER = 'counter';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackCCountries object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackCCountries($defaultProperties = array())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_CODE, self :: PROPERTY_COUNTRY, self :: PROPERTY_COUNTER);
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
     * Returns the id of this Dokeos185TrackCCountries.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the code of this Dokeos185TrackCCountries.
     * @return the code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Returns the country of this Dokeos185TrackCCountries.
     * @return the country.
     */
    function get_country()
    {
        return $this->get_default_property(self :: PROPERTY_COUNTRY);
    }

    /**
     * Returns the counter of this Dokeos185TrackCCountries.
     * @return the counter.
     */
    function get_counter()
    {
        return $this->get_default_property(self :: PROPERTY_COUNTER);
    }

    /**
     * Validation checks
     * @param Array $array
     */
    function is_valid()
    {
        if (!$this->get_country() || $this->get_counter() == null)
        {
            $this->create_failed_element($this->get_id());
            return false;
        }
        return true;
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_data()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition('type', 'country');
        $conditions[] = new EqualityCondition('name', $this->get_country());
        $condtion = new AndCondition($conditions);
        $countriestracker = new CountriesTracker();
        $trackeritems = $countriestracker->retrieve_tracker_items($condtion);

        if (count($trackeritems) != 0)
        {
            $countriestracker = $trackeritems[0];
            $countriestracker->set_value($countriestracker->get_value() + $this->get_counter());
            $countriestracker->update();
        }
        else
        {

            $countriestracker->set_name($this->get_country());
            $countriestracker->set_value($this->get_counter());
            $countriestracker->create();
        }
        return $countriestracker;
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