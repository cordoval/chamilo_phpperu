<?php
/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerEmailerComponent extends UserManager implements AdministrationComponent
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

			$manager = EmailManager :: construct($this);
			$manager->set_target_users($users);
			$manager->set_parameter(UserManager :: PARAM_USER_USER_ID, $ids);
			$manager->run();

        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
    	$breadcrumbtrail->add_help('user_emailer');
    }
    
    function get_additional_parameters()
    {
    	return array(UserManager :: PARAM_USER_USER_ID);
    }
}
?>