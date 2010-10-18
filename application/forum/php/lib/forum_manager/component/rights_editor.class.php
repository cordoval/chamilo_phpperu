<?php
/**
 * $Id: rights_editor.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class ForumManagerRightsEditorComponent extends ForumManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category = Request :: get(self :: PARAM_CATEGORY);

        if ($this->get_user()->is_platform_admin())
        {
        	if($category == 0)
        	{
        		$locations[] = ForumRights :: get_forums_subtree_root();
        	}
        	else
        	{
        		$locations[] = ForumRights :: get_location_by_identifier_from_forums_subtree($category);
        	}
        }
        
        $manager = new RightsEditorManager($this, $locations);
	    $manager->exclude_users(array($this->get_user_id()));
    	$manager->run();
    }
    
    function get_available_rights()
    {
    	return ForumRights :: get_available_rights_for_categories();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('ForumManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('forum_rights_editor');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_CATEGORY);
    }

}
?>