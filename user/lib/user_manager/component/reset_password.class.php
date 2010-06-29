<?php
/**
 * $Id: reset_password.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */
/**
 * This component can be used to reset the password of a user. The user will be
 * asked for his email-address and if the authentication source of the user
 * allows password resets, an email with further instructions will be send to
 * the user.
 */
class UserManagerResetPasswordComponent extends UserManager
{
    const PARAM_RESET_KEY = 'key';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('LostPassword')));
        $trail->add_help('user general');

        //$user_id = $this->get_user_id();
        $user_id = Session :: get_user_id();
        
        if ($this->get_platform_setting('allow_password_retrieval', 'admin') == false)
        {
            Display :: not_allowed();
        }
        if (isset($user_id))
        {
            $this->display_header();
            Display :: warning_message(Translation :: get('AlreadyRegistered'));
            $this->display_footer();
            exit();
        }
        $this->display_header();
        $request_key = Request :: get(self :: PARAM_RESET_KEY);
        $request_user_id = Request :: get(User :: PROPERTY_ID);
        if (! is_null($request_key) && ! is_null($request_user_id))
        {
            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user($request_user_id);
            if ($this->get_user_key($user) == $request_key)
            {
                $this->create_new_password($user);
                Event :: trigger('reset_password', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id()));
                Display :: normal_message('lang_your_password_has_been_emailed_to_you');
            }
            else
            {
                Display :: error_message(Translation :: get('InvalidRequest'));
            }
        }
        else
        {
            $form = new FormValidator('lost_password', 'post', $this->get_url());
            $form->addElement('text', User :: PROPERTY_EMAIL, Translation :: get('Email'));
            $form->addRule(User :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
            $form->addRule(User :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');
            $form->addElement('submit', 'submit', Translation :: get('Ok'));
            if ($form->validate())
            {
                $udm = UserDataManager :: get_instance();
                $values = $form->exportValues();
                $users = $udm->retrieve_users_by_email($values[User :: PROPERTY_EMAIL]);
                if (count($users) == 0)
                {
                    Display :: error_message('NoUserWithThisEmail');
                }
                else
                {
                    $failures = 0;
                    
                	foreach ($users as $index => $user)
                    {
                        $auth_source = $user->get_auth_source();
                        $auth = Authentication :: factory($auth_source);
                        if (! $auth instanceof ChangeablePassword)
                        {
                            Display :: error_message('ResetPasswordNotPossibleForThisUser');
                        }
                        else
                        { 
                            if(!$this->send_reset_link($user))
                            {
                            	$failures++;
                            }
                        }
                    }
                    
                    $message = $this->get_result($failures, count($users), 'ResetLinkHasNotBeenSend', 'ResetLinksHasNotBeenSend', 'ResetLinkHasBeenSend', 'ResetLinksHasBeenSend');
                    if($failures == 0)
                    {
                    	Display :: normal_message($message);
                    }
                    else
                    {
                    	Display :: error_message($message);
                    }
                }
            }
            else
            {
                $form->display();
            }
        }
        $this->display_footer();
    }

    /**
     * Creates a new random password for the given user and sends an email to
     * this user with the new password.
     * @param User $user
     * @return boolean True if successfull.
     */
    private function create_new_password($user)
    {
        $password = Text :: generate_password();
        $user->set_password(Hashing :: hash($password));
        $user->update();
        $mail_subject = Translation :: get('LoginRequest');
        $mail_body[] = $user->get_fullname() . ',';
        $mail_body[] = Translation :: get('YourAccountParam') . ' ' . $this->get_path(WEB_PATH);
        $mail_body[] = Translation :: get('UserName') . ' :' . $user->get_username();
        $mail_body[] = Translation :: get('Pass') . ' :' . $password;
        $mail_body = implode("\n", $mail_body);
        $mail = Mail :: factory($mail_subject, $mail_body, $user->get_email());
        return $mail->send();
    }

    /**
     * Sends an email to the user containing a reset link to request a password
     * change.
     * @param User $user
     * @return boolean True if successfull.
     */
    private function send_reset_link($user)
    {
        $url_params[self :: PARAM_RESET_KEY] = $this->get_user_key($user);
        $url_params[User :: PROPERTY_ID] = $user->get_id();
        $url = $this->get_url($url_params);
        $mail_subject = Translation :: get('LoginRequest');
        $mail_body[] = $user->get_fullname() . ',';
        $mail_body[] = Translation :: get('UserName') . ' :' . $user->get_username();
        $mail_body[] = Translation :: get('YourAccountParam') . ' ' . $this->get_path(WEB_PATH) . ': ' . $url;
        $mail_body = implode("\n", $mail_body);

        $mail = Mail :: factory($mail_subject, $mail_body, $user->get_email());
        return $mail->send();
    }

    /**
     * Creates a key which is used to identify the user
     * @param User $user
     * @return string The requested key
     */
    private function get_user_key($user)
    {
        global $security_key;
        return Hashing :: hash($security_key . $user->get_email());
    }
}
?>