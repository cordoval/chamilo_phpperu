<?php
/**
 * $Id: activity_changer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component
 */


/**
 * Component for change of activity
 */
class TrackingManagerActivityChangerComponent extends TrackingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('tracking general');
        
        $tracker_ids = Request :: get(TrackingManager :: PARAM_TRACKER_ID);
        $type = Request :: get(TrackingManager :: PARAM_TYPE);
        $event_ids = Request :: get(TrackingManager :: PARAM_EVENT_ID);
        
        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if (($type == 'event' && $event_ids) || ($type == 'tracker' && $event_ids && $tracker_ids) || ($type == 'all'))
        {
            switch ($type)
            {
                case 'event' :
                    $this->change_event_activity($event_ids);
                    break;
                case 'tracker' :
                    $this->change_tracker_activity($event_ids, $tracker_ids);
                    break;
                case 'all' :
                    $this->change_tracking_activity();
                    break;
            }
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get("NoObjectSelected"));
            $this->display_footer();
        }
    }

    /**
     * Function to change the activity of events
     * @param Array of event ids
     */
    function change_event_activity($event_ids)
    {
        if ($event_ids)
        {
            if (! is_array($event_ids))
            {
                $event_ids = array($event_ids);
            }
            
            $success = true;
            
            foreach ($event_ids as $event_id)
            {
                $event = $this->retrieve_event($event_id);
                if (Request :: get('extra'))
                {
                    $event->set_active(Request :: get('extra') == 'enable' ? 1 : 0);
                }
                else
                    $event->set_active(! $event->get_active());
                
                if (! $event->update())
                    $success = false;
            }
            
            $this->redirect(Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS));
        }
    }

    /**
     * Function to change the activity of trackers
     * @param int event_id the event_id
     * @param array of tracker ids
     */
    function change_tracker_activity($event_id, $tracker_ids)
    {
        if ($tracker_ids)
        {
            if (! is_array($tracker_ids))
            {
                $tracker_ids = array($tracker_ids);
            }
            
            $success = true;
            
            foreach ($tracker_ids as $tracker_id)
            {
                $relation = $this->retrieve_event_tracker_relation($event_id, $tracker_id);
                
                if (Request :: get('extra'))
                {
                    $relation->set_active(Request :: get('extra') == 'enable' ? 1 : 0);
                }
                else
                    $relation->set_active(! $relation->get_active());
                
                if (! $relation->update())
                    $success = false;
            }
            
            $this->redirect(Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => TrackingManager :: ACTION_VIEW_EVENT, TrackingManager :: PARAM_EVENT_ID => $event_id));
        
        }
    }

    /**
     * Enables / Disables all events and trackers
     */
    function change_tracking_activity()
    {
        $adm = AdminDataManager :: get_instance();
        $setting = $adm->retrieve_setting_from_variable_name('enable_tracking', 'tracking');
        $setting->set_value($setting->get_value() == 1 ? 0 : 1);
        $success = $setting->update();
        
        $this->redirect(Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS));
    }

}
?>