<?php
/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerActiveChangerComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
		$active = Request :: get(UserManager :: PARAM_ACTIVE);

        if(!is_array($ids))
        {
        	$ids = array($ids);
        }

        if (count($ids) > 0)
        {
        	$failures = 0;

			foreach($ids as $id)
			{
	            if(!UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $id))
	            {
	            	$failures++;
	            	continue;	
	            }
	            
				$user = $this->retrieve_user($id);
	            $user->set_active($active);

	            if ($user->update())
	            {
	                Event :: trigger('update', UserManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $user->get_id(), ChangesTracker :: PROPERTY_USER_ID => $this->get_user()->get_id()));
	            }
	            else
	            {
	            	$failures++;
	            }
			}

			if($active == 0)
				$message = $this->get_result($failures, count($ids), 'UserNotDeactivated' , 'UsersNotDeactivated', 'UserDeactivated', 'UsersDeactivated');
			else
				$message = $this->get_result($failures, count($ids), 'UserNotActivated' , 'UsersNotActivated', 'UserActivated', 'UsersActivated');

            $this->redirect($message, ($failures > 0), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));

        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_USER_APPROVAL_BROWSER)), Translation :: get('UserManagerUserApprovalBrowserComponent')));
    	$breadcrumbtrail->add_help('user_active_changer');
    }
    
    function get_additional_parameters()
    {
    	return array(UserManager :: PARAM_USER_USER_ID, UserManager :: PARAM_ACTIVE);
    }
}
?>