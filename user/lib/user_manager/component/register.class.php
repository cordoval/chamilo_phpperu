<?php
/**
 * $Id: register.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerRegisterComponent extends UserManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        
        if ($this->get_platform_setting('allow_registration', 'admin') == false)
        {
            Display :: not_allowed();
        }
        
        $user = $this->get_user();
        
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserRegister')));
        $trail->add_help('user general');
        
        if (isset($user))
        {
            $this->display_header($trail);
            Display :: warning_message(Translation :: get('AlreadyRegistered'));
            $this->display_footer();
            exit();
        }
        $user = new User();
        $user->set_platformadmin(0);
        $user->set_password(1);
        //$user->set_creator_id($user_info['user_id']);
        

        $form = new RegisterForm($user, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_user();
            if ($success == 1)
            {
                //$this->redirect(Translation :: get($success ? 'UserRegistered' : 'UserNotRegistered'), ($success ? false : true), array(), array(), false, Redirect :: TYPE_LINK);
                
            	$parameters = array();
            	
	            if (PlatformSetting :: get('allow_registration', 'user') == 2)
	        	{
	        		$parameters['message'] = Translation :: get('UserAwaitingApproval');
	        	}
            	
                Redirect :: link('', $parameters, array(), false, null);
            }
            else
            {
                Request :: set_get('error_message', Translation :: get('UsernameNotAvailable'));
                $this->display_header($trail);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>