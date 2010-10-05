<?php

/**
 * $Id: dokeos185_track_cos.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 track_c_os
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCOs extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_c_os';
    const DATABASE_NAME = 'statistics_database';

    /**
     * Dokeos185TrackCOs properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_OS = 'os';
    const PROPERTY_COUNTER = 'counter';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackCOs object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackCOs($defaultProperties = array())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_OS, self :: PROPERTY_COUNTER);
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
     * Returns the id of this Dokeos185TrackCOs.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the os of this Dokeos185TrackCOs.
     * @return the os.
     */
    function get_os()
    {
        return $this->get_default_property(self :: PROPERTY_OS);
    }

    /**
     * Returns the counter of this Dokeos185TrackCOs.
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
        if (!$this->get_os() || $this->get_counter() == null)
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
        $conditions[] = new EqualityCondition('type', 'os');
        $conditions[] = new EqualityCondition('name', $this->get_os());
        $condtion = new AndCondition($conditions);
        $ostracker = new OSTracker();
        $trackeritems = $ostracker->retrieve_tracker_items($condtion);

        if (count($trackeritems) != 0)
        {
            $ostracker = $trackeritems[0];
            $ostracker->set_value($ostracker->get_value() + $this->get_counter());
            $ostracker->update();
        }
        else
        {

            $ostracker->set_name($this->get_os());
            $ostracker->set_value($this->get_counter());
            $ostracker->create();
        }
        return $ostracker;
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