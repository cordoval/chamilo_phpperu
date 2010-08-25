<?php
/**
 * $Id: admin_event_viewer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component
 */
require_once dirname(__FILE__) . '/admin_event_viewer/admin_event_viewer_cell_renderer.class.php';
require_once dirname(__FILE__) . '/admin_event_viewer/admin_event_viewer_action_handler.class.php';

/**
 * Component for viewing tracker events
 */
class TrackingManagerAdminEventViewerComponent extends TrackingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => TrackingManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Tracking')));
        $trail->add(new Breadcrumb($this->get_browser_url(), Translation :: get('EventsList')));
        $trail->add(new Breadcrumb($this->get_url(array(TrackingManager :: PARAM_EVENT_ID => Request :: get('event_id'))), Translation :: get('ViewEvent')));
        $trail->add_help('tracking general');
        
        $event_id = Request :: get(TrackingManager :: PARAM_EVENT_ID);
        
        if (!TrackingRights :: is_allowed_in_tracking_subtree(TrackingRights :: VIEW_RIGHT, $event_id))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $event = $this->retrieve_event($event_id);
        
        $cellrenderer = new AdminEventViewerCellRenderer($this, $event);
        $actionhandler = new AdminEventViewerActionHandler($this, $event);
        
        $trackers = $this->retrieve_trackers_from_event($event_id);
        $trackertable = new SimpleTable($trackers, $cellrenderer, $actionhandler, "trackertable");
        
        $this->display_header();
        
        echo Translation :: get('You_are_viewing_trackers_for_event') . ': ' . $event->get_name() . '<br /><br />';
        
        echo $trackertable->toHTML();
        
        $this->display_footer();
    }
    
}
?>