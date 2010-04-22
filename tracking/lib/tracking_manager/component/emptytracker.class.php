<?php
/**
 * $Id: emptytracker.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Component to empty a tracker
 */
class TrackingManagerEmptyTrackerComponent extends TrackingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('tracking general');
        
        $tracker_ids = Request :: get(TrackingManager :: PARAM_TRACKER_ID);
        $event_ids = Request :: get(TrackingManager :: PARAM_EVENT_ID);
        $type = Request :: get(TrackingManager :: PARAM_TYPE);
        
        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if (($type == 'event' && $event_ids) || ($type == 'tracker' && $event_ids && $tracker_ids) || ($type == 'all'))
        {
            switch ($type)
            {
                case 'event' :
                    $this->empty_events($event_ids);
                    break;
                case 'tracker' :
                    $this->empty_trackers($event_ids, $tracker_ids);
                    break;
                case 'all' :
                    $this->empty_all_events();
                    break;
            }
        }
        else
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get("NoObjectSelected"));
            $this->display_footer();
        }
    }

    /**
     * Empty the chosen trackers for a given event
     * @param int $event_id the chosen event
     * @param int array $tracker_ids array of chosen trackers
     */
    function empty_trackers($event_id, $tracker_ids)
    {
        if (! is_array($tracker_ids))
        {
            $tracker_ids = array($tracker_ids);
        }
        
        $event = $this->retrieve_event($event_id);
        
        $success = true;
        
        foreach ($tracker_ids as $tracker_id)
        {
            $trackerregistration = $this->retrieve_tracker_registration($tracker_id);
            
            $classname = $trackerregistration->get_class();
            $filename = Utilities :: camelcase_to_underscores($classname);
            
            $fullpath = Path :: get(SYS_PATH) . $trackerregistration->get_path() . strtolower($filename) . '.class.php';
            require_once ($fullpath);
            
            $tracker = new $classname();
            if (! $tracker->empty_tracker($event))
                $success = false;
        
        }
        
        $this->redirect(Translation :: get($success ? 'TrackerEmpty' : 'TrackerNotEmpty'), ($success ? false : true), array(Application :: PARAM_ACTION => TrackingManager :: ACTION_VIEW_EVENT, TrackingManager :: PARAM_EVENT_ID => $event_id));
    }

    /**
     * Empty the chosen trackers for a given events
     * @param int array $event_ids the chosen events
     */
    function empty_events($event_ids)
    {
        if (! is_array($event_ids))
        {
            $event_ids = array($event_ids);
        }
        
        $success = true;
        
        foreach ($event_ids as $event_id)
        {
            $event = $this->retrieve_event($event_id);
            if (! $this->empty_trackers_for_event($event))
                $success = false;
        }
        
        $this->redirect(Translation :: get($success ? 'TrackerEmpty' : 'TrackerNotEmpty'), ($success ? false : true), array(Application :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS));
    }

    /**
     * auxiliary function for to clear all trackers for an event
     * @param Event $event
     */
    function empty_trackers_for_event($event)
    {
        $trackerregistrations = $this->retrieve_trackers_from_event($event->get_id());
        
        foreach ($trackerregistrations as $trackerregistration)
        {
            $classname = $trackerregistration->get_class();
            $filename = Utilities :: camelcase_to_underscores($classname);
            
            $fullpath = Path :: get(SYS_PATH) . $trackerregistration->get_path() . strtolower($filename) . '.class.php';
            require_once ($fullpath);
            
            $tracker = new $classname();
            if (! $tracker->empty_tracker($event))
                return false;
        
        }
        
        return true;
    }

    /**
     * Empty all events
     */
    function empty_all_events()
    {
        $events = $this->retrieve_events();
        $success = true;
        
        foreach ($events as $event)
        {
            if (! $this->empty_trackers_for_event($event))
                $success = $false;
            
            $this->redirect(Translation :: get($success ? 'TrackerEmpty' : 'TrackerNotEmpty'), ($success ? false : true), array(Application :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS));
        }
    }

}
?>