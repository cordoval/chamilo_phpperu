<?php
/**
 * $Id: dokeos185_track_cos.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_cos.class.php';
require_once dirname(__FILE__) . '/../../../user/trackers/os_tracker.class.php';

/**
 * This class presents a Dokeos185 track_c_os
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCOs extends MigrationDataClass
{
    private static $mgdm;
    
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
    function Dokeos185TrackCOs($defaultProperties = array ())
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
    function is_valid($array)
    {
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_os() || $this->get_counter() == null)
        {
            $mgdm->add_failed_element($this->get_id(), 'track_c_os');
            return false;
        }
        return true;
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_data
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

    /**
     * Gets all the trackers
     * @param Array $array
     * @return Array
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = 'statistics_database';
        $tablename = 'track_c_os';
        $classname = 'Dokeos185TrackCOs';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'statistics_database';
        $array['table'] = 'track_c_os';
        return $array;
    }
}

?>