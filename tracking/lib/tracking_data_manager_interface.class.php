<?php
/**
 * @package tracking.lib
 *
 * This is an interface for a data manager for the Tracking application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface TrackingDataManagerInterface
{

    function initialize();

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    function create_storage_unit($name, $properties, $indexes);

    /**
     * Creates an event in the database
     * @param Event $event
     */
    function create_event($event);

    /**
     * Creates a tracker in the database
     * @param Tracker $tracker
     */
    function create_tracker_registration($tracker);

    /**
     * Registers a tracker to an event
     * @param EventTrackerRelation $eventtrackerrelation
     */
    function create_event_tracker_relation($eventtrackerrelation);

    /**
     * Updates an event (needed for change of activity)
     * @param Event $event
     */
    function update_event($event);

    /**
     * Updates an event tracker relation (needed for change of activity)
     * @param EventTrackerRelation $eventtrackerrelation
     */
    function update_event_tracker_relation($eventtrackerrelation);

    /**
     * Retrieves the event with the given name
     * @param String $name
     */
    function retrieve_event_by_name($eventname, $block = null);

    /**
     * Retrieve all trackers from an event
     * @param Int $eventid
     * @param Bool $active true if only the active ones should be shown (default true)
     */
    function retrieve_trackers_from_event($event_id, $active = true);

    /**
     * Retrieves an event tracker relation by given id
     * @param int $event_id the event id
     * @param int $tracker_id the tracker id
     * @return EventTrackerRelation that belongs to the given id
     */
    function retrieve_event_tracker_relation($event_id, $tracker_id);

    /**
     * Retrieves a tracker registration by the given id
     * @param int $trackerid the tracker id
     * @param bool $active, extra value used for views in tables
     * @return Tracker Registration
     */
    function retrieve_tracker_registration($trackerid, $active);

    /**
     * Retrieves all events
     */
    function retrieve_events($condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Counts the events for a given condition
     * @param Condition $condition
     */
    function count_events($condition = null);

    /**
     * Retrieves an event by given id
     * @param int $event_id
     * @return Event $event
     */
    function retrieve_event($event_id);

    /* Tracker specific methods */
    /**
     * Creates a tracker item in the database
     * @param string $tablename the table name where the database has to be written to
     * @param MainTracker $tracker_item a subclass of MainTracker
     * @return true if creation is valid
     */
    function create_tracker_item($tablename, $tracker_item);

    /**
     * Retrieves all tracker items from the database
     * @param string $tablename the table name where the database has to be written to
     * @param string $classname the tracker's class name (needed to create the class when data is retrieved)
     * @param Condition $condition the condition applied to the retrieval
     * @return MainTracker $tracker a subclass of MainTracker
     */
    function retrieve_tracker_items($tablename, $classname, $condition);

    function retrieve_tracker_items_result_set($table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array(), $class_name = null);

    /**
     * Retrieves a tracker item from the database
     * @param string $tablename the table name where the database has to be written to
     * @param int $id the id of the tracker item
     * @param string $classname the tracker's class name (needed to create the class when data is retrieved)
     * @return MainTracker $tracker a subclass of MainTracker
     */
    function retrieve_tracker_item($tablename, $classname, $id);

    /**
     * Updates a tracker item in the database
     * @param string $tablename the table name where the database has to be written to
     * @param MainTracker $tracker_item a subclass of MainTracker
     * @return true if update is valid
     */
    function update_tracker_item($tablename, $tracker_item);

    /**
     * Deletes tracker items in the database
     * @param Condition conditon which items should be removed
     * @return true if tracker items are removed
     */
    function remove_tracker_items($tablename, $condition);

    /**
     * Creates a archive controller item in the database
     * @param ArchiveControllerItem
     * @return true if creation is valid
     */
    function create_archive_controller_item($archive_controller_item);

    function delete_events($condition = null);

    function delete_tracker_registrations($condition = null);

    function delete_orphaned_event_rel_tracker();

}
?>