<?php
namespace tracking;

use common\libraries\Path;
use common\libraries\Application;
use common\libraries\CoreApplication;
use common\libraries\Redirect;
use common\libraries\Translation;
use common\libraries\DynamicAction;
use common\libraries\Theme;

/**
 * $Id: tracking_manager.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager
 */

/**
 * A tracking manager provides some functionalities to the admin to manage
 * his trackers and events. For each functionality a component is available.
 */
class TrackingManager extends CoreApplication
{
    const APPLICATION_NAME = 'tracking';

    const PARAM_EVENT_ID = 'event_id';
    const PARAM_TRACKER_ID = 'track_id';
    const PARAM_REF_ID = 'ref_id';
    const PARAM_TYPE = 'type';
    const PARAM_EXTRA = 'extra';

    const ACTION_BROWSE_EVENTS = 'admin_event_browser';
    const ACTION_VIEW_EVENT = 'admin_event_viewer';
    const ACTION_CHANGE_ACTIVE = 'activity_changer';
    const ACTION_ACTIVATE_EVENT = 'event_activator';
    const ACTION_DEACTIVATE_EVENT = 'event_deactivator';
    const ACTION_EMPTY_TRACKER = 'empty_tracker';
    const ACTION_EMPTY_EVENT_TRACKERS = 'empty_event_tracker';
    const ACTION_ARCHIVE = 'archiver';
    const ACTION_MANAGE_RIGHTS = 'rights_editor';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_EVENTS;

    private $tdm;

    /**
     * Constructor
     * @param User $user The active user
     */
    function __construct($user)
    {
        parent :: __construct($user);
        //$this->tdm = TrackingDataManager :: get_instance();
    }

    private function get_tracking_data_manager()
    {
        if($this->tdm == null)
        {
            $this->tdm = $this->tdm = TrackingDataManager :: get_instance();
        }
        return $this->tdm;
    }

    public function set_tracking_data_manager(TrackingDataManager $tdm)
    {
        $this->tdm = $tdm;
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Method used by the administrator module to get the application links
     */
    public static function get_application_platform_admin_links($application = self :: APPLICATION_NAME)
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('List'), Translation :: get('ListDescription'), Theme :: get_image_path() . 'admin/list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_EVENTS), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('Archive'), Translation :: get('ArchiveDescription'), Theme :: get_image_path() . 'admin/archive.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_ARCHIVE), array(), false, Redirect :: TYPE_CORE));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;

        return $info;
    }

    /**
     * Gets the url for the event browser
     * @return String URL for event browser
     */
    function get_browser_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_EVENTS));
    }

    /**
     * Retrieves the change active url
     * @param string $type event or tracker
     * @param Object $object Event or Tracker Object
     * @return the change active component url
     */
    function get_change_active_url($type, $event_id, $tracker_id = null)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_ACTIVE;
        $parameters[self :: PARAM_TYPE] = $type;
        $parameters[self :: PARAM_EVENT_ID] = $event_id;
        if ($tracker_id)
            $parameters[self :: PARAM_TRACKER_ID] = $tracker_id;

        return $this->get_url($parameters);
    }

    /**
     * Retrieves the event viewer url
     * @param Event $event
     * @return the event viewer url for the given event
     */
    function get_event_viewer_url($event)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_EVENT, self :: PARAM_EVENT_ID => $event->get_id()));
    }

    /**
     * Retrieves the empty tracker url
     * @see TrackingManager :: get_empty_tracker_url()
     */
    function get_empty_tracker_url($type, $event_id, $tracker_id = null)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EMPTY_TRACKER, self :: PARAM_EVENT_ID => $event_id, self :: PARAM_TRACKER_ID => $tracker_id, self :: PARAM_TYPE => $type));
    }

    /**
     * Retrieves the platform administration link
     */
    function get_platform_administration_link()
    {
        return Path :: get(WEB_PATH) . 'index_admin.php';
    }

    /**
     * Retrieves the events
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param String $order_property
     */
    function retrieve_events($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this
            ->get_tracking_data_manager()
            ->retrieve_events($condition, $offset, $count, $order_property);
    }

    /**
     * Count the events from a given condition
     * @param Condition $conditions
     */
    function count_events($conditions = null)
    {
        return $this
            ->get_tracking_data_manager()
            ->count_events($conditions);
    }

    /**
     * Retrieves an event by the given id
     * @param int $event_id
     * @return Event event
     */
    function retrieve_event($event_id)
    {
        return $this
            ->get_tracking_data_manager()
            ->retrieve_event($event_id);
    }

    /**
     * Retrieves the trackers from a given event
     * @param int $event_id the event id
     * @return array of trackers
     */
    function retrieve_trackers_from_event($event_id)
    {
        return $this
            ->get_tracking_data_manager()
            ->retrieve_trackers_from_event($event_id, false);
    }

    /**
     * Retrieves the event tracker relation by given id's
     * @param int $event_id the event id
     * @param int $tracker_id the tracker id
     * @return EventTrackerRelation
     */
    function retrieve_event_tracker_relation($event_id, $tracker_id)
    {
        return $this
            ->get_tracking_data_manager()
            ->retrieve_event_tracker_relation($event_id, $tracker_id);
    }

    /**
     * Retrieves the tracker for the given id
     * @param int $tracker_id the given tracker id
     * @return TrackerRegistration the tracker registration
     */
    function retrieve_tracker_registration($tracker_id)
    {
        return $this
            ->get_tracking_data_manager()
            ->retrieve_tracker_registration($tracker_id);
    }

    /**
     * Retrieves an event by name
     * @param string $eventname
     * @return Event event
     */
    function retrieve_event_by_name($eventname)
    {
        return $this
            ->get_tracking_data_manager()
            ->retrieve_event_by_name($eventname);
    }

    function get_manage_rights_url($event_id)
    {
        return $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_MANAGE_RIGHTS,
                    self :: PARAM_EVENT_ID => $event_id
                )
        );
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}