<?php

/**
 * $Id: dokeos185_track_creferers.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 track_c_referers
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCReferers extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_c_referers';
    const DATABASE_NAME = 'statistics_database';

    /**
     * Dokeos185TrackCReferers properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_REFERER = 'referer';
    const PROPERTY_COUNTER = 'counter';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackCReferers object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackCReferers($defaultProperties = array())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_REFERER, self :: PROPERTY_COUNTER);
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
     * Returns the id of this Dokeos185TrackCReferers.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the referer of this Dokeos185TrackCReferers.
     * @return the referer.
     */
    function get_referer()
    {
        return $this->get_default_property(self :: PROPERTY_REFERER);
    }

    /**
     * Returns the counter of this Dokeos185TrackCReferers.
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
        if (!$this->get_referer() || $this->get_counter() == null)
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
        $conditions[] = new EqualityCondition('type', 'referer');
        $conditions[] = new EqualityCondition('name', $this->get_referer());
        $condtion = new AndCondition($conditions);
        $referertracker = new ReferrersTracker();
        $trackeritems = $referertracker->retrieve_tracker_items($condtion);

        if (count($trackeritems) != 0)
        {
            $referertracker = $trackeritems[0];
            $referertracker->set_value($referertracker->get_value() + $this->get_counter());
            $referertracker->update();
        }
        else
        {

            $referertracker->set_name($this->get_referer());
            $referertracker->set_value($this->get_counter());
            $referertracker->create();
        }
        return $referertracker;
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