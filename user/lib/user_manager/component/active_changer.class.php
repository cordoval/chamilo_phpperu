<?php
/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerActiveChangerComponent extends UserManager
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
}
?>