<?php
/**
 * $Id: tracking_data_manager.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib
 */

/**
 *	This is a skeleton for a data manager for tracking manager
 *	Data managers must extend this class and implement its abstract methods.
 *	If the user configuration dictates that the "database" data manager is to
 *	be used, this class will automatically attempt to instantiate
 *	"DatabaseTrackingDataManager"; hence, this naming convention must be
 *	respected for all extensions of this class.
 *
 *	@author Sven Vanpoucke
 */
abstract class TrackingDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function TrackingDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return TrackingDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'TrackingDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    abstract function create_storage_unit($name, $properties, $indexes);

    /**
     * Creates an event in the database
     * @param Event $event
     */
    abstract function create_event($event);

    /**
     * Creates a tracker in the database
     * @param Tracker $tracker
     */
    abstract function create_tracker_registration($tracker);

    /**
     * Registers a tracker to an event
     * @param EventTrackerRelation $eventtrackerrelation
     */
    abstract function create_event_tracker_relation($eventtrackerrelation);

    /**
     * Updates an event (needed for change of activity)
     * @param Event $event
     */
    abstract function update_event($event);

    /**
     * Updates an event tracker relation (needed for change of activity)
     * @param EventTrackerRelation $eventtrackerrelation
     */
    abstract function update_event_tracker_relation($eventtrackerrelation);

    /**
     * Retrieves the event with the given name
     * @param String $name
     */
    abstract function retrieve_event_by_name($eventname, $block = null);

    /**
     * Retrieve all trackers from an event
     * @param Int $eventid
     * @param Bool $active true if only the active ones should be shown (default true)
     */
    abstract function retrieve_trackers_from_event($event_id, $active = true);

    /**
     * Retrieves an event tracker relation by given id
     * @param int $event_id the event id
     * @param int $tracker_id the tracker id
     * @return EventTrackerRelation that belongs to the given id
     */
    abstract function retrieve_event_tracker_relation($event_id, $tracker_id);

    /**
     * Retrieves a tracker registration by the given id
     * @param int $trackerid the tracker id
     * @param bool $active, extra value used for views in tables
     * @return Tracker Registration
     */
    abstract function retrieve_tracker_registration($trackerid, $active);

    /**
     * Retrieves all events
     */
    abstract function retrieve_events($condition = null, $offset = null, $count = null, $order_property = null);

    /**
     * Counts the events for a given condition
     * @param Condition $condition
     */
    abstract function count_events($condition = null);

    /**
     * Retrieves an event by given id
     * @param int $event_id
     * @return Event $event
     */
    abstract function retrieve_event($event_id);

    /* Tracker specific methods */
    /**
     * Creates a tracker item in the database
     * @param string $tablename the table name where the database has to be written to
     * @param MainTracker $tracker_item a subclass of MainTracker
     * @return true if creation is valid
     */
    abstract function create_tracker_item($tablename, $tracker_item);

    /**
     * Retrieves all tracker items from the database
     * @param string $tablename the table name where the database has to be written to
     * @param string $classname the tracker's class name (needed to create the class when data is retrieved)
     * @param Condition $condition the condition applied to the retrieval
     * @return MainTracker $tracker a subclass of MainTracker
     */
    abstract function retrieve_tracker_items($tablename, $classname, $condition);

    abstract function retrieve_tracker_items_result_set($tablename, $classname, $condition, $order_by);

    /**
     * Retrieves a tracker item from the database
     * @param string $tablename the table name where the database has to be written to
     * @param int $id the id of the tracker item
     * @param string $classname the tracker's class name (needed to create the class when data is retrieved)
     * @return MainTracker $tracker a subclass of MainTracker
     */
    abstract function retrieve_tracker_item($tablename, $classname, $id);

    /**
     * Updates a tracker item in the database
     * @param string $tablename the table name where the database has to be written to
     * @param MainTracker $tracker_item a subclass of MainTracker
     * @return true if update is valid
     */
    abstract function update_tracker_item($tablename, $tracker_item);

    /**
     * Deletes tracker items in the database
     * @param Condition conditon which items should be removed
     * @return true if tracker items are removed
     */
    abstract function remove_tracker_items($tablename, $condition);

    /**
     * Creates a archive controller item in the database
     * @param ArchiveControllerItem
     * @return true if creation is valid
     */
    abstract function create_archive_controller_item($archive_controller_item);

    abstract function delete_events($condition = null);

    abstract function delete_tracker_registrations($condition = null);

    abstract function delete_orphaned_event_rel_tracker();
}
?>