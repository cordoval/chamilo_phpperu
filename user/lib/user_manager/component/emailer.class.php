<?php
/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerEmailerComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
        
        if(!is_array($ids))
        {
        	$ids = array($ids);
        } 
        
        if (count($ids) > 0)
        {
        	$failures = 0;
        	
			foreach($ids as $id)
			{
	            if(UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $id))
	            {
					$users[] = $this->retrieve_user($id);
	            }
			}
			
			$manager = new EmailManager($this, $users);
			$manager->set_parameter(UserManager :: PARAM_USER_USER_ID, $ids);
			$manager->run();
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
    function display_header($trail)
    {
    	$ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
        
    	$this->set_parameter(UserManager :: PARAM_USER_USER_ID, null);
    	
    	$trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => UserManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Users') ));
        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_USER_USER_ID => $ids)), Translation :: get('Emailer')));
        $trail->add_help('user general');
        
        return parent :: display_header();
    }
}
?>