<?php
/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerActiveChangerComponent extends UserManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
		$active = Request :: get(UserManager :: PARAM_ACTIVE);        
        
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
	            $user->set_active($active);
	            
	            if ($user->update())
	            {
	                Events :: trigger_event('update', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
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