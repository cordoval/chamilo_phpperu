<?php
/**
 * $Id: rights_editor.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class WebserviceManagerRightsEditorComponent extends WebserviceManager implements AdministrationComponent, DelegateComponent
{
	function run()
	{
		$webservices = Request :: get(WebserviceManager :: PARAM_WEBSERVICE_ID);
		$category = Request :: get(WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID);
		$this->set_parameter(WebserviceManager :: PARAM_WEBSERVICE_ID, $webservices);
		$this->set_parameter(WebserviceManager :: PARAM_WEBSERVICE_CATEGORY_ID, $category);

        if ($webservices && ! is_array($webservices))
        {
            $webservices = array($webservices);
        }

        $locations = array();

        foreach ($webservices as $webservice)
        {
        	$locations[] = WebserviceRights :: get_location_by_identifier_from_webservices_subtree(WebserviceRights :: TYPE_WEBSERVICE, $webservice);
        }

        if(count($locations) == 0)
        {
        	if ($category)
        	{
        		$locations[] = WebserviceRights :: get_location_by_identifier_from_webservices_subtree(WebserviceRights :: TYPE_WEBSERVICE_CATEGORY, $category);
        	}
        	else
        	{
        		$locations[] = WebserviceRights :: get_webservices_subtree_root();
        	}
        }
        
        $manager = new RightsEditorManager($this, $locations);
	    $manager->exclude_users(array($this->get_user_id()));
    	$manager->run();
    }
    
    function get_available_rights()
    {
    	return WebserviceRights :: get_available_rights();
    }
	
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WebserviceManager :: PARAM_ACTION => WebserviceManager :: ACTION_BROWSE_WEBSERVICES)), Translation :: get('WebserviceManagerWebserviceBrowserComponent')));
    	$breadcrumbtrail->add_help('webservice_rights_editor');
    }
    
}
?>