<?php
/**
 * $Id: tracker_registration.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib
 */

/**
 * This class presents a tracker registration
 *
 * @author Sven Vanpoucke
 */


class TrackerRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * Tracker properties
     */
    const PROPERTY_CLASS = 'class';
    const PROPERTY_PATH = 'path';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CLASS, self :: PROPERTY_PATH));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return TrackingDataManager :: get_instance();
    }

    /**
     * Returns the class of this Tracker.
     * @return the class.
     */
    function get_class()
    {
        return $this->get_default_property(self :: PROPERTY_CLASS);
    }

    /**
     * Sets the class of this Tracker.
     * @param class
     */
    function set_class($class)
    {
        $this->set_default_property(self :: PROPERTY_CLASS, $class);
    }

    /**
     * Returns the path of this Tracker.
     * @return the path.
     */
    function get_path()
    {
        return $this->get_default_property(self :: PROPERTY_PATH);
    }

    /**
     * Sets the path of this Tracker.
     * @param path
     */
    function set_path($path)
    {
        $this->set_default_property(self :: PROPERTY_PATH, $path);
    }

    /**
     * Creates this event in the database
     */
    function create()
    {
        $trkdmg = TrackingDataManager :: get_instance();
        return $trkdmg->create_tracker_registration($this);
    }

    /**
     * Returns the activity of this tracker registration for an event
     * @return bool active
     */
    function get_active()
    {
        return $this->active;
    }

    /**
     * Sets the activity of this tracker registration for an event
     * @param bool active
     */
    function set_active($active)
    {
        $this->active = $active;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>