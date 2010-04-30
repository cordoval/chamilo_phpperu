<?php
/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerUserApproverComponent extends UserManager
{
	const PARAM_CHOICE = 'choice';
	const CHOICE_APPROVE = 1;
	const CHOICE_DENY = 0;
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
        $choice = Request :: get(self :: PARAM_CHOICE);
    	if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
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
	            
	            if($choice == self :: CHOICE_APPROVE)
	            {
	            	$user->set_active(1);
	            	$user->set_approved(1);
	            	
	            	if($user->update())
	            	{
	            		Events :: trigger_event('update', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
	            	}
	            	else
	            	{
	            		$failures++;
	            	}
	            }
	            else
	            {
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
			}
            
			if($choice == self :: CHOICE_APPROVE)
			{
				$message = $this->get_result($failures, count($ids), 'UserNotApproved' , 'UsersNotApproved', 'UserApproved', 'UsersApproved');
			}
			else
			{
				$message = $this->get_result($failures, count($ids), 'UserNotDenied' , 'UsersNotDenied', 'UserDenied', 'UsersDenied');
			}
			
            $this->redirect($message, ($failures > 0), array(Application :: PARAM_ACTION => UserManager :: ACTION_USER_APPROVAL_BROWSER));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>