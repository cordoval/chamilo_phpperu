<?php
/**
 * $Id: creator.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerCreatorComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user_id = $this->get_user_id();
        
        if (!UserRights :: is_allowed_in_users_subtree(UserRights :: ADD_RIGHT, 0))
        {
            $this->display_header();
            Display :: warning_message(Translation :: get('AlreadyRegistered'));
            $this->display_footer();
            exit();
        }
        $user = new User();
        $user->set_platformadmin(0);
        $user->set_password(1);
        
        $user_info = $this->get_user();
        $user->set_creator_id($user_info->get_id());
        
        $form = new UserForm(UserForm :: TYPE_CREATE, $user, $this->get_user(), $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_user();
            if ($success == 1)
            {
                $this->redirect(Translation :: get($success ? 'UserCreated' : 'UserNotCreated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
            
            }
            else
            {
                Request :: set_get('error_message', Translation :: get('UsernameNotAvailable'));
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
    	$breadcrumbtrail->add_help('user_creator');
    }
    
}
?>