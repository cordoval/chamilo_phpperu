<?php
/**
 * $Id: tracking_manager_component.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager
 */


/**
 * Base class for a tracking manager component.
 * A tracking manager provides different tools to the end tracker. Each tool is
 * represented by a tracking manager component and should extend this class.
 */

abstract class TrackingManagerComponent extends CoreApplicationComponent
{

    /**
     * Constructor
     * @param trackingManager $tracking_manager The tracking manager which
     * provides this component
     */
    function TrackingManagerComponent($tracking_manager)
    {
        parent :: __construct($tracking_manager);
    }

    /**
     * Retrieves the browser url
     * @return the browser url
     * @see TrackingManager :: get_browser_url()
     */
    function get_browser_url()
    {
        return $this->get_parent()->get_browser_url();
    }

    /**
     * Retrieves the change active url
     * @see TrackingManager :: get_change_active_url;
     */
    function get_change_active_url($type, $event_id, $tracker_id = null)
    {
        return $this->get_parent()->get_change_active_url($type, $event_id, $tracker_id);
    }

    /**
     * Retrieves the event viewer url
     * @see TrackingManager :: get_event_viewer_url()
     */
    function get_event_viewer_url($event)
    {
        return $this->get_parent()->get_event_viewer_url($event);
    }

    /**
     * Retrieves the empty tracker url
     * @see TrackingManager :: get_empty_tracker_url()
     */
    function get_empty_tracker_url($type, $event_id, $tracker_id = null)
    {
        return $this->get_parent()->get_empty_tracker_url($type, $event_id, $tracker_id);
    }

    /**
     * Retrieves the platform administration link
     */
    function get_platform_administration_link()
    {
        return $this->get_parent()->get_platform_administration_link();
    }

    /**
     * Retrieves the events
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param String $order_property
     * @see TrackingManager :: retrieve_events();
     */
    function retrieve_events($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_events($condition, $offset, $count, $order_property);
    }

    /**
     * Count the events from a given condition
     * @param Condition $conditions
     */
    function count_events($conditions = null)
    {
        return $this->get_parent()->count_events($conditions);
    }

    /**
     * Retrieves the trackers from a given event
     * @see TrackingManager :: retrieve_trackers_from_event
     */
    function retrieve_trackers_from_event($event_id)
    {
        return $this->get_parent()->retrieve_trackers_from_event($event_id);
    }

    /**
     * Retrieves an event by the given id
     * @param int $event_id
     * @return Event event
     */
    function retrieve_event($event_id)
    {
        return $this->get_parent()->retrieve_event($event_id);
    }

    /**
     * Retrieves the event tracker relation by given id's
     * @param int $event_id the event id
     * @param int $tracker_id the tracker id
     * @return EventTrackerRelation
     * @see TrackingManager :: retrieve_event_tracker_relation
     */
    function retrieve_event_tracker_relation($event_id, $tracker_id)
    {
        return $this->get_parent()->retrieve_event_tracker_relation($event_id, $tracker_id);
    }

    /**
     * Retrieves the tracker for the given id
     * @param int $tracker_id the given tracker id
     * @return TrackerRegistration the tracker registration
     */
    function retrieve_tracker_registration($tracker_id)
    {
        return $this->get_parent()->retrieve_tracker_registration($tracker_id);
    }

    /**
     * Retrieves an event by name
     * @param string $eventname
     * @return Event event
     * @see TrackingManager :: retrieve_event_by_name
     */
    function retrieve_event_by_name($eventname)
    {
        return $this->get_parent()->retrieve_event_by_name($eventname);
    }

}
?>