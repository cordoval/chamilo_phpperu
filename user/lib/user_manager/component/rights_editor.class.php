<?php
/**
 * $Id: rights_editor.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class UserManagerRightsEditorComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => UserManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Users') ));
        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
        
    	$user_ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
        $this->set_parameter(UserManager :: PARAM_USER_USER_ID, $user_ids);

        if (! is_array($user_ids))
        {
            $user_ids = array($user_ids);
        }

        $locations = array();

        foreach ($user_ids as $user_id)
        {
        	if (UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $user_id))
        	{ 
        		$locations[] = UserRights :: get_location_by_identifier_from_users_subtree($user_id);
        	}
        }

        if(count($locations) == 0)
        {
        	if (UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, 0))
        	{
        		$locations = UserRights :: get_users_subtree_root();
        	}
        }
        
        $manager = new RightsEditorManager($this, $locations);
	    $manager->exclude_users(array($this->get_user_id()));
    	$manager->run();
    }
    
    function get_available_rights()
    {
    	return UserRights :: get_available_rights();
    }

}
?>