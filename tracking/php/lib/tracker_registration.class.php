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
    const PROPERTY_TRACKER = 'tracker';
    const PROPERTY_APPLICATION = 'application';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TRACKER, self :: PROPERTY_APPLICATION));
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
    function get_tracker()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKER);
    }

    /**
     * Sets the class of this Tracker.
     * @param class
     */
    function set_tracker($tracker)
    {
        $this->set_default_property(self :: PROPERTY_TRACKER, $tracker);
    }

    /**
     * Returns the application of this Tracker.
     * @return string The application.
     */
    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    /**
     * Sets the application of this Tracker.
     * @param string The application
     */
    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
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