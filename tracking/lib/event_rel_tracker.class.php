<?php
/**
 * $Id: event_rel_tracker.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib
 */

/**
 * This class presents a event_rel_tracker
 *
 * @author Sven Vanpoucke
 */


class EventRelTracker extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * EventRelTracker properties
     */
    const PROPERTY_EVENT_ID = 'event_id';
    const PROPERTY_TRACKER_ID = 'tracker_id';
    const PROPERTY_ACTIVE = 'active';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_EVENT_ID, self :: PROPERTY_TRACKER_ID, self :: PROPERTY_ACTIVE);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return TrackingDataManager :: get_instance();
    }

    /**
     * Returns the event_id of this EventRelTracker.
     * @return the event_id.
     */
    function get_event_id()
    {
        return $this->get_default_property(self :: PROPERTY_EVENT_ID);
    }

    /**
     * Sets the event_id of this EventRelTracker.
     * @param event_id
     */
    function set_event_id($event_id)
    {
        $this->set_default_property(self :: PROPERTY_EVENT_ID, $event_id);
    }

    /**
     * Returns the tracker_id of this EventRelTracker.
     * @return the tracker_id.
     */
    function get_tracker_id()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKER_ID);
    }

    /**
     * Sets the tracker_id of this EventRelTracker.
     * @param tracker_id
     */
    function set_tracker_id($tracker_id)
    {
        $this->set_default_property(self :: PROPERTY_TRACKER_ID, $tracker_id);
    }

    /**
     * Returns the active of this EventRelTracker.
     * @return the active.
     */
    function get_active()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }

    /**
     * Sets the active of this EventRelTracker.
     * @param active
     */
    function set_active($active)
    {
        $this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }

    /**
     * Creates this event tracker relation in the database
     */
    function create()
    {
        $trkdmg = TrackingDataManager :: get_instance();
        return $trkdmg->create_event_tracker_relation($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>