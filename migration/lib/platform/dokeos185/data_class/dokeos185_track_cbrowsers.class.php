<?php
/**
 * $Id: dokeos185_track_cbrowsers.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_cbrowsers.class.php';
require_once dirname(__FILE__) . '/../../../user/trackers/browsers_tracker.class.php';

/**
 * This class presents a Dokeos185 track_c_browsers
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCBrowsers extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185TrackCBrowsers properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_BROWSER = 'browser';
    const PROPERTY_COUNTER = 'counter';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackCBrowsers object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackCBrowsers($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_BROWSER, self :: PROPERTY_COUNTER);
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
     * Returns the id of this Dokeos185TrackCBrowsers.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the browser of this Dokeos185TrackCBrowsers.
     * @return the browser.
     */
    function get_browser()
    {
        return $this->get_default_property(self :: PROPERTY_BROWSER);
    }

    /**
     * Returns the counter of this Dokeos185TrackCBrowsers.
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
        if (! $this->get_browser() || $this->get_counter() == null)
        {
            self :: $mgdm->add_failed_element($this->get_id(), 'track_c_browsers');
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
        $conditions[] = new EqualityCondition('type', 'browser');
        $conditions[] = new EqualityCondition('name', $this->get_browser());
        $condtion = new AndCondition($conditions);
        $browsertracker = new BrowsersTracker();
        $trackeritems = $browsertracker->retrieve_tracker_items($condtion);
        
        if (count($trackeritems) != 0)
        {
            $browsertracker = $trackeritems[0];
            $browsertracker->set_value($browsertracker->get_value() + $this->get_counter());
            $browsertracker->update();
        }
        else
        {
            
            $browsertracker->set_name($this->get_browser());
            $browsertracker->set_value($this->get_counter());
            $browsertracker->create();
        }
        return $browsertracker;
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
        $tablename = 'track_c_browsers';
        $classname = 'Dokeos185TrackCBrowsers';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'statistics_database';
        $array['table'] = 'track_c_browsers';
        return $array;
    }
}

?>