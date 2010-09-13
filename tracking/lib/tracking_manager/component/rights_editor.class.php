<?php
/**
 * $Id: rights_editor.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class TrackingManagerRightsEditorComponent extends TrackingManager implements AdministrationComponent, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$events = Request :: get(TrackingManager :: PARAM_EVENT_ID);

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
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browser_url(), Translation :: get('TrackingManagerAdminEventBrowserComponent')));
    	$breadcrumbtrail->add_help('tracking_event_viewer');
    }
    
    function get_additional_parameters()
    {
    	return array(TrackingManager :: PARAM_EVENT_ID);
    }

}
?>