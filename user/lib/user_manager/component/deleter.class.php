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
        $id = Request :: get(UserManager :: PARAM_USER_USER_ID);
        if ($id)
        {
            $user = $this->retrieve_user($id);
            
            if (! $this->get_user()->is_platform_admin())
            {
                $trail = new BreadcrumbTrail();
                $trail->add_help('user general');
                $this->display_header($trail);
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }
            
            if (UserDataManager :: get_instance()->user_deletion_allowed($user))
                $success = $user->delete();
            
            if ($success)
                Events :: trigger_event('delete', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
            
            $this->redirect(Translation :: get($success ? 'UserDeleted' : 'UserNotDeleted'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>