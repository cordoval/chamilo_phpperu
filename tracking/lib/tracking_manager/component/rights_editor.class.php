<?php
/**
 * $Id: rights_editor.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class TrackingManagerRightsEditorComponent extends TrackingManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => TrackingManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Tracking')));
        $trail->add(new Breadcrumb($this->get_url(array(TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS)), Translation :: get('EventsList')));
        
    	$events = Request :: get(TrackingManager :: PARAM_EVENT_ID);
        $this->set_parameter(TrackingManager :: PARAM_EVENT_ID, $events);

        if ($events && ! is_array($events))
        {
            $events = array($events);
        }

        $locations = array();

        foreach ($events as $event)
        {
        	if (TrackingRights :: is_allowed_in_tracking_subtree(TrackingRights :: EDIT_RIGHT, $event))
        	{ 
        		$locations[] = TrackingRights :: get_location_by_identifier_from_tracking_subtree($event);
        	}
        }

        if(count($locations) == 0)
        {
        	if (TrackingRights :: is_allowed_in_tracking_subtree(TrackingRights :: EDIT_RIGHT, 0))
        	{
        		$locations[] = TrackingRights :: get_tracking_subtree_root();
        	}
        }
        
        $manager = new RightsEditorManager($this, $locations);
	    $manager->exclude_users(array($this->get_user_id()));
    	$manager->run();
    }
    
    function get_available_rights()
    {
    	return TrackingRights :: get_available_rights();
    }

}
?>