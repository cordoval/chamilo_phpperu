<?php
/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerDeleterComponent extends UserManager
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
	            $user = $this->retrieve_user($id);
	            
				if( !UserRights :: is_allowed_in_users_subtree(UserRights :: DELETE_RIGHT, $id) || !UserDataManager :: user_deletion_allowed($user) )
	            {
	                $failures++;
	            	continue;
	            }
	            
	            if ($user->delete())
	            {
	                Event :: trigger('delete', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
	            }
	            else
	            {
	            	$failures++;
	            }
			}
            
			$message = $this->get_result($failures, count($ids), 'UserNotDeleted' , 'UsersNotDeleted', 'UserDeleted', 'UsersDeleted');
			
            $this->redirect($message, ($failures > 0), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>