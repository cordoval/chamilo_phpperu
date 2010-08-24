<?php
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
    
    const ACTION_BROWSE_EVENTS = 'browse_events';
    const ACTION_VIEW_EVENT = 'view_event';
    const ACTION_CHANGE_ACTIVE = 'changeactive';
    const ACTION_ACTIVATE_EVENT = 'activate_event';
    const ACTION_DEACTIVATE_EVENT = 'deactivate_event';
    const ACTION_EMPTY_TRACKER = 'empty_tracker';
    const ACTION_EMPTY_EVENT_TRACKERS = 'empty_event_trackers';
    const ACTION_ARCHIVE = 'archive';
    const ACTION_MANAGE_RIGHTS = 'manage_rights';
    
    private $tdm;

    /**
     * Constructor
     * @param User $user The active user
     */
    function TrackingManager($user)
    {
        parent :: __construct($user);
        $this->tdm = TrackingDataManager :: get_instance();
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Run this tracking manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_EVENTS :
                $component = $this->create_component('AdminEventBrowser');
                break;
            case self :: ACTION_VIEW_EVENT :
                $component = $this->create_component('AdminEventViewer');
                break;
            case self :: ACTION_CHANGE_ACTIVE :
                $component = $this->create_component('ActivityChanger');
                break;
            case self :: ACTION_ACTIVATE_EVENT :
                $component = $this->create_component('ActivityChanger');
                Request :: set_get(self :: PARAM_TYPE, 'event');
                Request :: set_get(self :: PARAM_EXTRA, 'enable');
                break;
            case self :: ACTION_DEACTIVATE_EVENT :
                $component = $this->create_component('ActivityChanger');
                Request :: set_get(self :: PARAM_TYPE, 'event');
                Request :: set_get(self :: PARAM_EXTRA, 'disable');
                break;
            case self :: ACTION_EMPTY_TRACKER :
                $component = $this->create_component('EmptyTracker');
                break;
            case self :: ACTION_EMPTY_EVENT_TRACKERS :
                $component = $this->create_component('EmptyTracker');
                Request :: set_get(self :: PARAM_TYPE, 'event');
                break;
            case self :: ACTION_ARCHIVE :
                $component = $this->create_component('Archiver');
                break;
            case self :: ACTION_MANAGE_RIGHTS:
            	$component = $this->create_component('RightsEditor');
                break;
            default :
                $component = $this->create_component('AdminEventBrowser');
                break;
        }
        
        if ($component)
            $component->run();
    }

    /**
     * Method used by the administrator module to get the application links
     */
    public function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('List'), Translation :: get('ListDescription'), Theme :: get_image_path() . 'browse_list.png', $this->get_link(array(Application :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS)));
        $links[] = new DynamicAction(Translation :: get('Archive'), Translation :: get('ArchiveDescription'), Theme :: get_image_path() . 'browse_archive.png', $this->get_link(array(Application :: PARAM_ACTION => TrackingManager :: ACTION_ARCHIVE)));
                
        $info = parent :: get_application_platform_admin_links();
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
        return $this->tdm->retrieve_events($condition, $offset, $count, $order_property);
    }

    /**
     * Count the events from a given condition
     * @param Condition $conditions
     */
    function count_events($conditions = null)
    {
        return $this->tdm->count_events($conditions);
    }

    /**
     * Retrieves an event by the given id
     * @param int $event_id
     * @return Event event
     */
    function retrieve_event($event_id)
    {
        return $this->tdm->retrieve_event($event_id);
    }

    /**
     * Retrieves the trackers from a given event
     * @param int $event_id the event id
     * @return array of trackers
     */
    function retrieve_trackers_from_event($event_id)
    {
        return $this->tdm->retrieve_trackers_from_event($event_id, false);
    }

    /**
     * Retrieves the event tracker relation by given id's
     * @param int $event_id the event id
     * @param int $tracker_id the tracker id
     * @return EventTrackerRelation
     */
    function retrieve_event_tracker_relation($event_id, $tracker_id)
    {
        return $this->tdm->retrieve_event_tracker_relation($event_id, $tracker_id);
    }

    /**
     * Retrieves the tracker for the given id
     * @param int $tracker_id the given tracker id
     * @return TrackerRegistration the tracker registration
     */
    function retrieve_tracker_registration($tracker_id)
    {
        return $this->tdm->retrieve_tracker_registration($tracker_id);
    }

    /**
     * Retrieves an event by name
     * @param string $eventname
     * @return Event event
     */
    function retrieve_event_by_name($eventname)
    {
        return $this->tdm->retrieve_event_by_name($eventname);
    }
    
    function get_manage_rights_url($event_id)
    {
    	return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_RIGHTS, self :: PARAM_EVENT_ID => $event_id));
    }

}
?>