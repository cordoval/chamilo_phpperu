<?php
/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerDeleterComponent extends UserManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
        
	    if (! $this->get_user()->is_platform_admin())
        {
            $trail = new BreadcrumbTrail();
            $trail->add_help('user general');
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
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
	            
	            if (!UserDataManager :: get_instance()->user_deletion_allowed($user))
	            {
	                continue;
	            }
	            
	            if ($user->delete())
	            {
	                Events :: trigger_event('delete', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
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